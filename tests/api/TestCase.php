<?php

namespace Dystore\Tests\Api;

use Cartalyst\Converter\Laravel\ConverterServiceProvider;
use Dystore\Api\ApiHashidsServiceProvider;
use Dystore\Api\ApiServiceProvider;
use Dystore\Api\Domain\PaymentOptions\Modifiers\PaymentModifiers;
use Dystore\Api\Facades\Api;
use Dystore\Api\JsonApiServiceProvider;
use Dystore\Tests\Api\Stubs\Carts\Modifiers\TestPaymentModifier;
use Dystore\Tests\Api\Stubs\Carts\Modifiers\TestShippingModifier;
use Dystore\Tests\Api\Stubs\Lunar\TestTaxDriver;
use Dystore\Tests\Api\Stubs\Lunar\TestUrlGenerator;
use Dystore\Tests\Api\Traits\JsonApiTestHelpers;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Support\Facades\App;
use Kalnoy\Nestedset\NestedSetServiceProvider;
use LaravelJsonApi\Laravel\ServiceProvider;
use LaravelJsonApi\Testing\MakesJsonApiRequests;
use LaravelJsonApi\Testing\TestExceptionHandler;
use Livewire\LivewireServiceProvider;
use Lunar\Base\ShippingModifiers;
use Lunar\Facades\Taxes;
use Lunar\LunarServiceProvider;
use Lunar\Models\Channel;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\TaxClass;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Spatie\LaravelBlink\BlinkServiceProvider;
use Spatie\LaravelRay\RayServiceProvider;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;
use Vinkla\Hashids\HashidsServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    use JsonApiTestHelpers;
    use MakesJsonApiRequests;
    use WithWorkbench;

    protected function setUp(): void
    {
        parent::setUp();

        // Model::preventLazyLoading(! app()->isProduction());

        Taxes::extend(
            'test',
            fn (Application $app) => $app->make(TestTaxDriver::class),
        );

        Currency::factory()->create([
            'code' => 'EUR',
            'decimal_places' => 2,
            'default' => true,
        ]);

        // For test stability, make the required GB country deterministic.
        Country::updateOrCreate(
            ['iso2' => 'GB'],
            [
                'name' => 'United Kingdom',
                'iso3' => 'GBR',
                'phonecode' => '+44',
                'capital' => 'London',
                'currency' => 'GBP',
                'native' => 'en',
                'emoji' => '🇬🇧',
                'emoji_u' => 'U+1F1EC U+1F1E7',
            ]
        );

        Channel::factory()->create([
            'default' => true,
        ]);

        CustomerGroup::factory()->create([
            'default' => true,
        ]);

        TaxClass::factory()->create();

        App::get(ShippingModifiers::class)->add(TestShippingModifier::class);
        App::get(PaymentModifiers::class)->add(TestPaymentModifier::class);

        activity()->disableLogging();
    }

    /**
     * Set the currently logged in user for the application.
     *
     * @param  string|null  $guard
     * @return $this
     */
    public function actingAs(User $user, $guard = null): self
    {
        return $this->be($user, $guard ?? Api::getAuthGuard());
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
            ConverterServiceProvider::class,
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
             * Lunar configuration.
             */
            $config->set('lunar.cart_session.auto_create', true);
            $config->set('lunar.payments.default', 'offline');
            $config->set('lunar.urls.generator', TestUrlGenerator::class);
            $config->set('lunar.taxes.driver', 'test');

            /**
             * App configuration.
             */
            $config->set('auth.providers.users', [
                'driver' => 'eloquent',
                'model' => \Dystore\Api\Domain\Users\Models\User::class,
            ]);

            $config->set('auth.passwords.users', [
                'provider' => 'users',
                'table' => 'password_resets',
                'expire' => 60,
                'throttle' => 60,
            ]);

            // $config->set('auth.defaults', [
            //     'guard' => 'api',
            //     'passwords' => 'users',
            // ]);
            // $config->set('auth.guards.api', [
            //     'driver' => 'session',
            //     'provider' => 'users',
            // ]);

            $config->set('database.default', 'sqlite');
            $config->set('database.migrations', 'migrations');
            $config->set('database.connections.sqlite', [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]);

            $config->set('database.connections.mysql', [
                'driver' => 'mysql',
                'host' => 'mysql',
                'port' => '3306',
                'database' => 'dystore-testing',
                'username' => 'homestead',
                'password' => 'secret',
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
}
