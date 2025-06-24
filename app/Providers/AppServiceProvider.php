<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Integrations\Shopify\ShopifyConnector;
use App\Http\Integrations\WooCommerce\WooCommerceConnector;
use App\Http\Integrations\WordPress\WordPressConnector;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->singleton(ShopifyConnector::class, function (): ShopifyConnector {
            return new ShopifyConnector(
                config('shopify.store_url'),
                config('shopify.access_token')
            );
        });

        $this->app->singleton(WooCommerceConnector::class, function (): WooCommerceConnector {
            return new WooCommerceConnector(
                config('woocommerce.store_url'),
                config('woocommerce.consumer_key'),
                config('woocommerce.consumer_secret'),
            );
        });

        $this->app->singleton(WordPressConnector::class, function (): WordPressConnector {
            return new WordPressConnector(
                config('wordpress.store_url'),
                config('wordpress.api_user'),
                config('wordpress.application_password')
            );
        });
    }
}
