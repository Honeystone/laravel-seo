<?php

declare(strict_types=1);

namespace Honeystone\Seo\OpenGraph\Contracts;

interface Taggable
{
    /**
     * @param string|array<string> $tag
     */
    public function defaultTag(string|array $tag): void;
}
