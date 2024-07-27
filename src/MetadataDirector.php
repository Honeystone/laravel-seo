<?php

declare(strict_types=1);

namespace Honeystone\Seo;

use Honeystone\Seo\Concerns\HasConfig;
use Honeystone\Seo\Concerns\HasDefaults;
use Honeystone\Seo\Contracts\BuildsMetadata;
use Honeystone\Seo\Contracts\GeneratesMetadata;
use Honeystone\Seo\Contracts\RegistersGenerators;
use Honeystone\Seo\Generators\JsonLdGenerator;
use Honeystone\Seo\Generators\MetaGenerator;
use Honeystone\Seo\Generators\OpenGraphGenerator;
use Honeystone\Seo\Generators\TwitterGenerator;
use Honeystone\Seo\OpenGraph\AudioProperties;
use Honeystone\Seo\OpenGraph\Contracts\Type;
use Honeystone\Seo\OpenGraph\ImageProperties;
use Honeystone\Seo\OpenGraph\VideoProperties;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use RuntimeException;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Graph;
use Spatie\SchemaOrg\MultiTypedEntity;

use function array_map;
use function compact;
use function lcfirst;
use function str_replace;
use function trim;
use function view;

/**
 * @see MetaGenerator
 * @method self metaTitle(string $value, string|false|null $template = null)
 * @method self metaTitleTemplate(string $value)
 * @method self metaDescription(string $value)
 * @method self metaKeywords(string[] $value)
 * @method self metaCanonical(string $value)
 * @method self metaCanonicalEnabled(bool $value)
 * @method self metaRobots(string[] $value)
 * @method self metaTag(string $property, string|string[] $content)
 * @method self metaConfig(mixed[] $data)
 * @method self metaDefaults(mixed[] $data)
 *
 * @see TwitterGenerator
 * @method self twitterEnabled(bool $value)
 * @method self twitterSite(string $value)
 * @method self twitterCreator(string $value)
 * @method self twitterTitle(string $value)
 * @method self twitterDescription(string $value)
 * @method self twitterImage(string $value)
 * @method self twitterConfig(mixed[] $data)
 * @method self twitterDefaults(mixed[] $data)
 *
 * @see OpenGraphGenerator
 * @method string openGraphPrefix()
 * @method self openGraphEnabled(bool $value)
 * @method self openGraphSite(string $value)
 * @method self openGraphType(string|Type $value)
 * @method self openGraphTitle(string $value)
 * @method self openGraphDescription(string $value)
 * @method self openGraphImage(string|ImageProperties $value)
 * @method self openGraphImages(string[]|ImageProperties[] $value)
 * @method self openGraphUrl(string $value)
 * @method self openGraphAudio(string|string[]|AudioProperties|AudioProperties[] $value)
 * @method self openGraphVideo(string|VideoProperties $value)
 * @method self openGraphVideos(string[]|VideoProperties[] $value)
 * @method self openGraphDeterminer(string $value)
 * @method self openGraphLocale(string $value)
 * @method self openGraphAlternateLocales(string[] $value)
 * @method self openGraphProperty(string $property, string|string[] $content)
 * @method self openGraphConfig(mixed[] $data)
 * @method self openGraphDefaults(mixed[] $data)
 *
 * @see JsonLdGenerator
 * @method self jsonLdEnabled(bool $value)
 * @method self jsonLdType(string $value)
 * @method self jsonLdName(string $value)
 * @method self jsonLdTitle(string $value)
 * @method self jsonLdDescription(string $value)
 * @method self jsonLdImage(string $value)
 * @method self jsonLdImages(string[] $value)
 * @method self jsonLdUrl(string $value)
 * @method self jsonLdNonce(string $value)
 * @method self jsonLdProperty(string $property, string|string[] $content)
 * @method Graph jsonLdGraph()
 * @method MultiTypedEntity jsonLdMulti()
 * @method BaseType jsonLdFirst()
 * @method self jsonLdImport(Graph|MultiTypedEntity|BaseType $schema)
 * @method self jsonLdExpect(string ...$components)
 * @method self jsonLdCheckIn(string ...$components)
 * @method self jsonLdConfig(mixed[] $data)
 * @method self jsonLdDefaults(mixed[] $data)
 */
