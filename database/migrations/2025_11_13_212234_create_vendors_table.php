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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->date('date_of_birth')->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->decimal('commission_rate', 5, 2)->default(0)->comment('Commission rate as a percentage (e.g., 15.00 for 15%)');
            $table->boolean('is_active')->default(true)->comment('Indicates whether the vendor is currently active.');
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['first_name', 'last_name', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
