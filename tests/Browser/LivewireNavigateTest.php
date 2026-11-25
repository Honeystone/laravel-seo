<?php

declare(strict_types=1);

use Honeystone\Seo\Tests\Browser\Support\PageA;
use Honeystone\Seo\Tests\Browser\Support\PageB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;

function waitUntilInBrowser(mixed $page, string $expression): bool
{
    return (bool) $page->script(<<<JS
        new Promise((resolve) => {
            const started = Date.now();
            const timer = setInterval(() => {
                if ($expression) {
                    clearInterval(timer);
                    resolve(true);
                } else if (Date.now() - started > 5000) {
                    clearInterval(timer);
                    resolve(false);
                }
            }, 50);
        })
        JS);
}

function navigateTo(mixed $page, string $name): void
{
    $page->click('#to-other');

    expect(waitUntilInBrowser(
        $page,
        "document.title === '$name' && document.querySelector('h1')?.textContent === '$name'",
    ))
        ->toBeTrue("Timed out waiting to navigate to $name");
}

function jsonLdScripts(mixed $page): array
{
    return $page->script(<<<'JS'
        Array.from(document.querySelectorAll('script[type="application/ld+json"]'))
            .map((script) => script.textContent)
        JS);
}

beforeEach(function (): void {
    View::addNamespace('seo-test', __DIR__.'/views');

    Livewire::component('seo-test-page-a', PageA::class);
    Livewire::component('seo-test-page-b', PageB::class);

    foreach (['head', 'body'] as $placement) {
        Route::get("/$placement/a", PageA::class)->middleware('web');
        Route::get("/$placement/b", PageB::class)->middleware('web');
    }
});

it('replaces the head tags when navigating', function (): void {
    $page = visit('/head/a');

    expect($page->script('document.title'))
        ->toBe('Page A')
        ->and($page->script("document.querySelector('meta[name=description]').content"))
        ->toBe('Description for Page A');

    navigateTo($page, 'Page B');

    expect($page->script("document.querySelectorAll('meta[name=description]').length"))
        ->toBe(1)
        ->and($page->script("document.querySelector('meta[name=description]').content"))
        ->toBe('Description for Page B');
})->group('browser');

it('duplicates json-ld in the head when navigating', function (): void {
    $page = visit('/head/a');

    expect(jsonLdScripts($page))->toHaveCount(1);

    navigateTo($page, 'Page B');

    expect(count(jsonLdScripts($page)))->toBeGreaterThan(1);
})->group('browser');

it('replaces json-ld when it\'s rendered in the body', function (): void {
    $page = visit('/body/a');

    expect(jsonLdScripts($page))->toHaveCount(1)->and(jsonLdScripts($page)[0])->toContain('Page A');

    navigateTo($page, 'Page B');

    expect(jsonLdScripts($page))->toHaveCount(1)->and(jsonLdScripts($page)[0])->toContain('Page B');

    navigateTo($page, 'Page A');

    expect(jsonLdScripts($page))->toHaveCount(1)->and(jsonLdScripts($page)[0])->toContain('Page A');
})->group('browser');
