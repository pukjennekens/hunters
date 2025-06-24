<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\DataTransferObjects;

use Spatie\LaravelData\Data;

class ProductCategory extends Data
{
    public ?int $id;

    public string $name;

    public ?string $slug;

    public ?int $parent;

    public string $description;
}
