<?php

declare(strict_types=1);

namespace Honeystone\Seo\Generators;

use Honeystone\Seo\Concerns\HasConfig;
use Honeystone\Seo\Concerns\HasData;
use Honeystone\Seo\Concerns\HasDefaults;
use Honeystone\Seo\Contracts\GeneratesMetadata;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use RuntimeException;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Graph;
use Spatie\SchemaOrg\MultiTypedEntity;

use function array_diff;
use function array_filter;
use function array_unique;
use function array_values;
use function class_exists;
use function compact;
use function count;
use function implode;
use function json_encode;
use function sprintf;
use function url;
use function view;

final class JsonLdGenerator implements GeneratesMetadata
{
    use HasDefaults, HasData, HasConfig;

    /**
     * @var array<string, mixed>
     */
    private array $custom = [];

    private Graph|MultiTypedEntity|BaseType|null $imported = null;

    private ?BaseType $schema = null;

    /**
     * @var array<string>
     */
    private array $expected = [];

    /**
     * @var array<string>
     */
    private array $checkedIn = [];

    public function getName(): string
    {
        return 'json-ld';
    }

    /**
     * @return $this
     */
    public function enabled(bool $value): self
    {
        return $this->data(__FUNCTION__, $value);
    }

    /**
     * @param string|class-string<BaseType>|null $value
     *
     * @return $this
     */
    public function type(?string $value): self
    {
        return $this->data(__FUNCTION__, $value);
    }

    /**
     * @return $this
     */
    public function id(?string $value = null, string $append = '', ?bool $useUrl = null): self
    {
        if (
            $useUrl ||
            ($value === null && $append === '' && $useUrl === null)
        ) {
            return $this->data(__FUNCTION__, $this->getRaw('url', url()->current()).$append);
        }

        if ($value !== null) {
            return $this->data(__FUNCTION__, $value.$append);
        }

        return $this->data(__FUNCTION__, null);
    }

    /**
     * @return $this
     */
    public function name(?string $value): self
    {
        return $this->data(__FUNCTION__, $value);
    }

    /**
     * @return $this
     */
    public function title(?string $value): self
    {
        return $this->alias('name', $value);
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
    public function image(?string $value): self
    {
        return $this->data('images', [$value]);
    }

    /**
     * @param array<string|null> $value
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
     * @return $this
     */
    public function nonce(string $value): self
    {
        return $this->data(__FUNCTION__, $value);
    }

    /**
     * @param string|array<string, mixed> $content
     *
     * @return $this
     */
    public function property(string $property, string|array $content): self
    {
        $this->custom[$property] = $content;

        return $this;
    }

    public function graph(): Graph
    {
        if ($this->imported instanceof Graph) {
            return $this->imported;
        }

        return $this->imported = new Graph();
    }

    public function multi(): MultiTypedEntity
    {
        if ($this->imported instanceof MultiTypedEntity) {
            return $this->imported;
        }

        return $this->imported = new MultiTypedEntity();
    }

    public function schema(): ?BaseType
    {
        if (
            !($this->config['use-schema-org'] ?? true) ||
            !class_exists(BaseType::class)
        ) {
            return null;
        }

        $typeClass = $this->getRaw('type', 'WebPage');

        if (!Str::startsWith($typeClass, 'Spatie\\SchemaOrg\\')) {
            $typeClass = 'Spatie\\SchemaOrg\\'.Str::studly($typeClass);
        }

        if (!class_exists($typeClass)) {
            throw new RuntimeException("Invalid schema type `{$typeClass}`.");
        }

        if (!$this->schema instanceof $typeClass) {
            $this->schema = new $typeClass();
        }

        return $this->schema
            ->identifier($this->getRaw('id'))
            ->name($this->getRaw('name'))
            ->description($this->getRaw('description'))
            ->image($this->getRaw('images'))
            ->url($this->getRaw('url', url()->current()));
    }

    /**
     * @return $this
     */
    public function import(Graph|MultiTypedEntity|BaseType $schema): self
    {
        $this->imported = $schema;

        return $this;
    }

    /**
     * @param string ...$components
     *
     * @return $this
     */
    public function expect(string ...$components): self
    {
        $this->expected = array_unique([...$this->expected, ...$components]);

        return $this;
    }

    /**
     * @param string ...$components
     *
     * @return $this
     */
    public function checkIn(string ...$components): self
    {
        $this->checkedIn = array_unique([...$this->checkedIn, ...$components]);

        return $this;
    }

    public function generate(): View
    {
        $this->checkExpectations();

        $data = $this->getData();

        $generated = $data['enabled'] ?? $this->config['enabled'] ?? true ?
            $this->generateJsonLd($data) :
            '';

        return view('honeystone-seo::json-ld', compact('generated') + $data);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function generateJsonLd(array $data): string
    {
        $flags = JSON_UNESCAPED_UNICODE;

        if ($this->config['pretty'] ?? false) {
            $flags |= JSON_PRETTY_PRINT;
        }

        if (
            $this->imported instanceof Graph ||
            $this->imported instanceof MultiTypedEntity
        ) {

            if (
                ($this->config['place-on-graph'] ?? false) &&
                $this->schema() !== null
            ) {
                $this->imported->add($this->schema());
            }

            if ($this->imported instanceof Graph) {
                return json_encode($this->imported->toArray(), $flags);
            }
        }

        if ($this->schema() !== null && $this->imported === null) {

            return json_encode([
                ...$this->schema()->toArray(),
                ...($this->config['custom'] ?? []),
                ...$this->custom,
            ], $flags);
        }

        return json_encode(array_filter([
            '@context' => 'https://schema.org',
            '@type' => $data['type'] ?? $this->config['type'] ?? 'WebPage',
            '@id' => $data['id'] ?? null,
            'name' => $data['name'] ?? $this->config['name'] ?? null,
            'description' => $data['description'] ?? $this->config['description'] ?? null,
            'image' => array_values($data['images'] ?? $this->config['images'] ?? []),
            'url' => $data['url'] ?? $this->config['url'] ?? url()->current(),
            ...($this->config['custom'] ?? []),
            ...$this->custom,
            ...($this->imported?->toArray() ?? []),
        ]), $flags);
    }

    private function checkExpectations(): void
    {
        $mia = array_diff($this->expected, $this->checkedIn);
        $count = count($mia);

        if ($count > 0) {
            throw new RuntimeException(
                sprintf(
                    'The following (%d) expected JSON-LD components failed to check-in: %s',
                    $count,
                    implode(', ', $mia),
                ),
            );
        }

        $unexpected = array_diff($this->checkedIn, $this->expected);
        $count = count($unexpected);

        if ($count > 0) {
            throw new RuntimeException(
                sprintf(
                    'The following (%d) unexpected JSON-LD components checked-in: %s',
                    $count,
                    implode(', ', $unexpected),
                ),
            );
        }
    }
}
