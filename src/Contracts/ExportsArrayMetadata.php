<?php

declare(strict_types=1);

namespace Honeystone\Seo\Contracts;

interface ExportsArrayMetadata
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
