<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'woocommerce_order_id',
        'shopify_order_id',
        'woocommerce_order_status',
        'woocommerce_order_updated_at',
        'woocommerce_order_synced_at',
        'shopify_order_updated_at',
        'shopify_order_synced_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'woocommerce_order_status' => OrderStatusEnum::class,
        'woocommerce_order_updated_at' => 'timestamp',
        'woocommerce_order_synced_at' => 'timestamp',
        'shopify_order_updated_at' => 'timestamp',
        'shopify_order_synced_at' => 'timestamp',
    ];
}
