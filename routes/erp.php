<?php

use App\Http\Controllers\Management\ClientController;
use App\Http\Controllers\Product\ProductCategoryController;
use App\Http\Controllers\Product\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('erp')->group(function () {
    Route::prefix('product-categories')->group(function () {
        Route::get('/', [ProductCategoryController::class, 'index'])->name('erp.categories.index')->middleware('permission:product.index');

        Route::get('/create', [ProductCategoryController::class, 'create'])->name('erp.categories.create')->middleware('permission:product.create');
        Route::post('/', [ProductCategoryController::class, 'store'])->name('erp.categories.store')->middleware('permission:product.create');

        Route::get('/{category}/edit', [ProductCategoryController::class, 'edit'])->name('erp.categories.edit')->middleware('permission:product.edit');
        Route::patch('/{category}', [ProductCategoryController::class, 'update'])->name('erp.categories.update')->middleware('permission:product.edit');

        Route::delete('/{category}', [ProductCategoryController::class, 'destroy'])->name('erp.categories.destroy')->middleware('permission:product.destroy');
    });

    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('erp.products.index')->middleware('permission:product.index');

        Route::get('/create', [ProductController::class, 'create'])->name('erp.products.create')->middleware('permission:product.create');
        Route::post('/', [ProductController::class, 'store'])->name('erp.products.store')->middleware('permission:product.create');

        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('erp.products.edit')->middleware('permission:product.edit');
        Route::patch('/{product}', [ProductController::class, 'update'])->name('erp.products.update')->middleware('permission:product.edit');

        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('erp.products.destroy')->middleware('permission:product.destroy');
    });

    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('erp.clients.index')->middleware('permission:client.index');

        Route::get('/create', [ClientController::class, 'create'])->name('erp.clients.create')->middleware('permission:client.create');
        Route::post('/', [ClientController::class, 'store'])->name('erp.clients.store')->middleware('permission:client.create');

        Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('erp.clients.edit')->middleware('permission:client.edit');
        Route::patch('/{client}', [ClientController::class, 'update'])->name('erp.clients.update')->middleware('permission:client.edit');

        Route::get('/{client}', [ClientController::class, 'show'])->name('erp.clients.show')->middleware('permission:client.show');

        Route::delete('/{client}', [ClientController::class, 'destroy'])->name('erp.clients.destroy')->middleware('permission:client.destroy');
    });
});
