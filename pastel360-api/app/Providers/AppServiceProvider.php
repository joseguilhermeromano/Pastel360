<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\CustomerRepositoryInterface::class,
            \App\Repositories\CustomerRepository::class,
            \App\Repositories\Contracts\OrderRepositoryInterface::class,
            \App\Repositories\OrderRepository::class,
            \App\Repositories\Contracts\ProductRepositoryInterface::class,
            \App\Repositories\ProductRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
