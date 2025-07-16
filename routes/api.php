<?php

use App\Http\Controllers\RegisterController;

Route::post('/api/register', RegisterController::class)->name('auth.register');
