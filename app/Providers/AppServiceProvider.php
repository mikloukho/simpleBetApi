<?php

namespace App\Providers;

use App\Contracts\EventProviderInterface;
use App\Models\Event;
use App\Observers\EventObserver;
use App\Services\StubEventProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            EventProviderInterface::class,
            StubEventProvider::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::observe(EventObserver::class);
    }
}
