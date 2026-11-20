<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table): void {
            $table->comment('Stock ledger entries by physical location and product batch.');
            $table->id()->comment('Internal primary key.');
            $table->foreignId('product_batch_id')->comment('Product batch affected by the movement.')->constrained()->restrictOnDelete();
            $table->foreignId('storage_location_id')->comment('Physical location where the movement occurred.')->constrained()->restrictOnDelete();
            $table->foreignId('order_item_id')->nullable()->comment('Purchase or sale line that caused the movement, when applicable.')->constrained('order_items')->restrictOnDelete();
            $table->foreignId('return_of_id')->nullable()->comment('Original stock movement reversed by a return.')->constrained('stock_movements')->restrictOnDelete();
            $table->foreignId('created_by_id')->nullable()->comment('User who recorded the stock movement.')->constrained('users')->nullOnDelete();
            $table->enum('type', ['opening', 'receipt', 'issue', 'return'])->comment('Opening balance, incoming receipt, outgoing issue, or linked return.');
            $table->bigInteger('quantity_milliunits')->comment('Signed change in thousandths of a base unit. Positive adds stock.');
            $table->unsignedBigInteger('cost_cents')->nullable()->comment('Exact total acquisition cost in cents, required for opening stock.');
            $table->timestamp('occurred_at')->comment('Effective date and time of the movement.');
            $table->text('notes')->nullable()->comment('Reason, external reference, or other stock movement notes.');
            $table->timestamp('created_at')->nullable()->comment('When the movement was recorded.');

            $table->index(['product_batch_id', 'storage_location_id']);
            $table->index(['storage_location_id', 'occurred_at']);
            $table->index('order_item_id');
            $table->index('return_of_id');
            $table->index('created_by_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
