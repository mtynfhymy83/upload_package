<?php

namespace Matin\Media;

use Illuminate\Support\ServiceProvider;
use Matin\Media\Services\MediaService;

class LaravelMediaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/./routes/api.php');

        $this->publishes([
            __DIR__ . '/../config/media.php' => config_path('media.php'),
        ], 'media-config');

    }


    public function register(): void
    {
        $this->app->singleton(MediaService::class, function ($app) {
            return new MediaService($app); // اگر MediaService نیاز به $app دارد
            $this->mergeConfigFrom(
                __DIR__ . '/../config/media.php',
                'media'
            );

        });

//        // یا اگر MediaService نیازی به $app ندارد:
//        $this->app->singleton(MediaService::class, function () {
//            return new MediaService();
//        });
    }
}
