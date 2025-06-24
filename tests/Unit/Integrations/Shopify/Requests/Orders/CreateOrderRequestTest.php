<?php

declare(strict_types=1);

namespace Tests\Unit\Integrations\Shopify\Requests\Orders;

use App\Http\Integrations\Shopify\DataTransferObjects\Order;
use App\Http\Integrations\Shopify\Requests\Orders\CreateOrderRequest;
use App\Http\Integrations\Shopify\ShopifyConnector;
use ReflectionClass;
use Saloon\Enums\Method;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;
use Tests\Fixtures\Integrations\Shopify\OrderFixture;
use Tests\TestCase;

class CreateOrderRequestTest extends TestCase
{
    public function test_it_posts(): void
    {
        $orderFixture = Order::factory()->from(OrderFixture::create());
        $this->assertSame((new CreateOrderRequest($orderFixture))->getMethod(), Method::POST);
    }

    public function test_it_provides_the_order_in_the_body(): void
    {
        $orderFixture = Order::factory()->from(OrderFixture::create());

        Saloon::fake([
            CreateOrderRequest::class => MockResponse::make(['order' => $orderFixture->toArray()]),
        ]);

        $request = new CreateOrderRequest($orderFixture);

        app(ShopifyConnector::class)->send($request);

        Saloon::assertSent(function (CreateOrderRequest $request) use ($orderFixture): bool {
            $requestReflection = new ReflectionClass($request);
            $body = $requestReflection->getMethod('defaultBody')->invoke($request);

            $this->assertSame(
                ['order' => array_filter($orderFixture->toArray())],
                $body
            );

            return true;
        });
    }

    public function test_it_creates_a_dto(): void
    {
        $orderFixture = Order::factory()->from(OrderFixture::create());

        Saloon::fake([
            CreateOrderRequest::class => MockResponse::make(['order' => $orderFixture->toArray()]),
        ]);

        $request = new CreateOrderRequest($orderFixture);

        $response = app(ShopifyConnector::class)->send($request)->dto();

        $this->assertInstanceOf(Order::class, $response);
    }
}
