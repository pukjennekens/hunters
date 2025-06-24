<?php

declare(strict_types=1);

namespace Tests\Unit\Integrations\Shopify\Requests\Orders;

use App\Http\Integrations\Shopify\DataTransferObjects\Order;
use App\Http\Integrations\Shopify\Requests\Orders\ListOrdersRequest;
use App\Http\Integrations\Shopify\ShopifyConnector;
use Illuminate\Support\Collection;
use Saloon\Enums\Method;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;
use Tests\Fixtures\Integrations\Shopify\OrderFixture;
use Tests\TestCase;

class ListOrdersRequestTest extends TestCase
{
    public function test_it_gets(): void
    {
        $this->assertSame((new ListOrdersRequest)->getMethod(), Method::GET);
    }

    public function test_it_creates_a_dto(): void
    {
        Saloon::fake([
            ListOrdersRequest::class => MockResponse::make(['orders' => [
                Order::factory()->from(OrderFixture::create())->toArray(),
                Order::factory()->from(OrderFixture::create())->toArray(),
                Order::factory()->from(OrderFixture::create())->toArray(),
            ]]),
        ]);

        $response = app()->make(ShopifyConnector::class)->send(new ListOrdersRequest)->dto();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertCount(3, $response);
    }
}
