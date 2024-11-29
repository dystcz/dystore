<?php

namespace Dystcz\LunarApiProductViews;

use Dystcz\LunarApi\Base\Extensions\SchemaExtension;
use Dystcz\LunarApi\Base\Facades\SchemaManifestFacade;
use Dystcz\LunarApi\Domain\Products\JsonApi\V1\ProductSchema;
use Dystcz\LunarApiProductViews\Domain\Products\JsonApi\Sorts\RecentlyViewedSort;
use Illuminate\Support\ServiceProvider;

class LunarApiProductViewsServiceProvider extends ServiceProvider
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
        $this->app->singleton('lunar-api-product-views', function () {
            return new LunarApiProductViews;
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
            'lunar-api.product-views',
        );
    }

    /**
     * Publish config files.
     */
    protected function publishConfig(): void
    {
        $this->publishes([
            "{$this->root}/config/product-views.php" => config_path('lunar-api.product-views.php'),
        ], 'lunar-api-product-views');
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
