<?php

namespace Tests\Unit\Jobs\Shopify;

use App\Http\Integrations\WooCommerce\Requests\Orders\GetOrderRequest;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;
use Tests\TestCase;

class SyncOrderJobTest extends TestCase
{
    public function testItGetsTheOrder(): void
    {
        Saloon::fake([
            GetOrderRequest::class => MockResponse::make([]),
        ]);
    }
}
