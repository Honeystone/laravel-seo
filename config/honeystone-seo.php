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
