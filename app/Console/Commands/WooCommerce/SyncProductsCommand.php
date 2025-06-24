<?php

declare(strict_types=1);

namespace App\Console\Commands\WooCommerce;

use App\Jobs\WooCommerce\DeleteProductJob;
use App\Jobs\WooCommerce\SyncProductJob;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SyncProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'woocommerce:sync-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->getProductsToSync()->each(fn (Product $product) => SyncProductJob::dispatch($product));
        $this->getProductsToDelete()->each(fn (Product $product) => DeleteProductJob::dispatch($product));
    }

    /**
     * @return Collection<int, Product>
     */
    protected function getProductsToSync(): Collection
    {
        return Product::query()
            ->whereNotNull('shopify_product_id')
            ->where(function (Builder $query) {
                $query
                    ->whereColumn('woocommerce_product_synced_at', '<', 'shopify_product_updated_at')
                    ->orWhereNull('woocommerce_product_synced_at');
            })
            ->get();
    }

    /**
     * @return Collection<int, Product>
     */
    protected function getProductsToDelete(): Collection
    {
        return Product::query()
            ->whereNull('shopify_product_id')
            ->whereNotNull('woocommerce_product_id')
            ->get();
    }
}
