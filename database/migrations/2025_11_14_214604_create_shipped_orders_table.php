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
        Schema::create('shipped_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_orders_id')->constrained('sales_orders')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('tracking_number', 50)->unique();
            $table->string('carrier', 50);
            $table->date('shipped_date')->default(now());
            $table->enum('status', ['posted', 'in_transit', 'delivered', 'returned'])->default('posted');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['shipped_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipped_orders');
    }
};
