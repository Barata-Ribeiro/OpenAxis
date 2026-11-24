<?php

declare(strict_types=1);

use App\Actions\Erp\SettingsRecords;
use App\Http\Controllers\Erp\CashEntryController;
use App\Http\Controllers\Erp\CashFlowController;
use App\Http\Controllers\Erp\InventoryController;
use App\Http\Controllers\Erp\PayableController;
use App\Http\Controllers\Erp\PayablePaymentController;
use App\Http\Controllers\Erp\ProductController;
use App\Http\Controllers\Erp\SettingsRecordController;
use App\Http\Controllers\Erp\StockMovementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('erp')->name('erp.')->group(function (): void {
    Route::prefix('inventory')->name('inventory.')->group(function (): void {
        Route::get('/', InventoryController::class)->middleware('permission:product.index')->name('index');

        Route::controller(ProductController::class)->prefix('products')->name('products.')->group(function (): void {
            Route::get('create', 'create')->middleware('permission:product.create')->name('create');
            Route::post('/', 'store')->middleware('permission:product.create')->name('store');
            Route::get('{product}', 'show')->middleware('permission:product.show')->name('show');
            Route::get('{product}/edit', 'edit')->middleware('permission:product.edit')->name('edit');
            Route::patch('{product}', 'update')->middleware('permission:product.edit')->name('update');
            Route::get('{product}/archive', 'archive')->middleware('permission:product.destroy')->name('archive');
            Route::delete('{product}', 'destroy')->middleware('permission:product.destroy')->name('destroy');
        });

        Route::controller(StockMovementController::class)->prefix('movements')->name('movements.')->group(function (): void {
            Route::get('/', 'index')->middleware('permission:stock-movement.index')->name('index');
            Route::middleware('permission:stock-movement.create')->group(function (): void {
                Route::get('receipt', 'create')->defaults('direction', 'receipt')->name('receipt');
                Route::post('receipt', 'store')->defaults('direction', 'receipt')->name('store-receipt');
                Route::get('issue', 'create')->defaults('direction', 'issue')->name('issue');
                Route::post('issue', 'store')->defaults('direction', 'issue')->name('store-issue');
            });
            Route::get('{stockMovement}', 'show')->middleware('permission:stock-movement.show')->name('show');
        });
    });

    Route::prefix('cash-flow')->name('cash-flow.')->group(function (): void {
        Route::get('/', [CashFlowController::class, 'index'])->middleware('permission:cash-entry.index|financial-account.index')->name('index');

        Route::controller(CashEntryController::class)->group(function (): void {
            Route::middleware('permission:cash-entry.create')->group(function (): void {
                Route::get('receipts/create', 'create')->defaults('kind', 'receipt')->name('create-receipt');
                Route::post('receipts', 'store')->defaults('kind', 'receipt')->name('store-receipt');
                Route::get('expenses/create', 'create')->defaults('kind', 'expense')->name('create-expense');
                Route::post('expenses', 'store')->defaults('kind', 'expense')->name('store-expense');
            });
            Route::get('entries/{cashEntry}', 'show')->middleware('permission:cash-entry.show')->name('show-entry');
        });

        Route::controller(PayableController::class)->prefix('payables')->name('payables.')->group(function (): void {
            Route::get('create', 'create')->middleware('permission:financial-account.create')->name('create');
            Route::post('/', 'store')->middleware('permission:financial-account.create')->defaults('kind', 'payable')->name('store');
            Route::get('{financialAccount}', 'show')->middleware('permission:financial-account.show')->name('show');
            Route::get('{financialAccount}/edit', 'edit')->middleware('permission:financial-account.edit')->name('edit');
            Route::patch('{financialAccount}', 'update')->middleware('permission:financial-account.edit')->defaults('kind', 'payable')->name('update');
            Route::get('{financialAccount}/cancel', 'confirmDestroy')->middleware('permission:financial-account.cancel')->name('cancel');
            Route::delete('{financialAccount}', 'destroy')->middleware('permission:financial-account.cancel')->name('destroy');
            Route::controller(PayablePaymentController::class)->middleware(['permission:cash-entry.create', 'permission:financial-account.show'])->group(function (): void {
                Route::get('{financialAccount}/payment', 'create')->name('confirm-payment');
                Route::post('{financialAccount}/payment', 'store')->defaults('kind', 'payment')->name('pay');
            });
        });
    });

    Route::controller(SettingsRecordController::class)->prefix('settings')->name('settings.')->group(function (): void {
        foreach (SettingsRecords::MODULES as $section => $module) {
            Route::prefix($section)->name("$section.")->where(['record' => '[0-9]+'])->group(function () use ($section, $module): void {
                Route::get('/', 'index')->defaults('section', $section)->middleware("permission:$module.index")->name('index');
                Route::get('create', 'create')->defaults('section', $section)->middleware("permission:$module.create")->name('create');
                Route::post('/', 'store')->defaults('section', $section)->middleware("permission:$module.create")->name('store');
                Route::get('{record}', 'show')->defaults('section', $section)->middleware("permission:$module.show")->name('show');
                Route::get('{record}/edit', 'edit')->defaults('section', $section)->middleware("permission:$module.edit")->name('edit');
                Route::patch('{record}', 'update')->defaults('section', $section)->middleware("permission:$module.edit")->name('update');
                Route::get('{record}/archive', 'archive')->defaults('section', $section)->middleware("permission:$module.destroy")->name('archive');
                Route::delete('{record}', 'destroy')->defaults('section', $section)->middleware("permission:$module.destroy")->name('destroy');
            });
        }
    });
});
