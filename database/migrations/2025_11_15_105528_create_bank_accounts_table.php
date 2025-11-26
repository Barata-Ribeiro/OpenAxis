<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private bool $isValidSql;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->isValidSql = in_array(DB::getDriverName(), ['mysql', 'pgsql'], true);

        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->enum('type', ['cash', 'checking_account', 'savings_account', 'investment_account']);
            $table->string('bank_name', 100);
            $table->string('bank_agency', 20);
            $table->string('bank_account_number', 30);
            $table->string('pix_key')->nullable()->comment('Key for PIX transactions, if applicable.');
            $table->string('destination_name')->nullable()->comment('Name of the account holder for transfers.');
            $table->decimal('initial_balance', 10, 2)->default(0);
            $table->decimal('current_balance', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('name');
            $table->index('bank_account_number');

            if ($this->isValidSql) {
                $table->fullText(['name', 'bank_name', 'bank_agency', 'bank_account_number'], 'bank_accounts_fulltext_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
