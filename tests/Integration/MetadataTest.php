<?php

declare(strict_types=1);

use Honeystone\Seo\Generators\JsonLdGenerator;
use Honeystone\Seo\Generators\MetaGenerator;
use Honeystone\Seo\Generators\OpenGraphGenerator;
use Honeystone\Seo\Generators\TwitterGenerator;
use Honeystone\Seo\OpenGraph\ArticleProperties;
use Honeystone\Seo\OpenGraph\AudioProperties;
use Honeystone\Seo\OpenGraph\BookProperties;
use Honeystone\Seo\OpenGraph\ImageProperties;
use Honeystone\Seo\OpenGraph\ProfileProperties;
use Honeystone\Seo\OpenGraph\VideoProperties;
use Honeystone\Seo\Twitter\AppProperties;
use Honeystone\Seo\Twitter\PlayerProperties;
use Spatie\SchemaOrg\Graph;
use Spatie\SchemaOrg\MultiTypedEntity;
use Spatie\SchemaOrg\Person;

beforeEach(function (): void {
    seo()
        ->config(['sync' => ['url-canonical' => true]])
        ->defaults(['titleTemplate' => '{title}'])
        ->url('https://mywebsite.com');
});

it('propagates the default locale', function (): void {

    seo()->locale('Foo');

    expect(trim((string) seo()->generate('open-graph')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://mywebsite.com">
    <meta property="og:locale" content="Foo">
<!-- End Honeystone SEO -->
END
    );
});

it('overrides the locale', function (): void {

    seo()
        ->locale('Foo')
        ->openGraphLocale('Bar');

    expect(trim((string) seo()->generate('open-graph')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://mywebsite.com">
    <meta property="og:locale" content="Bar">
<!-- End Honeystone SEO -->
END
    );
});

it('propagates the default title', function (): void {

    seo()->title('Foo', template: false);

    expect(trim((string) seo()->generate()))->toBe(
<<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <title>Foo</title>
    <link rel="canonical" href="https://mywebsite.com">
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Foo">
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Foo">
    <meta property="og:url" content="https://mywebsite.com">
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","name":"Foo","url":"https:\/\/mywebsite.com"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('propagates the default title and template', function (): void {

    seo()->title('Foo', '--{title}--');

    expect(trim((string) seo()->generate()))->toBe(
<<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <title>--Foo--</title>
    <link rel="canonical" href="https://mywebsite.com">
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Foo">
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Foo">
    <meta property="og:url" content="https://mywebsite.com">
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","name":"Foo","url":"https:\/\/mywebsite.com"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('propagates the default title and concat template', function (): void {

    seo()->title('Foo', 'Bar');

    expect(trim((string) seo()->generate()))->toBe(
<<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <title>Foo - Bar</title>
    <link rel="canonical" href="https://mywebsite.com">
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Foo">
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Foo">
    <meta property="og:url" content="https://mywebsite.com">
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","name":"Foo","url":"https:\/\/mywebsite.com"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('propagates the default title and blank template', function (): void {

    seo()->title('Foo', '');

    expect(trim((string) seo()->generate()))->toBe(
<<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <title>Foo</title>
    <link rel="canonical" href="https://mywebsite.com">
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Foo">
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Foo">
    <meta property="og:url" content="https://mywebsite.com">
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","name":"Foo","url":"https:\/\/mywebsite.com"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('overrides the title', function (): void {

    seo()
        ->title('Foo', template: false)
        ->metaTitle('Bar')
        ->twitterTitle('Baz')
        ->openGraphTitle('FooBar')
        ->jsonLdTitle('FooBarBaz');

    expect(trim((string) seo()->generate()))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <title>Bar</title>
    <link rel="canonical" href="https://mywebsite.com">
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Baz">
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="FooBar">
    <meta property="og:url" content="https://mywebsite.com">
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","name":"FooBarBaz","url":"https:\/\/mywebsite.com"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('propagates the default description', function (): void {

    seo()->description('Foo');

    expect(trim((string) seo()->generate()))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <meta name="description" content="Foo">
    <link rel="canonical" href="https://mywebsite.com">
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:description" content="Foo">
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:description" content="Foo">
    <meta property="og:url" content="https://mywebsite.com">
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","description":"Foo","url":"https:\/\/mywebsite.com"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('overrides the description', function (): void {

    seo()
        ->description('Foo')
        ->metaDescription('Bar')
        ->twitterDescription('Baz')
        ->openGraphDescription('FooBar')
        ->jsonLdDescription('FooBarBaz');

    expect(trim((string) seo()->generate()))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <meta name="description" content="Bar">
    <link rel="canonical" href="https://mywebsite.com">
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:description" content="Baz">
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:description" content="FooBar">
    <meta property="og:url" content="https://mywebsite.com">
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","description":"FooBarBaz","url":"https:\/\/mywebsite.com"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('propagates the default keywords', function (): void {

    seo()->keywords('Foo', 'Bar');

    expect(trim((string) seo()->generate()))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <meta name="keywords" content="Foo,Bar">
    <link rel="canonical" href="https://mywebsite.com">
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://mywebsite.com">
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","url":"https:\/\/mywebsite.com"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('overrides the keywords', function (): void {

    seo()
        ->keywords('Foo')
        ->metaKeywords(['Bar', 'Baz']);

    expect(trim((string) seo()->generate()))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <meta name="keywords" content="Bar,Baz">
    <link rel="canonical" href="https://mywebsite.com">
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://mywebsite.com">
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","url":"https:\/\/mywebsite.com"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('syncs default keywords to tags', function (): void {

    seo()
        ->config(['sync' => ['keywords-tags' => true]])
        ->keywords('Foo', 'Bar')
        ->openGraphType(new ArticleProperties(section: 'Baz'));

    expect(trim((string) seo()->generate()))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <meta name="keywords" content="Foo,Bar">
    <link rel="canonical" href="https://mywebsite.com">
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <!-- Open Graph -->
    <meta property="og:type" content="article">
    <meta property="article:section" content="Baz">
    <meta property="article:tag" content="Foo">
    <meta property="article:tag" content="Bar">
    <meta property="og:url" content="https://mywebsite.com">
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","url":"https:\/\/mywebsite.com"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('propagates the default url', function (): void {

    seo()
        ->config(['sync' => ['url-canonical' => false]])
        ->url('https://foo.bar');

    expect(trim((string) seo()->generate()))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <link rel="canonical" href="https://mywebsite.com">
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://foo.bar">
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","url":"https:\/\/foo.bar"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('overrides the url', function (): void {

    seo()
        ->config(['sync' => ['url-canonical' => false]])
        ->url('https://foo.bar')
        ->openGraphUrl('https://bar.baz')
        ->jsonLdUrl('https://baz.foo');

    expect(trim((string) seo()->generate()))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <link rel="canonical" href="https://mywebsite.com">
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://bar.baz">
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","url":"https:\/\/baz.foo"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('propagates the default canonical url', function (): void {

    seo()
        ->config(['sync' => ['url-canonical' => false]])
        ->canonical('https://foo.bar');

    expect(trim((string) seo()->generate()))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <link rel="canonical" href="https://foo.bar">
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://mywebsite.com">
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","url":"https:\/\/mywebsite.com"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('overrides the canonical url', function (): void {

    seo()
        ->config(['sync' => ['url-canonical' => false]])
        ->canonical('https://foo.bar')
        ->metaCanonical('https://bar.baz');

    expect(trim((string) seo()->generate('meta')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <link rel="canonical" href="https://bar.baz">
<!-- End Honeystone SEO -->
END
    );
});

it('propagates the default canonical enabled flag', function (): void {

    seo()
        ->canonicalEnabled(false);

    expect(trim((string) seo()->generate('meta')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    
<!-- End Honeystone SEO -->
END
    );
});

it('overrides the canonical enabled flag', function (): void {

    seo()
        ->canonicalEnabled(false)
        ->metaCanonicalEnabled(true);

    expect(trim((string) seo()->generate('meta')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <link rel="canonical" href="https://mywebsite.com">
<!-- End Honeystone SEO -->
END
    );
});

it('propagates the default images', function (): void {

    seo()
        ->images(
            'https://mywebsite/image-1.png',
            'https://mywebsite/image-2.png',
            'https://mywebsite/image-3.png',
        );

    expect(trim((string) seo()->generate()))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <link rel="canonical" href="https://mywebsite.com">
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="https://mywebsite/image-1.png">
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:image" content="https://mywebsite/image-1.png">
    <meta property="og:image" content="https://mywebsite/image-2.png">
    <meta property="og:image" content="https://mywebsite/image-3.png">
    <meta property="og:url" content="https://mywebsite.com">
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","image":["https:\/\/mywebsite\/image-1.png","https:\/\/mywebsite\/image-2.png","https:\/\/mywebsite\/image-3.png"],"url":"https:\/\/mywebsite.com"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('overrides the images', function (): void {

    seo()
        ->images(
            'https://mywebsite/image-1.png',
            'https://mywebsite/image-2.png',
            'https://mywebsite/image-3.png',
        )
        ->twitterImage('https://mywebsite/image-2.png')
        ->openGraphImages([
            'https://mywebsite/image-3.png',
            'https://mywebsite/image-4.png',
        ])
        ->jsonLdImages(['https://mywebsite/image-5.png']);

    expect(trim((string) seo()->generate()))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <link rel="canonical" href="https://mywebsite.com">
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="https://mywebsite/image-2.png">
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:image" content="https://mywebsite/image-3.png">
    <meta property="og:image" content="https://mywebsite/image-4.png">
    <meta property="og:url" content="https://mywebsite.com">
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","image":["https:\/\/mywebsite\/image-5.png"],"url":"https:\/\/mywebsite.com"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('adds custom meta tags', function (): void {

    seo()
        ->metaTag('Foo', 'Bar')
        ->metaTag('Bar', ['Foo', 'Baz']);

    expect(trim((string) seo()->generate('meta')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <link rel="canonical" href="https://mywebsite.com">
    <meta name="Foo" content="Bar">
    <meta name="Bar" content="Foo">
    <meta name="Bar" content="Baz">
<!-- End Honeystone SEO -->
END
    );
});

it('default custom meta tags are ignored', function (): void {

    seo()
        ->defaults(['tag' => [['Foo' => 'Bar']]]);

    expect(trim((string) seo()->generate('meta')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <link rel="canonical" href="https://mywebsite.com">
<!-- End Honeystone SEO -->
END
    );
});

it('configured custom meta tags are merged', function (): void {

    seo()
        ->config([
            'generators' => [
                MetaGenerator::class => [
                    'custom' => [
                        [
                            'Foo' => 'Bar',
                        ],
                    ],
                ],
            ],
        ])
        ->metaTag('Bar', 'Baz');

    expect(trim((string) seo()->generate('meta')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <link rel="canonical" href="https://mywebsite.com">
    <meta name="Foo" content="Bar">
    <meta name="Bar" content="Baz">
<!-- End Honeystone SEO -->
END
    );
});

it('enables twitter', function (): void {

    seo()
        ->config([
            'generators' => [
                TwitterGenerator::class => [
                    'enabled' => false,
                ],
            ],
        ])
        ->twitterEnabled(true);

    expect(trim((string) seo()->generate('twitter')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
<!-- End Honeystone SEO -->
END
    );
});

it('disables twitter', function (): void {

    seo()
        ->config([
            'generators' => [
                TwitterGenerator::class => [
                    'enabled' => true,
                ],
            ],
        ])
        ->twitterEnabled(false);

    expect(trim((string) seo()->generate('twitter')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    
<!-- End Honeystone SEO -->
END
    );
});

it('sets the twitter card using a string', function (): void {

    seo()
        ->twitterCard('summary');

    expect(trim((string) seo()->generate('twitter')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary">
<!-- End Honeystone SEO -->
END
    );
});

it('sets the twitter card to app', function (): void {

    seo()
        ->twitterCard(new AppProperties(
            iphoneId: '888888888',
            iphoneName: 'Foo',
            iphoneUrl: 'foo://',
            ipadId: '888888888',
            ipadName: 'Foo',
            ipadUrl: 'foo://',
            googlePlayId: 'com.foo.bar.foobar',
            googlePlayName: 'Foo',
            googlePlayUrl: 'https://foo.bar.com/888888888',
        ));

    expect(trim((string) seo()->generate('twitter')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="app">
    <meta name="twitter:app:id:iphone" content="888888888">
    <meta name="twitter:app:name:iphone" content="Foo">
    <meta name="twitter:app:url:iphone" content="foo://">
    <meta name="twitter:app:id:ipad" content="888888888">
    <meta name="twitter:app:name:ipad" content="Foo">
    <meta name="twitter:app:url:ipad" content="foo://">
    <meta name="twitter:app:id:googleplay" content="com.foo.bar.foobar">
    <meta name="twitter:app:name:googleplay" content="Foo">
    <meta name="twitter:app:url:googleplay" content="https://foo.bar.com/888888888">
<!-- End Honeystone SEO -->
END
    );
});

it('sets the twitter card to player', function (): void {

    seo()
        ->twitterCard(new PlayerProperties(
            player: 'https://foo.bar/video',
            width: '800',
            height: '450',
            stream: 'https://foo.bar/raw-video',
        ));

    expect(trim((string) seo()->generate('twitter')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="player">
    <meta name="twitter:player" content="https://foo.bar/video">
    <meta name="twitter:player:width" content="800">
    <meta name="twitter:player:height" content="450">
    <meta name="twitter:player:stream" content="https://foo.bar/raw-video">
<!-- End Honeystone SEO -->
END
    );
});

it('sets the twitter creator', function (): void {

    seo()
        ->config([
            'generators' => [
                TwitterGenerator::class => [
                    'creator' => '',
                ],
            ],
        ])
        ->twitterCreator('Foo');

    expect(trim((string) seo()->generate('twitter')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:creator" content="Foo">
<!-- End Honeystone SEO -->
END
    );
});

it('sets the twitter creator id', function (): void {

    seo()
        ->config([
            'generators' => [
                TwitterGenerator::class => [
                    'creatorId' => '',
                ],
            ],
        ])
        ->twitterCreatorId('123');

    expect(trim((string) seo()->generate('twitter')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:creator:id" content="123">
<!-- End Honeystone SEO -->
END
    );
});

it('sets the twitter image and alt', function (): void {

    seo()
        ->twitterImage('https://mywebsite/image-2.png', 'Foo');

    expect(trim((string) seo()->generate('twitter')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="https://mywebsite/image-2.png">
    <meta name="twitter:image:alt" content="Foo">
<!-- End Honeystone SEO -->
END
    );
});

it('has the correct open graph prefix', function (): void {
    expect(seo()->openGraphPrefix())->toBe('og: https://ogp.me/ns#');
});

it('enables open graph', function (): void {

    seo()
        ->config([
            'generators' => [
                OpenGraphGenerator::class => [
                    'enabled' => false,
                ],
            ],
        ])
        ->openGraphEnabled(true);

    expect(trim((string) seo()->generate('open-graph')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://mywebsite.com">
<!-- End Honeystone SEO -->
END
    );
});

it('disables open graph', function (): void {

    seo()
        ->config([
            'generators' => [
                OpenGraphGenerator::class => [
                    'enabled' => true,
                ],
            ],
        ])
        ->openGraphEnabled(false);

    expect(trim((string) seo()->generate('open-graph')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    
<!-- End Honeystone SEO -->
END
    );
});

it('sets the open graph type using a string', function (): void {

    seo()
        ->config([
            'generators' => [
                OpenGraphGenerator::class => [
                    'type' => 'website',
                ],
            ],
        ])
        ->openGraphType('article');

    expect(trim((string) seo()->generate('open-graph')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Open Graph -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://mywebsite.com">
<!-- End Honeystone SEO -->
END
    );
});

it('sets the open graph type to article properties', function (): void {

    $now = new DateTime();

    seo()
        ->config([
            'generators' => [
                OpenGraphGenerator::class => [
                    'type' => 'website',
                ],
            ],
        ])
        ->openGraphType(new ArticleProperties(
            publishedTime: $now,
            modifiedTime: $now,
            expirationTime: $now,
            author: new ProfileProperties(
                username: 'PiranhaGeorge',
            ),
            section: 'Foo',
            tag: 'Bar',
        ));

    expect(seo()->openGraphPrefix())->toBe('og: https://ogp.me/ns# article: https://ogp.me/ns/article#');

    expect(trim((string) seo()->generate('open-graph')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Open Graph -->
    <meta property="og:type" content="article">
    <meta property="article:published_time" content="{$now->format('c')}">
    <meta property="article:modified_time" content="{$now->format('c')}">
    <meta property="article:expiration_time" content="{$now->format('c')}">
    <meta property="article:author:username" content="PiranhaGeorge">
    <meta property="article:section" content="Foo">
    <meta property="article:tag" content="Bar">
    <meta property="og:url" content="https://mywebsite.com">
<!-- End Honeystone SEO -->
END
    );
});

it('sets the open graph type to profile properties', function (): void {

    seo()
        ->config([
            'generators' => [
                OpenGraphGenerator::class => [
                    'type' => 'website',
                ],
            ],
        ])
        ->openGraphType(new ProfileProperties(
            firstName: 'George',
            lastName: 'Palmer',
            username: 'PiranhaGeorge',
            gender: 'male',
        ));

    expect(seo()->openGraphPrefix())->toBe('og: https://ogp.me/ns# profile: https://ogp.me/ns/profile#');

    expect(trim((string) seo()->generate('open-graph')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Open Graph -->
    <meta property="og:type" content="profile">
    <meta property="profile:first_name" content="George">
    <meta property="profile:last_name" content="Palmer">
    <meta property="profile:username" content="PiranhaGeorge">
    <meta property="profile:gender" content="male">
    <meta property="og:url" content="https://mywebsite.com">
<!-- End Honeystone SEO -->
END
    );
});

it('sets the open graph type to book properties', function (): void {

    seo()
        ->config([
            'generators' => [
                OpenGraphGenerator::class => [
                    'type' => 'website',
                ],
            ],
        ])
        ->openGraphType(new BookProperties(
            author: [
                new ProfileProperties(
                    firstName: 'Erich',
                    lastName: 'Gamma',
                ),
                new ProfileProperties(
                    firstName: 'Richard',
                    lastName: 'Helm',
                ),
                new ProfileProperties(
                    firstName: 'Ralph',
                    lastName: 'Johnson',
                ),
                new ProfileProperties(
                    firstName: 'John',
                    lastName: 'Vlissides',
                ),
            ],
            isbn: '978-0201633610',
            releaseDate: new DateTime('14 March 1995'),
            tag: ['1st', 'GoF'],
        ));

    expect(seo()->openGraphPrefix())->toBe('og: https://ogp.me/ns# book: https://ogp.me/ns/book#');

    expect(trim((string) seo()->generate('open-graph')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Open Graph -->
    <meta property="og:type" content="book">
    <meta property="book:author:first_name" content="Erich">
    <meta property="book:author:last_name" content="Gamma">
    <meta property="book:author:first_name" content="Richard">
    <meta property="book:author:last_name" content="Helm">
    <meta property="book:author:first_name" content="Ralph">
    <meta property="book:author:last_name" content="Johnson">
    <meta property="book:author:first_name" content="John">
    <meta property="book:author:last_name" content="Vlissides">
    <meta property="book:isnb" content="978-0201633610">
    <meta property="book:release_date" content="1995-03-14T00:00:00+00:00">
    <meta property="book:tag" content="1st">
    <meta property="book:tag" content="GoF">
    <meta property="og:url" content="https://mywebsite.com">
<!-- End Honeystone SEO -->
END
    );
});

it('sets the open graph image using image properties', function (): void {

    seo()
        ->openGraphImage(new ImageProperties(
            url: 'http://foo.bar/img.png',
            alt: 'Foo',
            width: '600',
            height: '500',
            secureUrl: 'https://foo.bar/img.png',
            type: 'image/png',
        ));

    expect(trim((string) seo()->generate('open-graph')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:image" content="http://foo.bar/img.png">
    <meta property="og:image:alt" content="Foo">
    <meta property="og:image:width" content="600">
    <meta property="og:image:height" content="500">
    <meta property="og:image:secure_url" content="https://foo.bar/img.png">
    <meta property="og:image:type" content="image/png">
    <meta property="og:url" content="https://mywebsite.com">
<!-- End Honeystone SEO -->
END
    );
});

it('sets the open graph images using image properties', function (): void {

    seo()
        ->openGraphImages([
            new ImageProperties(
                url: 'http://foo.bar/img.png',
                alt: 'Foo',
                width: '600',
                height: '500',
                secureUrl: 'https://foo.bar/img.png',
                type: 'image/png',
            ),
            new ImageProperties(
                url: 'http://foo.bar/img1.png',
                alt: 'Foo',
                width: '600',
                height: '500',
                secureUrl: 'https://foo.bar/img1.png',
                type: 'image/png',
            )
        ]);

    expect(trim((string) seo()->generate('open-graph')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:image" content="http://foo.bar/img.png">
    <meta property="og:image:alt" content="Foo">
    <meta property="og:image:width" content="600">
    <meta property="og:image:height" content="500">
    <meta property="og:image:secure_url" content="https://foo.bar/img.png">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image" content="http://foo.bar/img1.png">
    <meta property="og:image:alt" content="Foo">
    <meta property="og:image:width" content="600">
    <meta property="og:image:height" content="500">
    <meta property="og:image:secure_url" content="https://foo.bar/img1.png">
    <meta property="og:image:type" content="image/png">
    <meta property="og:url" content="https://mywebsite.com">
<!-- End Honeystone SEO -->
END
    );
});

it('sets one open graph audio using a string', function (): void {

    seo()
        ->openGraphAudio('https://foo.bar/song.mp3');

    expect(trim((string) seo()->generate('open-graph')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://mywebsite.com">
    <meta property="og:audio" content="https://foo.bar/song.mp3">
<!-- End Honeystone SEO -->
END
    );
});

it('sets one open graph audio using audio properties', function (): void {

    seo()
        ->openGraphAudio(new AudioProperties(
            url: 'http://foo.bar/song.mp3',
            secureUrl: 'https://foo.bar/song.mp3',
            type: 'audio/mpeg',
        ));

    expect(trim((string) seo()->generate('open-graph')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://mywebsite.com">
    <meta property="og:audio" content="http://foo.bar/song.mp3">
    <meta property="og:audio:secure_url" content="https://foo.bar/song.mp3">
    <meta property="og:audio:type" content="audio/mpeg">
<!-- End Honeystone SEO -->
END
    );
});

it('sets the open graph audio using strings', function (): void {

    seo()
        ->openGraphAudio([
            'https://foo.bar/song1.mp3',
            'https://foo.bar/song2.mp3',
        ]);

    expect(trim((string) seo()->generate('open-graph')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://mywebsite.com">
    <meta property="og:audio" content="https://foo.bar/song1.mp3">
    <meta property="og:audio" content="https://foo.bar/song2.mp3">
<!-- End Honeystone SEO -->
END
    );
});

it('sets the open graph audio using audio properties', function (): void {

    seo()
        ->openGraphAudio([
            new AudioProperties(
                url: 'http://foo.bar/song1.mp3',
                secureUrl: 'https://foo.bar/song1.mp3',
                type: 'audio/mpeg',
            ),
            new AudioProperties(
                url: 'http://foo.bar/song2.mp3',
                secureUrl: 'https://foo.bar/song2.mp3',
                type: 'audio/mpeg',
            ),
        ]);

    expect(trim((string) seo()->generate('open-graph')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://mywebsite.com">
    <meta property="og:audio" content="http://foo.bar/song1.mp3">
    <meta property="og:audio:secure_url" content="https://foo.bar/song1.mp3">
    <meta property="og:audio:type" content="audio/mpeg">
    <meta property="og:audio" content="http://foo.bar/song2.mp3">
    <meta property="og:audio:secure_url" content="https://foo.bar/song2.mp3">
    <meta property="og:audio:type" content="audio/mpeg">
<!-- End Honeystone SEO -->
END
    );
});

it('sets the open graph video using a string', function (): void {

    seo()
        ->openGraphVideo('https://foo.bar/movie.mp4');

    expect(trim((string) seo()->generate('open-graph')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://mywebsite.com">
    <meta property="og:video" content="https://foo.bar/movie.mp4">
<!-- End Honeystone SEO -->
END
    );
});

it('sets the open graph video using video properties', function (): void {

    seo()
        ->openGraphVideo(new VideoProperties(
            url: 'http://foo.bar/movie.mp4',
            alt: 'Foo',
            width: '1920',
            height: '1080',
            secureUrl: 'https://foo.bar/movie.mp4',
            type: 'video/mp4',
        ));

    expect(trim((string) seo()->generate('open-graph')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://mywebsite.com">
    <meta property="og:video" content="http://foo.bar/movie.mp4">
    <meta property="og:video:alt" content="Foo">
    <meta property="og:video:width" content="1920">
    <meta property="og:video:height" content="1080">
    <meta property="og:video:secure_url" content="https://foo.bar/movie.mp4">
    <meta property="og:video:type" content="video/mp4">
<!-- End Honeystone SEO -->
END
    );
});

it('sets the open graph videos using video properties', function (): void {

    seo()
        ->openGraphVideos([
            new VideoProperties(
                url: 'http://foo.bar/movie1.mp4',
                alt: 'Foo',
                width: '1920',
                height: '1080',
                secureUrl: 'https://foo.bar/movie1.mp4',
                type: 'video/mp4',
            ),
            new VideoProperties(
                url: 'http://foo.bar/movie2.mp4',
                alt: 'Foo',
                width: '1920',
                height: '1080',
                secureUrl: 'https://foo.bar/movie2.mp4',
                type: 'video/mp4',
            ),
        ]);

    expect(trim((string) seo()->generate('open-graph')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://mywebsite.com">
    <meta property="og:video" content="http://foo.bar/movie1.mp4">
    <meta property="og:video:alt" content="Foo">
    <meta property="og:video:width" content="1920">
    <meta property="og:video:height" content="1080">
    <meta property="og:video:secure_url" content="https://foo.bar/movie1.mp4">
    <meta property="og:video:type" content="video/mp4">
    <meta property="og:video" content="http://foo.bar/movie2.mp4">
    <meta property="og:video:alt" content="Foo">
    <meta property="og:video:width" content="1920">
    <meta property="og:video:height" content="1080">
    <meta property="og:video:secure_url" content="https://foo.bar/movie2.mp4">
    <meta property="og:video:type" content="video/mp4">
<!-- End Honeystone SEO -->
END
    );
});

it('sets the open graph videos using strings', function (): void {

    seo()
        ->openGraphVideos([
            'https://foo.bar/movie1.mp4',
            'https://foo.bar/movie2.mp4',
        ]);

    expect(trim((string) seo()->generate('open-graph')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://mywebsite.com">
    <meta property="og:video" content="https://foo.bar/movie1.mp4">
    <meta property="og:video" content="https://foo.bar/movie2.mp4">
<!-- End Honeystone SEO -->
END
    );
});

it('sets the open graph determiner', function (): void {

    seo()
        ->openGraphDeterminer(OpenGraphGenerator::DETERMINER_THE);

    expect(trim((string) seo()->generate('open-graph')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://mywebsite.com">
    <meta property="og:determiner" content="the">
<!-- End Honeystone SEO -->
END
    );
});

it('throws an exception when given an invalid open graph determiner', function (): void {

    seo()->openGraphDeterminer('Foo');

})->throws(UnexpectedValueException::class);


it('sets alternate open graph locales', function (): void {

    seo()
        ->openGraphAlternateLocales(['en_US', 'en_GB']);

    expect(trim((string) seo()->generate('open-graph')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://mywebsite.com">
    <meta property="og:locale:alternate" content="en_US">
    <meta property="og:locale:alternate" content="en_GB">
<!-- End Honeystone SEO -->
END
    );
});

it('adds custom open graph properties', function (): void {

    seo()
        ->openGraphProperty('Foo', 'Bar')
        ->openGraphProperty('Bar', ['Foo', 'Baz']);

    expect(trim((string) seo()->generate('open-graph')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://mywebsite.com">
    <meta property="Foo" content="Bar">
    <meta property="Bar" content="Foo">
    <meta property="Bar" content="Baz">
<!-- End Honeystone SEO -->
END
    );
});

it('default custom open graph properties are ignored', function (): void {

    seo()
        ->defaults(['property' => [['Foo' => 'Bar']]]);

    expect(trim((string) seo()->generate('open-graph')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://mywebsite.com">
<!-- End Honeystone SEO -->
END
    );
});

it('configured custom open graph properties are merged', function (): void {

    seo()
        ->config([
            'generators' => [
                OpenGraphGenerator::class => [
                    'custom' => [
                        [
                            'Foo' => 'Bar',
                        ],
                    ],
                ],
            ],
        ])
        ->openGraphProperty('Bar', 'Baz');

    expect(trim((string) seo()->generate('open-graph')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://mywebsite.com">
    <meta property="Foo" content="Bar">
    <meta property="Bar" content="Baz">
<!-- End Honeystone SEO -->
END
    );
});

it('enables json-ld', function (): void {

    seo()
        ->config([
            'generators' => [
                JsonLdGenerator::class => [
                    'enabled' => false,
                ],
            ],
        ])
        ->jsonLdEnabled(true);

    expect(trim((string) seo()->generate('json-ld')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","url":"https:\/\/mywebsite.com"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('disables json-ld', function (): void {

    seo()
        ->config([
            'generators' => [
                JsonLdGenerator::class => [
                    'enabled' => true,
                ],
            ],
        ])
        ->jsonLdEnabled(false);

    expect(trim((string) seo()->generate('json-ld')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    
<!-- End Honeystone SEO -->
END
    );
});

it('sets the json-ld type', function (): void {

    seo()
        ->config([
            'generators' => [
                JsonLdGenerator::class => [
                    'type' => 'WebPage',
                ],
            ],
        ])
        ->jsonLdType('Blog');

    expect(trim((string) seo()->generate('json-ld')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"Blog","url":"https:\/\/mywebsite.com"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('sets the json-ld id to url', function (): void {

    seo()->jsonLdId();

    expect(trim((string) seo()->generate('json-ld')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","url":"https:\/\/mywebsite.com","@id":"https:\/\/mywebsite.com"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('sets the json-ld id to url and appends hash', function (): void {

    seo()->jsonLdId(
        useUrl: true,
        append:'#LocalBusiness',
    );

    expect(trim((string) seo()->generate('json-ld')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","url":"https:\/\/mywebsite.com","@id":"https:\/\/mywebsite.com#LocalBusiness"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('sets the json-ld id to a value', function (): void {

    seo()->jsonLdId('Foo');

    expect(trim((string) seo()->generate('json-ld')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","url":"https:\/\/mywebsite.com","@id":"Foo"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('sets the json-ld id to a value and append', function (): void {

    seo()->jsonLdId('Foo', '#Bar');

    expect(trim((string) seo()->generate('json-ld')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","url":"https:\/\/mywebsite.com","@id":"Foo#Bar"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('sets the json-ld name', function (): void {

    seo()
        ->jsonLdName('Foo');

    expect(trim((string) seo()->generate('json-ld')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","name":"Foo","url":"https:\/\/mywebsite.com"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('sets the json-ld image', function (): void {

    seo()
        ->jsonLdImage('https://foo.bar/img.png');

    expect(trim((string) seo()->generate('json-ld')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","image":["https:\/\/foo.bar\/img.png"],"url":"https:\/\/mywebsite.com"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('sets the json-ld images', function (): void {

    seo()
        ->jsonLdImages([
            'https://foo.bar/img1.png',
            'https://foo.bar/img2.png',
            'https://foo.bar/img3.png',
        ]);

    expect(trim((string) seo()->generate('json-ld')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","image":["https:\/\/foo.bar\/img1.png","https:\/\/foo.bar\/img2.png","https:\/\/foo.bar\/img3.png"],"url":"https:\/\/mywebsite.com"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('sets the json-ld nonce', function (): void {

    seo()
        ->jsonLdNonce('foo');

    expect(trim((string) seo()->generate('json-ld')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- JSON-LD -->
    <script type="application/ld+json" nonce="foo">
        {"@context":"https:\/\/schema.org","@type":"WebPage","url":"https:\/\/mywebsite.com"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('sets custom json-ld properties', function (): void {

    seo()
        ->jsonLdProperty('foo', 'bar')
        ->jsonLdProperty('foo', [
            'bar' => [
                'baz',
            ],
        ]);

    expect(trim((string) seo()->generate('json-ld')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","url":"https:\/\/mywebsite.com","foo":{"bar":["baz"]}}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('builds a json-ld graph', function (): void {

    seo()->config([
        'generators' => [
            //place-on-graph was not available on 1.0, so we'll remove it from the config
            JsonLdGenerator::class => ['place-on-graph' => null],
        ],
    ]);

    seo()->jsonLdGraph()
        ->organization('honeystone')
            ->name('Honeystone')
            ->legalName('Honeystone Consulting Ltd.');

    seo()->jsonLdGraph()
        ->person('piranhageorge')
            ->givenName('George')
            ->familyName('Palmer');

    expect(trim((string) seo()->generate('json-ld')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@graph":[{"@type":"Organization","name":"Honeystone","legalName":"Honeystone Consulting Ltd."},{"@type":"Person","givenName":"George","familyName":"Palmer"}]}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('builds a json-ld multi', function (): void {

    seo()->config([
        'generators' => [
            //place-on-graph was not available on 1.0, so we'll remove it from the config
            JsonLdGenerator::class => ['place-on-graph' => null],
        ],
    ]);

    seo()->jsonLdMulti()
        ->organization()
            ->legalName('George Palmer inc,');

    seo()->jsonLdMulti()
        ->person()
            ->name('George Palmer')
            ->givenName('George')
            ->familyName('Palmer');

    expect(trim((string) seo()->generate('json-ld')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":["Organization","Person"],"name":"George Palmer","url":"https:\/\/mywebsite.com","legalName":"George Palmer inc,","givenName":"George","familyName":"Palmer"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('imports a json-ld graph', function (): void {

    seo()->config([
        'generators' => [
            //place-on-graph was not available on 1.0, so we'll remove it from the config
            JsonLdGenerator::class => ['place-on-graph' => null],
        ],
    ]);

    $graph = new Graph();

    $graph->person('piranhageorge')
        ->givenName('George')
        ->familyName('Palmer');

    seo()->jsonLdImport($graph);

    expect(trim((string) seo()->generate('json-ld')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@graph":[{"@type":"Person","givenName":"George","familyName":"Palmer"}]}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('imports a json-ld type', function (): void {

    seo()->jsonLdImport(
        (new Person())
            ->name('George Palmer'),
    );

    expect(trim((string) seo()->generate('json-ld')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"Person","name":"George Palmer","url":"https:\/\/mywebsite.com"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('imports a json-ld multi type', function (): void {

    seo()->config([
        'generators' => [
            //place-on-graph was not available on 1.0, so we'll remove it from the config
            JsonLdGenerator::class => ['place-on-graph' => null],
        ],
    ]);

    $multi = new MultiTypedEntity();

    $multi->person()
        ->givenName('George')
        ->familyName('Palmer');


    seo()
        ->jsonLdName('George Palmer')
        ->jsonLdImport($multi);

    expect(trim((string) seo()->generate('json-ld')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":["Person"],"name":"George Palmer","url":"https:\/\/mywebsite.com","givenName":"George","familyName":"Palmer"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('places configured json-ld on the graph', function (): void {

    seo()->config([
        'generators' => [
            JsonLdGenerator::class => [
                'place-on-graph' => true,
            ],
        ],
    ]);

    seo()
        ->description('foobarbaz')
        ->jsonLdType('Person')
        ->jsonLdName('Foo')
        ->jsonLdGraph()
            ->organization()
                ->name('Bar');

    expect(trim((string) seo()->generate('json-ld')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@graph":[{"@type":"Organization","name":"Bar"},{"@type":"Person","name":"Foo","description":"foobarbaz","url":"https:\/\/mywebsite.com"}]}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('places configured json-ld on multi', function (): void {

    seo()->config([
        'generators' => [
            JsonLdGenerator::class => [
                'place-on-graph' => true,
            ],
        ],
    ]);

    seo()
        ->description('foobarbaz')
        ->jsonLdType('Person')
        ->jsonLdName('Foo')
        ->jsonLdMulti()
            ->organization()
                ->name('Bar');

    //the name will be Foo because the multi must exist first to place the json-ld on it
    expect(trim((string) seo()->generate('json-ld')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":["Organization","Person"],"name":"Foo","description":"foobarbaz","url":"https:\/\/mywebsite.com"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('provides access to configured schema-org', function (): void {

    seo()
        ->title('Foo')
        ->jsonLdSchema()
            ->description('Bar');

    expect(trim((string) seo()->generate('json-ld')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","url":"https:\/\/mywebsite.com","description":"Bar","name":"Foo"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

test('configured values take precedence over schema-org values', function (): void {

    seo()
        ->title('Foo')
        ->jsonLdSchema()
            ->name('Bar');

    expect(trim((string) seo()->generate('json-ld')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","url":"https:\/\/mywebsite.com","name":"Foo"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

test('configured values can be modified after accessing schema-org', function (): void {

    seo()
        ->title('Foo')
        ->jsonLdSchema()
            ->name('Bar');

    seo()->jsonLdName('Baz');

    expect(trim((string) seo()->generate('json-ld')))->toBe(
        <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","url":"https:\/\/mywebsite.com","name":"Baz"}
    </script>
<!-- End Honeystone SEO -->
END
    );
});

it('does not require schema-org to function', function (): void {

    seo()->config([
        'generators' => [
            JsonLdGenerator::class => [
                'use-schema-org' => false,
            ],
        ],
    ]);

    seo()
        ->title('Foo')
        ->description('Bar');

    expect(seo()->jsonLdSchema())->toBeNull()
        ->and(trim((string) seo()->generate('json-ld')))->toBe(
            <<<END
<!-- Metadata generated using Honeystone SEO: https://github.com/honeystone/laravel-seo -->
    <!-- JSON-LD -->
    <script type="application/ld+json">
        {"@context":"https:\/\/schema.org","@type":"WebPage","name":"Foo","description":"Bar","url":"https:\/\/mywebsite.com"}
    </script>
<!-- End Honeystone SEO -->
END
        );
});

it('throws an exception for expected, but not checked in json-ld', function (): void {

    seo()
        ->jsonLdExpect('foo', 'bar')
        ->jsonLdCheckIn('bar');

    seo()->generate();

})->throws(RuntimeException::class, 'The following (1) expected JSON-LD components failed to check-in: foo');


it('throws an exception for checked in, but not expected json-ld', function (): void {

    seo()
        ->jsonLdExpect('foo')
        ->jsonLdCheckIn('foo', 'bar');

    seo()->generate();

})->throws(RuntimeException::class, 'The following (1) unexpected JSON-LD components checked-in: bar');
