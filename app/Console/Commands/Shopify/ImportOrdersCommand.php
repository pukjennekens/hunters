<?php

declare(strict_types=1);

namespace App\Console\Commands\Shopify;

use App\Http\Integrations\Shopify\DataTransferObjects\Order as ShopifyOrder;
use App\Http\Integrations\Shopify\Requests\Orders\ListOrdersRequest;
use App\Http\Integrations\Shopify\ShopifyConnector;
use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

class ImportOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopify:import-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @throws FatalRequestException
     * @throws RequestException
     */
    public function handle(): void
    {
        $this->getOrders()->each(fn (ShopifyOrder $shopifyOrder) => Order::query()->where('shopify_order_id', $shopifyOrder->id)->update([
            'shopify_order_updated_at' => $shopifyOrder->updatedAt,
        ]));
    }

    /**
     * @return Collection<int, ShopifyOrder>
     *
     * @throws FatalRequestException
     * @throws RequestException
     */
    protected function getOrders(): Collection
    {
        $request = new ListOrdersRequest;

        $latestOrderDate = Order::query()
            ->whereNotIn('woocommerce_order_status', [OrderStatusEnum::COMPLETED, OrderStatusEnum::FAILED, OrderStatusEnum::REFUNDED, OrderStatusEnum::TRASH])
            ->latest('shopify_order_updated_at')
            ->value('shopify_order_updated_at');

        if ($latestOrderDate) {
            $request->query()->add('updated_at_min', Carbon::createFromTimestamp($latestOrderDate)->format(ShopifyConnector::DATE_FORMAT));
        }

        return app(ShopifyConnector::class)
            ->send($request)
            ->dtoOrFail();
    }
}
