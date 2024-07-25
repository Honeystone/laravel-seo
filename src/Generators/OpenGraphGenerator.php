<?php

declare(strict_types=1);

namespace Honeystone\Seo\Generators;

use Honeystone\Seo\Concerns\HasConfig;
use Honeystone\Seo\Concerns\HasData;
use Honeystone\Seo\Concerns\HasDefaults;
use Honeystone\Seo\Contracts\GeneratesMetadata;
use Honeystone\Seo\OpenGraph\AudioProperties;
use Honeystone\Seo\OpenGraph\Contracts\Taggable;
use Honeystone\Seo\OpenGraph\Contracts\Type;
use Honeystone\Seo\OpenGraph\ImageProperties;
use Honeystone\Seo\OpenGraph\VideoProperties;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use UnexpectedValueException;

use function array_merge;
use function in_array;
use function seo;
use function view;

final class OpenGraphGenerator implements GeneratesMetadata
{
    use HasDefaults, HasData, HasConfig;

    public const PREFIX = 'og: https://ogp.me/ns#';

    public const DETERMINER_A = 'a';

    public const DETERMINER_AN = 'an';

    public const DETERMINER_THE = 'the';

    public const DETERMINER_AUTO = 'auto';

    public const DETERMINER_BLANK = '';

    private ?Type $type = null;

    /**
     * @var array<string>
     */
    private array $custom = [];

    public function getName(): string
    {
        return 'open-graph';
    }

    public function prefix(): string
    {
        if ($this->type !== null) {
            return self::PREFIX.' '.$this->type->getPrefix();
        }

        return self::PREFIX;
    }

    /**
     * @return $this
     */
    public function enabled(bool $value): self
    {
        return $this->data(__FUNCTION__, $value);
    }

    /**
     * @return $this
     */
    public function site(?string $value): self
    {
        return $this->data(__FUNCTION__, $value);
    }

    /**
     * @return $this
     */
    public function type(string|Type $value): self
    {
        if ($value instanceof Type) {
            $this->type = $value;
        }

        return $this->data(__FUNCTION__, $value);
    }

    /**
     * @return $this
     */
    public function title(?string $value): self
    {
        return $this->data(__FUNCTION__, $value);
    }

    /**
     * @return $this
     */
    public function description(?string $value): self
    {
        return $this->data(__FUNCTION__, $value);
    }

    /**
     * @return $this
     */
    public function image(string|ImageProperties|null $value): self
    {
        return $this->data('images', [$value]);
    }

    /**
     * @param array<string|ImageProperties|null> $value
     *
     * @return $this
     */
    public function images(array $value): self
    {
        return $this->data(__FUNCTION__, $value);
    }

    /**
     * @return $this
     */
    public function url(?string $value): self
    {
        return $this->data(__FUNCTION__, $value);
    }

    /**
     * @param array<string|AudioProperties|array<string|AudioProperties|null>>|null $value
     *
     * @return $this
     */
    public function audio(string|AudioProperties|array|null $value): self
    {
        return $this->data(__FUNCTION__, Arr::wrap($value));
    }

    /**
     * @return $this
     */
    public function video(string|VideoProperties|null $value): self
    {
        return $this->data('videos', [$value]);
    }

    /**
     * @param array<string|VideoProperties|null> $value
     *
     * @return $this
     */
    public function videos(array $value): self
    {
        return $this->data(__FUNCTION__, $value);
    }

    /**
     * @return $this
     */
    public function determiner(string $value): self
    {
        if (!in_array($value, ['a', 'an', 'the', 'auto', ''])) {
            throw new UnexpectedValueException('The determiner must be a, an, the auto or a blank string.');
        }

        return $this->data(__FUNCTION__, $value);
    }

    /**
     * @return $this
     */
    public function locale(?string $value): self
    {
        return $this->data(__FUNCTION__, $value);
    }

    /**
     * @param array<string|null> $value
     *
     * @return $this
     */
    public function alternateLocales(array $value): self
    {
        return $this->data(__FUNCTION__, $value);
    }

    /**
     * @param array<string|null> $value
     *
     * @return $this
     */
    public function tags(array $value): self
    {
        return $this->data(__FUNCTION__, $value);
    }

    /**
     * @param string|array<string> $content
     *
     * @return $this
     */
    public function property(string $property, string|array $content): self
    {
        $this->custom[] = [$property => $content];

        return $this;
    }

    public function generate(): View
    {
        $this->syncTags();

        return view('honeystone-seo::open-graph', [
            'custom' => array_merge($this->config['custom'] ?? [], $this->custom),
        ] + $this->getData());
    }

    private function syncTags(): void
    {
        if (
            $this->type instanceof Taggable &&
            /** @phpstan-ignore-next-line */
            seo()->getConfig('sync.keywords-tags')
        ) {
            $this->type->defaultTag($this->defaults['tags'] ?? []);
        }
    }
}
