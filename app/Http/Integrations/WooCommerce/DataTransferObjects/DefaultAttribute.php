<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\DataTransferObjects;

use Spatie\LaravelData\Data;

class DefaultAttribute extends Data
{
    public ?int $id;

    public ?string $name;

    public ?string $option;
}
