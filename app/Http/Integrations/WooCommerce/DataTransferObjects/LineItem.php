<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\DataTransferObjects;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;

class LineItem extends Data
{
    public function __construct(
        public readonly int $id,
        #[MapName('subtotal_tax')]
        public readonly string $subtotalTax,
        #[MapName('total_tax')]
        public readonly string $totalTax,

        /**
         * @var Collection<int, OrderTaxLine> $taxes
         */
        #[DataCollectionOf(OrderTaxLine::class)]
        public readonly Collection $taxes,
        public readonly ?string $sku,
        public readonly string $price,
    ) {}

    public ?string $name;

    #[MapName('product_id')]
    public ?string $productId;

    #[MapName('variation_id')]
    public ?string $variationId;

    public ?string $quantity;

    #[MapName('tax_class')]
    public ?string $taxClass;

    public ?string $subtotal;

    public ?string $total;

    /**
     * @var ?array<string, string>
     */
    #[MapName('meta_data')]
    public ?array $metaData;
}
