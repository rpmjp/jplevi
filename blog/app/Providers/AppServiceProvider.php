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
        // GD is what the shared host has; Imagick is not guaranteed there.
        $this->app->singleton(\Intervention\Image\ImageManager::class, fn () => new \Intervention\Image\ImageManager(
            new \Intervention\Image\Drivers\Gd\Driver,
        ));

        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
