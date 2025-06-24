<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\Requests\ProductVariations;

use App\Http\Integrations\WooCommerce\DataTransferObjects\Product;
use App\Http\Integrations\WooCommerce\DataTransferObjects\ProductVariation;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class CreateProductVariationRequest extends Request implements HasBody
{
    use HasJsonBody;

    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::POST;

    public function __construct(
        protected readonly Product|int $product,
        protected readonly ProductVariation $productVariation,
    ) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        $productId = is_int($this->product) ? $this->product : $this->product->id;

        return "/products/{$productId}/variations";
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return $this->productVariation->toArray();
    }
}
