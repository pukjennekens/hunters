<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\DataTransferObjects;

use App\Http\Integrations\WooCommerce\Enums\CatalogVisibilityEnum;
use App\Http\Integrations\WooCommerce\Enums\ProductStatusEnum;
use App\Http\Integrations\WooCommerce\Enums\ProductTypeEnum;
use App\Http\Integrations\WooCommerce\Enums\StockStatusEnum;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;

class Product extends Data
{
    public ?int $id;

    public string $name;

    public ?string $slug;

    public ?string $permalink;

    public ProductTypeEnum $type = ProductTypeEnum::SIMPLE;

    public ProductStatusEnum $status = ProductStatusEnum::PUBLISH;

    public bool $featured = false;

    #[MapName('catalog_visibility')]
    public CatalogVisibilityEnum $catalogVisibility = CatalogVisibilityEnum::VISIBLE;

    public ?string $description;

    #[MapName('short_description')]
    public ?string $shortDescription;

    public ?string $sku;

    #[MapName('regular_price')]
    public ?string $regularPrice;

    #[MapName('sale_price')]
    public ?string $salePrice;

    public bool $virtual = false;

    public bool $downloadable = false;

    #[MapName('manage_stock')]
    public bool $manageStock = false;

    #[MapName('stock_quantity')]
    public ?int $stockQuantity;

    #[MapName('stock_status')]
    public StockStatusEnum $stockStatus = StockStatusEnum::IN_STOCK;

    /**
     * @var list<array<string, mixed>>
     */
    #[MapName('meta_data')]
    public array $metaData;

    /**
     * @var Collection<int, Image> $images
     */
    #[DataCollectionOf(Image::class)]
    public Collection $images;

    /**
     * @var Collection<int, ProductAttribute> $attributes
     */
    #[DataCollectionOf(ProductAttribute::class)]
    public Collection $attributes;

    /**
     * @var Collection<int, ProductCategory> $categories
     */
    #[DataCollectionOf(ProductCategory::class)]
    public Collection $categories;

    /**
     * @var Collection<int, DefaultAttribute> $defaultAttributes
     */
    #[MapName('default_attributes')]
    #[DataCollectionOf(DefaultAttribute::class)]
    public Collection $defaultAttributes;
}
