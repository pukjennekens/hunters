<?php

declare(strict_types=1);

namespace App\Console\Commands\WooCommerce;

use App\Jobs\WooCommerce\SyncVariationsJob;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SyncVariationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'woocommerce:sync-variations';

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
        $this->getVariations()->each(fn (Product $variation) => SyncVariationsJob::dispatch($variation));
    }

    /**
     * @return Collection<int, Product>
     */
    protected function getVariations(): Collection
    {
        return Product::query()
            ->whereHas('variations', fn (Builder $query): Builder => $query
                ->whereNotNull('shopify_variation_id')
                ->whereNull('woocommerce_variation_id')
            )
            ->whereHas('variations', fn (Builder $query): Builder => $query
                ->whereColumn('woocommerce_variation_synced_at', '<', 'shopify_variation_updated_at')
            )
            ->orWhereHas('variations', fn (Builder $query): Builder => $query
                ->whereNotNull('woocommerce_variation_id')
                ->whereNull('shopify_variation_id')
            )
            ->get();
    }
}
