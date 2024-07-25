<?php

declare(strict_types=1);

namespace Honeystone\Seo\Concerns;

use function array_filter;

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
        $this->config = array_filter([...$this->config, ...$data]);

        return $this;
    }
}
