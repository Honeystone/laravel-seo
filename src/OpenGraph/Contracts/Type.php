<?php

declare(strict_types=1);

namespace Honeystone\Seo\OpenGraph\Contracts;

interface Type
{
    public function getPrefix(): string;

    public function getType(): string;
}
