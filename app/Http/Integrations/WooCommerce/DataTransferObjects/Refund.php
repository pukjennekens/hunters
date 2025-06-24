<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\DataTransferObjects;

use Spatie\LaravelData\Data;

class Refund extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $reason,
        public readonly string $total,
    ) {}
}
