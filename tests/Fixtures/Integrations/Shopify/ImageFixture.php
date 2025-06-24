<?php

declare(strict_types=1);

namespace Tests\Fixtures\Integrations\Shopify;

use Tests\Fixtures\BaseFixture;

class ImageFixture implements BaseFixture
{
    /**
     * {@inheritDoc}
     */
    public static function create(array $attributes = []): array
    {
        return array_merge([
            'id' => fake()->randomNumber(),
            'position' => fake()->randomNumber(),
            'src' => fake()->imageUrl(),
            'width' => fake()->randomNumber(),
            'height' => fake()->randomNumber(),
            'created_at' => fake()->dateTime(),
            'updated_at' => fake()->dateTime(),
        ], $attributes);
    }
}
