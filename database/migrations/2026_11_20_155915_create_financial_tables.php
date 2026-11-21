<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table): void {
            $table->comment('Configurable methods used to pay or receive money.');
            $table->id()->comment('Internal primary key.');
            $table->string('name')->unique()->comment('Payment method name, such as Cash, Pix, or Debit Card.');
            $table->enum('fee_type', ['fixed', 'percentage'])->default('fixed')->comment('Whether the processing fee is fixed or percentage-based.');
            $table->unsignedInteger('fee_rate')->default(0)->comment('Processing fee in cents when fixed or basis points when percentage.');
            $table->boolean('is_active')->default(true)->comment('Whether this method can be selected for new cash entries.');
            $table->timestamp('created_at')->nullable()->comment('When the record was created.');
            $table->timestamp('updated_at')->nullable()->comment('When the record was last updated.');
            $table->softDeletes()->comment('When the record was archived.');
        });

        Schema::create('expense_categories', function (Blueprint $table): void {
            $table->comment('Classification for bills and paid expenses.');
            $table->id()->comment('Internal primary key.');
            $table->string('name')->unique()->comment('Expense category name, such as Rent or Taxes.');
            $table->boolean('is_system')->default(false)->comment('Whether the category was supplied by the system reference seeder.');
            $table->timestamp('created_at')->nullable()->comment('When the record was created.');
            $table->timestamp('updated_at')->nullable()->comment('When the record was last updated.');
            $table->softDeletes()->comment('When the record was archived.');
        });

        Schema::create('financial_accounts', function (Blueprint $table): void {
            $table->comment('Accounts payable and receivable, including order installments.');
            $table->id()->comment('Internal primary key.');
            $table->enum('type', ['payable', 'receivable'])->comment('Whether the amount is owed or expected to be received.');
            $table->foreignId('order_id')->nullable()->comment('Purchase or sale that originated this obligation, when applicable.')->constrained('orders')->restrictOnDelete();
            $table->foreignId('expense_category_id')->nullable()->comment('Required classification for accounts payable.')->constrained('expense_categories')->restrictOnDelete();
            $table->foreignId('created_by_id')->nullable()->comment('User who recorded the obligation.')->constrained('users')->nullOnDelete();
            $table->string('description')->comment('Description of the amount payable or receivable.');
            $table->string('contact_name')->nullable()->comment('Historical contact name, required for a receivable linked to a sale.');
            $table->string('contact_phone', 40)->nullable()->comment('Historical contact phone, required for a receivable linked to a sale.');
            $table->unsignedBigInteger('amount_cents')->comment('Original amount due, in cents.');
            $table->date('due_on')->comment('Date when the amount is due.');
            $table->timestamp('cancelled_at')->nullable()->comment('When the unsettled obligation was cancelled.');
            $table->text('notes')->nullable()->comment('Optional internal notes about the obligation.');
            $table->timestamp('created_at')->nullable()->comment('When the record was created.');
            $table->timestamp('updated_at')->nullable()->comment('When the record was last updated.');

            $table->index(['type', 'cancelled_at', 'due_on']);
            $table->index('order_id');
            $table->index('expense_category_id');
            $table->index('created_by_id');
        });

        Schema::create('cash_entries', function (Blueprint $table): void {
            $table->comment('Posted receipts and expenses, including settlements of obligations.');
            $table->id()->comment('Internal primary key.');
            $table->enum('type', ['receipt', 'expense'])->comment('Direction of the cash flow.');
            $table->foreignId('financial_account_id')->nullable()->comment('Account settled by this entry, if any.')->constrained('financial_accounts')->restrictOnDelete();
            $table->foreignId('expense_category_id')->nullable()->comment('Classification for a manual expense.')->constrained('expense_categories')->restrictOnDelete();
            $table->foreignId('payment_method_id')->comment('Method actually used for the payment or receipt.')->constrained()->restrictOnDelete();
            $table->foreignId('created_by_id')->nullable()->comment('User who recorded the cash entry.')->constrained('users')->nullOnDelete();
            $table->string('description')->comment('Description of the actual receipt or expense.');
            $table->unsignedBigInteger('amount_cents')->comment('Gross amount paid or received, in cents, before processing fees.');
            $table->enum('fee_type', ['fixed', 'percentage'])->comment('Processing fee type captured at posting time.');
            $table->unsignedInteger('fee_rate')->comment('Processing fee rate captured in cents or basis points at posting time.');
            $table->unsignedBigInteger('fee_cents')->comment('Processing fee charged on the entry, in cents.');
            $table->timestamp('occurred_at')->comment('Effective date and time of the cash movement.');
            $table->text('notes')->nullable()->comment('Optional cash movement notes.');
            $table->timestamp('created_at')->nullable()->comment('When the cash entry was recorded.');

            $table->index(['type', 'occurred_at']);
            $table->index(['financial_account_id', 'occurred_at']);
            $table->index(['payment_method_id', 'occurred_at']);
            $table->index('expense_category_id');
            $table->index('created_by_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_entries');
        Schema::dropIfExists('financial_accounts');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('payment_methods');
    }
};
