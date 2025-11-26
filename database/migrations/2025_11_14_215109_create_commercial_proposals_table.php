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
        Schema::create('commercial_proposals', function (Blueprint $table) {
            $table->id();
            $table->string('code', length: 20)->unique();
            $table->foreignId('client_id')->constrained('partners')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained(table: 'vendors')->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('proposal_date')->default(now());
            $table->date('valid_until')->nullable();
            $table->enum('status', ['open', 'approved', 'rejected', 'expired'])->default('open');
            $table->decimal('total_value', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();

            $table->index(['proposal_date', 'status']);
            $table->index(['client_id', 'vendor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commercial_proposals');
    }
};
