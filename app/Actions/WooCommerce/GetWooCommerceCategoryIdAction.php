<?php

declare(strict_types=1);

namespace App\Actions\WooCommerce;

use App\Http\Integrations\WooCommerce\DataTransferObjects\ProductCategory as WooCommerceProductCategory;
use App\Http\Integrations\WooCommerce\Requests\ProductCategories\CreateProductCategoryRequest;
use App\Http\Integrations\WooCommerce\WooCommerceConnector;
use App\Models\ProductCategory;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

class GetWooCommerceCategoryIdAction
{
    /**
     * @throws FatalRequestException
     * @throws RequestException
     */
    public function handle(ProductCategory $productCategory): int
    {
        if (! empty($productCategory->woocommerce_id)) {
            return (int) $productCategory->woocommerce_id;
        }

        $woocommerceProductCategory = new WooCommerceProductCategory;
        $woocommerceProductCategory->name = $productCategory->shopify_handle;

        /**
         * @var WooCommerceProductCategory $responseWooCommerceProductCategory
         */
        $responseWooCommerceProductCategory = app(WooCommerceConnector::class)->send(
            new CreateProductCategoryRequest($woocommerceProductCategory)
        )->dtoOrFail();

        $productCategory->update([
            'woocommerce_id' => $responseWooCommerceProductCategory->id,
        ]);

        // @phpstan-ignore-next-line
        return $responseWooCommerceProductCategory->id;
    }
}
