<?php

use App\Http\Controllers\Product\ProductCategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('erp')->group(function () {
    Route::prefix('product-categories')->group(function () {
        Route::get('/', [ProductCategoryController::class, 'index'])->name('erp.categories.index');
        Route::get('/create', [ProductCategoryController::class, 'create'])->name('erp.categories.create');
    });
});
