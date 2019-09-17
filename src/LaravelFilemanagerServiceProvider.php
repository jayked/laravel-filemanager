<?php namespace Jayked\Laravelfilemanager;

use Config;
use Illuminate\Support\ServiceProvider;

/**
 * Class LaravelFilemanagerServiceProvider
 *
 * @package Unisharp\Laravelfilemanager
 */
class LaravelFilemanagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/lang', 'laravel-filemanager');

        $this->loadViewsFrom(__DIR__ . '/views', 'laravel-filemanager');

        $this->publishes([
            __DIR__ . '/config/lfm.php' => base_path('config/lfm.php'),
        ], 'lfm_config');

        $this->mergeConfigFrom(__DIR__ . '/config/lfm.php', 'lfm');

        $this->publishes([
            __DIR__ . '/../public' => public_path('vendor/laravel-filemanager'),
        ], 'lfm_public');

        $this->publishes([
            __DIR__ . '/views' => base_path('resources/views/vendor/laravel-filemanager'),
        ], 'lfm_view');

        if(Config::get('lfm.use_package_routes')) {
            $this->loadRoutesFrom(__DIR__ . '/routes.php');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('laravel-filemanager', function() {
            return true;
        });
    }
}
