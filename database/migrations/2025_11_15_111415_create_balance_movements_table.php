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
        Schema::create('balance_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained('bank_accounts')->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('type', ['input', 'output', 'transfer']);
            $table->decimal('amount', 10, 2);
            $table->date('movement_date')->default(now());
            $table->string('description');
            $table->string('reference_number', 50)->nullable();
            $table->foreignId('destination_account_id')->nullable()->constrained('bank_accounts')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();

            $table->index(['bank_account_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_movements');
    }
};
