<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ImageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    /**
     * @use HasFactory<ImageFactory>
     */
    use HasFactory;

    protected $fillable = [
        'imageable_id',
        'imageable_type',
        'url',
        'position',
        'shopify_id',
        'woocommerce_id',
    ];
}
