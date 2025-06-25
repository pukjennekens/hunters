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
use App\Http\Integrations\WooCommerce\Requests\Products\UpdateProductRequest;
use App\Http\Integrations\WooCommerce\Requests\ProductVariations\BatchUpdateProductVariationsRequest;
use App\Http\Integrations\WooCommerce\WooCommerceConnector;
use App\Mappers\WooCommerce\ShopifyProductVariantToWooCommerceProductVariationMapper;
use App\Models\Product;
use App\Models\Variation;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class SyncVariationsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Product $product,
        protected ?ShopifyProduct $shopifyProduct = null,
    ) {
        if (empty($this->product->woocommerce_product_id)) {
            $this->delete();

            return;
        }

        if (! $this->shopifyProduct) {
            $this->shopifyProduct = $this->getShopifyProduct();
        }

        $this->product->load(['variations', 'images']);
    }

    /**
     * Execute the job.
     */
    public function handle(WooCommerceConnector $wooCommerceConnector): void
    {
        $batchUpdateProductVariationRequestBody = new BatchUpdateProductVariationRequestBody;
        $batchUpdateProductVariationRequestBody->productId = (int) $this->product->woocommerce_product_id;

        $creatableVariations = $this->getCreatableVariations();
        $updatableVariations = $this->getUpdatableVariations();
        $deletableVariations = $this->getDeletableVariations();

        $batchUpdateProductVariationRequestBody->create = $this->mapVariationsCollectionToWooCommerceVariationsCollection($creatableVariations);
        $batchUpdateProductVariationRequestBody->update = $this->mapVariationsCollectionToWooCommerceVariationsCollection($updatableVariations);
        $batchUpdateProductVariationRequestBody->delete = $this->mapVariationsCollectionToWooCommerceVariationsCollection($deletableVariations);

        /**
         * @var BatchUpdateProductVariationRequestBody $responseBatchWooCommerceProductVariation
         */
        $responseBatchWooCommerceProductVariation = $wooCommerceConnector->send(new BatchUpdateProductVariationsRequest($batchUpdateProductVariationRequestBody))->dtoOrFail();

        $creatableVariations
            ->each(function (Variation $variation, int $index) use ($responseBatchWooCommerceProductVariation): void {
                $variation->update([
                    'woocommerce_variation_id' => $responseBatchWooCommerceProductVariation->create->get($index)?->id,
                    'woocommerce_variation_synced_at' => $responseBatchWooCommerceProductVariation->create->get($index)?->dateModified,
                ]);
            });

        $updatableVariations
            ->each(function (Variation $variation, int $index) use ($responseBatchWooCommerceProductVariation): void {
                $variation->update([
                    'woocommerce_variation_synced_at' => $responseBatchWooCommerceProductVariation->update->get($index)?->dateModified,
                ]);
            });

        $deletableVariations->each(fn (Variation $variation) => $variation->delete());

        $singleChoiceOptions = $this->shopifyProduct?->options->filter(fn (Option $option): bool => count($option->values) === 1);

        if ($singleChoiceOptions) {
            $wooCommerceProduct = new WooCommerceProduct;
            $wooCommerceProduct->id = (int) $this->product->woocommerce_product_id;

            $wooCommerceProduct->defaultAttributes = $singleChoiceOptions->map(function (Option $option): DefaultAttribute {
                $defaultAttribute = new DefaultAttribute;
                $defaultAttribute->name = $option->name;
                $defaultAttribute->option = Arr::first($option->values);

                return $defaultAttribute->only('name', 'option');
            })->values();

            $wooCommerceConnector->send(new UpdateProductRequest($wooCommerceProduct->only('id', 'defaultAttributes')))->dtoOrFail();
        }
    }

    protected function getShopifyProduct(): ShopifyProduct
    {
        return app(ShopifyConnector::class)
            ->debug()
            ->send(new GetProductRequest((int) $this->product->shopify_product_id))
            ->dtoOrFail();
    }

    /**
     * @return Collection<int, Variation>
     */
    protected function getCreatableVariations(): Collection
    {
        return $this->product->variations
            ->whereNotNull('shopify_variation_id')
            ->whereNull('woocommerce_variation_id');
    }

    /**
     * @return Collection<int, Variation>
     */
    protected function getUpdatableVariations(): Collection
    {
        return $this->product->variations
            ->whereNotNull('shopify_variation_id')
            ->whereNotNull('woocommerce_variation_id');
    }

    /**
     * @return Collection<int, Variation>
     */
    protected function getDeletableVariations(): Collection
    {
        return $this->product->variations
            ->whereNull('shopify_variation_id');
    }

    /**
     * @param  Collection<int, Variation>  $variations
     * @return Collection<int, ProductVariation>
     */
    protected function mapVariationsCollectionToWooCommerceVariationsCollection(Collection $variations): Collection
    {
        return $variations->map(function (Variation $variation): ProductVariation {
            $shopifyVariant = $this->shopifyProduct?->variants->firstWhere('id', $variation->shopify_variation_id);

            if (! $shopifyVariant) {
                throw new Exception('Variant could not be found: mismatch');
            }

            if (! $this->shopifyProduct) {
                throw new Exception('Shopify product not found for the variation: '.$variation->shopify_variation_id);
            }

            return app(ShopifyProductVariantToWooCommerceProductVariationMapper::class)->map($this->shopifyProduct, $shopifyVariant, $variation);
        });
    }
}
