<?php

declare(strict_types=1);

namespace Honeystone\Seo\Twitter;

use Honeystone\Seo\Twitter\Contracts\Card;
use JsonSerializable;

use function array_filter;

class AppProperties implements Card, JsonSerializable
{
    public function __construct(
        public readonly ?string $iphoneId = null,
        public readonly ?string $iphoneName = null,
        public readonly ?string $iphoneUrl = null,
        public readonly ?string $ipadId = null,
        public readonly ?string $ipadName = null,
        public readonly ?string $ipadUrl = null,
        public readonly ?string $googlePlayId = null,
        public readonly ?string $googlePlayName = null,
        public readonly ?string $googlePlayUrl = null,
        public readonly ?string $country = null,
    ) {}

    public function getName(): string
    {
        return 'app';
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'name' => $this->getName(),
                'iphoneId' => $this->iphoneId,
                'iphoneName' => $this->iphoneName,
                'iphoneUrl' => $this->iphoneUrl,
                'ipadId' => $this->ipadId,
                'ipadName' => $this->ipadName,
                'ipadUrl' => $this->ipadUrl,
                'googlePlayId' => $this->googlePlayId,
                'googlePlayName' => $this->googlePlayName,
                'googlePlayUrl' => $this->googlePlayUrl,
                'country' => $this->country,
            ],
            static fn (?string $value): bool => $value !== null,
        );
    }
}
