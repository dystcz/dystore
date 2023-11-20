<?php

namespace Dystcz\LunarApiStripeAdapter\Tests;

use Dystcz\LunarApiStripeAdapter\Tests\Stubs\Carts\Modifiers\TestShippingModifier;
use Dystcz\LunarApiStripeAdapter\Tests\Stubs\JsonApi\V1\Server;
use Dystcz\LunarApiStripeAdapter\Tests\Stubs\Lunar\TestTaxDriver;
use Dystcz\LunarApiStripeAdapter\Tests\Stubs\Lunar\TestUrlGenerator;
use Dystcz\LunarApiStripeAdapter\Tests\Stubs\Users\User;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use LaravelJsonApi\Testing\MakesJsonApiRequests;
use Lunar\Base\ShippingModifiers;
use Lunar\Facades\Taxes;
use Lunar\Models\Channel;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\TaxClass;
use Orchestra\Testbench\TestCase as Orchestra;

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

            // Lunar Stripe
            \Lunar\Stripe\StripePaymentsServiceProvider::class,

            // Livewire
            \Livewire\LivewireServiceProvider::class,

            // Lunar API
            \Dystcz\LunarApi\LunarApiServiceProvider::class,
            \Dystcz\LunarApi\JsonApiServiceProvider::class,

            // Lunar API Stripe Adapter
            \Dystcz\LunarApiStripeAdapter\LunarApiStripeAdapterServiceProvider::class,
        ];
    }

    /**
     * @param  Application  $app
     */
    public function getEnvironmentSetUp($app): void
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

        Config::set('services.stripe', [
            'public_key' => env('STRIPE_PUBLIC_KEY'),
            'key' => env('STRIPE_SECRET_KEY'),
            'webhooks' => [
                'payment_intent' => env('STRIPE_WEBHOOK_SECRET'),
            ],
        ]);

        // Default payment driver
        Config::set('lunar.payments.default', 'stripe');
        Config::set('lunar.payments.types', [
            'stripe' => [
                'driver' => 'stripe',
                'authorized' => 'payment-stripe',
            ],
        ]);
    }

    /**
     * Define database migrations.
     */
    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();
    }
}
