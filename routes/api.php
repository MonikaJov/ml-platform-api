<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\StoreUserController;

Route::post('/register', StoreUserController::class)->name('auth.register');
Route::post('/auth', LoginController::class)->name('auth.login');
