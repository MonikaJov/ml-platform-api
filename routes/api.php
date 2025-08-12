<?php

use App\Http\Controllers\DeleteUserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\RefreshController;
use App\Http\Controllers\StoreUserController;

Route::post('/register', StoreUserController::class)->name('auth.register');
Route::post('/auth', LoginController::class)->name('auth.login');
Route::post('/user/token/refresh', RefreshController::class)->name('auth.refresh');

Route::middleware(['token'])
    ->group(function () {
        Route::post('auth/logout', LogoutController::class)->name('auth.logout');
        Route::prefix('/users')
            ->name('users.')
            ->group(function () {
                Route::delete('/{user}', DeleteUserController::class)->name('delete');
            });
    });
