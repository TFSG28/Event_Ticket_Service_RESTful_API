<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\EventRepository;
use App\Repositories\ReservationRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(EventRepository::class, function ($app) {
            return new EventRepository(new EventRepository());
        });
        $this->app->bind(ReservationRepository::class, function ($app) {
            return new ReservationRepository(new ReservationRepository());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
