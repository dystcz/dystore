<?php

namespace Dystcz\LunarApiPaypalAdapter\Tests;

use Dystcz\LunarApi\JsonApiServiceProvider;
use Dystcz\LunarApi\LunarApiServiceProvider;
use Dystcz\LunarApiPaypalAdapter\LunarApiPaypalAdapterServiceProvider;
use Dystcz\LunarApiPaypalAdapter\Tests\Stubs\Carts\Modifiers\TestShippingModifier;
use Dystcz\LunarApiPaypalAdapter\Tests\Stubs\JsonApi\V1\Server;
use Dystcz\LunarApiPaypalAdapter\Tests\Stubs\Lunar\TestTaxDriver;
use Dystcz\LunarApiPaypalAdapter\Tests\Stubs\Lunar\TestUrlGenerator;
use Dystcz\LunarApiPaypalAdapter\Tests\Stubs\Users\User;
use Dystcz\LunarPaypal\LunarPaypalServiceProvider;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use LaravelJsonApi\Testing\MakesJsonApiRequests;
use Livewire\LivewireServiceProvider;
use Lunar\Base\ShippingModifiers;
use Lunar\Facades\Taxes;
use Lunar\Models\Channel;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\TaxClass;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\LaravelData\LaravelDataServiceProvider;

class TestCase extends Orchestra
{
    use MakesJsonApiRequests;

    protected function setUp(): void
    {
        parent::setUp();

        Taxes::extend('test', function ($app) {
            return $app->make(TestTaxDriver::class);
        });

        Currency::factory()->create([
            'code' => 'EUR',
            'decimal_places' => 2,
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

        activity()->disableLogging();
    }

    protected function getPackageProviders($app)
    {
        return [
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

            // Lunar Paypal
            LunarPaypalServiceProvider::class,

            // Livewire
            LivewireServiceProvider::class,

            // Laravel Data
            LaravelDataServiceProvider::class,

            // Lunar API
            LunarApiServiceProvider::class,
            JsonApiServiceProvider::class,

            // Lunar API Paypal Adapter
            LunarApiPaypalAdapterServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app->useEnvironmentPath(__DIR__.'/..');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);

        Config::set('auth.providers.users', [
            'driver' => 'eloquent',
            'model' => User::class,
        ]);

        /**
         * Lunar configuration
         */
        Config::set('lunar-api.additional_servers', [
            Server::class,
        ]);
        Config::set('lunar.urls.generator', TestUrlGenerator::class);
        Config::set('lunar.taxes.driver', 'test');

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

        // Default payment driver
        Config::set('lunar.payments.default', 'paypal');
        Config::set('lunar.payments.types', [
            'paypal' => [
                'driver' => 'paypal',
                'authorized' => 'payment-stripe',
            ],
        ]);

        Config::set('lunar.paypal', require __DIR__.'/../vendor/dystcz/lunar-paypal/config/paypal.php');
    }

    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations()
    {
        $this->loadLaravelMigrations();
    }
}
