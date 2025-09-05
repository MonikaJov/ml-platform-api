<?php

namespace App\Providers;

use App\Models\Dataset;
use App\Observers\Dataset\DatasetObserver;
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
    }
}
