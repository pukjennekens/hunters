<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'product_name',
        'product_category_id',
        'sync_id',
        'shopify_product_id',
        'woocommerce_product_id',
        'shopify_product_updated_at',
        'woocommerce_product_synced_at',
    ];

    protected $casts = [
        'shopify_product_updated_at' => 'timestamp',
        'woocommerce_product_synced_at' => 'timestamp',
    ];

    /**
     * @return HasMany<Variation, $this>
     */
    public function variations(): HasMany
    {
        return $this->hasMany(Variation::class);
    }

    /**
     * @return MorphMany<Image, $this>
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * @return HasMany<ProductCategory, $this>
     */
    public function productCategories(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'id', 'product_category_id');
    }
}
