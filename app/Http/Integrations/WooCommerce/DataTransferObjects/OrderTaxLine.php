<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\DataTransferObjects;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;

class OrderTaxLine extends Data
{
    public function __construct(
        public readonly int $id,
        #[MapName('rate_code')]
        public readonly string $rateCode,
        #[MapName('rate_id')]
        public readonly int $rateId,
        public readonly string $label,
        public readonly bool $compound,
        #[MapName('tax_total')]
        public readonly string $taxTotal,
        #[MapName('shipping_tax_total')]
        public readonly string $shippingTaxTotal,
    ) {}

    /**
     * @var ?array<string, string>
     */
    public ?array $metaData;
}
