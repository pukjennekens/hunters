<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\Requests\Products;

use App\Http\Integrations\WooCommerce\DataTransferObjects\Product;
use JsonException;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class CreateProductRequest extends Request implements HasBody
{
    use HasJsonBody;

    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::POST;

    public function __construct(protected readonly Product $product) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/products';
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultBody(): array
    {
        return $this->product->toArray();
    }

    /**
     * @throws JsonException
     */
    public function createDtoFromResponse(Response $response): Product
    {
        return Product::from($response->json());
    }
}
