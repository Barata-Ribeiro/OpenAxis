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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 320)->unique();
            $table->string('phone_number', 20)->nullable();
            $table->string('identification', 50)->unique()->comment('Social Security Number/Employer Identification Number of the client. If Brazilian, follow the CPF format or CNPJ for companies.');
            $table->enum('client_type', ['individual', 'company'])->default('individual')->comment('Type of client: individual or company. In Brazil would be Pessoa Física or Pessoa Jurídica.');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['email', 'client_type']);
            $table->index(['name', 'client_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
