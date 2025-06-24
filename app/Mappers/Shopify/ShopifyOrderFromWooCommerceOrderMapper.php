<?php

declare(strict_types=1);

namespace App\Mappers\Shopify;

use App\Http\Integrations\Shopify\DataTransferObjects\CustomerAddress;
use App\Http\Integrations\Shopify\DataTransferObjects\LineItem as ShopifyLineItem;
use App\Http\Integrations\Shopify\DataTransferObjects\Order as ShopifyOrder;
use App\Http\Integrations\WooCommerce\DataTransferObjects\LineItem as WooCommerceLineItem;
use App\Http\Integrations\WooCommerce\DataTransferObjects\Order as WooCommerceOrder;
use App\Models\Product;
use Exception;

class ShopifyOrderFromWooCommerceOrderMapper
{
    public function map(WooCommerceOrder $wooCommerceOrder): ShopifyOrder
    {
        $shopifyOrder = new ShopifyOrder;

        $shopifyOrder->totalPrice = (string) $wooCommerceOrder->total;

        if ($wooCommerceOrder->billingAddress->email) {
            $shopifyOrder->email = $wooCommerceOrder->billingAddress->email;
        }

        $shopifyBillingAddress = new CustomerAddress;
        $shopifyBillingAddress->firstName = $wooCommerceOrder->billingAddress->firstName;
        $shopifyBillingAddress->lastName = $wooCommerceOrder->billingAddress->lastName;
        $shopifyBillingAddress->company = $wooCommerceOrder->billingAddress->company;
        $shopifyBillingAddress->address1 = $wooCommerceOrder->billingAddress->address1;
        $shopifyBillingAddress->address2 = $wooCommerceOrder->billingAddress->address2;
        $shopifyBillingAddress->city = $wooCommerceOrder->billingAddress->city;
        $shopifyBillingAddress->province = $wooCommerceOrder->billingAddress->state;
        $shopifyBillingAddress->country = $wooCommerceOrder->billingAddress->country;
        $shopifyBillingAddress->zip = $wooCommerceOrder->billingAddress->postcode;
        $shopifyBillingAddress->phone = $wooCommerceOrder->billingAddress->phone;

        $shopifyOrder->billingAddress = $shopifyBillingAddress;

        $shopifyShippingAddress = new CustomerAddress;
        $shopifyShippingAddress->firstName = $wooCommerceOrder->shippingAddress->firstName;
        $shopifyShippingAddress->lastName = $wooCommerceOrder->shippingAddress->lastName;
        $shopifyShippingAddress->company = $wooCommerceOrder->shippingAddress->company;
        $shopifyShippingAddress->address1 = $wooCommerceOrder->shippingAddress->address1;
        $shopifyShippingAddress->address2 = $wooCommerceOrder->shippingAddress->address2;
        $shopifyShippingAddress->city = $wooCommerceOrder->shippingAddress->city;
        $shopifyShippingAddress->province = $wooCommerceOrder->shippingAddress->state;
        $shopifyShippingAddress->country = $wooCommerceOrder->shippingAddress->country;
        $shopifyShippingAddress->zip = $wooCommerceOrder->shippingAddress->postcode;
        $shopifyOrder->shippingAddress = $shopifyShippingAddress;

        $shopifyOrder->lineItems = $wooCommerceOrder->lineItems->map(function (WooCommerceLineItem $wooCommerceLineItem): ShopifyLineItem {
            $shopifyLineItem = new ShopifyLineItem;

            $product = Product::query()
                ->where('woocommerce_product_id', $wooCommerceLineItem->productId)
                ->with('variations')
                ->first();

            if (! $product) {
                throw new Exception('Product not found');
            }

            $shopifyLineItem->productId = (int) $product->shopify_product_id;
            $shopifyLineItem->quantity = (int) $wooCommerceLineItem->quantity;
            $shopifyLineItem->price = (string) $wooCommerceLineItem->total;

            if (! empty($wooCommerceLineItem->variationId)) {
                $variation = $product->variations->where('woocommerce_variation_id', $wooCommerceLineItem->variationId)->first();

                if (! $variation) {
                    throw new Exception('Variation not found for product ID: '.$product->id);
                }

                $shopifyLineItem->variantId = (int) $variation->shopify_variation_id;
            }

            return $shopifyLineItem;
        });

        return $shopifyOrder;
    }
}
