<?php

declare(strict_types=1);

namespace App\Jobs\WooCommerce;

use App\Enums\OrderStatusEnum;
use App\Http\Integrations\Shopify\DataTransferObjects\Order as ShopifyOrder;
use App\Http\Integrations\Shopify\Enums\FulfillmentStatusEnum;
use App\Http\Integrations\Shopify\Requests\Orders\GetOrderRequest;
use App\Http\Integrations\Shopify\ShopifyConnector;
use App\Http\Integrations\WooCommerce\DataTransferObjects\Order as WooCommerceOrder;
use App\Http\Integrations\WooCommerce\Enums\OrderStatusEnum as WooCommerceOrderStatusEnum;
use App\Http\Integrations\WooCommerce\Requests\Orders\UpdateOrderRequest;
use App\Http\Integrations\WooCommerce\WooCommerceConnector;
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
        protected Order $order
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ShopifyConnector $shopifyConnector, WooCommerceConnector $wooCommerceConnector): void
    {
        /**
         * @var ShopifyOrder $shopifyOrder
         */
        $shopifyOrder = $shopifyConnector->send(new GetOrderRequest((int) $this->order->shopify_order_id))->dtoOrFail();

        $orderStatus = match ($shopifyOrder->fulfillmentStatus) {
            FulfillmentStatusEnum::NULL, FulfillmentStatusEnum::PARTIAL => OrderStatusEnum::PROCESSING,
            FulfillmentStatusEnum::RESTOCKED => OrderStatusEnum::REFUNDED,
            FulfillmentStatusEnum::FULFILLED => OrderStatusEnum::COMPLETED,
            FulfillmentStatusEnum::NOT_ELIGIBLE => OrderStatusEnum::FAILED,
        };

        if (! empty($shopifyOrder->cancelledAt)) {
            $orderStatus = OrderStatusEnum::CANCELLED;
        }

        if ($orderStatus !== $this->order->woocommerce_order_status) {
            $wooCommerceOrder = new WooCommerceOrder(id: (int) $this->order->woocommerce_order_id);
            $wooCommerceOrder->status = WooCommerceOrderStatusEnum::tryFrom($orderStatus->value);

            /**
             * @var WooCommerceOrder $responseWooCommerceOrder
             */
            $responseWooCommerceOrder = $wooCommerceConnector->send(new UpdateOrderRequest($wooCommerceOrder))->dtoOrFail();

            $this->order->update([
                'woocommerce_order_status' => OrderStatusEnum::tryFrom($responseWooCommerceOrder->status->value),
                'woocommerce_order_updated_at' => $responseWooCommerceOrder->dateModified,
                'woocommerce_order_synced_at' => $responseWooCommerceOrder->dateModified,
            ]);
        }
    }
}
