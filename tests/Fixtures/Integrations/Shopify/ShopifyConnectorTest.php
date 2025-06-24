<?php

declare(strict_types=1);

namespace Tests\Fixtures\Integrations\Shopify;

use App\Http\Integrations\Shopify\ShopifyConnector;
use Tests\TestCase;

class ShopifyConnectorTest extends TestCase
{
    public function test_it_resolves_the_base_url(): void
    {
        $storeUrl = fake()->url();

        $connector = new ShopifyConnector($storeUrl, fake()->word());

        $this->assertStringStartsWith(
            $storeUrl,
            $connector->resolveBaseUrl(),
        );
    }

    public function test_it_provides_the_token(): void
    {
        $token = fake()->word();

        $connector = new ShopifyConnector(fake()->url(), $token);

        $this->assertEquals($token, $connector->getAuthenticator()->accessToken);
        $this->assertEquals('X-Shopify-Access-Token', $connector->getAuthenticator()->headerName);
    }
}
