<?php

namespace App\Console\Commands;

use App\Jobs\Shopify\ImportProductsJob;
use App\Jobs\WooCommerce\SyncProductJob;
use App\Models\Product;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'local:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $product = Product::find(1);

        ImportProductsJob::dispatch();
        // SyncProductJob::dispatch($product);
    }
}
