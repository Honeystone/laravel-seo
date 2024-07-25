<?php

declare(strict_types=1);

namespace Honeystone\Seo;

use Honeystone\Seo\Contracts\GeneratesMetadata;
use Honeystone\Seo\Contracts\RegistersGenerators;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Arr;

use function app;
use function array_keys;

final class Registry implements RegistersGenerators
{
    /**
     * @var array<string, GeneratesMetadata>
     */
    private array $generators = [];

    /**
     * @var array<class-string, array<string, mixed>>
     */
    private array $config;

    public function __construct(?Repository $config = null)
    {
        $this->config = $config !== null ?
            $config->get('honeystone-seo.generators') :
            [];

        $this->makeConfiguredGenerators();
    }

    public function register(string $name, GeneratesMetadata $generator): self
    {
        $this->generators[$name] = $generator;

        return $this;
    }

    public function get(string $name): GeneratesMetadata
    {
        return $this->generators[$name];
    }

    public function all(): array
    {
        return $this->generators;
    }

    public function only(array $names): array
    {
        return Arr::only($this->generators, $names);
    }

    public function allNames(): array
    {
        return array_keys($this->generators);
    }

    private function makeConfiguredGenerators(): void
    {
        foreach ($this->config as $class => $data) {

            /** @var GeneratesMetadata $generator */
            $generator = app($class);

            $this->register($generator->getName(), $generator->config($data));
        }
    }
}
