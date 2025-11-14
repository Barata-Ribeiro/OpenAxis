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
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['client', 'supplier', 'both'])->comment('Defines whether the partner is a client, supplier, or both.');
            $table->string('name', 100);
            $table->string('email', 320)->unique();
            $table->string('phone_number', 20)->nullable();
            $table->string('identification', 50)->unique()->comment('Social Security Number/Employer Identification Number of the client. If Brazilian, follow the CPF format or CNPJ for companies.');
            $table->boolean('is_active')->default(true)->comment('Indicates whether the supplier is currently active.');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'is_active']);
            $table->index(['name', 'type']);
            $table->index(['email', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
