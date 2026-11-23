<?php

use App\Models\CashEntry;
use App\Models\Customer;
use App\Models\ExpenseCategory;
use App\Models\FinancialAccount;
use App\Models\Order;
use App\Models\PaymentMethod;

test('a sale on credit retains contact details and is paid using the settlement method', function () {
    $customer = Customer::factory()->create(['name' => 'Buyer', 'phone' => '555-0100']);
    $sale = Order::factory()->for($customer)->create();
    $account = FinancialAccount::factory()->for($sale)->create(['amount_cents' => 5_000]);
    $method = PaymentMethod::factory()->create(['fee_type' => PaymentMethod::FEE_PERCENTAGE, 'fee_rate' => 250]);
    $settlements = CashEntry::factory()->forAccount($account)->for($method);
    $customer->update(['name' => 'Renamed buyer', 'phone' => '555-0200']);

    expect($account->refresh()->contact_name)->toBe('Buyer')
        ->and($account->contact_phone)->toBe('555-0100')
        ->and($account->status)->toBe(FinancialAccount::STATUS_PENDING);

    $first = $settlements->create(['amount_cents' => 3_000]);
    $method->update(['fee_rate' => 300]);

    expect($first->refresh()->fee_rate)->toBe(250)
        ->and($first->fee_cents)->toBe(75)
        ->and($account->status)->toBe(FinancialAccount::STATUS_PARTIALLY_PAID)
        ->and($account->outstandingCents())->toBe(2_000);

    expect(fn () => $settlements->create(['amount_cents' => 2_001]))->toThrow(DomainException::class);
    expect(fn () => $account->update(['cancelled_at' => now()]))->toThrow(DomainException::class);

    $settlements->create(['amount_cents' => 2_000]);

    expect($account->refresh()->status)->toBe(FinancialAccount::STATUS_PAID)
        ->and($account->outstandingCents())->toBe(0);
});

test('a bill stays separate from a manual expense and requires an outgoing settlement', function () {
    $category = ExpenseCategory::factory()->create();
    $account = FinancialAccount::factory()->payable($category)->create(['due_on' => today()->subDay()]);
    $manualExpense = CashEntry::factory()->expense($category)->create();
    $manualReceipt = CashEntry::factory()->create();

    expect($account->status)->toBe(FinancialAccount::STATUS_OVERDUE)
        ->and($account->expenseCategory->is($category))->toBeTrue()
        ->and($account->settledCents())->toBe(0)
        ->and($manualExpense->expenseCategory->is($category))->toBeTrue()
        ->and($manualExpense->financial_account_id)->toBeNull()
        ->and($manualReceipt->expense_category_id)->toBeNull();

    expect(fn () => CashEntry::factory()->for($account)->create())->toThrow(DomainException::class);

    $payment = CashEntry::factory()->forAccount($account)->create(['amount_cents' => $account->amount_cents]);

    expect($payment->type)->toBe(CashEntry::TYPE_EXPENSE)
        ->and($account->status)->toBe(FinancialAccount::STATUS_PAID);
});

test('an inactive payment method cannot be used for a new entry', function () {
    $method = PaymentMethod::factory()->create(['is_active' => false]);

    expect(fn () => CashEntry::factory()->for($method)->create())
        ->toThrow(DomainException::class, 'The payment method is not available for new entries.');
});
