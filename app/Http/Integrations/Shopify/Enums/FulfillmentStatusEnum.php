<?php

declare(strict_types=1);

namespace App\Http\Integrations\Shopify\Enums;

enum FulfillmentStatusEnum: string
{
    case NULL = 'null';
    case PARTIAL = 'partial';
    case RESTOCKED = 'restocked';
    case FULFILLED = 'fulfilled';
    case NOT_ELIGIBLE = 'not_eligible';
}
