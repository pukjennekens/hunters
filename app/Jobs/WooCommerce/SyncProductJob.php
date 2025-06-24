<?php

declare(strict_types=1);

namespace App\Jobs\WooCommerce;

use App\Http\Integrations\Shopify\DataTransferObjects\Option;
use App\Http\Integrations\Shopify\DataTransferObjects\Product as ShopifyProduct;
use App\Http\Integrations\Shopify\Requests\Products\GetProductRequest;
use App\Http\Integrations\Shopify\ShopifyConnector;
use App\Http\Integrations\WooCommerce\DataTransferObjects\DefaultAttribute;
use App\Http\Integrations\WooCommerce\DataTransferObjects\Product as WooCommerceProduct;
use App\Http\Integrations\WooCommerce\DataTransferObjects\ProductVariation;
use App\Http\Integrations\WooCommerce\DataTransferObjects\Requests\BatchUpdateProductVariationRequestBody;
use App\Http\Integrations\WooCommerce\Requests\Products\CreateProductRequest;
use App\Http\Integrations\WooCommerce\Requests\Products\UpdateProductRequest;
use App\Http\Integrations\WooCommerce\Requests\ProductVariations\BatchUpdateProductVariationsRequest;
use App\Http\Integrations\WooCommerce\WooCommerceConnector;
use App\Mappers\WooCommerce\ShopifyProductVariantToWooCommerceProductVariationMapper;
use App\Mappers\WooCommerce\WooCommerceProductFromProductMapper;
use App\Models\Image;
use App\Models\Product;
use App\Models\Variation;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

class SyncProductJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Product $product
    ) {}

    /**
     * Execute the job.
     */
    public function handle(WooCommerceConnector $wooCommerceConnector): void
    {
        $shopifyProduct = $this->getShopifyProduct($this->product);
        $wooCommerceProduct = app(WooCommerceProductFromProductMapper::class)->map($this->product, $shopifyProduct);

        if (! $this->product->woocommerce_product_id) {
            /**
             * @var WooCommerceProduct $responseProduct
             */
            $responseProduct = $wooCommerceConnector->send(new CreateProductRequest($wooCommerceProduct))->dtoOrFail();

            $this->product->update([
                'woocommerce_product_id' => $responseProduct->id,
            ]);
        } else {
            /**
             * @var WooCommerceProduct $responseProduct
             */
            $responseProduct = $wooCommerceConnector->send(new UpdateProductRequest($wooCommerceProduct))->dtoOrFail();
        }

        $this->product->images->each(fn (Image $image, int $index) => $image->update([
            'woocommerce_id' => $responseProduct->images->get($index)?->id,
        ]));

        $batchUpdateProductVariationRequestBody = new BatchUpdateProductVariationRequestBody;
        // @phpstan-ignore-next-line assign.propertyType
        $batchUpdateProductVariationRequestBody->productId = $responseProduct->id;

        $batchUpdateProductVariationRequestBody->create = $this->product->variations
            ->whereNotNull('shopify_variation_id')
            ->whereNull('woocommerce_variation_id')
            ->map(function (Variation $variation) use ($shopifyProduct): ProductVariation {
                $shopifyVariant = $shopifyProduct->variants->firstWhere('id', $variation->shopify_variation_id);

                if (! $shopifyVariant) {
                    throw new Exception('Variant could not be found: mismatch');
                }

                return app(ShopifyProductVariantToWooCommerceProductVariationMapper::class)->map($shopifyProduct, $shopifyVariant);
            });

        $batchUpdateProductVariationRequestBody->update = $this->product->variations
            ->whereNotNull('shopify_variation_id')
            ->whereNotNull('woocommerce_variation_id')
            ->map(function (Variation $variation) use ($shopifyProduct): ProductVariation {
                $shopifyVariant = $shopifyProduct->variants->firstWhere('id', $variation->shopify_variation_id);

                if (! $shopifyVariant) {
                    throw new Exception('Variant could not be found: mismatch');
                }

                return app(ShopifyProductVariantToWooCommerceProductVariationMapper::class)->map($shopifyProduct, $shopifyVariant, $variation);
            });

        $batchUpdateProductVariationRequestBody->delete = $this->product->variations
            ->whereNull('shopify_variation_id')
            ->map(function (Variation $variation): ProductVariation {
                $wooCommerceProductVariation = new ProductVariation(id: (int) $variation->woocommerce_variation_id);

                return $wooCommerceProductVariation->only('id');
            });

        /**
         * @var BatchUpdateProductVariationRequestBody $responseBatchWooCommerceProductVariation
         */
        $responseBatchWooCommerceProductVariation = $wooCommerceConnector->send(new BatchUpdateProductVariationsRequest($batchUpdateProductVariationRequestBody))->dtoOrFail();

        // Save the variation id's that came back from create
        $this->product->variations
            ->whereNotNull('shopify_variation_id')
            ->whereNull('woocommerce_variation_id')
            ->each(function (Variation $variation, int $index) use ($responseBatchWooCommerceProductVariation) {
                $variation->update([
                    'woocommerce_variation_id' => $responseBatchWooCommerceProductVariation->create->get($index)?->id,
                    'woocommerce_variation_synced_at' => $responseBatchWooCommerceProductVariation->create->get($index)?->dateModified,
                ]);
            });

        $singleChoiceOptions = $shopifyProduct->options->filter(fn (Option $option): bool => count($option->values) === 1);

        $responseProduct->defaultAttributes = $singleChoiceOptions->map(function (Option $option): DefaultAttribute {
            $defaultAttribute = new DefaultAttribute;
            $defaultAttribute->name = $option->name;
            $defaultAttribute->option = Arr::first($option->values);

            return $defaultAttribute->only('name', 'option');
        })->values();

        $wooCommerceConnector->send(new UpdateProductRequest($responseProduct->only('id', 'defaultAttributes')))->dtoOrFail();

        $this->product->update([
            'woocommerce_product_synced_at' => $shopifyProduct->updatedAt,
        ]);
    }

    /**
     * @throws InvalidArgumentException
     * @throws FatalRequestException
     * @throws RequestException
     */
    protected function getShopifyProduct(Product $product): ShopifyProduct
    {
        if (empty($product->shopify_product_id)) {
            throw new InvalidArgumentException('Product does not have a Shopify product ID.');
        }

        return app(ShopifyConnector::class)->send(
            new GetProductRequest($product->shopify_product_id)
        )->dtoOrFail();
    }
}
