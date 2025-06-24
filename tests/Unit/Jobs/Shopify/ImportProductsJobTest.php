<?php

namespace Tests\Unit\Jobs\Shopify;

use App\Http\Integrations\Shopify\Requests\Products\ListProductsRequest;
use App\Http\Integrations\Shopify\ShopifyConnector;
use App\Jobs\Shopify\ImportProductsJob;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Variation;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;
use Tests\Fixtures\Integrations\Shopify\ImageFixture;
use Tests\Fixtures\Integrations\Shopify\ProductFixture;
use Tests\Fixtures\Integrations\Shopify\VariantFixture;
use Tests\TestCase;
use App\Http\Integrations\Shopify\DataTransferObjects\Product as ShopifyProduct;

class ImportProductsJobTest extends TestCase
{
    public function testItImportsTheProducts(): void
    {
        $productId = fake()->unique()->randomNumber();
        $productCategory = fake()->word();
        $productTitle = fake()->title();
        $productUpdatedAt = fake()->dateTime();

        $variationId = fake()->unique()->randomNumber();

        $imageId = fake()->unique()->randomNumber();
        $imageSrc = fake()->imageUrl();

        Saloon::fake([
            ListProductsRequest::class => MockResponse::make(['products' => [
                ShopifyProduct::factory()->from(ProductFixture::create([
                    'id' => $productId,
                    'product_type' => $productCategory,
                    'title' => $productTitle,
                    'updated_at' => $productUpdatedAt->format(ShopifyConnector::DATE_FORMAT),

                    'variants' => [VariantFixture::create([
                        'id' => $variationId,
                    ])],

                    'images' => [ImageFixture::create([
                        'id' => $imageId,
                        'src' => $imageSrc,
                    ])],
                ])),
            ]]),
        ]);

        (new ImportProductsJob)->handle(
            app()->make(ShopifyConnector::class)
        );

        $this->assertDatabaseHas('product_categories', [
            'shopify_handle' => $productCategory,
        ]);

        $productCategoryId = ProductCategory::query()
            ->where('shopify_handle', $productCategory)
            ->value('id');

        $this->assertDatabaseHas('products', [
            'shopify_product_id' => $productId,
            'product_name' =>  $productTitle,
            'shopify_product_updated_at' => $productUpdatedAt,
            'product_category_id' => $productCategoryId,
        ]);

        $this->assertDatabaseHas('variations', [
            'shopify_variation_id' => $variationId,
        ]);

        $this->assertDatabaseHas('images', [
            'shopify_id' => $imageId,
            'url' => $imageSrc,
        ]);
    }

    public function testItFetchesOnlyApplicableProducts(): void
    {
        Saloon::fake([
            ListProductsRequest::class => MockResponse::make(['products' => []]),
        ]);

        (new ImportProductsJob)->handle(
            app()->make(ShopifyConnector::class)
        );

        Saloon::assertSent(function(ListProductsRequest $request): bool {
            $this->assertEquals(250, $request->query()->get('limit'));
            $this->assertEquals('published', $request->query()->get('published_status'));
            $this->assertEquals('active', $request->query()->get('status'));

            return true;
        });
    }

    public function testItNullsNonExistingProducts(): void
    {
        $existingProductId = fake()->unique()->randomNumber();

        Saloon::fake([
            ListProductsRequest::class => MockResponse::make(['products' => [
                ShopifyProduct::factory()->from(ProductFixture::create([
                    'id' => $existingProductId,
                ])),
            ]]),
        ]);

        $existingProduct = Product::factory()
            ->create([
                'shopify_product_id' => $existingProductId,
            ]);

        $nonExistingProduct = Product::factory()
            ->create([
                'shopify_product_id' => fake()->unique()->randomNumber(),
            ]);

        (new ImportProductsJob)->handle(
            app()->make(ShopifyConnector::class)
        );

        $existingProduct->refresh();
        $nonExistingProduct->refresh();

        $this->assertNotNull($existingProduct->shopify_product_id);
        $this->assertNull($nonExistingProduct->shopify_product_id);
    }

    public function testItNullsNonExistingVariants(): void
    {
        $productId = fake()->unique()->randomNumber();
        $existingVariantId = fake()->unique()->randomNumber();

        Saloon::fake([
            ListProductsRequest::class => MockResponse::make(['products' => [
                ShopifyProduct::factory()->from(ProductFixture::create([
                    'id' => $productId,

                    'variants' => [
                        VariantFixture::create(['id' => $existingVariantId]),
                    ],
                ])),
            ]]),
        ]);

        $product = Product::factory()
            ->has(Variation::factory()->state([
                'shopify_variation_id' => $existingVariantId,
            ]))
            ->has(Variation::factory())
            ->create([
                'shopify_product_id' => $productId,
            ]);

        (new ImportProductsJob())->handle(
            app()->make(ShopifyConnector::class)
        );

        $product->refresh();

        $this->assertNotNull($product->variations->firstWhere('shopify_variation_id', $existingVariantId));
        $this->assertTrue($product->variations->where('shopify_variation_id', null)->isNotEmpty());
    }
}
