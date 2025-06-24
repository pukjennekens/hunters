<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Variation extends Model
{
    /** @use HasFactory<\Database\Factories\VariationFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'shopify_variation_id',
        'woocommerce_variation_id',
        'woocommerce_variation_synced_at',
        'shopify_variation_updated_at',
    ];

    protected $casts = [
        'woocommerce_variation_synced_at' => 'timestamp',
        'shopify_variation_updated_at' => 'timestamp',
    ];

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
