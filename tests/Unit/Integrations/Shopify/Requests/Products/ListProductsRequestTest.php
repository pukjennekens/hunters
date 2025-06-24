<?php

declare(strict_types=1);

namespace Tests\Unit\Integrations\Shopify\Requests\Products;

use App\Http\Integrations\Shopify\DataTransferObjects\Product;
use App\Http\Integrations\Shopify\Requests\Products\ListProductsRequest;
use App\Http\Integrations\Shopify\ShopifyConnector;
use Illuminate\Support\Collection;
use Saloon\Enums\Method;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;
use Tests\Fixtures\Integrations\Shopify\ProductFixture;
use Tests\TestCase;

class ListProductsRequestTest extends TestCase
{
    public function test_it_gets(): void
    {
        $this->assertSame((new ListProductsRequest)->getMethod(), Method::GET);
    }

    public function test_it_creates_a_dto(): void
    {
        Saloon::fake([
            ListProductsRequest::class => MockResponse::make(['products' => [
                Product::factory()->from(ProductFixture::create())->toArray(),
                Product::factory()->from(ProductFixture::create())->toArray(),
                Product::factory()->from(ProductFixture::create())->toArray(),
            ]]),
        ]);

        $response = app()->make(ShopifyConnector::class)->send(new ListProductsRequest)->dto();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertCount(3, $response);
    }
}
