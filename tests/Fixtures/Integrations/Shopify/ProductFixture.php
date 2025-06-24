<?php

declare(strict_types=1);

namespace Tests\Fixtures\Integrations\Shopify;

use App\Http\Integrations\Shopify\Enums\ProductStatusEnum;
use App\Http\Integrations\Shopify\Enums\PublishedScopeEnum;
use Tests\Fixtures\BaseFixture;

class ProductFixture implements BaseFixture
{
    /**
     * {@inheritDoc}
     */
    public static function create(array $attributes = []): array
    {
        return array_merge([
            'id' => fake()->randomNumber(),
            'title' => fake()->title(),
            'handle' => fake()->slug(),
            'product_type' => fake()->word(),
            'published_scope' => fake()->randomElement(PublishedScopeEnum::cases()),
            'status' => fake()->randomElement(ProductStatusEnum::cases()),
            'body_html' => fake()->optional()->paragraph(),
            'vendor' => fake()->optional()->word(),
            'tags' => implode(',', fake()->optional()->words() ?? []),
            'images' => [ImageFixture::create()],
            'options' => [OptionFixture::create()],
            'variants' => [VariantFixture::create()],
            'created_at' => fake()->dateTime(),
            'updated_at' => fake()->dateTime(),
            'published_at' => fake()->optional()->dateTime(),
        ], $attributes);
    }
}
