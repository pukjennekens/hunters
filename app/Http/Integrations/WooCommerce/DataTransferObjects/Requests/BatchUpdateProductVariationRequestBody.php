<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\DataTransferObjects\Requests;

use App\Http\Integrations\WooCommerce\DataTransferObjects\ProductVariation;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class BatchUpdateProductVariationRequestBody extends Data
{
    public int $productId;

    /**
     * @var Collection<int, ProductVariation> $create
     */
    #[DataCollectionOf(ProductVariation::class)]
    public Collection $create;

    /**
     * @var Collection<int, ProductVariation> $update
     */
    #[DataCollectionOf(ProductVariation::class)]
    public Collection $update;

    /**
     * @var Collection<int, ProductVariation> $delete
     */
    #[DataCollectionOf(ProductVariation::class)]
    public Collection $delete;
}
