<?php

declare(strict_types=1);

namespace App\Http\Integrations\Shopify\Requests\Orders;

use App\Http\Integrations\Shopify\DataTransferObjects\Order;
use Illuminate\Support\Collection;
use JsonException;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class ListOrdersRequest extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/orders.json';
    }

    /**
     * @return Collection<int, Order>
     *
     * @throws JsonException
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        return Order::collect($response->json('orders', []), Collection::class);
    }
}
