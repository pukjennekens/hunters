<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('variations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->references('id')->on('products')->cascadeOnDelete();

            $table->string('shopify_variation_id')->unique()->nullable();
            $table->string('woocommerce_variation_id')->unique()->nullable();

            $table->timestamp('woocommerce_variation_synced_at')->nullable();
            $table->timestamp('shopify_variation_updated_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variations');
    }
};
