<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Mail\NewUserMail;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('administrative')->group(function () {
    Route::resource('roles', RoleController::class)
        ->only('index')
        ->names(['index' => 'administrative.roles.index'])
        ->middlewareFor('index', 'permission:role.index');

    Route::delete('users/{user}/force', [UserController::class, 'forceDestroy'])
        ->name('administrative.users.force-destroy')
        ->middleware('permission:user.destroy');

    Route::resource('users', UserController::class)
        ->names([
            'index' => 'administrative.users.index',
            'create' => 'administrative.users.create',
            'store' => 'administrative.users.store',
            'show' => 'administrative.users.show',
            'edit' => 'administrative.users.edit',
            'update' => 'administrative.users.update',
            'destroy' => 'administrative.users.destroy',
        ])
        ->middlewareFor('index', 'permission:user.index')
        ->middlewareFor(['create', 'store'], 'permission:user.create')
        ->middlewareFor('show', 'permission:user.show')
        ->middlewareFor(['edit', 'update'], 'permission:user.edit')
        ->middlewareFor('destroy', 'permission:user.destroy');

    Route::prefix('mailable')->group(function () {
        Route::get('/new-account', function () {
            $user = Auth::user();
            $password = Str::password();

            return new NewUserMail($user->name, $user->email, $password);
        })->name('administrative.mailable.new-account')->middleware('permission:user.create');
    });
});
