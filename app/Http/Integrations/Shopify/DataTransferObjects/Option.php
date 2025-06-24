<?php

declare(strict_types=1);

namespace App\Http\Integrations\Shopify\DataTransferObjects;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;

class Option extends Data
{
    public int $id;

    #[MapName('product_id')]
    public int $productId;

    public string $name;

    public int $position;

    /**
     * @var list<string>
     */
    public array $values;
}
