<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;

beforeEach(function (): void {
    seo()->config(['sync' => ['url-canonical' => true]])->url('https://mywebsite.com');
});

it('renders json-ld on its own so it can be placed in the body', function (): void {

    seo()->title('Foo', template: false)->description('Bar');

    $head = Blade::render("@metadata('meta', 'open-graph', 'twitter')");
    $body = Blade::render("@metadata('json-ld')");

    expect($head)
        ->toContain('<title>Foo</title>')
        ->not->toContain('application/ld+json')
        ->and($body)
        ->toContain('application/ld+json')
        ->not->toContain('<title>');
});

it('renders every generator when no arguments are given', function (): void {

    seo()->title('Foo', template: false);

    expect(Blade::render('@metadata'))
        ->toContain('<title>Foo</title>')
        ->toContain('application/ld+json');
});
