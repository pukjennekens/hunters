<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Http\Integrations\WooCommerce\DataTransferObjects\Order;
use App\Http\Integrations\WooCommerce\Requests\Orders\ListOrdersRequest;
use App\Http\Integrations\WooCommerce\WooCommerceConnector;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

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
    public function handle(WooCommerceConnector $wooCommerceConnector): void
    {
        // Get the first 10 products
        $product = Product::find(21);

        /**
         * @var Collection<int, Order> $response
         */
        $response = $wooCommerceConnector->debug()->send(new ListOrdersRequest)->dto();
        dd($response->first());
    }
}
