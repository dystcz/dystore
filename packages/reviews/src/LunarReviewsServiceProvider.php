<?php

namespace Dystcz\LunarApiReviews;

use Dystcz\LunarApi\Base\Contracts\ResourceManifest;
use Dystcz\LunarApi\Base\Contracts\SchemaManifest;
use Dystcz\LunarApi\Base\Extensions\ResourceExtension;
use Dystcz\LunarApi\Base\Extensions\SchemaExtension;
use Dystcz\LunarApi\Base\Facades\SchemaManifestFacade;
use Dystcz\LunarApi\Domain\Products\JsonApi\V1\ProductResource;
use Dystcz\LunarApi\Domain\Products\JsonApi\V1\ProductSchema;
use Dystcz\LunarApi\Domain\ProductVariants\JsonApi\V1\ProductVariantResource;
use Dystcz\LunarApi\Domain\ProductVariants\JsonApi\V1\ProductVariantSchema;
use Dystcz\LunarApi\Support\Config\Collections\DomainConfigCollection;
use Dystcz\LunarApiReviews\Domain\Hub\Components\Slots\ReviewsSlot;
use Dystcz\LunarApiReviews\Domain\Reviews\JsonApi\V1\ReviewSchema;
use Dystcz\LunarApiReviews\Domain\Reviews\Models\Review;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Fields\Relations\HasManyThrough;
use Livewire\Livewire;
use Lunar\Hub\Facades\Slot;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;

class LunarReviewsServiceProvider extends ServiceProvider
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

        $this->registerDynamicRelations();

        $this->extendSchemas();
    }

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom("{$this->root}/database/migrations");
        $this->loadViewsFrom(__DIR__.'/Domain/Hub/resources/views', 'lunar-api-reviews');
        $this->loadRoutesFrom("{$this->root}/routes/api.php");

        Livewire::component('lunar-api-reviews::reviews-slot', ReviewsSlot::class);

        Slot::register('product.show', ReviewsSlot::class);

        if ($this->app->runningInConsole()) {
            $this->publishConfig();
            $this->publishViews();
        }
    }

    /**
     * Register config files.
     */
    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(
            "{$this->root}/config/reviews.php",
            'lunar-api.reviews',
        );
    }

    /**
     * Publish config files.
     */
    protected function publishConfig(): void
    {
        $this->publishes([
            "{$this->root}/config/reviews.php" => config_path('lunar-api.reviews.php'),
        ], 'lunar-api-reviews');
    }

    /**
     * Register schemas.
     */
    public function registerSchemas(): void
    {
        SchemaManifestFacade::registerSchema(ReviewSchema::class);
    }

    /**
     * Publish views.
     */
    protected function publishViews(): void
    {
        $this->publishes([
            __DIR__.'/Domain/Hub/resources/views' => resource_path('views/vendor/lunar-api-reviews'),
        ], 'views');
    }

    /**
     * Register dynamic relations.
     */
    protected function registerDynamicRelations(): void
    {
        ProductVariant::resolveRelationUsing('reviews', function ($model) {
            return $model->morphMany(Review::class, 'purchasable');
        });

        Product::resolveRelationUsing('reviews', function ($model) {
            return $model
                ->hasManyThrough(
                    Review::class,
                    ProductVariant::class,
                    'product_id',
                    'purchasable_id'
                )
                ->where(
                    'purchasable_type',
                    ProductVariant::class
                );
        });
    }

    /**
     * Extend schemas.
     */
    protected function extendSchemas(): void
    {
        /** @var SchemaManifest $schemaManifest */
        $schemaManifest = $this->app->make(SchemaManifest::class);

        /** @var ResourceManifest $resourceManifest */
        $resourceManifest = $this->app->make(ResourceManifest::class);

        /** @var SchemaExtension $productSchemaExtenstion */
        $productSchemaExtenstion = $schemaManifest::extend(ProductSchema::class);

        $productSchemaExtenstion
            ->setIncludePaths([
                'reviews',
                'variants.reviews',
            ])
            ->setFields([
                HasManyThrough::make('reviews'),
            ])
            ->setShowRelated([
                'reviews',
            ])
            ->setShowRelationship([
                'reviews',
            ]);

        /** @var ResourceExtension $productResourceExtension */
        $productResourceExtension = $resourceManifest::extend(ProductResource::class);

        $productResourceExtension
            ->setRelationships(fn ($resource) => [
                $resource->relation('reviews'),
            ]);

        /** @var SchemaExtension $productVariantSchemaExtenstion */
        $productVariantSchemaExtenstion = $schemaManifest::extend(ProductVariantSchema::class);

        $productVariantSchemaExtenstion
            ->setIncludePaths([
                'reviews',
            ])
            ->setFields([
                HasMany::make('reviews'),
            ])
            ->setShowRelated([
                'reviews',
            ])
            ->setShowRelationship([
                'reviews',
            ]);

        /** @var ResourceExtension $productVariantResourceExtension */
        $productVariantResourceExtension = $resourceManifest::extend(ProductVariantResource::class);

        $productVariantResourceExtension
            ->setRelationships(fn ($resource) => [
                'reviews' => $resource->relation('reviews'),
            ]);
    }

    /**
     * Register the application's policies.
     */
    public function registerPolicies(): void
    {
        DomainConfigCollection::fromConfig('lunar-api.reviews.domains')
            ->getPolicies()
            ->each(
                fn (string $policy, string $model) => Gate::policy($model, $policy),
            );
    }
}
