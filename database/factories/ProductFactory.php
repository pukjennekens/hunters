<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_name' => fake()->title,
            'product_category_id' => fake()->optional()->passthrough(ProductCategory::factory()),
            'sync_id' => uniqid(),
            'shopify_product_id' => fake()->unique()->optional()->numerify(),
            'woocommerce_product_id' => fake()->unique()->optional()->numerify(),
            'shopify_product_updated_at' => fake()->optional()->dateTime(),
            'woocommerce_product_synced_at' => fake()->optional()->dateTime(),
        ];
    }
}
