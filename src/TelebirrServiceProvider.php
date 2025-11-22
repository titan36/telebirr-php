<?php

namespace Afroeltechnologies\TelebirrLaravel;

use Illuminate\Support\ServiceProvider;

class TelebirrServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/telebirr.php', 'telebirr'
        );

        $this->app->singleton('telebirr', function ($app) {
            // Make sure private/public key paths are absolute
            $privateKeyPath = config('telebirr.private_key_path');
            $publicKeyPath  = config('telebirr.public_key_path');

            if (!file_exists($privateKeyPath)) {
                // Try resolving relative to base path
                $privateKeyPath = base_path($privateKeyPath);
            }

            if (!file_exists($publicKeyPath)) {
                $publicKeyPath = base_path($publicKeyPath);
            }

            return new TelebirrService(
                config('telebirr.base_url'),
                config('telebirr.fabric_app_id'),
                config('telebirr.app_secret'),
                config('telebirr.merchant_app_id'),
                config('telebirr.merchant_code'),
                $privateKeyPath,
                $publicKeyPath
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/telebirr.php' => config_path('telebirr.php'),
        ], 'telebirr-config');

        // Publish keys directory
        $this->publishes([
            __DIR__.'/../keys' => storage_path('app/telebirr/keys'),
        ], 'telebirr-keys');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}