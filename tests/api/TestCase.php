<?php

namespace Dystore\Api\Tests;

use Dystore\Api\Domain\PaymentOptions\Modifiers\PaymentModifiers;
use Dystore\Api\Facades\Api;
use Dystore\Api\Tests\Stubs\Carts\Modifiers\TestPaymentModifier;
use Dystore\Api\Tests\Stubs\Carts\Modifiers\TestShippingModifier;
use Dystore\Api\Tests\Stubs\Lunar\TestTaxDriver;
use Dystore\Api\Tests\Stubs\Lunar\TestUrlGenerator;
use Dystore\Api\Tests\Traits\JsonApiTestHelpers;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Support\Facades\App;
use LaravelJsonApi\Testing\MakesJsonApiRequests;
use LaravelJsonApi\Testing\TestExceptionHandler;
use Lunar\Base\ShippingModifiers;
use Lunar\Facades\Taxes;
use Lunar\Models\Channel;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\TaxClass;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    use JsonApiTestHelpers;
    use MakesJsonApiRequests;
    use WithWorkbench;

    protected function setUp(): void
    {
        parent::setUp();

        Taxes::extend(
            'test',
            fn (Application $app) => $app->make(TestTaxDriver::class),
        );

        Currency::factory()->create([
            'code' => 'EUR',
            'decimal_places' => 2,
            'default' => true,
        ]);

        Country::factory()->create([
            'name' => 'United Kingdom',
            'iso3' => 'GBR',
            'iso2' => 'GB',
            'phonecode' => '+44',
            'capital' => 'London',
            'currency' => 'GBP',
            'native' => 'English',
        ]);

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
     * @param  Application  $app
     */
    protected function getPackageProviders($app): array
    {
        return [
            // Ray
            \Spatie\LaravelRay\RayServiceProvider::class,

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

            // Livewire
            \Livewire\LivewireServiceProvider::class,

            // Lunar Api
            \Dystore\Api\LunarApiServiceProvider::class,
            \Dystore\Api\JsonApiServiceProvider::class,

            // Hashids
            \Vinkla\Hashids\HashidsServiceProvider::class,
            \Dystore\Api\ApiHashidsServiceProvider::class,
        ];
    }

    /**
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $app->useEnvironmentPath(__DIR__.'/..');
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

    /**
     * Set the currently logged in user for the application.
     *
     * @param  string|null  $guard
     * @return $this
     */
    public function actingAs(User $user, $guard = null): TestCase
    {
        return $this->be($user, $guard ?? Api::getAuthGuard());
    }
}
