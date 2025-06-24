<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\DataTransferObjects;

use App\Http\Integrations\WooCommerce\WooCommerceConnector;
use Illuminate\Support\Carbon;
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

        public readonly ?string $role = null,

        public readonly ?string $username = null,

        public readonly ?string $password = null,

        #[MapName('is_paying_customer')]
        public readonly bool $isPayingCustomer = false,

        #[MapName('avatar_url')]
        public readonly ?string $avatarUrl = null,

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
    ) {}

    public string $email;

    #[MapName('first_name')]
    public ?string $firstName;

    #[MapName('last_name')]
    public ?string $lastName;

    public ?OrderBillingAddress $billing = null;

    public ?OrderShippingAddress $shipping = null;
}
