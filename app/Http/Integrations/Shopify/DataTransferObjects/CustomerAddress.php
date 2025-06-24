<?php

declare(strict_types=1);

namespace App\Http\Integrations\Shopify\DataTransferObjects;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;

class CustomerAddress extends Data
{
    public ?string $address1;

    public ?string $address2;

    public ?string $city;

    public ?string $company;

    public ?string $country;

    #[MapName('country_code')]
    public ?string $countryCode;

    #[MapName('first_name')]
    public ?string $firstName;

    #[MapName('last_name')]
    public ?string $lastName;

    public ?string $latitude;

    public ?string $longitude;

    public ?string $name;

    public ?string $phone;

    public ?string $province;

    #[MapName('province_code')]
    public ?string $provinceCode;

    public ?string $zip;
}
