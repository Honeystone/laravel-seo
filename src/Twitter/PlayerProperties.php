<?php

declare(strict_types=1);

namespace Honeystone\Seo\Twitter;

use Honeystone\Seo\Twitter\Contracts\Card;
use JsonSerializable;

use function array_filter;

class PlayerProperties implements Card, JsonSerializable
{
    public function __construct(
        public readonly string $player,
        public readonly string $width,
        public readonly string $height,
        public readonly ?string $stream,
    ) {}

    public function getName(): string
    {
        return 'player';
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'name' => $this->getName(),
                'player' => $this->player,
                'width' => $this->width,
                'height' => $this->height,
                'stream' => $this->stream,
            ],
            static fn (?string $value): bool => $value !== null,
        );
    }
}
