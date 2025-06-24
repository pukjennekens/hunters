<?php

declare(strict_types=1);

namespace App\Http\Integrations\Shopify\DataTransferObjects;

use App\Http\Integrations\Shopify\Enums\FulfillmentStatusEnum;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;

class LineItem extends Data
{
    public function __construct(
        public readonly ?int $id = null,
    ) {}

    #[MapName('fulfillment_status')]
    public ?FulfillmentStatusEnum $fulfillmentStatus = null;

    public string $price;

    #[MapName('product_id')]
    public int $productId;

    public int $quantity;

    #[MapName('variant_id')]
    public ?int $variantId = null;

    #[MapName('variant_title')]
    public ?string $variantTitle = null;

    public ?string $title = null;
}
