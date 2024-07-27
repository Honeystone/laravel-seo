<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Preset
    |--------------------------------------------------------------------------
    |
    | This option controls the default preset that will be used by PHP Insights
    | to make your code reliable, simple, and clean. However, you can always
    | adjust the `Metrics` and `Insights` below in this configuration file.
    |
    | Supported: "default", "laravel", "symfony", "magento2", "drupal"
    |
    */

    'preset' => 'laravel',

    /*
    |--------------------------------------------------------------------------
    | IDE
    |--------------------------------------------------------------------------
    |
    | This options allow to add hyperlinks in your terminal to quickly open
    | files in your favorite IDE while browsing your PhpInsights report.
    |
    | Supported: "textmate", "macvim", "emacs", "sublime", "phpstorm",
    | "atom", "vscode".
    |
    | If you have another IDE that is not in this list but which provide an
    | url-handler, you could fill this config with a pattern like this:
    |
    | myide://open?url=file://%f&line=%l
    |
    */

    'ide' => 'phpstorm',

    /*
    |--------------------------------------------------------------------------
    | Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may adjust all the various `Insights` that will be used by PHP
    | Insights. You can either add, remove or configure `Insights`. Keep in
    | mind, that all added `Insights` must belong to a specific `Metric`.
    |
    */

    'exclude' => [
        'build',
        'config',
        'resources',
        'tests',
        'vendor',
        'workbench',
    ],

    'add' => [
        NunoMaduro\PhpInsights\Domain\Metrics\Code\Classes::class => [
            PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer::class,
        ],
        NunoMaduro\PhpInsights\Domain\Metrics\Code\Code::class => [
            SlevomatCodingStandard\Sniffs\ControlStructures\RequireNullCoalesceOperatorSniff::class,
            SlevomatCodingStandard\Sniffs\ControlStructures\RequireShortTernaryOperatorSniff::class,
        ],
        NunoMaduro\PhpInsights\Domain\Metrics\Code\Comments::class => [
            SlevomatCodingStandard\Sniffs\Commenting\ForbiddenAnnotationsSniff::class,
            SlevomatCodingStandard\Sniffs\Commenting\InlineDocCommentDeclarationSniff::class,
        ],
        NunoMaduro\PhpInsights\Domain\Metrics\Code\Functions::class => [
            PhpCsFixer\Fixer\FunctionNotation\VoidReturnFixer::class,
        ],
    ],

    'remove' => [
        NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineFunctions::class,
        NunoMaduro\PhpInsights\Domain\Insights\ForbiddenTraits::class,
        NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff::class,
        PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterNotSniff::class,
        PhpCsFixer\Fixer\Basic\BracesFixer::class, //doesn't allow blank line
        SlevomatCodingStandard\Sniffs\Classes\SuperfluousExceptionNamingSniff::class,
        SlevomatCodingStandard\Sniffs\ControlStructures\AssignmentInConditionSniff::class,
        SlevomatCodingStandard\Sniffs\ControlStructures\DisallowShortTernaryOperatorSniff::class,
        SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff::class,

        //we'll leave these checks to phpstan
        SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\PropertyTypeHintSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSniff::class,
    ],

    'config' => [
        PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff::class => [
            'lineLimit' => 120,
            'absoluteLineLimit' => 0,
        ],
        PhpCsFixer\Fixer\Import\OrderedImportsFixer::class => [
            'imports_order' => ['class', 'const', 'function'],
        ],
        SlevomatCodingStandard\Sniffs\Commenting\ForbiddenAnnotationsSniff::class => [
            'forbiddenAnnotations' => [
                '@author',
                '@const',
                '@copyright',
                '@created',
                '@license',
                '@package',
                '@version',
            ],
        ],
        SlevomatCodingStandard\Sniffs\Functions\FunctionLengthSniff::class => [
            'maxLinesLength' => 30,
        ],
        SlevomatCodingStandard\Sniffs\Namespaces\UseSpacingSniff::class => [
            'linesCountBetweenUseTypes' => 1,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Requirements
    |--------------------------------------------------------------------------
    |
    | Here you may define a level you want to reach per `Insights` category.
    | When a score is lower than the minimum level defined, then an error
    | code will be returned. This is optional and individually defined.
    |
    */

    'requirements' => [
        'min-quality' => 99,
        'min-complexity' => 81.2,
        'min-architecture' => 86.7,
        'min-style' => 98.8,
        'disable-security-check' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Threads
    |--------------------------------------------------------------------------
    |
    | Here you may adjust how many threads (core) PHPInsights can use to perform
    | the analyse. This is optional, don't provide it and the tool will guess
    | the max core number available. This accept null value or integer > 0.
    |
    */

    'threads' => null,

];
