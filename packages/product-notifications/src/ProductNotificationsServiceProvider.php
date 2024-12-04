<?php

namespace Dystore\ProductNotifications;

use Dystcz\LunarApi\Base\Facades\SchemaManifestFacade;
use Dystcz\LunarApi\Support\Config\Collections\DomainConfigCollection;
use Dystore\ProductNotifications\Domain\ProductNotifications\JsonApi\V1\ProductNotificationSchema;
use Dystore\ProductNotifications\Domain\ProductNotifications\Models\ProductNotification;
use Dystore\ProductNotifications\Domain\ProductNotifications\Observers\ProductVariantObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Lunar\Models\ProductVariant;

class ProductNotificationsServiceProvider extends ServiceProvider
{
    protected $root = __DIR__.'/..';

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->registerConfig();

        $this->registerSchemas();

        $this->booting(function () {
            $this->registerPolicies();
        });

        $this->loadTranslationsFrom(
            "{$this->root}/lang",
            'dystore-product-notifications',
        );
    }

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->registerObservers();
        $this->registerDynamicRelations();

        $this->loadMigrationsFrom("{$this->root}/database/migrations");
        $this->loadRoutesFrom("{$this->root}/routes/api.php");

        if ($this->app->runningInConsole()) {
            $this->publishConfig();
        }
    }

    /**
     * Register config files.
     */
    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(
            "{$this->root}/config/product-notifications.php",
            'dystore.product-notifications',
        );
    }

    /**
     * Publish config files.
     */
    protected function publishConfig(): void
    {
        $this->publishes([
            "{$this->root}/config/product-notifications.php" => config_path('dystore/product-notifications.php'),
        ], 'dystore-product-notifications');
    }

    /**
     * Publish translations.
     */
    protected function publishTranslations(): void
    {
        $this->publishes([
            "{$this->root}/lang" => $this->app->langPath('vendor/dystore-product-notifications'),
        ], 'dystore-product-notifications.translations');
    }

    /**
     * Register schemas.
     */
    public function registerObservers(): void
    {
        ProductVariant::observe(ProductVariantObserver::class);
    }

    /**
     * Register schemas.
     */
    public function registerSchemas(): void
    {
        SchemaManifestFacade::registerSchema(ProductNotificationSchema::class);
    }

    /**
     * Register dynamic relations.
     */
    protected function registerDynamicRelations(): void
    {
        ProductVariant::resolveRelationUsing('notifications', function ($model) {
            return $model->morphMany(ProductNotification::class, 'purchasable');
        });
    }

    /**
     * Register the application's policies.
     */
    public function registerPolicies(): void
    {
        DomainConfigCollection::fromConfig('dystore.product-notifications.domains')
            ->getPolicies()
            ->each(
                fn (string $policy, string $model) => Gate::policy($model, $policy),
            );

    }
}
