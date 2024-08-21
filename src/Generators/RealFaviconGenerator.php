<?php

declare(strict_types=1);

namespace Honeystone\Seo\Generators;

use Honeystone\Seo\Concerns\HasConfig;
use Honeystone\Seo\Concerns\HasData;
use Honeystone\Seo\Concerns\HasDefaults;
use Honeystone\Seo\Contracts\GeneratesMetadata;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use ZipArchive;

use function base64_encode;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function json_decode;
use function mkdir;
use function public_path;
use function rtrim;
use function unlink;
use function view;

use const JSON_THROW_ON_ERROR;

final class RealFaviconGenerator implements GeneratesMetadata
{
    use HasDefaults, HasData, HasConfig;

    /**
     * @var array<string, bool>
     */
    private array $settings = [
        'error_on_image_too_small' => true,
        'readme_file' => false,
        'html_code_file' => true,
    ];

    public function getName(): string
    {
        return 'favicon';
    }

    public function fetch(?string $apiKey = null, ?string $image = null): self
    {
        $this->checkForCleanDirectory();

        $url = $this->pullExistingUrl() ?? $this->generateFavicon($apiKey, $image);

        $this->fetchPackage($url);

        $this->extractPackage();

        return $this;
    }

    public function generate(): ?View
    {
        if (
            !($this->config['enabled'] ?? true) ||
            !file_exists($this->getFaviconPath('html_code.html'))
        ) {
            return null;
        }

        return view('honeystone-seo::favicons', [
            'html' => file_get_contents($this->getFaviconPath('html_code.html')),
        ]);
    }

    private function generateFavicon(?string $apiKey = null, ?string $image = null): string
    {
        $response = Http::post('https://realfavicongenerator.net/api/favicon', [
            'favicon_generation' => [
                'api_key' => $apiKey ?? $this->config['apiKey'],
                'master_picture' => $this->config['master_picture'] ?? [
                    'type' => 'inline',
                    'content' => base64_encode(file_get_contents($image ?? resource_path($this->config['image']))),
                ],
                'files_location' => [
                    'type' => 'path',
                    'path' => '/favicons',
                ],
                'favicon_design' => ['desktop_browser' => []] + ($this->config['design'] ?? []),
                'settings' => $this->settings + ($this->config['settings'] ?? []),
            ],
        ])
            ->throw();

        $url = Arr::get(
            json_decode($response->body(), true, JSON_THROW_ON_ERROR),
            'favicon_generation_result.favicon.package_url',
        );

        if (!file_exists($this->getFaviconPath())) {

            if (!mkdir($this->getFaviconPath())) {
                throw new RuntimeException('Failed to create favicon directory.');
            }
        }

        file_put_contents($this->getFaviconPath('url.txt'), $url);

        return $url;
    }

    /**
     * We already have a URL, so we should try and grab the package one more
     * time before starting the process again.
     */
    private function pullExistingUrl(): ?string
    {
        if (!file_exists($this->getFaviconPath('url.txt'))) {
            return null;
        }

        $url = file_get_contents($this->getFaviconPath('url.txt'));

        unlink($this->getFaviconPath('url.txt'));

        return $url;
    }

    private function fetchPackage(string $url): void
    {
        Http::sink($this->getFaviconPath('package.zip'))
            ->get($url)
            ->throw();

        if (file_exists($this->getFaviconPath('url.txt'))) {
            unlink($this->getFaviconPath('url.txt'));
        }
    }

    private function extractPackage(): void
    {
        $zip = new ZipArchive();

        if ($zip->open($this->getFaviconPath('package.zip')) === false) {
            throw new RuntimeException('Unable to open favicon package.');
        }

        if (!$zip->extractTo($this->getFaviconPath())) {

            $zip->close();

            throw new RuntimeException('Failed to extract favicon package.');
        }

        $zip->close();

        unlink($this->getFaviconPath('package.zip'));
    }

    private function checkForCleanDirectory(): void
    {
        if (
            file_exists($this->getFaviconPath()) &&
            !file_exists($this->getFaviconPath('url.txt'))
        ) {
            throw new RuntimeException(
                'Favicons appear to have already been generated. Remove the `'.
                $this->getFaviconPath().'` to generate new icons.',
            );
        }
    }

    private function getFaviconPath(string $append = ''): string
    {
        return public_path(rtrim(rtrim($this->config['path'] ?? 'favicons', '/').'/'.$append, '/'));
    }
}
