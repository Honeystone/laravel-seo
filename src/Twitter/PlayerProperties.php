<?php

declare(strict_types=1);

namespace Honeystone\Seo\Twitter;

use Honeystone\Seo\Twitter\Contracts\Card;

class PlayerProperties implements Card
{
    public function __construct(
        public readonly string $player,
        public readonly string $width,
        public readonly string $height,
        public readonly ?string $stream,
    ) {
    }

    public function getName(): string
    {
        return 'player';
    }
}
