<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\DataTransferObjects;

use App\Http\Integrations\WooCommerce\Enums\CurrencyEnum;
use App\Http\Integrations\WooCommerce\Enums\OrderStatusEnum;
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

class Order extends Data
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $number = null,
        #[MapName('order_key')]
        public readonly ?string $orderKey = null,
        public readonly ?string $version = null,
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

        #[MapName('discount_total')]
        public readonly ?string $discountTotal = null,

        #[MapName('discount_tax')]
        public readonly ?string $discountTax = null,

        #[MapName('shipping_total')]
        public readonly ?string $shippingTotal = null,

        #[MapName('shipping_tax')]
        public readonly ?string $shippingTax = null,

        #[MapName('cart_tax')]
        public readonly ?string $cartTax = null,

        public readonly ?string $total = null,

        #[MapName('total_tax')]
        public readonly ?string $totalTax = null,

        #[MapName('prices_include_tax')]
        public readonly ?bool $pricesIncludeTax = null,
        #[MapName('customer_ip_address')]
        public readonly ?string $customerIpAddress = null,

        #[MapName('customer_user_agent')]
        public readonly ?string $customerUserAgent = null,

        #[MapName('date_paid')]
        #[WithCast(DateTimeInterfaceCast::class, format: WooCommerceConnector::DATE_FORMAT)]
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: WooCommerceConnector::DATE_FORMAT)]
        public readonly ?string $datePaid = null,

        #[MapName('date_paid_gmt')]
        #[WithCast(DateTimeInterfaceCast::class, format: WooCommerceConnector::DATE_FORMAT)]
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: WooCommerceConnector::DATE_FORMAT)]
        public readonly ?string $datePaidGmt = null,

        #[MapName('date_completed')]
        #[WithCast(DateTimeInterfaceCast::class, format: WooCommerceConnector::DATE_FORMAT)]
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: WooCommerceConnector::DATE_FORMAT)]
        public readonly ?string $dateCompleted = null,

        #[MapName('date_completed_gmt')]
        #[WithCast(DateTimeInterfaceCast::class, format: WooCommerceConnector::DATE_FORMAT)]
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: WooCommerceConnector::DATE_FORMAT)]
        public readonly ?string $dateCompletedGmt = null,

        #[MapName('cart_hash')]
        public readonly ?string $cartHash = null,

        /**
         * @var Collection<int, OrderTaxLine> $taxLines
         */
        #[MapName('tax_lines')]
        #[DataCollectionOf(OrderTaxLine::class)]
        public readonly ?Collection $taxLines = null,

        /**
         * @var Collection<int, Refund> $refunds
         */
        #[DataCollectionOf(Refund::class)]
        public readonly ?Collection $refunds = null,
    ) {}

    #[MapName('parent_id')]
    public ?int $parentId;

    #[MapName('created_via')]
    public ?string $createdVia;

    public OrderStatusEnum $status = OrderStatusEnum::PENDING;

    public CurrencyEnum $currency = CurrencyEnum::USD;

    #[MapName('customer_id')]
    public int $customerId = 0;

    #[MapName('customer_note')]
    public string $customerNote;

    #[MapName('billing')]
    public OrderBillingAddress $billingAddress;

    #[MapName('shipping')]
    public OrderShippingAddress $shippingAddress;

    #[MapName('payment_method')]
    public string $paymentMethod;

    #[MapName('payment_method_title')]
    public string $paymentMethodTitle;

    #[MapName('transaction_id')]
    public string $transactionId;

    /**
     * @var array<string, string> $metaData
     */
    #[MapName('meta_data')]
    public array $metaData;

    /**
     * @var Collection<int, LineItem> $lineItems
     */
    #[MapName('line_items')]
    #[DataCollectionOf(LineItem::class)]
    public Collection $lineItems;

    /**
     * @var Collection<int, ShippingLine> $shippingLines;
     */
    #[MapName('shipping_lines')]
    #[DataCollectionOf(ShippingLine::class)]
    public Collection $shippingLines;

    /**
     * @var Collection<int, FeeLine> $feeLines
     */
    #[MapName('fee_lines')]
    #[DataCollectionOf(FeeLine::class)]
    public Collection $feeLines;

    /**
     * @var Collection<int, CouponLine> $couponLines
     */
    #[MapName('coupon_lines')]
    #[DataCollectionOf(CouponLine::class)]
    public Collection $couponLines;

    #[MapName('set_paid')]
    public ?bool $setPaid = null;
}
