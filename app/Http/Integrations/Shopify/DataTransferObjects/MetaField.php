<?php

declare(strict_types=1);

namespace App\Http\Integrations\Shopify\DataTransferObjects;

use Spatie\LaravelData\Data;

class MetaField extends Data
{
    public string $key;

    public string $namespace;

    public string $value;

    public string $type;

    public string $description;
}
