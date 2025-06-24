<?php

declare(strict_types=1);

namespace App\Console\Commands\Shopify;

use App\Jobs\Shopify\ImportProductsJob;
use Illuminate\Console\Command;

class ImportProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopify:import-products {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all products from Shopify';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        ImportProductsJob::dispatch($this->option('force'));
    }
}
