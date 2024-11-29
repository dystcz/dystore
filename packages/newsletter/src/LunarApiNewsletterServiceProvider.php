<?php

namespace Dystcz\LunarApiNewsletter;

use Illuminate\Support\ServiceProvider;

class LunarApiNewsletterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'lunar-api-newsletter');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/lunar-api-newsletter.php' => config_path('lunar-api-newsletter.php'),
            ], 'config');

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/lunar-api-newsletter'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/lunar-api-newsletter.php', 'lunar-api-newsletter');

        // Register the main class to use with the facade
        $this->app->singleton('lunar-api-newsletter', function () {
            return new LunarApiNewsletter();
        });
    }
}
