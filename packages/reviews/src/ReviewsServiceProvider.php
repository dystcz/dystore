<?php

namespace Dystore\Reviews;

use Dystore\Api\Base\Facades\JsonApiManifest;
use Dystore\Api\Domain\Products\JsonApi\V1\ProductResource;
use Dystore\Api\Domain\Products\JsonApi\V1\ProductSchema;
use Dystore\Api\Domain\ProductVariants\JsonApi\V1\ProductVariantResource;
use Dystore\Api\Domain\ProductVariants\JsonApi\V1\ProductVariantSchema;
use Dystore\Api\Support\Config\Collections\DomainConfigCollection;
use Dystore\Reviews\Domain\Reviews\JsonApi\V1\ReviewResource;
use Dystore\Reviews\Domain\Reviews\JsonApi\V1\ReviewSchema;
use Dystore\Reviews\Domain\Reviews\Models\Review;
use Dystore\Reviews\Domain\Reviews\Observers\ReviewObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use LaravelJsonApi\Eloquent\Fields\Number;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Fields\Relations\HasManyThrough;
use LaravelJsonApi\Eloquent\Resources\Relation;
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

        $this->booting(function () {
            $this->registerPolicies();
        });

        JsonApiManifest::addSchema(ReviewSchema::class);
        JsonApiManifest::addResource(ReviewResource::class);

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

        Review::observe(ReviewObserver::class);

        if ($this->app->runningInConsole()) {
            $this->publishConfig();
            $this->publishTranslations();
            // $this->publishViews();
        }
    }

    public function registerPolicies(): void
    {
        DomainConfigCollection::fromConfig('dystore.reviews.domains')
            ->getPolicies()
            ->each(fn (string $policy, string $model) => Gate::policy($model, $policy));
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(
            "{$this->root}/config/reviews.php",
            'dystore.reviews',
        );
    }

    protected function publishConfig(): void
    {
        $this->publishes([
            "{$this->root}/config/reviews.php" => config_path('dystore/reviews.php'),
        ], 'dystore-reviews');
    }

    protected function publishTranslations(): void
    {
        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/dystore-reviews'),
        ], 'dystore-reviews.translations');
    }

    protected function registerDynamicRelations(): void
    {
        Product::resolveRelationUsing('variantReviews', function ($model) {
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
        $productSchema = JsonApiManifest::schema(ProductSchema::class);

        $productSchema->with()->add('reviews');
        $productSchema->includePaths()->push(...[
            'reviews',
            'reviews.user',
            'reviews.user.customers',
            'variants.reviews',
            'variants.reviews.user',
            'variants.reviews.user.customers',
        ]);
        $productSchema->fields()->push(...[
            Number::make('review_count')
                ->extractUsing(
                    static fn (Product $model) => $model->relationLoaded('reviews')
                        ? $model->reviews->count()
                        : $model->reviews()->count(),
                ),
            HasManyThrough::make('reviews')->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),
        ]);
        $productSchema->showRelated()->add('reviews');
        $productSchema->showRelationships()->add('reviews');

        JsonApiManifest::resource(ProductResource::class)
            ->relationships()
            ->add(fn (ProductResource $resource) => $resource->relation('reviews'));

        $variantSchema = JsonApiManifest::schema(ProductVariantSchema::class);

        $variantSchema->includePaths()->push(...[
            'reviews',
            'reviews.user',
            'reviews.user.customers',
        ]);

        $variantSchema->fields()->push(...[
            HasMany::make('reviews')
                ->serializeUsing(
                    static fn ($relation) => $relation->withoutLinks(),
                ),
        ]);

        $variantSchema->showRelated()->add('reviews');
        $variantSchema->showRelationships()->add('reviews');

        $variantResource = JsonApiManifest::resource(ProductVariantResource::class);

        $variantResource
            ->relationships()
            ->add(fn (ProductVariantResource $resource) => $resource->relation('reviews'));
    }
}
