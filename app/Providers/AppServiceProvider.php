<?php

namespace App\Providers;

use App\Repositories\CollegeRepository;
use App\Repositories\Interfaces\CollegeRepositoryInterface;
use App\Utils\CollegesParser;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            CollegesParser::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
