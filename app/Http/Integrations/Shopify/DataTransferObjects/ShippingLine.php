<?php

declare(strict_types=1);

namespace App\Http\Integrations\Shopify\DataTransferObjects;

use Spatie\LaravelData\Data;

class ShippingLine extends Data
{
    public string $title;

    public string $price;
}
