<?php

declare(strict_types=1);

namespace Tests\Fixtures\Integrations\Shopify;

use Tests\Fixtures\BaseFixture;

class OptionFixture implements BaseFixture
{
    /**
     * {@inheritDoc}
     */
    public static function create(array $attributes = []): array
    {
        return array_merge([
            'id' => fake()->randomNumber(),
            'product_id' => fake()->randomNumber(),
            'name' => fake()->title(),
            'position' => fake()->randomNumber(),
            'values' => array_map(
                fn () => fake()->word(),
                range(1, fake()->numberBetween(1, 5))
            ),
        ], $attributes);
    }
}
