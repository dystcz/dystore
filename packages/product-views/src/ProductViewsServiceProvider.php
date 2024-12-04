<?php

namespace Dystore\ProductViews;

use Dystore\Api\Base\Extensions\SchemaExtension;
use Dystore\Api\Base\Facades\SchemaManifestFacade;
use Dystore\Api\Domain\Products\JsonApi\V1\ProductSchema;
use Dystore\ProductViews\Domain\Products\JsonApi\Sorts\RecentlyViewedSort;
use Illuminate\Support\ServiceProvider;

class ProductViewsServiceProvider extends ServiceProvider
{
    protected $root = __DIR__.'/..';

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->registerConfig();

        $this->extendSchemas();

        // Register the main class to use with the facade
        $this->app->singleton('dystore-product-views', function () {
            return new ProductViews;
        });
    }

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
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
            "{$this->root}/config/product-views.php",
            'dystore.product-views',
        );
    }

    /**
     * Publish config files.
     */
    protected function publishConfig(): void
    {
        $this->publishes([
            "{$this->root}/config/product-views.php" => config_path('dystore/product-views.php'),
        ], 'dystore-product-views');
    }

    /**
     * Extend schemas.
     */
    protected function extendSchemas(): void
    {
        /** @var SchemaExtension $productSchemaExtenstion */
        $productSchemaExtenstion = SchemaManifestFacade::extend(ProductSchema::class);

        $productSchemaExtenstion->setSortables([
            RecentlyViewedSort::make('recently_viewed'),
        ]);
    }
}
