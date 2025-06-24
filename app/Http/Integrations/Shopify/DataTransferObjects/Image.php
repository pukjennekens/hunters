<?php

declare(strict_types=1);

namespace App\Http\Integrations\Shopify\DataTransferObjects;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class Image extends Data
{
    public int $id;

    public int $position;

    public string $src;

    public int $width;

    public int $height;

    #[MapName('created_at')]
    #[WithCast(DateTimeInterfaceCast::class, format: DATE_ATOM)]
    public Carbon $createdAt;

    #[MapName('updated_at')]
    #[WithCast(DateTimeInterfaceCast::class, format: DATE_ATOM)]
    public Carbon $updatedAt;
}
