<?php

namespace App\Providers;

use App\Repositories\CollegeRepository;
use App\Repositories\Interfaces\CollegeRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            CollegeRepositoryInterface::class,
            CollegeRepository::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
