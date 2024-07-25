<?php

declare(strict_types=1);

namespace Honeystone\Seo\OpenGraph;

use Honeystone\Seo\OpenGraph\Contracts\Type;

class ProfileProperties implements Type
{
    public function __construct(
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
        public readonly ?string $username = null,
        public readonly ?string $gender = null,
    ) {
    }

    public function getPrefix(): string
    {
        return 'profile: https://ogp.me/ns/profile#';
    }

    public function getType(): string
    {
        return 'profile';
    }
}
