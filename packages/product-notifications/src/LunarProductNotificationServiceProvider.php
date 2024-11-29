<?php

namespace Dystcz\LunarProductNotification;

use Dystcz\LunarProductNotification\Domain\ProductNotifications\Models\ProductNotification;
use Dystcz\LunarProductNotification\Domain\ProductNotifications\Observers\ProductVariantObserver;
use Dystcz\LunarProductNotification\Domain\ProductNotifications\Policies\ProductNotificationPolicy;
use Illuminate\Support\ServiceProvider;
use Lunar\Models\ProductVariant;

class LunarProductNotificationServiceProvider extends ServiceProvider
{
    protected $policies = [
        ProductNotification::class => ProductNotificationPolicy::class,
    ];

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->registerDynamicRelations();

        ProductVariant::observe(ProductVariantObserver::class);

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/lunar-product-notifications.php' => config_path('lunar-product-notifications.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/lunar-product-notifications.php', 'lunar-product-notifications');
    }

    protected function registerDynamicRelations(): void
    {
        ProductVariant::resolveRelationUsing('notifications', function ($model) {
            return $model->morphMany(ProductNotification::class, 'purchasable');
        });
    }
}
