<?php

declare(strict_types=1);

namespace App\Http\Integrations\WordPress;

use Saloon\Http\Auth\BasicAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class WordPressConnector extends Connector
{
    use AcceptsJson;

    public function __construct(
        protected string $storeUrl,
        protected string $apiUser,
        protected string $applicationPassword,
    ) {}

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return "{$this->storeUrl}/wp-json/wp/v2";
    }

    protected function defaultAuth(): BasicAuthenticator
    {
        return new BasicAuthenticator($this->apiUser, $this->applicationPassword);
    }
}
