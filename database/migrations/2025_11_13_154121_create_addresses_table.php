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

        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->morphs('addressable');
            $table->enum('type', ['billing', 'shipping', 'billing_and_shipping', 'other'])->default('other');
            $table->string('label', 100)->nullable()->comment('A label to identify the address, e.g., Home, Office');
            $table->string('street', 150);
            $table->string('number', 20);
            $table->string('complement', 100);
            $table->string('neighborhood', 100);
            $table->string('city', 100);
            $table->string('state', 100);
            $table->string('postal_code', 20);
            $table->string('country', 100)->default('USA');
            $table->boolean('is_primary')->default(false)->comment('Indicates if this is the primary address for the entity');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['city', 'state', 'postal_code']);

            if ($this->isValidSql) {
                $table->fullText(['label', 'street', 'neighborhood', 'city', 'state', 'postal_code', 'country'], 'addresses_fulltext_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
