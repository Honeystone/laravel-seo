<?php

declare(strict_types=1);

namespace Honeystone\Seo;

use Honeystone\Seo\Concerns\HasConfig;
use Honeystone\Seo\Concerns\HasDefaults;
use Honeystone\Seo\Contracts\BuildsMetadata;
use Honeystone\Seo\Contracts\GeneratesMetadata;
use Honeystone\Seo\Contracts\RegistersGenerators;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use RuntimeException;

use function array_filter;
use function array_map;
use function compact;
use function lcfirst;
use function str_replace;
use function trim;
use function view;

final class MetadataDirector implements BuildsMetadata
{
    use HasDefaults;

    use HasConfig {
        config as private setConfig;
    }

    public function __construct(
        private readonly RegistersGenerators $register,
        ?Repository $config = null,
    ) {
        $this->config = $config !== null ?
            $config->get('honeystone-seo') :
            [];
    }

    /**
     * @param array<string, mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        $callName = $name;

        $name = Arr::first(
            $this->register->allNames(),
            static fn (string $name): bool => Str::startsWith($callName, Str::camel($name)),
        );

        if ($name === null) {
            throw new RuntimeException("Call to undefined method {$callName}.");
        }

        $method = lcfirst(str_replace(Str::camel($name), '', $callName));

        $response = $this->generator($name)->{$method}(...$arguments);

        if ($response instanceof GeneratesMetadata) {
            return $this;
        }

        return $response;
    }

    public function getName(): string
    {
        return 'director';
    }

    public function getConfig(string $path, mixed $default = false): mixed
    {
        return Arr::get($this->config, $path, $default);
    }

    public function locale(?string $value): self
    {
        return $this->default(__FUNCTION__, $value);
    }

    public function title(?string $value, string|false|null $template = null): self
    {
        if ($template !== null) {
            $this->default('titleTemplate', $template === false ? '' : $template);
        }

        return $this->default(__FUNCTION__, $value);
    }

    public function description(?string $value): self
    {
        return $this->default(__FUNCTION__, $value);
    }

    public function keywords(?string ...$value): self
    {
        $this->default(__FUNCTION__, $value);

        if (
            $this->getConfig('sync.keywords-tags') &&
            !isset($this->defaults['tags'])
        ) {
            $this->default('tags', $value);
        }

        return $this;
    }

    public function url(?string $value): self
    {
        $this->default(__FUNCTION__, $value);

        if (
            $this->getConfig('sync.url-canonical') &&
            !isset($this->defaults['canonical'])
        ) {
            $this->canonical($value);
        }

        return $this;
    }

    public function canonical(?string $value): self
    {
        $this->default(__FUNCTION__, $value);

        if (
            $this->getConfig('sync.url-canonical') &&
            !isset($this->defaults['url'])
        ) {
            $this->url($value);
        }

        return $this;
    }

    public function canonicalEnabled(bool $value): self
    {
        return $this->default(__FUNCTION__, $value);
    }

    public function images(?string ...$value): self
    {
        return $this->default(__FUNCTION__, $value);
    }

    public function robots(?string ...$value): self
    {
        return $this->default(__FUNCTION__, $value);
    }

    public function generator(string $name): GeneratesMetadata
    {
        return $this->register->get($name);
    }

    public function generate(string ...$only): View
    {
        $generated = implode(
            "\n    ",
            array_filter(array_map(
                fn (GeneratesMetadata $generator): string => trim(
                    /** @phpstan-ignore-next-line */
                    (string) $generator->generate(),
                ),
                count($only) > 0 ?
                    $this->register->only($only) :
                    $this->register->all(),
            )),
        );

        return view('honeystone-seo::layout', compact('generated'));
    }

    public function config(array $data): self
    {
        $this->setConfig($data);

        $this->propagateConfig();

        return $this;
    }

    /**
     * @return $this
     */
    private function default(string $key, mixed $value): self
    {
        $this->defaults[$key] = $value;

        $this->propagateDefaults();

        return $this;
    }

    private function propagateConfig(): void
    {
        foreach ($this->register->all() as $generator) {
            $generator->config($this->getConfig('generators.'.$generator::class, []));
        }
    }

    private function propagateDefaults(): void
    {
        foreach ($this->register->all() as $generator) {
            $generator->defaults($this->defaults);
        }
    }
}
