<?php

declare(strict_types=1);

use Honeystone\Seo\Generators\RealFaviconGenerator;
use Illuminate\Support\Facades\Http;

afterEach(function (): void {
    unlink(public_path('favicons/html_code.html'));
    unlink(public_path('favicons/favicon-16x16.png'));
    rmdir(public_path('favicons'));
});

it('fetches favicons', function (): void {

    Http::fake([
        'https://realfavicongenerator.net/api/favicon' => Http::response([
            'favicon_generation_result' => [
                'favicon' => [
                    'package_url' => 'https://foo.bar',
                ],
            ],
        ]),
        'https://foo.bar' => Http::response(file_get_contents(__DIR__.'/Fixture/package.zip')),
    ]);

    seo()
        ->faviconFetch('foobar', __DIR__.'/Fixture/rocket.svg');

    expect(trim((string) seo()->generate('favicon')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Favicons -->
<link rel="icon" type="image/png" sizes="16x16" href="/favicons/favicon-16x16.png">
<!-- End Honeystone SEO -->
END
    );
});

it('fetches favicons in two stages', function (): void {

    Http::fake([
        'https://realfavicongenerator.net/api/favicon' => Http::response([
            'favicon_generation_result' => [
                'favicon' => [
                    'package_url' => 'https://foo.bar',
                ],
            ],
        ]),
        'https://foo.bar' => Http::sequence()
            ->pushStatus(503)
            ->push(file_get_contents(__DIR__.'/Fixture/package.zip')),
    ]);

    try {

        seo()
            ->faviconFetch('foobar', __DIR__.'/Fixture/rocket.svg');

    } catch (Throwable) {}

    expect(seo()->faviconGenerate())->toBeNull();

    seo()
        ->faviconFetch('foobar');

    expect(trim((string) seo()->generate('favicon')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Favicons -->
<link rel="icon" type="image/png" sizes="16x16" href="/favicons/favicon-16x16.png">
<!-- End Honeystone SEO -->
END
    );
});

it('can be disabled', function (): void {

    seo()->config([
        'generators' => [
            RealFaviconGenerator::class => [
                'enabled' => false,
            ],
        ],
    ]);

    Http::fake([
        'https://realfavicongenerator.net/api/favicon' => Http::response([
            'favicon_generation_result' => [
                'favicon' => [
                    'package_url' => 'https://foo.bar',
                ],
            ],
        ]),
        'https://foo.bar' => Http::response(file_get_contents(__DIR__.'/Fixture/package.zip')),
    ]);

    seo()
        ->faviconFetch('foobar', __DIR__.'/Fixture/rocket.svg');

    expect(seo()->faviconGenerate())->toBeNull();
});

it('throws exception for dirty directory', function (): void {

    Http::fake([
        'https://realfavicongenerator.net/api/favicon' => Http::response([
            'favicon_generation_result' => [
                'favicon' => [
                    'package_url' => 'https://foo.bar',
                ],
            ],
        ]),
        'https://foo.bar' => Http::response(file_get_contents(__DIR__.'/Fixture/package.zip')),
    ]);

    seo()
        ->faviconFetch('foobar', __DIR__.'/Fixture/rocket.svg');

    seo()
        ->faviconFetch('foobar', __DIR__.'/Fixture/rocket.svg');

})
    ->throws(RuntimeException::class);

it('fetches favicons using command', function (): void {

    config()->set('honeystone-seo.generators.'.RealFaviconGenerator::class.'.apiKey', 'foobar');
    config()->set('honeystone-seo.generators.'.RealFaviconGenerator::class.'.image', 'rocket.svg');

    Http::fake([
        'https://realfavicongenerator.net/api/favicon' => Http::response([
            'favicon_generation_result' => [
                'favicon' => [
                    'package_url' => 'https://foo.bar',
                ],
            ],
        ]),
        'https://foo.bar' => Http::response(file_get_contents(__DIR__.'/Fixture/package.zip')),
    ]);

    copy(__DIR__.'/Fixture/rocket.svg', resource_path('rocket.svg'));

    $this->artisan('seo:generate-favicons')->assertSuccessful();

    expect(trim((string) seo()->generate('favicon')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Favicons -->
<link rel="icon" type="image/png" sizes="16x16" href="/favicons/favicon-16x16.png">
<!-- End Honeystone SEO -->
END
    );

    unlink(resource_path('rocket.svg'));
});

it('fetches favicons using command without config', function (): void {

    Http::fake([
        'https://realfavicongenerator.net/api/favicon' => Http::response([
            'favicon_generation_result' => [
                'favicon' => [
                    'package_url' => 'https://foo.bar',
                ],
            ],
        ]),
        'https://foo.bar' => Http::response(file_get_contents(__DIR__.'/Fixture/package.zip')),
    ]);

    copy(__DIR__.'/Fixture/rocket.svg', resource_path('rocket.svg'));

    $this->artisan('seo:generate-favicons')
        ->expectsQuestion('Enter your API key', 'foobar')
        ->expectsQuestion('Where is your source image?', resource_path('rocket.svg'))
        ->assertSuccessful();

    expect(trim((string) seo()->generate('favicon')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Favicons -->
<link rel="icon" type="image/png" sizes="16x16" href="/favicons/favicon-16x16.png">
<!-- End Honeystone SEO -->
END
    );

    unlink(resource_path('rocket.svg'));
});
