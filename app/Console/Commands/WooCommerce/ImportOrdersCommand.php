<?php

declare(strict_types=1);

namespace App\Console\Commands\WooCommerce;

use App\Enums\OrderStatusEnum;
use App\Http\Integrations\WooCommerce\DataTransferObjects\Order as WooCommerceOrder;
use App\Http\Integrations\WooCommerce\Requests\Orders\ListOrdersRequest;
use App\Http\Integrations\WooCommerce\WooCommerceConnector;
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
    protected $signature = 'woocommerce:import-orders';

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
        $this->getOrders()->each(fn (WooCommerceOrder $woocommerceOrder) => Order::query()->updateOrCreate([
            'woocommerce_order_id' => $woocommerceOrder->id,
        ], [
            'woocommerce_order_status' => OrderStatusEnum::from($woocommerceOrder->status->value),
            'woocommerce_order_updated_at' => $woocommerceOrder->dateModified,
        ]));
    }

    /**
     * @return Collection<int, WooCommerceOrder>
     *
     * @throws FatalRequestException
     * @throws RequestException
     */
    public function getOrders(): Collection
    {
        $request = new ListOrdersRequest;

        $latestOrderDate = Order::query()
            ->latest('woocommerce_order_updated_at')
            ->value('woocommerce_order_updated_at');

        if (! empty($latestOrderDate)) {
            $request->query()->add('modified_after', Carbon::parse($latestOrderDate)->format(WooCommerceConnector::DATE_FORMAT));
        }

        return app(WooCommerceConnector::class)
            ->send($request)
            ->dtoOrFail();
    }
}
