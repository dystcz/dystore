<?php

namespace Dystore\Reviews;

use Dystore\Api\Base\Contracts\ResourceManifest;
use Dystore\Api\Base\Contracts\SchemaManifest;
use Dystore\Api\Base\Extensions\ResourceExtension;
use Dystore\Api\Base\Extensions\SchemaExtension;
use Dystore\Api\Base\Facades\SchemaManifest as SchemaManifestFacade;
use Dystore\Api\Domain\Products\JsonApi\V1\ProductResource;
use Dystore\Api\Domain\Products\JsonApi\V1\ProductSchema;
use Dystore\Api\Domain\ProductVariants\JsonApi\V1\ProductVariantResource;
use Dystore\Api\Domain\ProductVariants\JsonApi\V1\ProductVariantSchema;
use Dystore\Api\Support\Config\Collections\DomainConfigCollection;
use Dystore\Reviews\Domain\Reviews\JsonApi\V1\ReviewSchema;
use Dystore\Reviews\Domain\Reviews\Models\Review;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use LaravelJsonApi\Eloquent\Fields\Number;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;

class ReviewsServiceProvider extends ServiceProvider
{
    protected $root = __DIR__.'/..';

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->registerConfig();

        $this->loadTranslationsFrom(
            "{$this->root}/lang",
            'dystore-reviews',
        );

        $this->bindControllers();
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
        $this->loadViewsFrom(__DIR__.'/Domain/Hub/resources/views', 'dystore-reviews');
        $this->loadRoutesFrom("{$this->root}/routes/api.php");

        Relation::morphMap([
            'review' => Review::class,
        ]);

        if ($this->app->runningInConsole()) {
            $this->publishConfig();
            $this->publishTranslations();
            // $this->publishViews();
        }
    }

    /**
     * Register schemas.
     */
    public function registerSchemas(): void
    {
        SchemaManifestFacade::registerSchema(ReviewSchema::class);
    }

    /**
     * Bind controllers.
     */
    protected function bindControllers(): void
    {
        $controllers = [
            \Dystore\Reviews\Domain\Reviews\Contacts\ReviewsController::class => \Dystore\Reviews\Domain\Reviews\Http\Controllers\ReviewsController::class,
            \Dystore\Reviews\Domain\Reviews\Contacts\PublishReviewsController::class => \Dystore\Reviews\Domain\Reviews\Http\Controllers\PublishReviewsController::class,
        ];

        foreach ($controllers as $abstract => $concrete) {
            $this->app->bind($abstract, fn (Application $app) => $app->make($concrete));
        }
    }

    /**
     * Register the application's policies.
     */
    public function registerPolicies(): void
    {
        DomainConfigCollection::fromConfig('dystore.reviews.domains')
            ->getPolicies()
            ->each(
                fn (string $policy, string $model) => Gate::policy($model, $policy),
            );
    }

    /**
     * Register config files.
     */
    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(
            "{$this->root}/config/reviews.php",
            'dystore.reviews',
        );
    }

    /**
     * Publish config files.
     */
    protected function publishConfig(): void
    {
        $this->publishes([
            "{$this->root}/config/reviews.php" => config_path('dystore/reviews.php'),
        ], 'dystore-reviews');
    }

    /**
     * Publish translations.
     */
    protected function publishTranslations(): void
    {
        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/dystore-reviews'),
        ], 'dystore-reviews.translations');
    }

    /**
     * Register dynamic relations.
     */
    protected function registerDynamicRelations(): void
    {
        Product::resolveRelationUsing('reviews', function (Product $model) {
            return $model->morphMany(Review::class, 'purchasable');
        });

        ProductVariant::resolveRelationUsing('reviews', function (ProductVariant $model) {
            return $model->morphMany(Review::class, 'purchasable');
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
            ->setWith([
                'reviews',
            ])
            ->setIncludePaths([
                'reviews',
                'reviews.user',
                'reviews.user.customers',
                'variants.reviews',
                'variants.reviews.user',
                'variants.reviews.user.customers',
            ])
            ->setFields([
                fn () => Number::make('rating', 'review_rating'),
                fn () => Number::make('review_count')
                    ->extractUsing(
                        static fn ($model) => $model->relationLoaded('reviews')
                            ? $model->reviews->count()
                            : $model->reviews()->count(),
                    ),
                fn () => HasMany::make('reviews', 'reviews')
                    ->serializeUsing(
                        static fn ($relation) => $relation->withoutLinks(),
                    ),
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
                'reviews.user',
                'reviews.user.customers',
            ])
            ->setFields([
                fn () => HasMany::make('reviews', 'reviews')->serializeUsing(
                    static fn ($relation) => $relation->withoutLinks(),
                ),
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
}
