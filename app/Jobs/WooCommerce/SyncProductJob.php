<?php

declare(strict_types=1);

namespace App\Jobs\WooCommerce;

use App\Http\Integrations\Shopify\DataTransferObjects\Product as ShopifyProduct;
use App\Http\Integrations\Shopify\Requests\Products\GetProductRequest;
use App\Http\Integrations\Shopify\ShopifyConnector;
use App\Http\Integrations\WooCommerce\DataTransferObjects\Product as WooCommerceProduct;
use App\Http\Integrations\WooCommerce\Requests\Products\CreateProductRequest;
use App\Http\Integrations\WooCommerce\Requests\Products\UpdateProductRequest;
use App\Http\Integrations\WooCommerce\WooCommerceConnector;
use App\Mappers\WooCommerce\WooCommerceProductFromProductMapper;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
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

        $this->product->update([
            'woocommerce_product_synced_at' => $shopifyProduct->updatedAt,
        ]);

        SyncVariationsJob::dispatch($this->product, $shopifyProduct);
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
