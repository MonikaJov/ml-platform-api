<?php

use App\Http\Controllers\StoreUserController;

Route::post('/register', StoreUserController::class)->name('auth.register');
