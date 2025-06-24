<?php

declare(strict_types=1);

namespace App\Http\Integrations\Shopify\Enums;

enum CustomerStateEnum: string
{
    case DISABLED = 'disabled';
    case INVITED = 'invited';
    case ENABLED = 'enabled';
    case DECLINED = 'declined';
}
