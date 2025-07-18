<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RefreshController;
use App\Http\Controllers\StoreUserController;

Route::post('/register', StoreUserController::class)->name('auth.register');
Route::post('/auth', LoginController::class)->name('auth.login');
Route::post('/user/token/refresh', RefreshController::class)->name('auth.refresh');
