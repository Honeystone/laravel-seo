<?php

declare(strict_types=1);

namespace Honeystone\Seo\OpenGraph;

use DateTime;
use Honeystone\Seo\OpenGraph\Contracts\Taggable;
use Honeystone\Seo\OpenGraph\Contracts\Type;

class BookProperties implements Type, Taggable
{
    /**
     * @param ProfileProperties|array<ProfileProperties>|null $author
     * @param string|array<string>|null $tag
     */
    public function __construct(
        public readonly ProfileProperties|array|null $author = null,
        public readonly ?string $isbn = null,
        public readonly ?DateTime $releaseDate = null,
        public string|array|null $tag = null,
    ) {
    }

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
}
