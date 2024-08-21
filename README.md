# Honeystone SEO Configurator for Laravel

![Static Badge](https://img.shields.io/badge/tests-passing-green)
![GitHub License](https://img.shields.io/github/license/honeystone/laravel-seo)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/honeystone/laravel-seo)](https://packagist.org/packages/honeystone/laravel-seo)
![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/honeystone/laravel-seo/php)
![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/honeystone/laravel-seo/illuminate%2Fcontracts?label=laravel)
[![Static Badge](https://img.shields.io/badge/honeystone-fa6900)](https://honeystone.com)

The Honeystone SEO package makes configuring SEO metadata from anywhere within your Laravel application a breeze.

Included are metadata generators for general metadata, X (Formally Twitter) Cards, Open Graph, JSON-LD Schema, and
Favicons (generated using [RealFaviconGenerator](https://realfavicongenerator.net)).

This package was designed with extensibility in mind, so your own custom metadata generators can also be added with
ease.

## Support us

[![Support Us](https://honeystone.com/images/github/support-us.webp)](https://honeystone.com)

We are committed to delivering high-quality open source packages maintained by the team at Honeystone. If you would
like to support our efforts, simply use our packages, recommend them and contribute.

If you need any help with your project, or require any custom development, please [get in touch](https://honeystone.com/contact-us).

## Installation

```shell
composer require honeystone/laravel-seo
```

Publish the configuration file with:

```shell
php artisan vendor:publish --tag=honeystone-seo-config
```

## Usage

The package provides a helper function, `seo()`, and some Blade directives, `@metadata` and `@openGraphPrefix`. You
can also typehint the `Honeystone\Seo\MetadataDirector` if you prefer to use dependency injection.

Setting metadata is a simple as chaining methods:

```php
seo()
    ->title('A fantastic blog post', 'My Awesome Website!')
    ->description('Theres really a lot of great stuff in here...')
    ->images(
        'https://mywebsite.com/images/blog-1/cover-image.webp',
        'https://mywebsite.com/images/blog-1/another-image.webp',
    );
```

Once you've set your metadata, you can render it using:

```php
seo()->generate();
```

Alternatively, you can also use the `@metadata` Blade directive.

The rendered result will look something like this:

```html
<title>A fantastic blog post - My Awesome Website!</title>
<meta name="description" content="Theres really a lot of great stuff in here...">
<link rel="canonical" href="https://mywebsite.com/blog/a-fantastic-blog-post">

<!-- Twitter Cards -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="A fantastic blog post">
<meta name="twitter:description" content="Theres really a lot of great stuff in here...">
<meta name="twitter:image" content="https://mywebsite.com/images/blog-1/cover-image.webp">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:title" content="A fantastic blog post">
<meta property="og:description" content="Theres really a lot of great stuff in here...">
<meta property="og:image" content="https://mywebsite.com/images/blog-1/cover-image.webp">
<meta property="og:image" content="https://mywebsite.com/images/blog-1/another-image.webp">
<meta property="og:url" content="https://mywebsite.com/blog/a-fantastic-blog-post">

<!-- JSON-LD -->
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "A fantastic blog post",
        "description": "Theres really a lot of great stuff in here...",
        "image": [
            "https://mywebsite.com/images/blog-1/cover-image.webp",
            "https://mywebsite.com/images/blog-1/another-image.webp"
        ],
        "url": "https://mywebsite.com"
    }
</script>
```

### Default methods

Values provided to default methods will automatically propagate to all configured metadata generators.

The following default methods are available:

```php
seo()
    ->locale('en_GB')
    ->title('A fantastic blog post', template: '🔥🔥 {title} 🔥🔥')
    ->description('Theres really a lot of great stuff in here...')
    ->keywords('foo', 'bar', 'baz')
    ->url('https://mywebsite.com/blog/a-fantastic-blog-post') //defaults to the current url
    ->canonical('https://mywebsite.com/blog/a-fantastic-blog-post') //by default url and canonical are in sync, see config
    ->canonicalEnabled(true) //enabled by default, see config
    ->images(
        'https://mywebsite.com/images/blog-1/cover-image.webp',
        'https://mywebsite.com/images/blog-1/another-image.webp',
    )
    ->robots('🤖', '🤖', '🤖');
```

The full baseline looks like this:

```html
<title>🔥🔥 A fantastic blog post 🔥🔥</title>
<meta name="description" content="Theres really a lot of great stuff in here...">
<meta name="keywords" content="foo,bar,baz">
<link rel="canonical" href="https://mywebsite.com/blog/a-fantastic-blog-post">
<meta name="robots" content="🤖,🤖,🤖">

<!-- Twitter Cards -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="A fantastic blog post">
<meta name="twitter:description" content="Theres really a lot of great stuff in here...">
<meta name="twitter:image" content="https://mywebsite.com/images/blog-1/cover-image.webp">

<!-- Open Graph -->
<meta property="og:site_name" content="My Website">
<meta property="og:title" content="A fantastic blog post">
<meta property="og:description" content="Theres really a lot of great stuff in here...">
<meta property="og:image" content="https://mywebsite.com/images/blog-1/cover-image.webp">
<meta property="og:image" content="https://mywebsite.com/images/blog-1/another-image.webp">
<meta property="og:url" content="https://mywebsite.com/blog/a-fantastic-blog-post">
<meta property="og:locale" content="en_GB">

<!-- JSON-LD -->
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "A fantastic blog post",
        "description": "Theres really a lot of great stuff in here...",
        "image": [
            "https://mywebsite.com/images/blog-1/cover-image.webp",
            "https://mywebsite.com/images/blog-1/another-image.webp"
        ],
        "url": "https://mywebsite.com/blog/a-fantastic-blog-post"
    }
</script>
```

For your homepage you'll probably want to disable the title template:

```php
seo()->title('My Awesome Website!', template: false);
```

### Meta methods

The meta methods are provided by the `Honeystone\Seo\Generators\MetaGenerator` class.

Here's the full list:

```php
seo()
    ->metaTitle('A fantastic blog post')
    ->metaTitleTemplate('🔥🔥 {title} 🔥🔥')
    ->metaDescription('Theres really a lot of great stuff in here...')
    ->metaKeywords('foo', 'bar', 'baz')
    ->metaCanonical('https://mywebsite.com/blog/a-fantastic-blog-post')
    ->metaCanonicalEnabled(true)
    ->metaRobots('🤖', '🤖', '🤖');
```

All of these are provided by the default methods and propagate through to the meta generator.

If you only want to render the meta generator, use `seo()->generate('meta')` or `@metadata('meta')`

### Twitter methods

The meta methods are provided by the `Honeystone\Seo\Generators\TwitterGenerator` class.

Here's the full list:

```php
seo()
    ->twitterEnabled(true) //enabled by default, see config
    ->twitterSite('@MyWebsite')
    ->twitterCreator('@MyTwitter')
    ->twitterTitle('A fantastic blog post') //defaults to title()
    ->twitterDescription('Theres really a lot of great stuff in here...') //defaults to description()
    ->twitterImage('https://mywebsite.com/images/blog-1/cover-image.webp'); //defaults to the first in images()
```

### Open Graph methods

The meta methods are provided by the `Honeystone\Seo\Generators\TwitterGenerator` class.

Here's the full list:

```php
seo()
    ->openGraphEnabled(true) //enabled by default, see config
    ->openGraphSite('My Website')
    ->openGraphType('website') //defaults to website, see config
    ->openGraphTitle('A fantastic blog post') //defaults to title()
    ->openGraphDescription('Theres really a lot of great stuff in here...') //defaults to description()
    ->openGraphImage('https://mywebsite.com/images/blog-1/cover-image.webp')
    ->openGraphImages([
        'https://mywebsite.com/images/blog-1/cover-image.webp',
        'https://mywebsite.com/images/blog-1/another-image.webp',
    ]) //defaults to images()
    ->openGraphUrl('https://mywebsite.com/blog/a-fantastic-blog-post') //defaults to url()
    ->openGraphAudio([
        'https://mywebsite.com/music/song1.mp3',
        'https://mywebsite.com/music/song2.mp3',
    ])
    ->openGraphVideo('https://mywebsite.com/films/video1.mp4')
    ->openGraphVideos([
        'https://mywebsite.com/films/video1.mp4',
        'https://mywebsite.com/films/video2.mp4',
    ])
    ->openGraphDeterminer(OpenGraphGenerator::DETERMINER_A)
    ->openGraphLocale('en_GB') //defaults to locale()
    ->openGraphAlternateLocales(['en_US'])
    ->openGraphProperty('custom:property', '💀');
```

You can also use the following non-vertical supported types:
```php
use Honeystone\Seo\OpenGraph\ArticleProperties;
use Honeystone\Seo\OpenGraph\BookProperties;
use Honeystone\Seo\OpenGraph\ProfileProperties;

//article
seo()
    ->openGraphType(new ArticleProperties(
        publishedTime: new DateTime('now'),
        modifiedTime: new DateTime('now'),
        expirationTime: null,
        author: new ProfileProperties(
            username: 'PiranhaGeorge',
        ),
        section: 'Foo',
        tag: 'Bar',
    ));

//book
seo()
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

//profile
seo()
    ->openGraphType(new ProfileProperties(
        username: 'PiranhaGeorge'
        firstName: 'George',
        lastName: 'Palmer',
        gender: 'male',
    ));
```

You can provide more data for images, audio and videos using their respective properties classes:

```php
    use Honeystone\Seo\OpenGraph\AudioProperties;
    use Honeystone\Seo\OpenGraph\ImageProperties;
    use Honeystone\Seo\OpenGraph\VideoProperties;

    seo()->openGraphAudio(new AudioProperties(
        url: 'http://foo.bar/song.mp3',
        secureUrl: 'https://foo.bar/song.mp3',
        type: 'audio/mpeg',
    ));

    seo()->openGraphImage(new ImageProperties(
        url: 'http://foo.bar/img.png',
        alt: 'Foo',
        width: '800',
        height: '450',
        secureUrl: 'https://foo.bar/img.png',
        type: 'image/png',
    ));

    seo()->openGraphVideo(new VideoProperties(
        url: 'http://foo.bar/movie.mp4',
        alt: 'Foo',
        width: '1920',
        height: '1080',
        secureUrl: 'https://foo.bar/movie.mp4',
        type: 'video/mp4',
    ));
```

Here's an example using `ArticleProperties` and `ImageProperties`:

```html
<!-- Open Graph -->
<meta property="og:site_name" content="My Website">
<meta property="og:type" content="article">
<meta property="article:published_time" content="2024-07-25T21:39:40+00:00">
<meta property="article:modified_time" content="2024-07-25T21:39:40+00:00">
<meta property="article:author:username" content="PiranhaGeorge">
<meta property="article:section" content="Foo">
<meta property="article:tag" content="Bar">
<meta property="og:title" content="A fantastic blog post">
<meta property="og:description" content="Theres really a lot of great stuff in here...">
<meta property="og:image" content="http://foo.bar/img.png">
<meta property="og:image:alt" content="Foo">
<meta property="og:image:width" content="800">
<meta property="og:image:height" content="450">
<meta property="og:image:secure_url" content="https://foo.bar/img.png">
<meta property="og:image:type" content="image/png">
<meta property="og:url" content="https://mywebsite.com">
<meta property="og:determiner" content="a">
<meta property="og:locale" content="en_GB">
<meta property="og:locale:alternate" content="en_US">
<meta property="custom:property" content="💀">
```

To set the prefix, you can use the `@openGraphPrefix` Blade directive or `seo()->openGraphPrefix()` like so:

```html
<head prefix="@openGraphPrefix">
    ...
</head>
```

### JSON-LD methods

The meta methods are provided by the `Honeystone\Seo\Generators\JsonLdGenerator` class.

Here's the full list:

```php
seo()
    ->jsonLdEnabled(true) //enabled by default, see config
    ->jsonLdType('WebPage') //defaults to WebPage, see config
    ->jsonLdName('A fantastic blog post') //defaults to title()
    ->jsonLdDescription('Theres really a lot of great stuff in here...') //defaults to description()
    ->jsonLdImage('https://mywebsite.com/images/blog-1/cover-image.webp')
    ->jsonLdImages([
        'https://mywebsite.com/images/blog-1/cover-image.webp',
        'https://mywebsite.com/images/blog-1/another-image.webp',
    ]) //defaults to images()
    ->jsonLdUrl('https://mywebsite.com/blog/a-fantastic-blog-post') //defaults to url()
    ->jsonLdNonce('some-value') //sets a nonce value for your content security policy
    ->jsonLdProperty('alternateName', 'Foo');
```

And the output:

```html
<!-- JSON-LD -->
<script type="application/ld+json" nonce="some-value">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "A fantastic blog post",
        "description": "Theres really a lot of great stuff in here...",
        "image": [
            "https://mywebsite.com/images/blog-1/cover-image.webp",
            "https://mywebsite.com/images/blog-1/another-image.webp"
        ],
        "url": "https://mywebsite.com/blog/a-fantastic-blog-post",
        "alternateName": "Foo"
    }
</script>
```

But Wait, There's More!

Rather than reinventing the wheel, this package has support for the incredible
[spatie/schema-org](https://github.com/spatie/schema-org) package. You can use the `jsonLdImport()` method to import an
exising schema, or build your schema using the fluent interface.

```php
    //graph
    seo()->jsonLdGraph()
        ->organization('honeystone')
            ->name('Honeystone')
            ->legalName('Honeystone Consulting Ltd.');

    //or a MultiTypedEntity
    seo()->jsonLdMulti()
        ->organization('honeystone')
            ->name('Honeystone')
            ->legalName('Honeystone Consulting Ltd.');
```

Just don't forget to install the `spatie/schema-org` package to use this functionality.

#### Expectations / Check Ins

It's highly likely you'll be building your graph from many locations around your application, e.g. middleware,
controllers, view composers, view components, etc.

This is where expectations come in. Simply specify your expectations, and then ensure the other parts of your
application check in. If something failed to check in, an exception will be thrown. Conversely, if something unexpected
checked in, an exception will also be thrown.

```php
//perhaps in a controller
seo()
    ->title('Something awesome')
    ->jsonLdExpect('featured-tags', 'gallery', 'contact');

//maybe in a view composer or component
seo()
    ->jsonLdCheckIn('gallery')
    ->jsonLdGraph()
        ->imageGallery()
            ->image([
                ...
            ]);
```

You'll be warned immediately if `'featured-tags'` or `'contact'` fail to check in.

This feature is entirely optional. Just don't set any expectations, or check in, and no exceptions will be thrown.

### Favicon generation

Using the RealFaviconGenerator API, you can now generate favicons with this package. Simply
[request an API key](https://realfavicongenerator.net/api/#register_key) and pop it in your config, configure your
source image, and then run the command:

```bash
php artisan seo:generate-favicons
```

### Model integration

This package doesn't include any specific functionality for integrating models. Ultimately, you'll always need to map
your model attributes to this package. For example, if your model has a `meta_description` attribute, you will need to
map it to `description`, otherwise this package would not know to consume it.

With this in mind, we have a simple pattern that should get you what you need.

Start by adding a new method to your model, and set your metadata using the model's attributes within:

```php
use Honeystone\Seo\MetadataDirector;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    public function seo(): MetadataDirector
    {
        return seo()
            ->title($this->meta_title)
            ->description($this->meta_description)
            ->jsonLdExpect('featured-items');
    }
}

```

Then in your controller, just call the method and chain any additional metadata:

```php
use Illuminate\Contracts\View\View;

class PageController
{
    public function __invoke(Page $page): View
    {
        $page->seo()
            ->jsonLdCheckIn('featured-items')
            ->jsonLdGraph()
                ->itemList()
                    ->name('Featured items')
                    ->itemListElement([
                        //...
                    ]);
    }
}

```

### Custom generators

To create a custom generator, simply implement the `Honeystone\Seo\Contracts\MetadataGenerator` contract and add it to
your config file in the generators section. You can specify any configuration for your generator here too.

### Configuration

Here's the full config file:

```php
<?php

declare(strict_types=1);

use Honeystone\Seo\Generators;

return [

    'generators' => [
        Generators\MetaGenerator::class => [
            'title' => env('APP_NAME'),
            'titleTemplate' => '{title} - '.env('APP_NAME'),
            'description' => '',
            'keywords' => [],
            'canonicalEnabled' => true,
            'canonical' => null, //null to use current url
            'robots' => [],
            'custom' => [
                //[
                //    'greeting' => 'Hey, thanks for checking out the source code of our website. '.
                //        'Hopefully you find what you are looking for 👍'
                //],
                //[
                //    'google-site-verification' => 'xxx',
                //],
            ],
        ],
        Generators\TwitterGenerator::class => [
            'enabled' => true,
            'site' => '', //@twitterUsername
            'creator' => '',
            'title' => '',
            'description' => '',
            'image' => '',
        ],
        Generators\OpenGraphGenerator::class => [
            'enabled' => true,
            'site' => env('APP_NAME'),
            'type' => 'website',
            'title' => '',
            'description' => '',
            'images' => [],
            'audio' => [],
            'videos' => [],
            'determiner' => '',
            'url' => null, //null to use current url
            'locale' => '',
            'alternateLocales' => [],
            'custom' => [],
        ],
        Generators\JsonLdGenerator::class => [
            'enabled' => true,
            'pretty' => env('APP_DEBUG'),
            'type' => 'WebPage',
            'name' => '',
            'description' => '',
            'images' => [],
            'url' => null, //null to use current url
            'custom' => [],

            //determines if the configured json-ld is automatically placed on the graph
            'place-on-graph' => true,
        ],
        Generators\RealFaviconGenerator::class => [
            'enabled' => true,
            'apiKey' => env('REAL_FAVICON_KEY'),
            'image' => '', //the source image path, relative to /resources

            //see https://realfavicongenerator.net/api/non_interactive_api#favicon_design
            'design' => [
                'ios' => [
                    'picture_aspect' => 'no_change',
                    'app_name' => env('APP_NAME'),
                    'assets' => [
                        'ios6_and_prior_icons' => false,
                        'ios7_and_prior_icons' => false,
                        'precomposed_icons' => false,
                        'declare_only_default_icon' => true,
                    ],
                ],
                'windows' => [
                    'picture_aspect' => 'no_change',
                    'background_color' => '#222',
                    'app_name' => env('APP_NAME'),
                    'assets' => [
                        'windows_80_ie_10_tile' => false,
                        'windows_10_ie_11_edge_tiles' => [
                            'small' => false,
                            'medium' => true,
                            'big' => false,
                            'rectangle' => false,
                        ],
                    ],
                ],
                'firefox_app' => [
                    'picture_aspect' => 'no_change',
                    'manifest' => [
                        'app_name' => env('APP_NAME'),
                        'app_description' => '',
                        'developer_name' => '',
                        'developer_url' => '',
                    ],
                ],
                'android_chrome' => [
                    'picture_aspect' => 'no_change',
                    'manifest' => [
                        'name' => env('APP_NAME'),
                        'display' => 'browser',
                        'theme_color' => '#222',
                    ],
                    'assets' => [
                        'legacy_icon' => false,
                        'low_resolution_icons' => false,
                    ],
                ],
                'safari_pinned_tab' => [
                    'picture_aspect' => 'silhouette',
                    'theme_color' => '#222',
                ],
            ],

            //see https://realfavicongenerator.net/api/non_interactive_api#settings
            'settings' => [
                'compression' => 3,
                'scaling_algorithm' => 'Mitchell',
            ],
        ],
    ],

    'sync' => [
        'url-canonical' => true,
        'keywords-tags' => false,
    ],
];
```

## Changelog

A list of changes can be found in the [CHANGELOG.md](CHANGELOG.md) file.

## License

[MIT](LICENSE.md) © [Honeystone Consulting Ltd](https://honeystone.com)
