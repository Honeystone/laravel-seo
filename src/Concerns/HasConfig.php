<?php

declare(strict_types=1);

namespace Honeystone\Seo\Concerns;

use function array_filter;
use function in_array;

trait HasConfig
{
    /**
     * @var array<string, mixed>
     */
    private array $config = [];

    /**
     * @param array<string, mixed> $data
     *
     * @return $this
     */
    public function config(array $data): self
    {
        $this->config = array_filter(
            [...$this->config, ...$data],
            //remove meaningless config
            static fn (mixed $value): bool => !in_array($value, [null, '', []], true),
        );

        return $this;
    }
}
