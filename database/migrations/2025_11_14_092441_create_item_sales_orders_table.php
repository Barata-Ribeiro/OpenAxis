<?php

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
        Schema::create('item_sales_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained('sales_orders')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('subtotal_price', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['sales_order_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_sales_orders');
    }
};
