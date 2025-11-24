<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50)->unique()->comment('Stock Keeping Unit; a unique identifier for the product, example: TSBLMA101');
            $table->string('name', 100)->comment('Name of the product');
            $table->text('description')->nullable()->comment('Description of the product');
            $table->string('slug', 100)->unique()->comment('Slug for the product');
            $table->decimal('cost_price', 10, 2)->comment('Cost price of the product');
            $table->decimal('selling_price', 10, 2)->comment('Selling price of the product');
            $table->integer('current_stock')->default(0)->comment('Current stock level of the product');
            $table->integer('minimum_stock')->default(5)->comment('Minimum stock level of the product');
            $table->decimal('comission', 4, 0)->default(0)->comment('Commission percentage for the product');
            $table->boolean('is_active')->default(true);
            $table->foreignId('product_category_id')->constrained('product_categories')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('sku');
            $table->index(['name', 'is_active']);

            if ($this->isValidSql) {
                $table->fullText(['sku', 'name', 'description'], 'products_fulltext_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
