<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\Requests\Orders;

use App\Http\Integrations\WooCommerce\DataTransferObjects\Order;
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
        protected readonly int|string $orderId,
    ) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return "/orders/{$this->orderId}";
    }

    public function createDtoFromResponse(Response $response): Order
    {
        return Order::from($response->json());
    }
}
