<?php

declare(strict_types=1);

namespace Honeystone\Seo\Tests;

use Honeystone\Seo\Providers\SeoServiceProvider;
use Illuminate\Support\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    /**
     * @return array<class-string<ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [SeoServiceProvider::class];
    }
}
