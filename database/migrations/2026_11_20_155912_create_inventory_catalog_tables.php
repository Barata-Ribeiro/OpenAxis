<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private bool $isValidSql;

    public function up(): void
    {
        $this->isValidSql = in_array(DB::getDriverName(), ['mysql', 'pgsql'], true);

        Schema::create('categories', function (Blueprint $table): void {
            $table->comment('Hierarchical product categories.');
            $table->id()->comment('Internal primary key.');
            $table->string('name')->comment('Display name of the category.');
            $table->foreignId('parent_id')->nullable()->comment('Parent category. Null identifies a root category.')->constrained('categories')->restrictOnDelete();
            $table->timestamp('created_at')->nullable()->comment('When the record was created.');
            $table->timestamp('updated_at')->nullable()->comment('When the record was last updated.');
            $table->softDeletes()->comment('When the record was archived.');

            $table->index(['parent_id', 'name']);
        });

        Schema::create('unit_of_measures', function (Blueprint $table): void {
            $table->comment('Units for expressing product quantities.');
            $table->id()->comment('Internal primary key.');
            $table->string('code', 16)->unique()->comment('Stable unit code, such as EA, L, or KG.');
            $table->string('name')->comment('Display name of the unit.');
            $table->unsignedTinyInteger('quantity_decimals')->default(0)->comment('Allowed fractional digits, from zero through three.');
            $table->timestamp('created_at')->nullable()->comment('When the record was created.');
            $table->timestamp('updated_at')->nullable()->comment('When the record was last updated.');
            $table->softDeletes()->comment('When the record was archived.');
        });

        Schema::create('storage_locations', function (Blueprint $table): void {
            $table->comment('Physical locations where inventory is stored.');
            $table->id()->comment('Internal primary key.');
            $table->string('code', 48)->unique()->comment('Stable warehouse, room, or bin code.');
            $table->string('name')->comment('Display name of the location.');
            $table->boolean('is_active')->default(true)->comment('Whether the location is available to new inventory operations.');
            $table->timestamp('created_at')->nullable()->comment('When the record was created.');
            $table->timestamp('updated_at')->nullable()->comment('When the record was last updated.');
            $table->softDeletes()->comment('When the record was archived.');
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->comment('Catalog of individually stocked SKUs.');
            $table->id()->comment('Internal primary key.');
            $table->foreignId('unit_of_measure_id')->comment('Base unit used for purchasing, selling, and inventory.')->constrained()->restrictOnDelete();
            $table->foreignId('storage_location_id')->nullable()->comment('Preferred location for new stock. Actual locations are recorded on movements.')->constrained('storage_locations')->restrictOnDelete();
            $table->string('sku', 64)->unique()->comment('Stable stock keeping unit used to generate batch codes.');
            $table->string('name', 180)->comment('Display name of the product.');
            $table->string('slug', 200)->unique()->comment('Unique URL-friendly product identifier.');
            $table->text('description')->comment('Product description. An empty string represents no description.');
            $table->unsignedInteger('cost_price_cents')->default(0)->comment('Current reference purchase cost per base unit, in cents.');
            $table->unsignedInteger('sale_price_cents')->default(0)->comment('Current reference selling price per base unit, in cents.');
            $table->unsignedBigInteger('minimum_stock_milliunits')->default(0)->comment('Reorder threshold in thousandths of a base unit.');
            $table->boolean('is_active')->default(true)->comment('Whether the product is available for new transactions.');
            $table->timestamp('created_at')->nullable()->comment('When the record was created.');
            $table->timestamp('updated_at')->nullable()->comment('When the record was last updated.');
            $table->softDeletes()->comment('When the record was archived.');

            $table->index(['storage_location_id', 'is_active']);
            $table->index('unit_of_measure_id');
            $table->index(['is_active', 'name']);

            if ($this->isValidSql) {
                $table->fullText(['name', 'description'])->language('simple');
            }
        });

        Schema::create('category_product', function (Blueprint $table): void {
            $table->comment('Many-to-many assignment of products to categories.');
            $table->foreignId('category_id')->comment('Assigned product category.')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->comment('Categorized product.')->constrained()->cascadeOnDelete();
            $table->primary(['category_id', 'product_id']);
            $table->index('product_id');
        });

        Schema::create('images', function (Blueprint $table): void {
            $table->comment('Ordered references to image files attached to models.');
            $table->id()->comment('Internal primary key.');
            $table->string('imageable_type')->comment('Polymorphic type of the associated model.');
            $table->unsignedBigInteger('imageable_id')->comment('Primary key of the associated model.');
            $table->string('disk')->comment('Laravel filesystem disk that stores the image.');
            $table->string('path', 1024)->comment('Path to the original image relative to its disk.');
            $table->unsignedInteger('sort_order')->default(0)->comment('Display position among images of the same model.');
            $table->timestamp('created_at')->nullable()->comment('When the record was created.');
            $table->timestamp('updated_at')->nullable()->comment('When the record was last updated.');

            $table->index(['imageable_type', 'imageable_id', 'sort_order']);
        });

        Schema::create('product_batches', function (Blueprint $table): void {
            $table->comment('Internal batches with independent expiry dates for each SKU.');
            $table->id()->comment('Internal primary key.');
            $table->foreignId('product_id')->comment('Product identified by this batch.')->constrained()->restrictOnDelete();
            $table->unsignedInteger('sequence')->comment('Sequential batch number within the product.');
            $table->string('code', 80)->unique()->comment('Generated internal batch code, such as RI435-001.');
            $table->string('supplier_lot_code')->nullable()->comment('Lot reference printed or supplied by the vendor.');
            $table->date('expires_on')->nullable()->comment('Expiry date of the units in this batch, when applicable.');
            $table->timestamp('created_at')->nullable()->comment('When the record was created.');
            $table->timestamp('updated_at')->nullable()->comment('When the record was last updated.');

            $table->unique(['product_id', 'sequence']);
            $table->index('expires_on');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_batches');
        Schema::dropIfExists('images');
        Schema::dropIfExists('category_product');
        Schema::dropIfExists('products');
        Schema::dropIfExists('storage_locations');
        Schema::dropIfExists('unit_of_measures');
        Schema::dropIfExists('categories');
    }
};
