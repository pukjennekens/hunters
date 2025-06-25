<?php

declare(strict_types=1);

namespace Tests\Unit\Commands\Shopify;

use App\Console\Commands\Shopify\SyncOrdersCommand;
use App\Jobs\Shopify\SyncOrderJob;
use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Queue;
use Mockery;
use ReflectionClass;
use Tests\TestCase;

class SyncOrdersCommandTest extends TestCase
{
    public function test_it_does_not_fetch_unprocessable_orders(): void
    {
        Order::factory()
            ->create([
                'shopify_order_id' => fake()->unique()->numerify(),
                'shopify_order_updated_at' => now()->addDay(),
                'woocommerce_order_updated_at' => now(),
            ]);

        Order::factory()
            ->create([
                'shopify_order_id' => fake()->unique()->numerify(),
                'shopify_order_updated_at' => now(),
                'woocommerce_order_updated_at' => now()->subDays(2),
            ]);

        $commandMock = Mockery::mock(SyncOrdersCommand::class)->shouldAllowMockingProtectedMethods();
        $commandMock->expects('getOrders')->passthru();

        $result = $commandMock->getOrders();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
    }

    public function test_it_fetches_processable_orders(): void
    {
        Order::factory()
            ->create([
                'shopify_order_id' => null,
                'shopify_order_updated_at' => now(),
                'woocommerce_order_updated_at' => now()->subDays(2),
            ]);

        Order::factory()
            ->create([
                'shopify_order_id' => fake()->unique()->numerify(),
                'shopify_order_updated_at' => now()->subDay(),
                'woocommerce_order_updated_at' => now(),
            ]);

        $commandMock = Mockery::mock(SyncOrdersCommand::class)->shouldAllowMockingProtectedMethods();
        $commandMock->expects('getOrders')->passthru();

        $result = $commandMock->getOrders();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
    }

    public function test_it_dispatches_the_sync_order_job(): void
    {
        Queue::fake();

        $orderMock = Mockery::mock(Order::class);

        $commandMock = Mockery::mock(SyncOrdersCommand::class)->shouldAllowMockingProtectedMethods();

        $commandMock->shouldReceive('getOrders')
            ->once()
            ->andReturn(collect([$orderMock]));

        $commandMock->expects('handle')->passthru();

        $commandMock->handle();

        Queue::assertPushed(SyncOrderJob::class, function (SyncOrderJob $job) use ($orderMock): bool {
            $jobReflection = new ReflectionClass($job);

            $this->assertSame($orderMock, $jobReflection->getProperty('order')->getValue($job));

            return true;
        });
    }
}
