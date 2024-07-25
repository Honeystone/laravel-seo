<?php

declare(strict_types=1);

namespace Honeystone\Seo\OpenGraph;

use DateTime;
use Honeystone\Seo\OpenGraph\Contracts\Taggable;
use Honeystone\Seo\OpenGraph\Contracts\Type;

class ArticleProperties implements Type, Taggable
{
    /**
     * @param ProfileProperties|array<ProfileProperties>|null $author
     * @param string|array<string>|null $tag
     */
    public function __construct(
        public readonly ?DateTime $publishedTime = null,
        public readonly ?DateTime $modifiedTime = null,
        public readonly ?DateTime $expirationTime = null,
        public readonly ProfileProperties|array|null $author = null,
        public readonly ?string $section = null,
        public string|array|null $tag = null,
    ) {
    }

    public function getPrefix(): string
    {
        return 'article: https://ogp.me/ns/article#';
    }

    public function getType(): string
    {
        return 'article';
    }

    public function defaultTag(string|array $tag): void
    {
        if ($this->tag === null) {
            $this->tag = $tag;
        }
    }
}
