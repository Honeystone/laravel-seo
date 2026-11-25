<?php

declare(strict_types=1);

namespace Honeystone\Seo\OpenGraph;

use DateTimeInterface;
use Honeystone\Seo\OpenGraph\Contracts\Taggable;
use Honeystone\Seo\OpenGraph\Contracts\Type;
use JsonSerializable;

use function array_filter;

class BookProperties implements JsonSerializable, Taggable, Type
{
    /**
     * @param ProfileProperties|array<ProfileProperties>|null $author
     * @param string|array<string>|null $tag
     */
    public function __construct(
        public readonly ProfileProperties|array|null $author = null,
        public readonly ?string $isbn = null,
        public readonly ?DateTimeInterface $releaseDate = null,
        public string|array|null $tag = null,
    ) {}

    public function getPrefix(): string
    {
        return 'book: https://ogp.me/ns/book#';
    }

    public function getType(): string
    {
        return 'book';
    }

    public function defaultTag(string|array $tag): void
    {
        if ($this->tag === null) {
            $this->tag = $tag;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'type' => $this->getType(),
                'author' => $this->author,
                'isbn' => $this->isbn,
                'releaseDate' => $this->releaseDate?->format('c'),
                'tag' => $this->tag,
            ],
            static fn (mixed $value): bool => $value !== null,
        );
    }
}
