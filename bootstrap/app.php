<?php

declare(strict_types=1);

use App\Console\Commands\Shopify\ImportOrdersCommand as ShopifyImportOrdersCommand;
use App\Console\Commands\Shopify\ImportProductsCommand as ShopifyImportProductsCommand;
use App\Console\Commands\Shopify\SyncOrdersCommand as ShopifySyncOrdersCommand;
use App\Console\Commands\WooCommerce\ImportOrdersCommand as WooCommerceImportOrdersCommand;
use App\Console\Commands\WooCommerce\SyncOrdersCommand as WooCommerceSyncOrdersCommand;
use App\Console\Commands\WooCommerce\SyncProductsCommand as WooCommerceSyncProductsCommand;
use App\Console\Commands\WooCommerce\SyncVariationsCommand as WooCommerceSyncVariationsCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command(ShopifyImportProductsCommand::class)->hourly();
        $schedule->command(WooCommerceSyncProductsCommand::class)->hourlyAt(30);
        $schedule->command(WooCommerceSyncVariationsCommand::class)->hourlyAt(15);

        $schedule->command(WooCommerceImportOrdersCommand::class)->hourly();
        $schedule->command(ShopifySyncOrdersCommand::class)->hourlyAt(30);

        $schedule->command(ShopifyImportOrdersCommand::class)->hourly();
        $schedule->command(WooCommerceSyncOrdersCommand::class)->hourlyAt(30);
    })
    ->create();
