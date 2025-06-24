<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\Enums;

enum CatalogVisibilityEnum: string
{
    case VISIBLE = 'visible';
    case CATALOG = 'catalog';
    case SEARCH = 'search';
    case HIDDEN = 'hidden';
}
