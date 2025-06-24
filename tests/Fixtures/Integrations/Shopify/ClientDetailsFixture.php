<?php

declare(strict_types=1);

namespace Tests\Fixtures\Integrations\Shopify;

use Tests\Fixtures\BaseFixture;

class ClientDetailsFixture implements BaseFixture
{
    public static function create(array $attributes = []): array
    {
        return array_merge([
            'acceptLanguage' => fake()->optional()->languageCode(),
            'browserHeight' => fake()->optional()->randomNumber(),
            'browserIp' => fake()->optional()->ipv4(),
            'browserWidth' => fake()->optional()->randomNumber(),
            'sessionHash' => fake()->optional()->uuid(),
            'userAgent' => fake()->optional()->userAgent(),
        ], $attributes);
    }
}
