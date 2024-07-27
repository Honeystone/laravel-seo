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
            'site' => env('APP_NAME'),
            'creator' => '',
            'title' => '',
            'description' => '',
            'image' => '',
        ],
        Generators\OpenGraphGenerator::class => [
            'enabled' => true,
            'site' => '', //@twitterUsername
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
    ],

    'sync' => [
        'url-canonical' => true,
        'keywords-tags' => false,
    ],
];
