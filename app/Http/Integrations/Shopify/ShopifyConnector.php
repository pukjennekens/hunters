<?php

declare(strict_types=1);

namespace App\Http\Integrations\Shopify;

use Saloon\Http\Auth\HeaderAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\HasPagination;
use Saloon\PaginationPlugin\CursorPaginator;
use Saloon\Traits\Plugins\AcceptsJson;

class ShopifyConnector extends Connector implements HasPagination
{
    const DATE_FORMAT = 'Y-m-d\TH:i:sP';

    use AcceptsJson;

    public function __construct(
        protected readonly string $storeUrl,
        protected readonly string $accessToken,
    ) {}

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return "{$this->storeUrl}/admin/api/2025-07";
    }

    protected function defaultAuth(): HeaderAuthenticator
    {
        return new HeaderAuthenticator($this->accessToken, 'X-Shopify-Access-Token');
    }

    public function paginate(Request $request): CursorPaginator
    {
        return new class(connector: $this, request: $request) extends CursorPaginator
        {
            protected function getNextCursor(Response $response): int|string
            {
                $cursors = $this->getCursors($response);

                if (empty($cursors['next'])) {
                    return 0;
                }

                return $cursors['next'];
            }

            protected function isLastPage(Response $response): bool
            {
                return ! empty($this->getCursors($response)['next']);
            }

            protected function getPageItems(Response $response, Request $request): array
            {
                return $response->json('items', []);
            }

            /**
             * @return array<'next'|'previous', string>
             */
            protected function getCursors(Response $response): array
            {
                $links = [];
                $header = $response->header('Link');

                if (! $header || ! is_string($header)) {
                    return $links;
                }

                $parts = explode(',', $header);

                foreach ($parts as $part) {
                    $part = trim($part);
                    if (preg_match('/<([^>]+)>;\s*rel="([^"]+)"/', $part, $matches)) {
                        $rel = $matches[2];

                        if (! in_array($rel, ['next', 'previous'])) {
                            return $links;
                        }

                        $query = parse_url($matches[1], PHP_URL_QUERY);

                        if (! $query) {
                            return $links;
                        }

                        parse_str($query, $queryParams);

                        if (isset($queryParams['page_info']) && is_string($queryParams['page_info'])) {
                            $links[$rel] = $queryParams['page_info'];
                        }
                    }
                }

                return $links;
            }
        };
    }
}
