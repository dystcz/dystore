<?php

namespace Dystore\Tests\ProductViews;

use Dystore\Api\ApiHashidsServiceProvider;
use Dystore\Api\ApiServiceProvider;
use Dystore\Api\JsonApiServiceProvider;
use Dystore\ProductViews\ProductViewsServiceProvider;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Support\Facades\Redis;
use Kalnoy\Nestedset\NestedSetServiceProvider;
use LaravelJsonApi\Laravel\ServiceProvider;
use LaravelJsonApi\Testing\MakesJsonApiRequests;
use LaravelJsonApi\Testing\TestExceptionHandler;
use Livewire\LivewireServiceProvider;
use Lunar\Database\Factories\LanguageFactory;
use Lunar\LunarServiceProvider;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Spatie\LaravelBlink\BlinkServiceProvider;
use Spatie\LaravelRay\RayServiceProvider;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;
use Vinkla\Hashids\HashidsServiceProvider;

abstract class TestCase extends Orchestra
{
    use MakesJsonApiRequests;
    use WithWorkbench;

    protected function setUp(): void
    {
        parent::setUp();

        LanguageFactory::new()->create([
            'code' => 'en',
            'name' => 'English',
        ]);

        activity()->disableLogging();
    }

    /**
     * Clean up the testing environment before the next test.
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        // Clean up after each test
        Redis::flushall();
    }

    /**
     * @param  Application  $app
     */
    protected function getPackageProviders($app): array
    {
        return [
            // Ray
            RayServiceProvider::class,

            // Laravel JsonApi
            \LaravelJsonApi\Encoder\Neomerx\ServiceProvider::class,
            ServiceProvider::class,
            \LaravelJsonApi\Spec\ServiceProvider::class,

            // Lunar core
            LunarServiceProvider::class,
            MediaLibraryServiceProvider::class,
            ActivitylogServiceProvider::class,
            NestedSetServiceProvider::class,
            BlinkServiceProvider::class,

            // Livewire
            LivewireServiceProvider::class,

            // Lunar Api
            ApiServiceProvider::class,
            JsonApiServiceProvider::class,

            // Hashids
            HashidsServiceProvider::class,
            ApiHashidsServiceProvider::class,

            // Lunar API product views
            ProductViewsServiceProvider::class,
        ];
    }

    /**
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $app->useEnvironmentPath(__DIR__.'/../..');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);

        tap($app['config'], function (Repository $config) {
            /**
             * App configuration.
             */
            $config->set('queue.default', 'sync');

            $config->set('auth.providers.users', [
                'driver' => 'eloquent',
                'model' => Stubs\Users\User::class,
            ]);

            $config->set('database.default', 'sqlite');
            $config->set('database.migrations', 'migrations');
            $config->set('database.connections.sqlite', [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]);
        });
    }

    /**
     * Define database migrations.
     */
    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();
        // $this->loadMigrationsFrom(workbench_path('database/migrations'));
    }

    protected function resolveApplicationExceptionHandler($app): void
    {
        $app->singleton(
            ExceptionHandler::class,
            TestExceptionHandler::class
        );
    }
}
