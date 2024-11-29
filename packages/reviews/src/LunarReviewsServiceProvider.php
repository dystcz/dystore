<?php

namespace Dystcz\LunarApiReviews;

use Dystcz\LunarApi\Domain\JsonApi\Extensions\Contracts\ResourceManifest;
use Dystcz\LunarApi\Domain\JsonApi\Extensions\Contracts\SchemaManifest;
use Dystcz\LunarApi\Domain\JsonApi\Extensions\Resource\ResourceExtension;
use Dystcz\LunarApi\Domain\JsonApi\Extensions\Schema\SchemaExtension;
use Dystcz\LunarApi\Domain\Products\JsonApi\V1\ProductResource;
use Dystcz\LunarApi\Domain\Products\JsonApi\V1\ProductSchema;
use Dystcz\LunarApi\Domain\ProductVariants\JsonApi\V1\ProductVariantResource;
use Dystcz\LunarApi\Domain\ProductVariants\JsonApi\V1\ProductVariantSchema;
use Dystcz\LunarApiReviews\Domain\Hub\Components\Slots\ReviewsSlot;
use Dystcz\LunarApiReviews\Domain\Reviews\Models\Review;
use Dystcz\LunarApiReviews\Domain\Reviews\Policies\ReviewPolicy;
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
    protected $policies = [
        Review::class => ReviewPolicy::class,
    ];

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/Domain/Hub/resources/views', 'lunar-api-reviews');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        Livewire::component('lunar-api-reviews::reviews-slot', ReviewsSlot::class);

        Slot::register('product.show', ReviewsSlot::class);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/lunar-api-reviews.php' => config_path('lunar-api-reviews.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/Domain/Hub/resources/views' => resource_path('views/vendor/lunar-api-reviews'),
            ], 'views');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/lunar-api-reviews.php', 'lunar-api-reviews');

        $this->booting(function () {
            $this->registerPolicies();
        });

        $this->registerDynamicRelations();
        $this->extendSchemas();
    }

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

    protected function extendSchemas(): void
    {
        $schemaManifest = $this->app->make(SchemaManifest::class);
        $resourceManifest = $this->app->make(ResourceManifest::class);

        /** @var SchemaExtension $productSchemaExtenstion */
        $productSchemaExtenstion = $schemaManifest::for(ProductSchema::class);

        $productSchemaExtenstion
            ->setIncludePaths(['reviews', 'variants.reviews'])
            ->setFields([
                HasManyThrough::make('reviews'),
            ])
            ->setShowRelated(['reviews'])
            ->setShowRelationship(['reviews']);

        /** @var ResourceExtension $productResourceExtension */
        $productResourceExtension = $resourceManifest::for(ProductResource::class);

        $productResourceExtension
            ->setRelationships(fn ($resource) => [
                $resource->relation('reviews'),
            ]);

        /** @var SchemaExtension $productVariantSchemaExtenstion */
        $productVariantSchemaExtenstion = $schemaManifest::for(ProductVariantSchema::class);

        $productVariantSchemaExtenstion
            ->setIncludePaths(['reviews'])
            ->setFields([
                HasMany::make('reviews'),
            ])
            ->setShowRelated(['reviews'])
            ->setShowRelationship(['reviews']);

        /** @var ResourceExtension $productVariantResourceExtension */
        $productVariantResourceExtension = $resourceManifest::for(ProductVariantResource::class);

        $productVariantResourceExtension
            ->setRelationships(fn ($resource) => [
                'reviews' => $resource->relation('reviews'),
            ]);
    }

    /**
     * Register the application's policies.
     *
     * @return void
     */
    public function registerPolicies()
    {
        foreach ($this->policies() as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    /**
     * Get the policies defined on the provider.
     *
     * @return array<class-string, class-string>
     */
    public function policies()
    {
        return $this->policies;
    }
}
