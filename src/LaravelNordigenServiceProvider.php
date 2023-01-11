<?php

namespace Hypnodev\LaravelNordigen;

use Hypnodev\LaravelNordigen\LaravelNordigen;
use Illuminate\Support\ServiceProvider;

class LaravelNordigenServiceProvider extends ServiceProvider
{
    public function register() : void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-nordigen.php', 'laravel-nordigen');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        // Register the service the package provides.
        $this->app->singleton('laravel-nordigen', function ($app) {
            return new LaravelNordigen;
        });
    }

    public function boot() : void
    {
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    public function provides()
    {
        return ['laravel-nordigen'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/laravel-nordigen.php' => config_path('laravel-nordigen.php'),
        ], 'laravel-nordigen-config');
    }
}
