<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Mail\NewUserMail;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('administrative')->group(function () {
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('administrative.roles.index')->middleware('permission:role.index');
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('administrative.users.index')->middleware('permission:user.index');

        Route::get('/create', [UserController::class, 'create'])->name('administrative.users.create')->middleware('permission:user.create');
        Route::post('/', [UserController::class, 'store'])->name('administrative.users.store')->middleware('permission:user.create');

        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('administrative.users.edit')->middleware('permission:user.edit');
        Route::patch('/{user}', [UserController::class, 'update'])->name('administrative.users.update')->middleware('permission:user.edit');

        Route::delete('/{user}', [UserController::class, 'destroy'])->name('administrative.users.destroy')->middleware('permission:user.delete');
    });

    Route::prefix('mailable')->group(function () {
        Route::get('/new-account', function () {
            $user = Auth::user();
            $password = Str::password();

            return new NewUserMail($user->name, $user->email, $password);
        })->name('administrative.mailable.new-account')->middleware('permission:user.create');
    });
});
