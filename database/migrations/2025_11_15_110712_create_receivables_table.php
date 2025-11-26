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
        Schema::create('receivables', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('description');
            $table->foreignId('client_id')->constrained('partners')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('issue_date')->useCurrent();
            $table->date('due_date');
            $table->date('received_date')->nullable();
            $table->enum('status', ['pending', 'received', 'cancelled'])->default('pending');
            $table->enum('payment_method', ['bank_transfer', 'credit_card', 'cash', 'check']);
            $table->foreignId('bank_account_id')->constrained('bank_accounts')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('sales_order_id')->constrained('sales_orders')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('reference_number', 50)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();

            $table->index(['client_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receivables');
    }
};
