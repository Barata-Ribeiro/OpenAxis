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
        Schema::create('item_commercial_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commercial_proposal_id')->constrained('commercial_proposals')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal_price', 10, 2);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['commercial_proposal_id', 'product_id'], 'idx_item_cp_prod');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_commercial_proposals');
    }
};
