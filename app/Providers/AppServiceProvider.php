<?php

namespace App\Providers;

use App\Models\ProjectMessage;
use App\Observers\ProjectMessageObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ProjectMessage::observe(ProjectMessageObserver::class);
    }
}
