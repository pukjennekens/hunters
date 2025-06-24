<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\DataTransferObjects;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;

class OrderAddress extends Data
{
    #[MapName('first_name')]
    public ?string $firstName;

    #[MapName('last_name')]
    public ?string $lastName;

    public ?string $company;

    #[MapName('address_1')]
    public ?string $address1;

    #[MapName('address_2')]
    public ?string $address2;

    public ?string $city;

    public ?string $state;

    public ?string $postcode;

    public ?string $country;
}