final class MetadataDirector implements BuildsMetadata
{
    use HasDefaults;

    use HasConfig {
        config as private setConfig;
    }

    public function __construct(
        private readonly RegistersGenerators $register,
        ?Repository $config = null,
    ) {
        $this->config = $config !== null ?
            $config->get('honeystone-seo') :
            [];
    }

    /**
     * @param array<string, mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        $callName = $name;

        $name = Arr::first(
            $this->register->allNames(),
            static fn (string $name): bool => Str::startsWith($callName, Str::camel($name)),
        );

        if ($name === null) {
            throw new RuntimeException("Call to undefined method {$callName}.");
        }

        $method = lcfirst(str_replace(Str::camel($name), '', $callName));

        $response = $this->generator($name)->{$method}(...$arguments);

        if ($response instanceof GeneratesMetadata) {
            return $this;
        }

        return $response;
    }

    public function getName(): string
    {
        return 'director';
    }

    public function getConfig(string $path, mixed $default = false): mixed
    {
        return Arr::get($this->config, $path, $default);
    }

    public function locale(?string $value): self
    {
        $this->defaults[__FUNCTION__] = $value;

        return $this;
    }

    public function title(?string $value, string|false|null $template = null): self
    {
        $this->defaults[__FUNCTION__] = $value;

        if ($template !== null) {
            $this->defaults['titleTemplate'] = $template;
        }
        if ($template === false) {
            $this->defaults['titleTemplate'] = '';
        }

        return $this;
    }

    public function description(?string $value): self
    {
        $this->defaults[__FUNCTION__] = $value;

        return $this;
    }

    public function keywords(?string ...$value): self
    {
        $this->defaults[__FUNCTION__] = $value;

        if (
            $this->getConfig('sync.keywords-tags') &&
            !isset($this->defaults['tags'])
        ) {
            $this->defaults['tags'] = $value;
        }

        return $this;
    }

    public function url(?string $value): self
    {
        $this->defaults[__FUNCTION__] = $value;

        if (
            $this->getConfig('sync.url-canonical') &&
            !isset($this->defaults['canonical'])
        ) {
            $this->canonical($value);
        }

        return $this;
    }

    public function canonical(?string $value): self
    {
        $this->defaults[__FUNCTION__] = $value;

        if (
            $this->getConfig('sync.url-canonical') &&
            !isset($this->defaults['url'])
        ) {
            $this->url($value);
        }

        return $this;
    }

    public function canonicalEnabled(bool $value): self
    {
        $this->defaults[__FUNCTION__] = $value;

        return $this;
    }

    public function images(?string ...$value): self
    {
        $this->defaults[__FUNCTION__] = $value;

        return $this;
    }

    public function robots(?string ...$value): self
    {
        $this->defaults[__FUNCTION__] = $value;

        return $this;
    }

    public function generator(string $name): GeneratesMetadata
    {
        return $this->register->get($name);
    }

    public function generate(string ...$only): View
    {
        $this->propagateDefaults();

        $generated = implode(
            "\n    ",
            array_map(
                fn (GeneratesMetadata $generator): string => trim(
                    /** @phpstan-ignore-next-line */
                    (string) $generator->generate(),
                ),
                count($only) > 0 ?
                    $this->register->only($only) :
                    $this->register->all(),
            ),
        );

        return view('honeystone-seo::layout', compact('generated'));
    }

    public function config(array $data): self
    {
        $this->setConfig($data);

        foreach ($this->register->all() as $generator) {
            $generator->config($this->getConfig('generators.'.$generator::class, []));
        }

        return $this;
    }

    private function propagateDefaults(): void
    {
        foreach ($this->register->all() as $generator) {
            $generator->defaults($this->defaults);
        }
    }
}
