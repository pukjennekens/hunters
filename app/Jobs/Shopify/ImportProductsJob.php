<?php

declare(strict_types=1);

namespace App\Jobs\Shopify;

use App\Http\Integrations\Shopify\DataTransferObjects\Image;
use App\Http\Integrations\Shopify\DataTransferObjects\Product as ShopifyProduct;
use App\Http\Integrations\Shopify\DataTransferObjects\Variant;
use App\Http\Integrations\Shopify\Requests\Products\ListProductsRequest;
use App\Http\Integrations\Shopify\ShopifyConnector;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

class ImportProductsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected bool $force = false,
        protected ?string $cursor = null,
        protected ?string $syncId = null,
    ) {
        if (! $syncId) {
            $this->syncId = uniqid();
        }
    }

    /**
     * Execute the job.
     *
     * @throws FatalRequestException
     * @throws RequestException
     */
    public function handle(ShopifyConnector $shopifyConnector): void
    {
        $request = new ListProductsRequest;
        $request->query()->add('limit', 250);
        $request->query()->add('published_status', 'published');
        $request->query()->add('status', 'active');

        if (! $this->force) {
            $latestProduct = Product::query()
                ->latest('shopify_product_updated_at')
                ->first();

            if ($latestProduct && $latestProduct->shopify_product_updated_at) {
                $request->query()->add(
                    'updated_at_min',
                    Carbon::createFromTimestamp($latestProduct->shopify_product_updated_at)
                        ->addSecond()
                        ->format(ShopifyConnector::DATE_FORMAT)
                );
            }
        }

        /**
         * @var Collection<int, ShopifyProduct> $shopifyProducts
         */
        $shopifyProducts = $shopifyConnector->send($request)->dto();

        $shopifyProducts->each(function (ShopifyProduct $shopifyProduct): void {
            if (! empty($shopifyProduct->product_type)) {
                $productCategory = ProductCategory::query()
                    ->firstOrCreate([
                        'shopify_handle' => $shopifyProduct->product_type,
                    ]);
            }

            $product = Product::query()
                ->updateOrCreate([
                    'shopify_product_id' => $shopifyProduct->id,
                ], [
                    'product_name' => $shopifyProduct->title,
                    'sync_id' => $this->syncId,
                    'product_category_id' => isset($productCategory) ? $productCategory->id : null,
                    'shopify_product_updated_at' => $shopifyProduct->updatedAt,
                ]);

            $product->variations()
                ->whereNotIn('shopify_variation_id', $shopifyProduct->variants->pluck('id')->toArray())
                ->update([
                    'shopify_variation_id' => null,
                ]);

            $shopifyProduct->variants
                ->each(fn (Variant $shopifyVariant) => $product->variations()->updateOrCreate([
                    'shopify_variation_id' => $shopifyVariant->id,
                ], [
                    'shopify_variation_updated_at' => $shopifyVariant->updatedAt,
                ]));

            $shopifyProduct->images->each(fn (Image $shopifyImage) => $product->images()->updateOrCreate([
                'shopify_id' => $shopifyImage->id,
            ], [
                'url' => $shopifyImage->src,
                'position' => $shopifyImage->position,
            ]));

            $product->images()
                ->whereNotIn('shopify_id', $shopifyProduct->images->pluck('id')->toArray())
                ->update([
                    'shopify_id' => null,
                ]);
        });

        Product::query()
            ->whereNot('sync_id', $this->syncId)
            ->update([
                'shopify_product_id' => null,
            ]);
    }
}
