<?php

declare(strict_types=1);

namespace App\Jobs\Shopify;

use App\Http\Integrations\Shopify\Requests\Orders\CreateOrderRequest;
use App\Http\Integrations\Shopify\ShopifyConnector;
use App\Http\Integrations\WooCommerce\Requests\Orders\GetOrderRequest;
use App\Http\Integrations\WooCommerce\WooCommerceConnector;
use App\Mappers\Shopify\ShopifyOrderFromWooCommerceOrderMapper;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncOrderJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected readonly Order $order,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(WooCommerceConnector $wooCommerceConnector, ShopifyConnector $shopifyConnector): void
    {
        $woocommerceOrder = $wooCommerceConnector->send(new GetOrderRequest($this->order->woocommerce_order_id))->dtoOrFail();
        $shopifyOrder = app(ShopifyOrderFromWooCommerceOrderMapper::class)->map($woocommerceOrder);

        $shopifyConnector->send(new CreateOrderRequest($shopifyOrder));
    }
}
