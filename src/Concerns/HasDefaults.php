<?php

declare(strict_types=1);

namespace Honeystone\Seo\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait HasDefaults
{
    /**
     * @var array<string, mixed>
     */
    private array $defaults = [];

    /**
     * @param array<string, mixed> $data
     *
     * @return $this
     */
    public function defaults(array $data): self
    {
        $this->defaults = [
            ...$this->defaults,
            ...Arr::where(
                $data,
                fn (mixed $value, string $key): bool => method_exists($this, Str::camel($key)),
            ),
        ];

        return $this;
    }
}
