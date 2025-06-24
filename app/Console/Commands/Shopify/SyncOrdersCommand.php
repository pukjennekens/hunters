<?php

declare(strict_types=1);

namespace App\Console\Commands\Shopify;

use App\Jobs\Shopify\SyncOrderJob;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class SyncOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopify:sync-orders';

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
        $this->getOrders()->each(fn (Order $order) => SyncOrderJob::dispatch($order));
    }

    /**
     * @return Collection<int, Order>
     */
    protected function getOrders(): Collection
    {
        return Order::query()
            ->whereNull('shopify_order_id')
            ->orWhereColumn('shopify_order_updated_at', '<', 'woocommerce_order_updated_at')
            ->get();
    }
}
