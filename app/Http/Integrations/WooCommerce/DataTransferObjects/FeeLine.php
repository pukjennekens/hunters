<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\DataTransferObjects;

use App\Http\Integrations\WooCommerce\Enums\TaxStatusEnum;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;

class FeeLine extends Data
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

    public ?string $name;

    #[MapName('tax_class')]
    public ?string $taxClass;

    #[MapName('tax_status')]
    public ?TaxStatusEnum $taxStatus = null;

    public ?string $total;

    /**
     * @var ?array<string, string>
     */
    #[MapName('meta_data')]
    public ?array $metaData;
}
