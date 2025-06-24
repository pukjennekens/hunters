<?php

declare(strict_types=1);

namespace Tests\Unit\Integrations\Shopify\Requests\Products;

use App\Http\Integrations\Shopify\DataTransferObjects\Product;
use App\Http\Integrations\Shopify\Requests\Products\GetProductRequest;
use App\Http\Integrations\Shopify\ShopifyConnector;
use Saloon\Enums\Method;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;
use Tests\Fixtures\Integrations\Shopify\ProductFixture;
use Tests\TestCase;

class GetProductRequestTest extends TestCase
{
    public function test_it_gets(): void
    {
        $this->assertSame((new GetProductRequest(fake()->randomNumber()))->getMethod(), Method::GET);
    }

    public function test_it_provides_the_order_id_in_the_params(): void
    {
        $productId = fake()->unique()->randomNumber();

        $productFixture = Product::factory()->from(ProductFixture::create(['id' => $productId]));
        $orderObjectRequest = new GetProductRequest($productFixture);
        $this->assertSame("/products/{$productId}.json", $orderObjectRequest->resolveEndpoint());

        $orderIntRequest = new GetProductRequest($productId);
        $this->assertSame("/products/{$productId}.json", $orderIntRequest->resolveEndpoint());

        $orderStringRequest = new GetProductRequest((string) $productId);
        $this->assertSame("/products/{$productId}.json", $orderStringRequest->resolveEndpoint());
    }

    public function test_it_creates_a_dto(): void
    {
        $productFixture = Product::factory()->from(ProductFixture::create());

        Saloon::fake([
            GetProductRequest::class => MockResponse::make(['product' => $productFixture->toArray()]),
        ]);

        $request = new GetProductRequest($productFixture->id);

        $response = app()->make(ShopifyConnector::class)->send($request)->dto();

        $this->assertInstanceOf(Product::class, $response);
        $this->assertSame($productFixture->toArray(), $response->toArray());
    }
}
