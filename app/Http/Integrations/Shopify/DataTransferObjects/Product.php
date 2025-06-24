<?php

declare(strict_types=1);

namespace App\Http\Integrations\Shopify\DataTransferObjects;

use App\Http\Integrations\Shopify\Enums\ProductStatusEnum;
use App\Http\Integrations\Shopify\Enums\PublishedScopeEnum;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class Product extends Data
{
    public int $id;

    public string $title;

    public string $handle;

    public string $product_type;

    #[MapName('published_scope')]
    public PublishedScopeEnum $publishedScope;

    public ProductStatusEnum $status;

    #[MapName('body_html')]
    public ?string $bodyHtml;

    public ?string $vendor;

    public string $tags;

    /**
     * @var Collection<int, Image>
     */
    #[DataCollectionOf(Image::class)]
    public Collection $images;

    /**
     * @var Collection<int, Option>
     */
    #[DataCollectionOf(Option::class)]
    public Collection $options;

    /**
     * @var Collection<int, Variant>
     */
    #[DataCollectionOf(Variant::class)]
    public Collection $variants;

    #[MapName('created_at')]
    #[WithCast(DateTimeInterfaceCast::class, format: DATE_ATOM)]
    public Carbon $createdAt;

    #[MapName('updated_at')]
    #[WithCast(DateTimeInterfaceCast::class, format: DATE_ATOM)]
    public Carbon $updatedAt;

    #[MapName('published_at')]
    #[WithCast(DateTimeInterfaceCast::class, format: DATE_ATOM)]
    public ?Carbon $publishedAt;
}
