<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Middleware\FileMustExistMiddleware;
use App\Http\Middleware\RecordsMustBeRelatedMiddleware;
use App\Http\Middleware\UniquePerModelMiddleware;
use App\Http\Middleware\VerifyInternalServiceTokenMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class RouteServiceProvider extends ServiceProvider
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
        $this->app['router']->aliasMiddleware('file_must_exist', FileMustExistMiddleware::class);
        $this->app['router']->aliasMiddleware('records_must_be_related', RecordsMustBeRelatedMiddleware::class);
        $this->app['router']->aliasMiddleware('ml_engine_token', VerifyInternalServiceTokenMiddleware::class);
    }
}
