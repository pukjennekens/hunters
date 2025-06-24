<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce;

use Saloon\Http\Auth\BasicAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class WooCommerceConnector extends Connector
{
    const DATE_FORMAT = 'Y-m-d\TH:i:s';

    use AcceptsJson;

    public function __construct(
        protected string $storeUrl,
        protected string $consumerKey,
        protected string $consumerSecret
    ) {}

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return "{$this->storeUrl}/wp-json/wc/v3";
    }

    protected function defaultAuth(): BasicAuthenticator
    {
        return new BasicAuthenticator(
            $this->consumerKey,
            $this->consumerSecret
        );
    }
}
