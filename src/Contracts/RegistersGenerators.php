<?php

declare(strict_types=1);

namespace Honeystone\Seo\Contracts;

interface RegistersGenerators
{
    /**
     * @return $this
     */
    public function register(string $name, GeneratesMetadata $generator): self;

    public function get(string $name): GeneratesMetadata;

    /**
     * @return array<string, GeneratesMetadata>
     */
    public function all(): array;

    /**
     * @param array<string> $names
     *
     * @return array<string, GeneratesMetadata>
     */
    public function only(array $names): array;

    /**
     * @return array<string>
     */
    public function allNames(): array;
}
