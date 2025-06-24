<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\DataTransferObjects;

class OrderBillingAddress extends OrderAddress
{
    public ?string $email;

    public ?string $phone;
}
