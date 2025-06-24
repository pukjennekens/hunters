<?php

declare(strict_types=1);

namespace App\Http\Integrations\Shopify\Requests\Products;

use App\Http\Integrations\Shopify\DataTransferObjects\Product;
use Illuminate\Support\Collection;
use JsonException;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class ListProductsRequest extends Request implements Paginatable
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/products.json';
    }

    /**
     * @return Collection<int, Product>
     *
     * @throws JsonException
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        return Product::collect($response->json('products', []), Collection::class);
    }
}
