<?php

declare(strict_types=1);

namespace Honeystone\Seo\Contracts;

use Illuminate\Contracts\View\View;

interface GeneratesMetadata
{
    public function getName(): string;

    /**
     * @param array<string, mixed> $data
     *
     * @return $this
     */
    public function config(array $data): self;

    /**
     * @param array<string, mixed> $data
     *
     * @return $this
     */
    public function defaults(array $data): self;

    public function generate(): View|string|null;
}
