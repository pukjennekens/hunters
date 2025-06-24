<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\Enums;

enum ProductTypeEnum: string
{
    case SIMPLE = 'simple';
    case GROUPED = 'grouped';
    case VARIABLE = 'variable';
    case EXTERNAL = 'external';
}
