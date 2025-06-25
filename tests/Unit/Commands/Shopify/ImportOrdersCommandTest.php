<?php

declare(strict_types=1);

namespace Tests\Unit\Commands\Shopify;

use App\Console\Commands\Shopify\ImportOrdersCommand;
use App\Enums\OrderStatusEnum;
use App\Http\Integrations\Shopify\DataTransferObjects\Order as ShopifyOrder;
use App\Http\Integrations\Shopify\Requests\Orders\ListOrdersRequest;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Mockery;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;
use Tests\Fixtures\Integrations\Shopify\OrderFixture;
use Tests\TestCase;

class ImportOrdersCommandTest extends TestCase
{
    public function test_it_does_not_fetch_unprocessable_orders(): void
    {
        Order::factory()
            ->count(4)
            ->sequence(
                ['woocommerce_order_status' => OrderStatusEnum::COMPLETED],
                ['woocommerce_order_status' => OrderStatusEnum::FAILED],
                ['woocommerce_order_status' => OrderStatusEnum::REFUNDED],
                ['woocommerce_order_status' => OrderStatusEnum::TRASH],
            )
            ->create();

        Saloon::fake([
            ListOrdersRequest::class => MockResponse::make(['orders' => []]),
        ]);

        $commandMock = Mockery::mock(ImportOrdersCommand::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $commandMock->getOrders();

        Saloon::assertSent(function (ListOrdersRequest $request): bool {
            $this->assertEmpty($request->query()->get('updated_at_min'));

            return true;
        });
    }

    public function test_it_only_fetches_last_updated_orders(): void
    {
        $latestChange = fake()->dateTime();

        Order::factory()
            ->count(4)
            ->sequence(
                ['shopify_order_updated_at' => $latestChange],
                ['shopify_order_updated_at' => fake()->dateTime(max: $latestChange)],
                ['shopify_order_updated_at' => fake()->dateTime(max: $latestChange)],
                ['shopify_order_updated_at' => fake()->dateTime(max: $latestChange)],
            )
            ->create([
                'woocommerce_order_status' => OrderStatusEnum::PROCESSING,
            ]);

        Saloon::fake([
            ListOrdersRequest::class => MockResponse::make(['orders' => []]),
        ]);

        $commandMock = Mockery::mock(ImportOrdersCommand::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $commandMock->getOrders();

        Saloon::assertSent(function (ListOrdersRequest $request) use ($latestChange): bool {
            $this->assertTrue(Carbon::parse($request->query()->get('updated_at_min'))->equalTo(
                Carbon::parse($latestChange)
            ));

            return true;
        });
    }

    public function test_it_updates_the_orders(): void
    {
        $orderId = fake()->unique()->randomNumber();
        $newUpdatedAt = fake()->dateTime();

        $order = Order::factory()
            ->create([
                'woocommerce_order_status' => OrderStatusEnum::PROCESSING,
                'shopify_order_updated_at' => fake()->dateTime(),
                'shopify_order_id' => $orderId,
            ]);

        Saloon::fake([
            ListOrdersRequest::class => MockResponse::make(['orders' => [
                ShopifyOrder::factory()->from(OrderFixture::create([
                    'id' => $orderId,
                    'updated_at' => $newUpdatedAt,
                ], newOrder: false)),
            ]]),
        ]);

        (new ImportOrdersCommand)->handle();

        $order->refresh();

        $this->assertEquals($newUpdatedAt->getTimestamp(), $order->shopify_order_updated_at);
    }
}
