<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\DataTransferObjects;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;

class ShippingLine extends Data
{
    public function __construct(
        public readonly int $id,
        #[MapName('total_tax')]
        public readonly string $totalTax,

        /**
         * @var Collection<int, OrderTaxLine> $taxes
         */
        #[DataCollectionOf(OrderTaxLine::class)]
        public readonly Collection $taxes,
    ) {}

    #[MapName('method_title')]
    public ?string $methodTitle;

    #[MapName('method_id')]
    public ?string $methodId;

    public ?string $total;

    /**
     * @var ?array<string, string>
     */
    #[MapName('meta_data')]
    public ?array $metaData;
}
