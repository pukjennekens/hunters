<?php

declare(strict_types=1);

namespace App\Http\Integrations\Shopify\Enums;

enum CancelReasonEnum: string
{
    case CUSTOMER = 'customer';
    case FRAUD = 'fraud';
    case INVENTORY = 'inventory';
    case DECLINED = 'declined';
    case OTHER = 'other';
}
