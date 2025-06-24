<?php

declare(strict_types=1);

namespace App\Http\Integrations\Shopify\Enums;

enum InventoryPolicyEnum: string
{
    case CONTINUE = 'continue';
    case DENY = 'deny';
}
