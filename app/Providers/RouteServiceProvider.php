<?php

namespace App\Providers;

use App\Http\Middleware\UniquePerModelMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware('api')
            ->prefix('api')
            ->as('api.')
            ->group(base_path('routes/api.php'));

        Route::middleware('web')
            ->group(base_path('routes/web.php'));

        $this->app['router']->aliasMiddleware('unique_per_model', UniquePerModelMiddleware::class);
    }
}
