<?php

namespace Dystore\Stripe;

use Dystore\Stripe\Managers\StripeManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class StripeServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register(): void
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/stripe.php', 'dystore.stripe');
        $this->mergeConfigFrom(__DIR__.'/../config/stripe-webhooks.php', 'stripe-webhooks');
    }

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->app->singleton(
            'lunar:stripe',
            fn (Application $app) => $app->make(StripeManager::class),
        );

        $this->loadRoutesFrom(__DIR__.'/../routes/webhooks.php');

        StripePaymentAdapter::register();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/stripe.php' => config_path('dystore/stripe.php'),
            ], 'dystore.stripe');

            $this->publishes([
                __DIR__.'/../config/stripe-webhooks.php' => config_path('stripe-webhooks.php'),
            ], 'dystore.stripe');
        }
    }
}
