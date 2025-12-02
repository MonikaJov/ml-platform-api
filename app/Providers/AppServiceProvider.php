<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Dataset;
use App\Models\ProblemDetail;
use App\Models\User;
use App\Observers\Dataset\DatasetObserver;
use App\Observers\ProblemDetail\ProblemDetailObserver;
use App\Observers\User\UserObserver;
use Dedoc\Scramble\Scramble;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    public function boot(): void
    {
        JsonResource::withoutWrapping();
        Dataset::observe(DatasetObserver::class);
        User::observe(UserObserver::class);
        ProblemDetail::observe(ProblemDetailObserver::class);

        Scramble::configure()
            ->routes(function (Route $route) {
                return Str::startsWith($route->uri, 'api/');
            });
    }
}
