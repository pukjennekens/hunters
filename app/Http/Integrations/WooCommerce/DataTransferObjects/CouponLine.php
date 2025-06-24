<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\DataTransferObjects;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;

class CouponLine extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $discount,
        #[MapName('discount_tax')]
        public readonly string $discountTax,
    ) {}

    public ?string $code;

    /**
     * @var ?array<string, string> $metaData
     */
    #[MapName('meta_data')]
    public ?array $metaData;
}
