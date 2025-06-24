<?php

declare(strict_types=1);

namespace Tests\Fixtures\Integrations\Shopify;

use Tests\Fixtures\BaseFixture;

class CustomerAddressFixture implements BaseFixture
{
    /**
     * {@inheritDoc}
     */
    public static function create(array $attributes = []): array
    {
        return array_merge([
            'address1' => fake()->optional()->address(),
            'address2' => fake()->optional()->address(),
            'city' => fake()->optional()->city(),
            'company' => fake()->optional()->company(),
            'country' => fake()->optional()->country(),
            'country_code' => fake()->optional()->countryCode(),
            'first_mame' => fake()->optional()->firstName(),
            'last_mame' => fake()->optional()->lastName(),
            'latitude' => fake()->optional()->latitude(),
            'longitude' => fake()->optional()->longitude(),
            'name' => fake()->optional()->name(),
            'phone' => fake()->optional()->phoneNumber(),
            'province' => fake()->optional()->city(),
            'province_code' => fake()->optional()->citySuffix(),
            'zip' => fake()->optional()->postcode(),
        ], $attributes);
    }
}
