<?php

declare(strict_types=1);

namespace App\Http\Integrations\Shopify\DataTransferObjects;

use App\Http\Integrations\Shopify\Enums\InventoryPolicyEnum;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class Variant extends Data
{
    public int $id;

    #[MapName('image_id')]
    public ?int $imageId;

    #[MapName('inventory_item_id')]
    public int $inventoryItemId;

    #[MapName('inventory_management')]
    public ?string $inventoryManagement;

    #[MapName('inventory_policy')]
    public InventoryPolicyEnum $inventoryPolicy;

    #[MapName('inventory_quantity')]
    public int $inventoryQuantity;

    public ?string $option1;

    public ?string $option2;

    public ?string $option3;

    public string $barcode;

    #[MapName('fulfillment_service')]
    public string $fulfillmentService;

    public string $price;

    #[MapName('compare_at_price')]
    public ?string $compareAtPrice;

    #[MapName('created_at')]
    #[WithCast(DateTimeInterfaceCast::class, format: DATE_ATOM)]
    public Carbon $createdAt;

    #[MapName('updated_at')]
    #[WithCast(DateTimeInterfaceCast::class, format: DATE_ATOM)]
    public Carbon $updatedAt;

    public ?string $sku;
}
