<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\Requests\Customers;

use App\Http\Integrations\WooCommerce\DataTransferObjects\Customer;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetCustomerRequest extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    public function __construct(
        protected Customer|string|int $customerId,
    ) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        if ($this->customerId instanceof Customer) {
            $this->customerId = (int) $this->customerId->id;
        }

        return "/customers/{$this->customerId}";
    }

    public function createDtoFromResponse(Response $response): Customer
    {
        return Customer::from($response->json());
    }
}
