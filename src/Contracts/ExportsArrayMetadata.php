<?php

namespace Honeystone\Seo\Contracts;

/**
 * Implemented by metadata generators that can expose their payload as an array.
 */
interface ExportsArrayMetadata
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}

