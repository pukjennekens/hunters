<?php

declare(strict_types=1);

namespace Tests\Fixtures\Integrations\Shopify;

use App\Http\Integrations\Shopify\Enums\CancelReasonEnum;
use App\Http\Integrations\Shopify\Enums\FulfillmentStatusEnum;
use App\Http\Integrations\WooCommerce\Enums\CurrencyEnum;
use Tests\Fixtures\BaseFixture;

class OrderFixture implements BaseFixture
{
    public static function create(array $attributes = [], ?bool $newOrder = null): array
    {
        $orderInfo = [
            'id' => fake()->unique()->randomNumber(),
            'app_id' => fake()->unique()->randomNumber(),
            'browser_ip' => fake()->ipv4(),
            'client_details' => ClientDetailsFixture::create(),
            'closed_at' => fake()->optional()->dateTime(),
            'confirmation_number' => fake()->optional()->numerify(),
            'confirmed' => fake()->boolean(),
            'updated_at' => fake()->dateTime(),
            'created_at' => fake()->dateTime(),
            'currency' => fake()->randomElement(CurrencyEnum::cases()),
        ];

        if (is_null($newOrder)) {
            $orderInfo = fake()->optional()->passthrough($orderInfo) ?? [];
        }

        if (is_bool($newOrder) && $newOrder) {
            $orderInfo = [];
        }

        return array_merge([
            ...$orderInfo,

            'billing_address' => CustomerAddressFixture::create(),
            'shipping_address' => CustomerAddressFixture::create(),
            'buyer_accepts_marketing' => fake()->boolean(),
            'cancel_reason' => fake()->optional()->randomElement(CancelReasonEnum::cases()),
            'cancelled_at' => fake()->optional()->dateTime(),
            'fulfillment_status' => fake()->randomElement(FulfillmentStatusEnum::cases()),
            'line_items' => [LineItemFixture::create()],
            'note' => fake()->optional()->sentence(),
            'shipping_lines' => [ShippingLineFixture::create()],
            'total_price' => (string) fake()->randomFloat(2, 0, 1000),
            'email' => fake()->safeEmail(),
        ], $attributes);
    }
}
