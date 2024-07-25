<?php

declare(strict_types=1);

namespace Honeystone\Seo\Contracts;

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
