<?php

declare(strict_types=1);

namespace Honeystone\Seo\Tests\Browser\Support;

final class PageA extends Page
{
    protected function pageName(): string
    {
        return 'Page A';
    }

    protected function otherPageUrl(): string
    {
        return '/b';
    }
}
