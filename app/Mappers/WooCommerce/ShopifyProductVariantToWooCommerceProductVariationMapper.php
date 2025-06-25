<?php

declare(strict_types=1);

namespace App\Mappers\WooCommerce;

use App\Http\Integrations\Shopify\DataTransferObjects\Product as ShopifyProduct;
use App\Http\Integrations\Shopify\DataTransferObjects\Variant as ShopifyVariant;
use App\Http\Integrations\WooCommerce\DataTransferObjects\ProductVariation as WooCommerceProductVariation;
use App\Http\Integrations\WooCommerce\DataTransferObjects\ProductVariationAttribute;
use App\Http\Integrations\WooCommerce\Enums\StockStatusEnum;
use App\Models\Variation;

class ShopifyProductVariantToWooCommerceProductVariationMapper
{
    public function map(ShopifyProduct $shopifyProduct, ShopifyVariant $shopifyVariant, ?Variation $variation = null): WooCommerceProductVariation
    {
        $wooCommerceProductVariation = new WooCommerceProductVariation(id: (int) $variation?->woocommerce_variation_id ?: null);

        $wooCommerceProductVariation->manageStock = true;
        $wooCommerceProductVariation->stockQuantity = $shopifyVariant->inventoryQuantity;
        $wooCommerceProductVariation->stockStatus = StockStatusEnum::IN_STOCK;

        $wooCommerceProductVariation->regularPrice = $shopifyVariant->compareAtPrice ?: $shopifyVariant->price;
        $wooCommerceProductVariation->salePrice = $shopifyVariant->price;
        $wooCommerceProductVariation->sku = $shopifyVariant->sku;

        if (! isset($wooCommerceProductVariation->attributes)) {
            $wooCommerceProductVariation->attributes = collect();
        }

        if (! empty($shopifyVariant->option1)) {
            $option1ProductVariationAttribute = new ProductVariationAttribute;
            $option1ProductVariationAttribute->name = $shopifyProduct->options->get(0)?->name;
            $option1ProductVariationAttribute->option = $shopifyVariant->option1;

            $wooCommerceProductVariation->attributes->add($option1ProductVariationAttribute);
        }

        if (! empty($shopifyVariant->option2)) {
            $option2ProductVariationAttribute = new ProductVariationAttribute;
            $option2ProductVariationAttribute->name = $shopifyProduct->options->get(1)?->name;
            $option2ProductVariationAttribute->option = $shopifyVariant->option2;

            $wooCommerceProductVariation->attributes->add($option2ProductVariationAttribute);
        }

        if (! empty($shopifyVariant->option3)) {
            $option3ProductVariationAttribute = new ProductVariationAttribute;
            $option3ProductVariationAttribute->name = $shopifyProduct->options->get(2)?->name;
            $option3ProductVariationAttribute->option = $shopifyVariant->option3;

            $wooCommerceProductVariation->attributes->add($option3ProductVariationAttribute);
        }

        return $wooCommerceProductVariation;
    }
}
