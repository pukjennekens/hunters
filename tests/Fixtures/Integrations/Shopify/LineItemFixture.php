<?php

declare(strict_types=1);

namespace Tests\Fixtures\Integrations\Shopify;

use App\Http\Integrations\Shopify\Enums\FulfillmentStatusEnum;
use Tests\Fixtures\BaseFixture;

class LineItemFixture implements BaseFixture
{
    /**
     * {@inheritDoc}
     */
    public static function create(array $attributes = []): array
    {
        return array_merge([
            'id' => fake()->unique()->optional()->randomNumber(),
            'fulfillment_status' => fake()->randomElement(FulfillmentStatusEnum::cases()),
            'price' => (string) fake()->randomFloat(2, 0, 1000),
            'product_id' => fake()->unique()->randomNumber(),
            'quantity' => fake()->randomNumber(2, false),
            'variant_id' => fake()->unique()->optional()->randomNumber(),
            'variant_title' => fake()->optional()->words(3, true),
            'title' => fake()->optional()->words(3, true),
        ], $attributes);
    }
}
