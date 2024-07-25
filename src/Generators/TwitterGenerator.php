<?php

declare(strict_types=1);

namespace Honeystone\Seo\Generators;

use Honeystone\Seo\Concerns\HasConfig;
use Honeystone\Seo\Concerns\HasData;
use Honeystone\Seo\Concerns\HasDefaults;
use Honeystone\Seo\Contracts\GeneratesMetadata;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;

use function view;

final class TwitterGenerator implements GeneratesMetadata
{
    use HasDefaults, HasData, HasConfig;

    public function getName(): string
    {
        return 'twitter';
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
    public function creator(?string $value): self
    {
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
    public function image(?string $value): self
    {
        return $this->data(__FUNCTION__, $value);
    }

    public function generate(): View
    {
        return view('honeystone-seo::twitter', $this->getData());
    }

    /**
     * @param array<string> $value
     *
     * @return $this
     */
    protected function images(array $value): self
    {
        $this->alias('image', Arr::first($value));

        return $this;
    }
}
