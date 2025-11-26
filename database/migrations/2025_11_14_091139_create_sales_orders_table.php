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
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('partners')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('order_number', 20)->unique();
            $table->date('order_date')->default(now());
            $table->date('delivery_date')->nullable();
            $table->enum('status', ['pending', 'delivered', 'canceled'])->default('pending');
            $table->decimal('product_cost', 10, 2)->default(0)->comment('Cost of the products in the sales order');
            $table->decimal('delivery_cost', 10, 2)->default(0)->comment('Cost of delivery for the sales order');
            $table->decimal('discount_cost', 10, 2)->default(0)->comment('Discount applied to the sales order');
            $table->decimal('total_cost', 10, 2)->default(0)->comment('Total cost of the sales order');
            $table->decimal('product_value', 10, 2)->default(0)->comment('Total value of the products in the sales order');
            $table->decimal('total_commission', 10, 2)->default(0)->comment('Total commission for the sales order');
            $table->enum('payment_method', ['cash', 'credit_card', 'debit_card', 'bank_transfer'])->default('cash');
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();

            $table->index(['order_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
