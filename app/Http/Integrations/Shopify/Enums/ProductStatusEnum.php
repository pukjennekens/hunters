<?php

declare(strict_types=1);

namespace App\Http\Integrations\Shopify\Enums;

enum ProductStatusEnum: string
{
    case ACTIVE = 'active';
    case ARCHIVED = 'archived';
    case DRAFT = 'draft';
}
