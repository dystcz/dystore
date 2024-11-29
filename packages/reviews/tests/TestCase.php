<?php

namespace Dystcz\LunarApiReviews\Tests;

use Dystcz\LunarApi\Base\Facades\SchemaManifestFacade;
use Dystcz\LunarApiReviews\Domain\Reviews\Models\Review;
use Dystcz\LunarApiReviews\Tests\Stubs\Lunar\TestUrlGenerator;
use Dystcz\LunarApiReviews\Tests\Stubs\Users\User;
use Dystcz\LunarApiReviews\Tests\Stubs\Users\UserSchema;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;
use LaravelJsonApi\Testing\MakesJsonApiRequests;
use LaravelJsonApi\Testing\TestExceptionHandler;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use MakesJsonApiRequests;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('auth.providers.users', [
            'driver' => 'eloquent',
            'model' => User::class,
        ]);

        Config::set('lunar.urls.generator', TestUrlGenerator::class);

        activity()->disableLogging();
    }

    /**
     * @param  Application  $app
     */
    protected function getPackageProviders($app): array
    {
        return [
            // Ray
            \Spatie\LaravelRay\RayServiceProvider::class,

            // Spatie Permissions
            \Spatie\Permission\PermissionServiceProvider::class,

            // Laravel JsonApi
            \LaravelJsonApi\Encoder\Neomerx\ServiceProvider::class,
            \LaravelJsonApi\Laravel\ServiceProvider::class,
            \LaravelJsonApi\Spec\ServiceProvider::class,

            // Lunar core
            \Lunar\LunarServiceProvider::class,
            \Spatie\MediaLibrary\MediaLibraryServiceProvider::class,
            \Spatie\Activitylog\ActivitylogServiceProvider::class,
            \Cartalyst\Converter\Laravel\ConverterServiceProvider::class,
            \Kalnoy\Nestedset\NestedSetServiceProvider::class,
            \Spatie\LaravelBlink\BlinkServiceProvider::class,

            // Lunar Api
            \Dystcz\LunarApi\LunarApiServiceProvider::class,
            \Dystcz\LunarApi\JsonApiServiceProvider::class,

            // Livewire
            \Lunar\LivewireTables\LivewireTablesServiceProvider::class,
            \Livewire\LivewireServiceProvider::class,

            // Lunar Hub
            \Lunar\Hub\AdminHubServiceProvider::class,

            // Lunar Reviews
            \Dystcz\LunarApiReviews\LunarReviewsServiceProvider::class,
        ];
    }

    /**
     * @param  Application  $app
     */
    public function getEnvironmentSetUp($app): void
    {
        /**
         * App configuration
         */
        Config::set('database.default', 'sqlite');
        Config::set('database.migrations', 'migrations');

        Config::set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        ProductVariant::resolveRelationUsing('reviews', function ($model) {
            return $model->morphMany(Review::class, 'purchasable');
        });

        Product::resolveRelationUsing('reviews', function ($model) {
            return $model->morphMany(Review::class, 'purchasable');
        });

        /**
         * Schema configuration.
         */
        SchemaManifestFacade::registerSchema(UserSchema::class);
        // SchemaManifestFacade::registerSchema(ProductVariantSchema::class);
    }

    /**
     * Resolve application HTTP exception handler implementation.
     */
    protected function resolveApplicationExceptionHandler($app): void
    {
        $app->singleton(
            ExceptionHandler::class,
            TestExceptionHandler::class
        );
    }

    /**
     * Define database migrations.
     */
    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();
    }
}
