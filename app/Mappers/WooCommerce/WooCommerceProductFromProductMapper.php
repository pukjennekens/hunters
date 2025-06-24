<?php

declare(strict_types=1);

namespace App\Mappers\WooCommerce;

use App\Actions\WooCommerce\GetWooCommerceCategoryIdAction;
use App\Http\Integrations\Shopify\DataTransferObjects\Option;
use App\Http\Integrations\Shopify\DataTransferObjects\Product as ShopifyProduct;
use App\Http\Integrations\Shopify\Enums\ProductStatusEnum as ShopifyProductStatusEnum;
use App\Http\Integrations\Shopify\Requests\Products\GetProductRequest;
use App\Http\Integrations\Shopify\ShopifyConnector;
use App\Http\Integrations\WooCommerce\DataTransferObjects\Image as WooCommerceImage;
use App\Http\Integrations\WooCommerce\DataTransferObjects\Product as WooCommerceProduct;
use App\Http\Integrations\WooCommerce\DataTransferObjects\ProductAttribute;
use App\Http\Integrations\WooCommerce\DataTransferObjects\ProductCategory as WooCommerceProductCategory;
use App\Http\Integrations\WooCommerce\Enums\ProductStatusEnum as WooCommerceProductStatusEnum;
use App\Http\Integrations\WooCommerce\Enums\ProductTypeEnum;
use App\Models\Image;
use App\Models\Product;
use App\Models\ProductCategory;
use InvalidArgumentException;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

class WooCommerceProductFromProductMapper
{
    /**
     * @throws FatalRequestException
     * @throws RequestException
     */
    public function map(Product $product, ?ShopifyProduct $shopifyProduct = null): WooCommerceProduct
    {
        if (! $shopifyProduct) {
            $shopifyProduct = $this->getShopifyProduct($product);
        }

        $wooCommerceProduct = new WooCommerceProduct;

        $wooCommerceProduct->id = (int) $product->woocommerce_product_id ?: null;

        $wooCommerceProduct->name = $shopifyProduct->title;
        $wooCommerceProduct->description = $shopifyProduct->bodyHtml;
        $wooCommerceProduct->type = ProductTypeEnum::VARIABLE;

        if ($shopifyProduct->variants->count() === 1 && $shopifyProduct->options->count() === 1 && count($shopifyProduct->options->first()?->values ?: []) <= 1) {
            $wooCommerceProduct->type = ProductTypeEnum::SIMPLE;

            $wooCommerceProduct->sku = $product->id.'_'.$shopifyProduct->variants->first()?->sku;
            $wooCommerceProduct->regularPrice = $shopifyProduct->variants->first()?->compareAtPrice ?: $shopifyProduct->variants->first()?->price;
            $wooCommerceProduct->salePrice = $shopifyProduct->variants->first()?->price;

            $wooCommerceProduct->manageStock = true;
            $wooCommerceProduct->stockQuantity = $shopifyProduct->variants->first()?->inventoryQuantity;
        }

        $wooCommerceProduct->status = match ($shopifyProduct->status) {
            ShopifyProductStatusEnum::ACTIVE => WooCommerceProductStatusEnum::PUBLISH,
            default => WooCommerceProductStatusEnum::DRAFT
        };

        $wooCommerceProduct->categories = $product->productCategories()
            ->get()
            ->map(function (ProductCategory $productCategory): WooCommerceProductCategory {
                $woocommerceProductCategory = new WooCommerceProductCategory;
                $woocommerceProductCategory->id = app(GetWooCommerceCategoryIdAction::class)->handle($productCategory);

                return $woocommerceProductCategory->only('id');
            });

        $wooCommerceProduct->attributes = $shopifyProduct->options->map(function (Option $option): ProductAttribute {
            $woocommerceProductAttribute = new ProductAttribute;
            $woocommerceProductAttribute->name = $option->name;
            $woocommerceProductAttribute->options = $option->values;
            $woocommerceProductAttribute->position = $option->position;
            $woocommerceProductAttribute->visible = true;
            $woocommerceProductAttribute->variation = true;

            return $woocommerceProductAttribute;
        });

        $brandProductAttribute = new ProductAttribute;
        $brandProductAttribute->name = 'Brand';
        $brandProductAttribute->visible = true;
        $brandProductAttribute->variation = false;
        $brandProductAttribute->options = array_filter([$shopifyProduct->vendor]);

        $wooCommerceProduct->attributes->add($brandProductAttribute);

        $wooCommerceProduct->images = $product->images()
            ->orderBy('position')
            ->get()
            ->map(function (Image $image): WooCommerceImage {
                $wooCommerceImage = new WooCommerceImage;

                if (! empty($image->woocommerce_id)) {
                    $wooCommerceImage->id = (int) $image->woocommerce_id;

                    return $wooCommerceImage->only('id');
                }

                $imageUrl = $image->url;
                $imageUrl = preg_replace('/(\.[^.]+)$/', '_grande$1', $imageUrl);

                $wooCommerceImage->src = $imageUrl;

                return $wooCommerceImage->only('src');
            })
            ->values();

        return $wooCommerceProduct;
    }

    /**
     * @throws InvalidArgumentException
     * @throws FatalRequestException
     * @throws RequestException
     */
    protected function getShopifyProduct(Product $product): ShopifyProduct
    {
        if (empty($product->shopify_product_id)) {
            throw new InvalidArgumentException('Product does not have a Shopify product ID.');
        }

        return app(ShopifyConnector::class)->send(
            new GetProductRequest($product->shopify_product_id)
        )->dtoOrFail();
    }
}
