<?php

declare(strict_types=1);

namespace Honeystone\Seo\Concerns;

use Illuminate\Support\Str;

use function array_filter;
use function array_key_exists;
use function array_keys;
use function is_array;
use function method_exists;
use function property_exists;

trait HasData
{
    /**
     * @var array<string, mixed>
     */
    private array $data = [];

    /**
     * @return $this
     */
    private function data(string $key, mixed $value): self
    {
        if (is_array($value)) {

            $value = array_filter($value);

            if (count($value) === 0) {
                $value = null;
            }
        }

        if ($value === null) {

            unset($this->data[$key]);

            return $this;
        }

        $this->data[$key] = $value;

        return $this;
    }

    /**
     * @return $this
     */
    private function alias(string $key, mixed $value): self
    {
        if (!array_key_exists($key, $this->data)) {
            $this->data($key, $value);
        }

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    private function getData(): array
    {
        $this->applyDefaults();

        /** @phpstan-ignore-next-line */
        if (property_exists($this, 'config')) {
            return ['config' => $this->config] + $this->data;
        }

        return $this->data;
    }

    private function getRaw(string $key, mixed $default = null, bool $baseOnConfig = true): mixed
    {
        $value = null;

        /** @phpstan-ignore-next-line */
        if ($baseOnConfig && property_exists($this, 'config')) {
            $value = $this->config[$key] ?? $default;
        }

        /** @phpstan-ignore-next-line */
        if (property_exists($this, 'defaults')) {
            $value = $this->defaults[$key] ?? $value;
        }

        return $this->data[$key] ?? $value;
    }

    private function applyDefaults(): void
    {
        /** @phpstan-ignore-next-line */
        if (!property_exists($this, 'defaults')) {
            return;
        }

        foreach (array_keys($this->defaults) as $key) {
            if (!array_key_exists($key, $this->data)) {
                $this->applyDefault($key);
            }
        }
    }

    private function applyDefault(string $key): void
    {
        $method = Str::camel($key);

        if (method_exists($this, $method)) {
            $this->{$method}($this->defaults[$key]);
            return;
        }

        $this->data[$key] = $this->defaults[$key];
    }
}
