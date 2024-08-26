<?php

declare(strict_types=1);

namespace Honeystone\Seo\Contracts;

use Honeystone\Seo\Generators\RealFaviconGenerator;
use Honeystone\Seo\OpenGraph\AudioProperties;
use Honeystone\Seo\OpenGraph\Contracts\Type;
use Honeystone\Seo\OpenGraph\ImageProperties;
use Honeystone\Seo\OpenGraph\VideoProperties;
use Honeystone\Seo\Twitter\Contracts\Card;
use Spatie\SchemaOrg\Graph;
use Spatie\SchemaOrg\MultiTypedEntity;

/**
 * @see MetaGenerator
 * @method self metaTitle(string|null $value, string|false|null $template = null)
 * @method self metaTitleTemplate(string|null $value)
 * @method self metaDescription(string|null $value)
 * @method self metaKeywords(string[] $value)
 * @method self metaCanonical(string|null $value)
 * @method self metaCanonicalEnabled(bool $value)
 * @method self metaRobots(string[] $value)
 * @method self metaTag(string $property, string|string[] $content)
 * @method self metaConfig(mixed[] $data)
 * @method self metaDefaults(mixed[] $data)
 *
 * @see TwitterGenerator
 * @method self twitterEnabled(bool $value)
 * @method self twitterSite(string|null $value)
 * @method self twitterCard(string|Card $value)
 * @method self twitterCreator(string|null $value)
 * @method self twitterCreatorId(string|null $value)
 * @method self twitterTitle(string|null $value)
 * @method self twitterDescription(string|null $value)
 * @method self twitterImage(string|null $value, string|null $alt = null)
 * @method self twitterConfig(mixed[] $data)
 * @method self twitterDefaults(mixed[] $data)
 *
 * @see OpenGraphGenerator
 * @method string openGraphPrefix()
 * @method self openGraphEnabled(bool $value)
 * @method self openGraphSite(string|null $value)
 * @method self openGraphType(string|Type $value)
 * @method self openGraphTitle(string|null $value)
 * @method self openGraphDescription(string|null $value)
 * @method self openGraphImage(string|ImageProperties|null $value)
 * @method self openGraphImages(string[]|ImageProperties[] $value)
 * @method self openGraphUrl(string|null $value)
 * @method self openGraphAudio(string|string[]|AudioProperties|AudioProperties[]|null $value)
 * @method self openGraphVideo(string|VideoProperties|null $value)
 * @method self openGraphVideos(string[]|VideoProperties[] $value)
 * @method self openGraphDeterminer(string $value)
 * @method self openGraphLocale(string|null $value)
 * @method self openGraphAlternateLocales(string[] $value)
 * @method self openGraphProperty(string $property, string|string[] $content)
 * @method self openGraphConfig(mixed[] $data)
 * @method self openGraphDefaults(mixed[] $data)
 *
 * @see JsonLdGenerator
 * @method self jsonLdEnabled(bool $value)
 * @method self jsonLdType(string|null $value)
 * @method self jsonLdName(string|null $value)
 * @method self jsonLdId(string|null $value = null, string $append = '', bool|null $useUrl = null)
 * @method self jsonLdTitle(string|null $value)
 * @method self jsonLdDescription(string|null $value)
 * @method self jsonLdImage(string|null $value)
 * @method self jsonLdImages(string[] $value)
 * @method self jsonLdUrl(string|null $value)
 * @method self jsonLdNonce(string $value)
 * @method self jsonLdProperty(string $property, string|string[] $content)
 * @method Graph jsonLdGraph()
 * @method MultiTypedEntity jsonLdMulti()
 * @method BaseType jsonLdSchema()
 * @method self jsonLdImport(Graph|MultiTypedEntity|BaseType $schema)
 * @method self jsonLdExpect(string ...$components)
 * @method self jsonLdCheckIn(string ...$components)
 * @method self jsonLdConfig(mixed[] $data)
 * @method self jsonLdDefaults(mixed[] $data)
 *
 * @see RealFaviconGenerator
 * @method self faviconFetch(string|null $apiKey = null, string|null $image = null)
 * @method self faviconConfig(mixed[] $data)
 * @method self faviconDefaults(mixed[] $data)
 */
interface BuildsMetadata extends GeneratesMetadata
{
    /**
     * @return $this
     */
    public function locale(?string $value): self;

    /**
     * @return $this
     */
    public function title(?string $value, string|false|null $template = null): self;

    /**
     * @return $this
     */
    public function description(?string $value): self;

    /**
     * @param string ...$value
     *
     * @return $this
     */
    public function keywords(?string ...$value): self;

    /**
     * @return $this
     */
    public function url(?string $value): self;

    /**
     * @return $this
     */
    public function canonical(?string $value): self;

    /**
     * @return $this
     */
    public function canonicalEnabled(bool $value): self;

    /**
     * @param string ...$value
     *
     * @return $this
     */
    public function images(?string ...$value): self;

    /**
     * @param string ...$value
     *
     * @return $this
     */
    public function robots(?string ...$value): self;

    public function generator(string $name): GeneratesMetadata;
}
