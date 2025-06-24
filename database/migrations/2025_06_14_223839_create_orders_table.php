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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('woocommerce_order_id')->unique();
            $table->string('shopify_order_id')->nullable()->unique();

            $table->string('woocommerce_order_status');

            $table->timestamp('woocommerce_order_updated_at');
            $table->timestamp('woocommerce_order_synced_at')->nullable();
            $table->timestamp('shopify_order_updated_at')->nullable();
            $table->timestamp('shopify_order_synced_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
