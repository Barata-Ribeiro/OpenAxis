<?php

use App\Http\Controllers\Management\ClientController;
use App\Http\Controllers\Management\PayableController;
use App\Http\Controllers\Management\PaymentConditionController;
use App\Http\Controllers\Management\PurchaseOrderController;
use App\Http\Controllers\Management\ReceivableController;
use App\Http\Controllers\Management\SalesOrderController;
use App\Http\Controllers\Management\SupplierController;
use App\Http\Controllers\Management\VendorController;
use App\Http\Controllers\Product\InventoryController;
use App\Http\Controllers\Product\ProductCategoryController;
use App\Http\Controllers\Product\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('erp')->group(function () {
    Route::resource('product-categories', ProductCategoryController::class)
        ->except('show')
        ->parameters(['product-categories' => 'category'])
        ->names([
            'index' => 'erp.categories.index',
            'create' => 'erp.categories.create',
            'store' => 'erp.categories.store',
            'edit' => 'erp.categories.edit',
            'update' => 'erp.categories.update',
            'destroy' => 'erp.categories.destroy',
        ])
        ->middlewareFor('index', 'permission:product.index')
        ->middlewareFor(['create', 'store'], 'permission:product.create')
        ->middlewareFor(['edit', 'update'], 'permission:product.edit')
        ->middlewareFor('destroy', 'permission:product.destroy');

    Route::delete('products/{product}/force', [ProductController::class, 'forceDestroy'])
        ->name('erp.products.force-destroy')
        ->middleware('permission:product.destroy');
    Route::get('products/generate-csv', [ProductController::class, 'generateCsv'])
        ->name('erp.products.generate-csv')
        ->middleware('permission:product.index');
    Route::resource('products', ProductController::class)
        ->names([
            'index' => 'erp.products.index',
            'create' => 'erp.products.create',
            'store' => 'erp.products.store',
            'show' => 'erp.products.show',
            'edit' => 'erp.products.edit',
            'update' => 'erp.products.update',
            'destroy' => 'erp.products.destroy',
        ])
        ->middlewareFor('index', 'permission:product.index')
        ->middlewareFor(['create', 'store'], 'permission:product.create')
        ->middlewareFor('show', 'permission:product.show')
        ->middlewareFor(['edit', 'update'], 'permission:product.edit')
        ->middlewareFor('destroy', 'permission:product.destroy');

    Route::get('inventory/generate-csv', [InventoryController::class, 'generateCsv'])
        ->name('erp.inventory.generate-csv')
        ->middleware('permission:supply.index');
    Route::resource('inventory', InventoryController::class)
        ->parameters(['inventory' => 'product'])
        ->names([
            'index' => 'erp.inventory.index',
            'create' => 'erp.inventory.create',
            'store' => 'erp.inventory.store',
            'show' => 'erp.inventory.show',
            'edit' => 'erp.inventory.edit',
            'update' => 'erp.inventory.update',
            'destroy' => 'erp.inventory.destroy',
        ])
        ->middlewareFor('index', 'permission:supply.index')
        ->middlewareFor(['create', 'store'], 'permission:supply.create')
        ->middlewareFor('show', 'permission:supply.show')
        ->middlewareFor(['edit', 'update'], 'permission:supply.edit')
        ->middlewareFor('destroy', 'permission:supply.destroy');

    Route::delete('clients/{client}/force', [ClientController::class, 'forceDestroy'])
        ->name('erp.clients.force-destroy')
        ->middleware('permission:client.destroy');
    Route::get('clients/generate-csv', [ClientController::class, 'generateCsv'])
        ->name('erp.clients.generate-csv')
        ->middleware('permission:client.index');
    Route::resource('clients', ClientController::class)
        ->names([
            'index' => 'erp.clients.index',
            'create' => 'erp.clients.create',
            'store' => 'erp.clients.store',
            'show' => 'erp.clients.show',
            'edit' => 'erp.clients.edit',
            'update' => 'erp.clients.update',
            'destroy' => 'erp.clients.destroy',
        ])
        ->middlewareFor('index', 'permission:client.index')
        ->middlewareFor(['create', 'store'], 'permission:client.create')
        ->middlewareFor('show', 'permission:client.show')
        ->middlewareFor(['edit', 'update'], 'permission:client.edit')
        ->middlewareFor('destroy', 'permission:client.destroy');

    Route::resource('purchase-orders', PurchaseOrderController::class)
        ->except('show')
        ->parameters(['purchase-orders' => 'purchaseOrder'])
        ->names([
            'index' => 'erp.purchase-orders.index',
            'create' => 'erp.purchase-orders.create',
            'store' => 'erp.purchase-orders.store',
            'edit' => 'erp.purchase-orders.edit',
            'update' => 'erp.purchase-orders.update',
            'destroy' => 'erp.purchase-orders.destroy',
        ])
        ->middlewareFor('index', 'permission:order.index')
        ->middlewareFor(['create', 'store'], 'permission:order.create')
        ->middlewareFor(['edit', 'update'], 'permission:order.edit')
        ->middlewareFor('destroy', 'permission:order.destroy');

    Route::get('sales-orders/generate-csv', [SalesOrderController::class, 'generateCsv'])
        ->name('erp.sales-orders.generate-csv')
        ->middleware('permission:sale.index');
    Route::resource('sales-orders', SalesOrderController::class)
        ->except('show')
        ->parameters(['sales-orders' => 'salesOrder'])
        ->names([
            'index' => 'erp.sales-orders.index',
            'create' => 'erp.sales-orders.create',
            'store' => 'erp.sales-orders.store',
            'edit' => 'erp.sales-orders.edit',
            'update' => 'erp.sales-orders.update',
            'destroy' => 'erp.sales-orders.destroy',
        ])
        ->middlewareFor('index', 'permission:sale.index')
        ->middlewareFor(['create', 'store'], 'permission:sale.create')
        ->middlewareFor(['edit', 'update'], 'permission:sale.edit')
        ->middlewareFor('destroy', 'permission:sale.destroy');

    Route::delete('vendors/{vendor}/force', [VendorController::class, 'forceDestroy'])
        ->name('erp.vendors.force-destroy')
        ->middleware('permission:vendor.destroy');
    Route::resource('vendors', VendorController::class)
        ->names([
            'index' => 'erp.vendors.index',
            'create' => 'erp.vendors.create',
            'store' => 'erp.vendors.store',
            'show' => 'erp.vendors.show',
            'edit' => 'erp.vendors.edit',
            'update' => 'erp.vendors.update',
            'destroy' => 'erp.vendors.destroy',
        ])
        ->middlewareFor('index', 'permission:vendor.index')
        ->middlewareFor(['create', 'store'], 'permission:vendor.create')
        ->middlewareFor('show', 'permission:vendor.show')
        ->middlewareFor(['edit', 'update'], 'permission:vendor.edit')
        ->middlewareFor('destroy', 'permission:vendor.destroy');

    Route::delete('suppliers/{supplier}/force', [SupplierController::class, 'forceDestroy'])
        ->name('erp.suppliers.force-destroy')
        ->middleware('permission:supplier.destroy');
    Route::resource('suppliers', SupplierController::class)
        ->names([
            'index' => 'erp.suppliers.index',
            'create' => 'erp.suppliers.create',
            'store' => 'erp.suppliers.store',
            'show' => 'erp.suppliers.show',
            'edit' => 'erp.suppliers.edit',
            'update' => 'erp.suppliers.update',
            'destroy' => 'erp.suppliers.destroy',
        ])
        ->middlewareFor('index', 'permission:supplier.index')
        ->middlewareFor(['create', 'store'], 'permission:supplier.create')
        ->middlewareFor('show', 'permission:supplier.show')
        ->middlewareFor(['edit', 'update'], 'permission:supplier.edit')
        ->middlewareFor('destroy', 'permission:supplier.destroy');

    Route::get('/payables/generate-csv', [PayableController::class, 'generateCsv'])
        ->name('erp.payables.generate-csv')
        ->middleware('permission:finance.index');
    Route::resource('payables', PayableController::class)
        ->parameters(['payables' => 'payable'])
        ->names([
            'index' => 'erp.payables.index',
            'show' => 'erp.payables.show',
            'create' => 'erp.payables.create',
            'store' => 'erp.payables.store',
            'edit' => 'erp.payables.edit',
            'update' => 'erp.payables.update',
            'destroy' => 'erp.payables.destroy',
        ])
        ->middlewareFor('index', 'permission:finance.index')
        ->middlewareFor('show', 'permission:finance.show')
        ->middlewareFor(['create', 'store'], 'permission:finance.create')
        ->middlewareFor(['edit', 'update'], 'permission:finance.edit')
        ->middlewareFor('destroy', 'permission:finance.destroy');

    Route::resource('receivables', ReceivableController::class)
        ->parameters(['receivables' => 'receivable'])
        ->names([
            'index' => 'erp.receivables.index',
            'show' => 'erp.receivables.show',
            'create' => 'erp.receivables.create',
            'store' => 'erp.receivables.store',
            'edit' => 'erp.receivables.edit',
            'update' => 'erp.receivables.update',
            'destroy' => 'erp.receivables.destroy',
        ])
        ->middlewareFor('index', 'permission:finance.index')
        ->middlewareFor('show', 'permission:finance.show')
        ->middlewareFor(['create', 'store'], 'permission:finance.create')
        ->middlewareFor(['edit', 'update'], 'permission:finance.edit')
        ->middlewareFor('destroy', 'permission:finance.destroy');

    Route::resource('payment-conditions', PaymentConditionController::class)
        ->except('show')
        ->parameters(['payment-conditions' => 'paymentCondition'])
        ->names([
            'index' => 'erp.payment-conditions.index',
            'create' => 'erp.payment-conditions.create',
            'store' => 'erp.payment-conditions.store',
            'edit' => 'erp.payment-conditions.edit',
            'update' => 'erp.payment-conditions.update',
            'destroy' => 'erp.payment-conditions.destroy',
        ])
        ->middlewareFor('index', 'permission:finance.index')
        ->middlewareFor(['create', 'store'], 'permission:finance.create')
        ->middlewareFor(['edit', 'update'], 'permission:finance.edit')
        ->middlewareFor('destroy', 'permission:finance.destroy');
});
