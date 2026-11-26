<?php

declare(strict_types=1);

use App\Http\Controllers\Erp\CashFlow\CashEntryController;
use App\Http\Controllers\Erp\CashFlow\CashFlowController;
use App\Http\Controllers\Erp\CashFlow\PayableController;
use App\Http\Controllers\Erp\CashFlow\PayablePaymentController;
use App\Http\Controllers\Erp\Inventory\InventoryController;
use App\Http\Controllers\Erp\Inventory\ProductController;
use App\Http\Controllers\Erp\Inventory\StockMovementController;
use App\Http\Controllers\Erp\Settings\CategoryController;
use App\Http\Controllers\Erp\Settings\ExpenseCategoryController;
use App\Http\Controllers\Erp\Settings\PaymentMethodController;
use App\Http\Controllers\Erp\Settings\RoleController;
use App\Http\Controllers\Erp\Settings\StorageLocationController;
use App\Http\Controllers\Erp\Settings\UnitOfMeasureController;
use App\Http\Controllers\Erp\Settings\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('erp')->name('erp.')->group(function (): void {
    Route::prefix('inventory')->name('inventory.')->group(function (): void {
        Route::get('/', InventoryController::class)->middleware('permission:product.index')->name('index');

        Route::controller(ProductController::class)->prefix('products')->name('products.')->group(function (): void {
            Route::get('/', 'index')->middleware('permission:product.index')->name('index');
            Route::get('create', 'create')->middleware('permission:product.create')->name('create');
            Route::post('/', 'store')->middleware('permission:product.create')->name('store');
            Route::get('{product}', 'show')->withTrashed()->middleware('permission:product.show')->name('show');
            Route::get('{product}/edit', 'edit')->middleware('permission:product.edit')->name('edit');
            Route::patch('{product}', 'update')->middleware('permission:product.edit')->name('update');
            Route::get('{product}/archive', 'archive')->middleware('permission:product.destroy')->name('archive');
            Route::patch('{product}/restore', 'restore')->withTrashed()->middleware('permission:product.destroy')->name('restore');
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

    Route::prefix('settings')->name('settings.')->group(function (): void {
        $resources = [
            'payment-methods' => [PaymentMethodController::class, 'paymentMethod', 'payment-method', true],
            'expense-categories' => [ExpenseCategoryController::class, 'expenseCategory', 'expense-category', true],
            'categories' => [CategoryController::class, 'category', 'category', true],
            'units' => [UnitOfMeasureController::class, 'unit', 'unit-of-measure', true],
            'locations' => [StorageLocationController::class, 'location', 'storage-location', true],
            'users' => [UserController::class, 'user', 'user', false],
            'roles' => [RoleController::class, 'role', 'role', false],
        ];

        foreach ($resources as $resource => [$controller, $parameter, $permission, $softDeletes]) {
            $record = "$resource/{{$parameter}}";
            $canDestroy = "permission:$permission.destroy";

            Route::get("$record/archive", [$controller, $softDeletes ? 'archive' : 'confirmDestroy'])
                ->whereNumber($parameter)->middleware($canDestroy)->name("$resource.archive");

            if ($softDeletes) {
                Route::patch("$record/restore", [$controller, 'restore'])
                    ->whereNumber($parameter)->name("$resource.restore")->withTrashed()->middleware($canDestroy);
            } else {
                Route::get("$record/delete", [$controller, 'confirmDestroy'])
                    ->whereNumber($parameter)->middleware($canDestroy)->name("$resource.confirm-destroy");
            }

            $routes = Route::resource($resource, $controller)
                ->parameters([$resource => $parameter])
                ->where([$parameter => '[0-9]+'])
                ->middlewareFor('index', "permission:$permission.index")
                ->middlewareFor(['create', 'store'], "permission:$permission.create")
                ->middlewareFor('show', "permission:$permission.show")
                ->middlewareFor(['edit', 'update'], "permission:$permission.edit")
                ->middlewareFor('destroy', $canDestroy);

            if ($softDeletes) {
                $routes->withTrashed(['show']);
            }

            $routes->register();
        }
    });
});
