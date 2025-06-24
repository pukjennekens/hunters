<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\DataTransferObjects;

use Spatie\LaravelData\Data;

class ProductAttribute extends Data
{
    // public ?int $id;

    public ?string $name;

    public ?int $position;

    public ?bool $visible;

    public ?bool $variation;

    /**
     * @var ?list<string>
     */
    public ?array $options;
}
