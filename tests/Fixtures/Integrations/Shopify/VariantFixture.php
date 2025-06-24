<?php

declare(strict_types=1);

namespace Tests\Fixtures\Integrations\Shopify;

use App\Http\Integrations\Shopify\Enums\InventoryPolicyEnum;
use Tests\Fixtures\BaseFixture;

class VariantFixture implements BaseFixture
{
    /**
     * {@inheritDoc}
     */
    public static function create(array $attributes = []): array
    {
        return array_merge([
            'id' => fake()->randomNumber(),
            'image_id' => fake()->optional()->randomNumber(),
            'inventory_item_id' => fake()->randomNumber(),
            'inventory_management' => fake()->optional()->word(),
            'inventory_policy' => fake()->randomElement(InventoryPolicyEnum::cases()),
            'inventory_quantity' => fake()->randomNumber(),
            'option1' => fake()->optional()->word(),
            'option2' => fake()->optional()->word(),
            'option3' => fake()->optional()->word(),
            'barcode' => fake()->ean13(),
            'fulfillment_service' => fake()->word(),
            'price' => (string) fake()->randomFloat(2, 1, 1000),
            'compare_at_price' => (string) fake()->optional()->randomFloat(2, 1, 1000) ?? null,
            'created_at' => fake()->dateTime(),
            'updated_at' => fake()->dateTime(),
            'sku' => fake()->optional()->word(),
        ], $attributes);
    }
}
