<?php

declare(strict_types=1);

namespace Honeystone\Seo\Commands;

use Honeystone\Seo\Generators\RealFaviconGenerator;
use Illuminate\Console\Command;

use function config;
use function file_exists;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;
use function public_path;
use function rtrim;
use function seo;

final class GenerateFavicons extends Command
{
    /**
     * @var string
     */
    protected $signature = 'seo:generate-favicons {key?} {image?}';

    /**
     * @var string
     */
    protected $description = 'Generate the favicons.';

    public function handle(): int
    {
        $configPath = 'honeystone-seo.generators.'.RealFaviconGenerator::class.'.';
        $storagePath = rtrim(config($configPath.'path') ?? 'favicons', '/');

        if (file_exists(public_path($storagePath))) {
            error('Favicons have already been generated. Remove `/public/'.$storagePath.'` before regenerating them.');
            return 1;
        }

        $key = null;
        $image = null;

        if (!config($configPath.'apiKey')) {

            note('API key was not present in the config file.');

            $key = $this->argument('key') ?? text(
                label: 'Enter your API key',
                hint: 'A key can be obtained from the realfavicongenerator.net website.',
                required: true,
            );

        } else {
            info('Using configured API key.');
        }

        if (!config($configPath.'image')) {

            note('Source image was not present in the config file.');

            $image = $this->argument('image') ?? text(
                label: 'Where is your source image?',
                placeholder: './path/to/image.png',
                required: true,
            );

            if (!file_exists($image)) {
                error('This image does not exist.');
                return 1;
            }

        } else {
            info('Using configured source image.');
        }

        spin(
            message: 'Fetching favicons...',
            callback: fn () => seo()->faviconFetch($key, $image),
        );

        info('Favicons generated successfully.');  return 0;
    }
}
