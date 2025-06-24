<?php

declare(strict_types=1);

namespace App\Http\Integrations\WooCommerce\Enums;

enum StockStatusEnum: string
{
    case IN_STOCK = 'instock';
    case OUT_OF_STOCK = 'outofstock';
    case ON_BACKORDER = 'onbackorder';
}
