<?php

declare(strict_types=1);

namespace App\Http\Integrations\Shopify\DataTransferObjects;

use App\Http\Integrations\Shopify\Enums\CancelReasonEnum;
use App\Http\Integrations\Shopify\Enums\FulfillmentStatusEnum;
use App\Http\Integrations\Shopify\ShopifyConnector;
use App\Http\Integrations\WooCommerce\Enums\CurrencyEnum;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class Order extends Data
{
    public function __construct(
        public readonly ?int $id = null,

        #[MapName('app_id')]
        public readonly ?int $appId = null,
        #[MapName('browser_ip')]
        public readonly ?string $browserIp = null,
        #[MapName('client_details')]
        public readonly ?ClientDetails $clientDetails = null,

        #[MapName('closed_at')]
        #[WithCast(DateTimeInterfaceCast::class, format: ShopifyConnector::DATE_FORMAT)]
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: ShopifyConnector::DATE_FORMAT)]
        public readonly ?Carbon $closedAt = null,

        #[MapName('confirmation_number')]
        public readonly ?string $confirmationNumber = null,

        public ?bool $confirmed = null,

        #[MapName('updated_at')]
        #[WithCast(DateTimeInterfaceCast::class, format: ShopifyConnector::DATE_FORMAT)]
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: ShopifyConnector::DATE_FORMAT)]
        public readonly ?Carbon $updatedAt = null,

        #[MapName('created_at')]
        #[WithCast(DateTimeInterfaceCast::class, format: ShopifyConnector::DATE_FORMAT)]
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: ShopifyConnector::DATE_FORMAT)]
        public readonly ?Carbon $createdAt = null,

        public readonly ?CurrencyEnum $currency = null,
    ) {}

    #[MapName('billing_address')]
    public CustomerAddress $billingAddress;

    #[MapName('shipping_address')]
    public ?CustomerAddress $shippingAddress;

    #[MapName('buyer_accepts_marketing')]
    public bool $buyerAcceptsMarketing = false;

    #[MapName('cancel_reason')]
    public ?CancelReasonEnum $cancelReason = null;

    #[MapName('cancelled_at')]
    #[WithCast(DateTimeInterfaceCast::class, format: ShopifyConnector::DATE_FORMAT)]
    #[WithTransformer(DateTimeInterfaceTransformer::class, format: ShopifyConnector::DATE_FORMAT)]
    public ?Carbon $cancelledAt;

    #[MapName('fulfillment_status')]
    public ?FulfillmentStatusEnum $fulfillmentStatus;

    /**
     * @var Collection<int, LineItem> $lineItems
     */
    #[MapName('line_items')]
    #[DataCollectionOf(LineItem::class)]
    public Collection $lineItems;

    public ?string $note = null;

    /**
     * @var Collection<int, ShippingLine> $shippingLines
     */
    #[MapName('shipping_lines')]
    #[DataCollectionOf(ShippingLine::class)]
    public Collection $shippingLines;

    #[MapName('total_price')]
    public string $totalPrice;

    public string $email;
}
