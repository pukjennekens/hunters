<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\Enums;

enum ProductStatusEnum: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case PRIVATE = 'private';
    case PUBLISH = 'publish';
}
