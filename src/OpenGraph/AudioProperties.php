<?php

declare(strict_types=1);

namespace Honeystone\Seo\OpenGraph;

class AudioProperties
{
    public function __construct(
        public readonly ?string $url = null,
        public readonly ?string $secureUrl = null,
        public readonly ?string $type = null,
    ) {
    }
}
