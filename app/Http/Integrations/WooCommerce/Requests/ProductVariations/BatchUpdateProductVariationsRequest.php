<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\Requests\ProductVariations;

use App\Http\Integrations\WooCommerce\DataTransferObjects\Requests\BatchUpdateProductVariationRequestBody;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class BatchUpdateProductVariationsRequest extends Request implements HasBody
{
    use HasJsonBody;

    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::POST;

    public function __construct(protected readonly BatchUpdateProductVariationRequestBody $batchUpdateProductVariationRequestBody) {}

    /**
     * @return array<string, array<string, mixed>>
     */
    protected function defaultBody(): array
    {
        return [
            'create' => array_filter($this->batchUpdateProductVariationRequestBody->create->except('id', 'product_id')->toArray()),
            'update' => array_filter($this->batchUpdateProductVariationRequestBody->update->except('product_id')->toArray()),
            'delete' => array_filter($this->batchUpdateProductVariationRequestBody->delete->only('id')->toArray()),
        ];
    }

    public function createDtoFromResponse(Response $response): BatchUpdateProductVariationRequestBody
    {
        return BatchUpdateProductVariationRequestBody::from($response->json());
    }

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return "/products/{$this->batchUpdateProductVariationRequestBody->productId}/variations/batch";
    }
}
