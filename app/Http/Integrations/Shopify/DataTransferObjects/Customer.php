<?php

declare(strict_types=1);

namespace App\Http\Integrations\Shopify\DataTransferObjects;

use App\Http\Integrations\Shopify\Enums\CustomerStateEnum;
use App\Http\Integrations\Shopify\ShopifyConnector;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class Customer extends Data
{
    public function __construct(
        public readonly ?int $id = null,

        public readonly CustomerStateEnum $state = CustomerStateEnum::DISABLED,

        #[MapName('default_address')]
        public ?CustomerAddress $defaultAddress = null,

        #[MapName('last_order_id')]
        public readonly ?int $lastOrderId = null,

        #[MapName('last_order_name')]
        public readonly ?string $lastOrderName = null,

        #[MapName('orders_count')]
        public readonly int $ordersCount = 0,

        #[MapName('updated_at')]
        #[WithCast(DateTimeInterfaceCast::class, format: ShopifyConnector::DATE_FORMAT)]
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: ShopifyConnector::DATE_FORMAT)]
        public readonly ?Carbon $updatedAt = null,

        #[MapName('created_at')]
        #[WithCast(DateTimeInterfaceCast::class, format: ShopifyConnector::DATE_FORMAT)]
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: ShopifyConnector::DATE_FORMAT)]
        public readonly ?Carbon $createdAt = null,
    ) {}

    /**
     * @var Collection<int, CustomerAddress> $addresses
     */
    #[DataCollectionOf(CustomerAddress::class)]
    public Collection $addresses;

    public string $email;

    #[MapName('first_name')]
    public string $fistName;

    #[MapName('last_name')]
    public string $lastName;

    /**
     * @var Collection<int, MetaField> $metaFields
     */
    #[MapName('metafield')]
    #[DataCollectionOf(MetaField::class)]
    public Collection $metaFields;

    public ?string $note = null;

    public ?string $phone;
}
