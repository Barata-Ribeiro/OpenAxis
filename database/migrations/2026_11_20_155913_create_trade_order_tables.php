<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table): void {
            $table->comment('Contact records for customers.');
            $table->id()->comment('Internal primary key.');
            $table->string('name')->comment('Display or legal name.');
            $table->string('phone', 40)->nullable()->comment('Contact phone number.');
            $table->string('email')->nullable()->comment('Contact email address.');
            $table->timestamp('created_at')->nullable()->comment('When the record was created.');
            $table->timestamp('updated_at')->nullable()->comment('When the record was last updated.');
            $table->softDeletes()->comment('When the record was archived.');

            $table->index('name');
        });

        Schema::create('suppliers', function (Blueprint $table): void {
            $table->comment('Contact records for suppliers.');
            $table->id()->comment('Internal primary key.');
            $table->string('name')->comment('Display or legal name.');
            $table->string('phone', 40)->nullable()->comment('Contact phone number.');
            $table->string('email')->nullable()->comment('Contact email address.');
            $table->timestamp('created_at')->nullable()->comment('When the record was created.');
            $table->timestamp('updated_at')->nullable()->comment('When the record was last updated.');
            $table->softDeletes()->comment('When the record was archived.');

            $table->index('name');
        });

        Schema::create('orders', function (Blueprint $table): void {
            $table->comment('Purchase and sale documents with a shared reference namespace.');
            $table->id()->comment('Internal primary key.');
            $table->ulid('number')->unique()->comment('Generated public reference, displayed with a leading number sign.');
            $table->enum('type', ['purchase', 'sale'])->comment('Direction of the trade order.');
            $table->enum('status', ['draft', 'confirmed', 'completed', 'cancelled'])->default('draft')->comment('Current operational state of the order.');
            $table->foreignId('customer_id')->nullable()->comment('Customer associated with a sale, when identified.')->constrained('customers')->restrictOnDelete();
            $table->foreignId('supplier_id')->nullable()->comment('Supplier associated with a purchase.')->constrained('suppliers')->restrictOnDelete();
            $table->foreignId('created_by_id')->nullable()->comment('User who recorded the order.')->constrained('users')->nullOnDelete();
            $table->timestamp('ordered_at')->comment('Effective date and time of the order.');
            $table->string('external_reference')->nullable()->comment('Supplier invoice or other external document reference.');
            $table->enum('delivery_fee_type', ['fixed', 'percentage'])->nullable()->comment('Delivery fee calculation type, when a fee applies.');
            $table->unsignedInteger('delivery_fee_rate')->default(0)->comment('Delivery fee rate in cents when fixed or basis points when percentage.');
            $table->text('notes')->nullable()->comment('Optional operational notes.');
            $table->timestamp('created_at')->nullable()->comment('When the record was created.');
            $table->timestamp('updated_at')->nullable()->comment('When the record was last updated.');

            $table->index(['type', 'status', 'ordered_at']);
            $table->index(['customer_id', 'ordered_at']);
            $table->index(['supplier_id', 'ordered_at']);
            $table->index('created_by_id');
        });

        Schema::create('order_items', function (Blueprint $table): void {
            $table->comment('Purchased or sold products and their historical price and identity.');
            $table->id()->comment('Internal primary key.');
            $table->foreignId('order_id')->comment('Order containing this item.')->constrained()->restrictOnDelete();
            $table->foreignId('product_id')->comment('Product purchased or sold.')->constrained()->restrictOnDelete();
            $table->string('product_sku', 64)->comment('Product SKU at the time this item was recorded.');
            $table->string('product_name', 180)->comment('Product name at the time this item was recorded.');
            $table->string('unit_code', 16)->comment('Base unit code at the time this item was recorded.');
            $table->unsignedBigInteger('quantity_milliunits')->comment('Ordered quantity in thousandths of a base unit.');
            $table->unsignedInteger('unit_price_cents')->comment('Charged purchase or sale price per base unit, in cents.');
            $table->unsignedBigInteger('line_total_cents')->comment('Extended price rounded to the nearest cent.');
            $table->timestamp('created_at')->nullable()->comment('When the record was created.');
            $table->timestamp('updated_at')->nullable()->comment('When the record was last updated.');

            $table->index('order_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('customers');
    }
};
