<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\DataTransferObjects;

use App\Http\Integrations\WooCommerce\Enums\ProductVariationStatusEnum;
use App\Http\Integrations\WooCommerce\Enums\StockStatusEnum;
use App\Http\Integrations\WooCommerce\WooCommerceConnector;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class ProductVariation extends Data
{
    public function __construct(
        public readonly ?int $id = null,

        public readonly ?string $permalink = null,

        public readonly ?string $price = null,

        #[MapName('on_sale')]
        public readonly bool $onSale = false,

        public readonly bool $purchasable = true,

        #[MapName('date_created')]
        #[WithCast(DateTimeInterfaceCast::class, format: WooCommerceConnector::DATE_FORMAT)]
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: WooCommerceConnector::DATE_FORMAT)]
        public readonly ?Carbon $dateCreated = null,

        #[MapName('date_created_gmt')]
        #[WithCast(DateTimeInterfaceCast::class, format: WooCommerceConnector::DATE_FORMAT)]
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: WooCommerceConnector::DATE_FORMAT)]
        public readonly ?Carbon $dateCreatedGmt = null,

        #[MapName('date_modified')]
        #[WithCast(DateTimeInterfaceCast::class, format: WooCommerceConnector::DATE_FORMAT)]
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: WooCommerceConnector::DATE_FORMAT)]
        public readonly ?Carbon $dateModified = null,

        #[MapName('date_modified_gmt')]
        #[WithCast(DateTimeInterfaceCast::class, format: WooCommerceConnector::DATE_FORMAT)]
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: WooCommerceConnector::DATE_FORMAT)]
        public readonly ?Carbon $dateModifiedGmt = null,
    ) {}

    public ?string $description;

    public ?string $sku;

    #[MapName('regular_price')]
    public string $regularPrice;

    #[MapName('sale_price')]
    public ?string $salePrice = null;

    #[MapName('date_on_sale_from')]
    #[WithCast(DateTimeInterfaceCast::class, format: WooCommerceConnector::DATE_FORMAT)]
    #[WithTransformer(DateTimeInterfaceTransformer::class, format: WooCommerceConnector::DATE_FORMAT)]
    public ?Carbon $dataSaleFrom;

    #[MapName('date_on_sale_from_gmt')]
    #[WithCast(DateTimeInterfaceCast::class, format: WooCommerceConnector::DATE_FORMAT)]
    #[WithTransformer(DateTimeInterfaceTransformer::class, format: WooCommerceConnector::DATE_FORMAT)]
    public ?Carbon $dataSaleFromGmt;

    #[MapName('date_on_sale_to')]
    #[WithCast(DateTimeInterfaceCast::class, format: WooCommerceConnector::DATE_FORMAT)]
    #[WithTransformer(DateTimeInterfaceTransformer::class, format: WooCommerceConnector::DATE_FORMAT)]
    public ?Carbon $dataSaleTo;

    #[MapName('date_on_sale_to_gmt')]
    #[WithCast(DateTimeInterfaceCast::class, format: WooCommerceConnector::DATE_FORMAT)]
    #[WithTransformer(DateTimeInterfaceTransformer::class, format: WooCommerceConnector::DATE_FORMAT)]
    public ?Carbon $dataSaleToGmt;

    public ProductVariationStatusEnum $status = ProductVariationStatusEnum::PUBLISH;

    public bool $virtual = false;

    public bool $downloadable = false;

    #[MapName('manage_stock')]
    public bool $manageStock = false;

    #[MapName('stock_quantity')]
    public ?int $stockQuantity;

    #[MapName('stock_status')]
    public StockStatusEnum $stockStatus = StockStatusEnum::IN_STOCK;

    /**
     * @var Collection<int, ProductVariationAttribute> $attributes
     */
    #[DataCollectionOf(ProductVariationAttribute::class)]
    public Collection $attributes;

    #[MapName('menu_order')]
    public ?int $menuOrder;
}
