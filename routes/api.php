<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RefreshController;
use App\Http\Controllers\Dataset\DeleteDatasetController;
use App\Http\Controllers\Dataset\StoreDatasetController;
use App\Http\Controllers\User\DeleteUserController;
use App\Http\Controllers\User\PatchUserController;
use App\Http\Controllers\User\StoreUserController;

Route::post('/register', StoreUserController::class)->name('auth.register');
Route::post('/auth', LoginController::class)->name('auth.login');
Route::post('/user/token/refresh', RefreshController::class)->name('auth.refresh');

Route::middleware(['token'])
    ->group(function () {
        Route::post('auth/logout', LogoutController::class)->name('auth.logout');
        Route::prefix('/users')
            ->name('users.')
            ->group(function () {
                Route::delete('/{user}', DeleteUserController::class)->name('delete')->middleware('can:delete,user');
                Route::patch('/{user}', PatchUserController::class)->name('patch')->middleware('can:update,user');
            });
        Route::prefix('/datasets')
            ->name('datasets.')
            ->group(function () {
                Route::post('/', StoreDatasetController::class)->name('store');
                Route::delete('/{dataset}', DeleteDatasetController::class)->name('delete')->middleware('can:delete,dataset');
            });
    });
