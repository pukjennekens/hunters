<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\Requests\Products;

use App\Http\Integrations\WooCommerce\DataTransferObjects\Product;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class DeleteProductRequest extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::DELETE;

    public function __construct(protected readonly string|int|Product $product, protected readonly bool $force = false) {}

    protected function defaultQuery(): array
    {
        return [
            'force' => $this->force ? 'true' : 'false',
        ];
    }

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        $productId = is_string($this->product) || is_int($this->product)
            ? (string) $this->product
            : $this->product->id;

        return "/products/{$productId}";
    }
}
