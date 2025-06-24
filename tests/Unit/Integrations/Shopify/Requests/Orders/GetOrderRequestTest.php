<?php

declare(strict_types=1);

namespace Tests\Unit\Integrations\Shopify\Requests\Orders;

use App\Http\Integrations\Shopify\DataTransferObjects\Order;
use App\Http\Integrations\Shopify\Requests\Orders\GetOrderRequest;
use App\Http\Integrations\Shopify\ShopifyConnector;
use Saloon\Enums\Method;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;
use Tests\Fixtures\Integrations\Shopify\OrderFixture;
use Tests\TestCase;

class GetOrderRequestTest extends TestCase
{
    public function test_it_gets(): void
    {
        $orderFixture = Order::factory()->from(OrderFixture::create());
        $this->assertSame((new GetOrderRequest($orderFixture))->getMethod(), Method::GET);
    }

    public function test_it_provides_the_order_id_in_the_params(): void
    {
        $orderId = fake()->unique()->randomNumber();

        $orderFixture = Order::factory()->from(OrderFixture::create(['id' => $orderId]));
        $orderObjectRequest = new GetOrderRequest($orderFixture);

        $this->assertSame("/orders/{$orderId}.json", $orderObjectRequest->resolveEndpoint());

        $orderIntRequest = new GetOrderRequest($orderId);
        $this->assertSame("/orders/{$orderId}.json", $orderIntRequest->resolveEndpoint());

        $orderStringRequest = new GetOrderRequest((string) $orderId);
        $this->assertSame("/orders/{$orderId}.json", $orderStringRequest->resolveEndpoint());
    }

    public function test_it_creates_a_dto(): void
    {
        $orderFixture = Order::factory()->from(OrderFixture::create(newOrder: false));

        Saloon::fake([
            GetOrderRequest::class => MockResponse::make(['order' => $orderFixture->toArray()]),
        ]);

        $request = new GetOrderRequest($orderFixture->id);

        $response = app()->make(ShopifyConnector::class)->send($request)->dto();

        $this->assertInstanceOf(Order::class, $response);
        $this->assertSame($orderFixture->toArray(), $response->toArray());
    }
}
