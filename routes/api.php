<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RefreshController;
use App\Http\Controllers\Dataset\DeleteDatasetController;
use App\Http\Controllers\Dataset\StoreDatasetController;
use App\Http\Controllers\ProblemDetail\StoreProblemDetailController;
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
                //NOTE: remember to add observations when deleting dataset (also delete problem details)
                Route::delete('/{dataset}', DeleteDatasetController::class)->name('delete')->middleware('can:delete,dataset');
            });
        Route::prefix('datasets/{dataset}/problem-details')
            ->name('dataset.problem-details.')
            ->group(function () {
                //TODO: Add update and delete routes
                //NOTE: remember to add observations when deleting problem detail
                Route::post('/', StoreProblemDetailController::class)->name('store')->middleware(['can:create,App\Models\ProblemDetail,dataset', 'unique_per_model']);
            });
    });
