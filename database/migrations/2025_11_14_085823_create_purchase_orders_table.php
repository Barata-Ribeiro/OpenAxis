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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique();
            $table->date('order_date')->default(now());
            $table->date('forecast_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'received', 'cancelled'])->default('pending');
            $table->decimal('total_cost', 10, 2)->default(0)->comment('Total cost of the purchase order');
            $table->text('notes')->nullable();
            $table->foreignId('supplier_id')->constrained('partners')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['order_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
