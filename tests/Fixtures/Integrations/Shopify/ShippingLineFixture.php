<?php

declare(strict_types=1);

namespace Tests\Fixtures\Integrations\Shopify;

use Tests\Fixtures\BaseFixture;

class ShippingLineFixture implements BaseFixture
{
    /**
     * {@inheritDoc}
     */
    public static function create(array $attributes = []): array
    {
        return array_merge([
            'title' => fake()->sentence(),
            'price' => (string) fake()->randomFloat(2, 0, 1000),
        ], $attributes);
    }
}
