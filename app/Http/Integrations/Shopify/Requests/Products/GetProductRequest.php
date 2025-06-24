<?php

declare(strict_types=1);

namespace App\Http\Integrations\Shopify\Requests\Products;

use App\Http\Integrations\Shopify\DataTransferObjects\Product;
use JsonException;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetProductRequest extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    public function __construct(
        protected int|string|Product $productId,
    ) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        if ($this->productId instanceof Product) {
            $this->productId = $this->productId->id;
        }

        return "/products/{$this->productId}.json";
    }

    /**
     * @throws JsonException
     */
    public function createDtoFromResponse(Response $response): Product
    {
        return Product::from($response->json('product'));
    }
}
