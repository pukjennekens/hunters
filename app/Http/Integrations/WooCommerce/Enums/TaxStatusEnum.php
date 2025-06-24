<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\Enums;

enum TaxStatusEnum: string
{
    case TAXABLE = 'taxable';
    case NONE = 'none';
}
