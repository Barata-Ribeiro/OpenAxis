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
        Schema::create('payment_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('Unique code representing the payment condition.');
            $table->string('name', 100)->comment('Name of the payment condition.');
            $table->integer('days_until_due')->default(0)->comment('Number of days until the payment is due.');
            $table->integer('installments')->comment('Number of installments for the payment condition.');
            $table->boolean('is_active')->default(true)->comment('Indicates whether the payment condition is currently active.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_conditions');
    }
};
