<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\Requests\ProductCategories;

use App\Http\Integrations\WooCommerce\DataTransferObjects\ProductCategory;
use JsonException;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class CreateProductCategoryRequest extends Request implements HasBody
{
    use HasJsonBody;

    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::POST;

    public function __construct(protected ProductCategory $productCategory) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/products/categories';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return $this->productCategory->toArray();
    }

    /**
     * @throws JsonException
     */
    public function createDtoFromResponse(Response $response): ProductCategory
    {
        return ProductCategory::from($response->json());
    }
}
