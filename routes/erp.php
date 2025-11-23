<?php

use App\Http\Controllers\Product\ProductCategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('erp')->group(function () {
    Route::prefix('product-categories')->group(function () {
        Route::get('/', [ProductCategoryController::class, 'index'])->name('erp.categories.index')->middleware('permission:product.index');

        Route::get('/create', [ProductCategoryController::class, 'create'])->name('erp.categories.create')->middleware('permission:product.create');
        Route::post('/', [ProductCategoryController::class, 'store'])->name('erp.categories.store')->middleware('permission:product.create');
    });
});
