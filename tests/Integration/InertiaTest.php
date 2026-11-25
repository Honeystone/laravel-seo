<?php

declare(strict_types=1);

use Honeystone\Seo\Http\Middleware\GenerateInertiaMetadata;
use Honeystone\Seo\Http\Middleware\GenerateInertiaMetadataReact;
use Honeystone\Seo\OpenGraph\ArticleProperties;
use Honeystone\Seo\OpenGraph\BookProperties;
use Honeystone\Seo\OpenGraph\ProfileProperties;
use Honeystone\Seo\Providers\SeoServiceProvider;
use Honeystone\Seo\Twitter\AppProperties;
use Honeystone\Seo\Twitter\PlayerProperties;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

/**
 * @return array<string, mixed>
 */
function roundTrip(JsonSerializable $value): array
{
    return json_decode(json_encode($value, JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);
}

describe('serialisation', function (): void {
    it('serialises profiles', function (): void {
        expect(roundTrip(new ProfileProperties(firstName: 'Ada', lastName: 'Lovelace')))->toBe([
            'type' => 'profile',
            'firstName' => 'Ada',
            'lastName' => 'Lovelace',
        ]);
    });

    it('serialises articles with ISO-8601 dates and nested authors', function (): void {
        $article = new ArticleProperties(
            publishedTime: new DateTimeImmutable('2026-01-02T03:04:05+00:00'),
            author: [new ProfileProperties(firstName: 'Ada'), new ProfileProperties(firstName: 'Grace')],
            section: 'Tech',
            tag: ['php', 'seo'],
        );

        expect(roundTrip($article))->toBe([
            'type' => 'article',
            'publishedTime' => '2026-01-02T03:04:05+00:00',
            'author' => [
                ['type' => 'profile', 'firstName' => 'Ada'],
                ['type' => 'profile', 'firstName' => 'Grace'],
            ],
            'section' => 'Tech',
            'tag' => ['php', 'seo'],
        ]);
    });

    it('serialises books', function (): void {
        $book = new BookProperties(
            author: new ProfileProperties(username: 'ada'),
            isbn: '978-3-16-148410-0',
            releaseDate: new DateTimeImmutable('2026-03-04T00:00:00+00:00'),
        );

        expect(roundTrip($book))->toBe([
            'type' => 'book',
            'author' => ['type' => 'profile', 'username' => 'ada'],
            'isbn' => '978-3-16-148410-0',
            'releaseDate' => '2026-03-04T00:00:00+00:00',
        ]);
    });

    it('serialises twitter cards with their name', function (): void {
        expect(roundTrip(new AppProperties(iphoneId: '123', googlePlayName: 'App')))
            ->toBe([
                'name' => 'app',
                'iphoneId' => '123',
                'googlePlayName' => 'App',
            ])
            ->and(roundTrip(new PlayerProperties('https://x.test/p', '640', '480', null)))
            ->toBe([
                'name' => 'player',
                'player' => 'https://x.test/p',
                'width' => '640',
                'height' => '480',
            ]);
    });
});

describe('publishing', function (): void {
    it('publishes the react component with its shared module', function (): void {
        $paths = ServiceProvider::pathsToPublish(SeoServiceProvider::class, 'honeystone-seo-inertia-react');

        expect(array_values(array_map('basename', $paths)))
            ->toBe(['seo.ts', 'SeoHead.tsx'])
            ->and(array_filter(array_keys($paths), 'file_exists'))
            ->toHaveCount(count($paths));
    });

    it('publishes the vue component with its shared module', function (): void {
        $paths = ServiceProvider::pathsToPublish(SeoServiceProvider::class, 'honeystone-seo-inertia-vue');

        expect(array_values(array_map('basename', $paths)))
            ->toBe(['seo.ts', 'SeoHead.vue'])
            ->and(array_filter(array_keys($paths), 'file_exists'))
            ->toHaveCount(count($paths));
    });

    it('publishes everything with the combined tag', function (): void {
        $paths = ServiceProvider::pathsToPublish(SeoServiceProvider::class, 'honeystone-seo-inertia');

        expect($paths)->toHaveCount(3);
    });
});

describe('middleware', function (): void {
    it('shares the payload and the rendered html', function (): void {
        seo()->title('Foo', template: false);

        new GenerateInertiaMetadata()->handle(Request::create('/'), static fn () => response('ok'));

        $shared = Inertia::getShared();

        expect($shared)
            ->toHaveKeys(['seo', 'seoPayload'])
            ->and($shared['seoPayload']())
            ->toHaveKey('meta')
            ->and($shared['seo']())
            ->toContain('Foo');
    })->skip(!class_exists(Inertia::class), 'inertiajs/inertia-laravel is not installed');

    it('still shares the payload from the deprecated react middleware', function (): void {
        new GenerateInertiaMetadataReact()->handle(Request::create('/'), static fn () => response('ok'));

        expect(Inertia::getShared())->toHaveKey('seoPayload');
    })->skip(!class_exists(Inertia::class), 'inertiajs/inertia-laravel is not installed');
});
