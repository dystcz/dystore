<?php

namespace Dystore\ProductViews;

use Dystore\Api\Base\Facades\JsonApiManifest;
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
        // Register the main class to use with the facade
        $this->app->singleton('dystore-product-views', fn () => new ProductViews);

        $this->registerConfig();
        $this->extendSchemas();
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

    protected function publishConfig(): void
    {
        $this->publishes([
            "{$this->root}/config/product-views.php" => config_path('dystore/product-views.php'),
        ], 'dystore-product-views');
    }

    protected function extendSchemas(): void
    {
        JsonApiManifest::schema(ProductSchema::class)
            ->sortables()
            ->add(fn () => RecentlyViewedSort::make('recently_viewed'));
    }
}
