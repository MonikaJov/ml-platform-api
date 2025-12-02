<?php

namespace App\Providers;

use App\Models\Dataset;
use App\Models\ProblemDetail;
use App\Models\User;
use App\Observers\Dataset\DatasetObserver;
use App\Observers\ProblemDetail\ProblemDetailObserver;
use App\Observers\User\UserObserver;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
    }
}
