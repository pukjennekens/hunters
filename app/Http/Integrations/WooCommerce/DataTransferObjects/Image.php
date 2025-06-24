<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\DataTransferObjects;

use Spatie\LaravelData\Data;

class Image extends Data
{
    public ?int $id;

    public ?string $src;

    public ?string $name;

    public ?string $alt;
}
