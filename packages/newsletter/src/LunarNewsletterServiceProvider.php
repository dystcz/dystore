<?php

namespace Dystcz\LunarNewsletter;

use Illuminate\Support\ServiceProvider;

class LunarNewsletterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'lunar-newsletter');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/lunar-newsletter.php' => config_path('lunar-newsletter.php'),
            ], 'config');

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/lunar-newsletter'),
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
        $this->mergeConfigFrom(__DIR__.'/../config/lunar-newsletter.php', 'lunar-newsletter');

        // Register the main class to use with the facade
        $this->app->singleton('lunar-newsletter', function () {
            return new LunarNewsletter();
        });
    }
}
