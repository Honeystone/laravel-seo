<?php

declare(strict_types=1);

namespace Honeystone\Seo\Tests\Browser\Support;

final class PageB extends Page
{
    protected function pageName(): string
    {
        return 'Page B';
    }

    protected function otherPageUrl(): string
    {
        return '/a';
    }
}
