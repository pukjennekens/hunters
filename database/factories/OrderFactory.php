<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'woocommerce_order_id' => fake()->unique()->numerify(),
            'shopify_order_id' => fake()->unique()->optional()->numerify(),
            'woocommerce_order_status' => fake()->randomElement(OrderStatusEnum::cases()),
            'woocommerce_order_updated_at' => fake()->dateTime(),
            'woocommerce_order_synced_at' => fake()->optional()->dateTime(),
            'shopify_order_updated_at' => fake()->optional()->dateTime(),
            'shopify_order_synced_at' => fake()->optional()->dateTime(),
        ];
    }
}
