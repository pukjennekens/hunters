<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\Enums;

enum OrderStatusEnum: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case ON_HOLD = 'on-hold';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case FAILED = 'failed';
    case TRASH = 'trash';
}
