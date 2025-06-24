<?php

declare(strict_types=1);

namespace App\Http\Integrations\Shopify\Requests\Orders;

use App\Http\Integrations\Shopify\DataTransferObjects\Order;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetOrderRequest extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    public function __construct(
        protected int|string|Order $orderId,
    ) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        if ($this->orderId instanceof Order) {
            $this->orderId = (int) $this->orderId->id;
        }

        return "/orders/{$this->orderId}.json";
    }

    public function createDtoFromResponse(Response $response): Order
    {
        return Order::from($response->json('order'));
    }
}
