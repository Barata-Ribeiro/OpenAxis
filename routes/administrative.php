<?php

use App\Http\Controllers\Admin\NotifierController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Mail\NewUserMail;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('administrative')->group(function () {
    Route::resource('roles', RoleController::class)
        ->names([
            'index' => 'administrative.roles.index',
            'create' => 'administrative.roles.create',
            'store' => 'administrative.roles.store',
            'show' => 'administrative.roles.show',
            'edit' => 'administrative.roles.edit',
            'update' => 'administrative.roles.update',
            'destroy' => 'administrative.roles.destroy',
        ])
        ->middlewareFor('index', 'permission:role.index')
        ->middlewareFor(['create', 'store'], 'permission:role.create')
        ->middlewareFor('show', 'permission:role.show')
        ->middlewareFor(['edit', 'update'], 'permission:role.edit')
        ->middlewareFor('destroy', 'permission:role.destroy');

    Route::delete('users/{user}/force', [UserController::class, 'forceDestroy'])
        ->name('administrative.users.force-destroy')
        ->middleware('permission:user.destroy');
    Route::get('users/generate-csv', [UserController::class, 'generateCsv'])
        ->name('administrative.users.generate-csv')
        ->middleware('permission:user.index');

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

    Route::prefix('notifier')->group(function () {
        Route::get('/create', [NotifierController::class, 'create'])->name('administrative.notifier.create');
        Route::post('/notify', [NotifierController::class, 'notify'])->name('administrative.notifier.notify');
    });

    Route::prefix('mailable')->group(function () {
        Route::get('/new-account', function () {
            $user = Auth::user();
            $password = Str::password();

            return new NewUserMail($user->name, $user->email, $password);
        })->name('administrative.mailable.new-account')->middleware('permission:user.create');
    });
});
