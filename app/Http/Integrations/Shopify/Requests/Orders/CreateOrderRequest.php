<?php

declare(strict_types=1);

namespace App\Http\Integrations\Shopify\Requests\Orders;

use App\Http\Integrations\Shopify\DataTransferObjects\Order;
use JsonException;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class CreateOrderRequest extends Request implements HasBody
{
    use HasJsonBody;

    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::POST;

    public function __construct(
        protected readonly Order $order,
    ) {}

    /**
     * @return array<string, array<string, mixed>>
     */
    protected function defaultBody(): array
    {
        return ['order' => array_filter($this->order->toArray())];
    }

    /**
     * @throws JsonException
     */
    public function createDtoFromResponse(Response $response): Order
    {
        return Order::from($response->json('order'));
    }

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/orders.json';
    }
}
