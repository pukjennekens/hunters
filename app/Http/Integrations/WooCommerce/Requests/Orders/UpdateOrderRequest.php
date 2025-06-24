<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\Requests\Orders;

use App\Http\Integrations\WooCommerce\DataTransferObjects\Order;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class UpdateOrderRequest extends Request implements HasBody
{
    use HasJsonBody;

    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::PUT;

    public function __construct(
        protected readonly Order $order,
    ) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return "/orders/{$this->order->id}";
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return array_filter($this->order->toArray());
    }

    public function createDtoFromResponse(Response $response): Order
    {
        return Order::from($response->json());
    }
}
