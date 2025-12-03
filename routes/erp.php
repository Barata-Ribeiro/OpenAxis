<?php

use App\Http\Controllers\Management\ClientController;
use App\Http\Controllers\Management\PaymentConditionController;
use App\Http\Controllers\Management\SalesOrderController;
use App\Http\Controllers\Management\SupplierController;
use App\Http\Controllers\Management\VendorController;
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

    Route::resource('products', ProductController::class)
        ->except('show')
        ->names([
            'index' => 'erp.products.index',
            'create' => 'erp.products.create',
            'store' => 'erp.products.store',
            'edit' => 'erp.products.edit',
            'update' => 'erp.products.update',
            'destroy' => 'erp.products.destroy',
        ])
        ->middlewareFor('index', 'permission:product.index')
        ->middlewareFor(['create', 'store'], 'permission:product.create')
        ->middlewareFor(['edit', 'update'], 'permission:product.edit')
        ->middlewareFor('destroy', 'permission:product.destroy');

    Route::delete('clients/{client}/force', [ClientController::class, 'forceDestroy'])
        ->name('erp.clients.force-destroy')
        ->middleware('permission:client.destroy');

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
