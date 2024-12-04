<?php

namespace Dystore\Stripe\Tests;

use Dystore\Stripe\Tests\Stubs\Carts\Modifiers\TestShippingModifier;
use Dystore\Stripe\Tests\Stubs\Lunar\TestTaxDriver;
use Dystore\Stripe\Tests\Stubs\Lunar\TestUrlGenerator;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use LaravelJsonApi\Testing\MakesJsonApiRequests;
use LaravelJsonApi\Testing\TestExceptionHandler;
use Lunar\Base\ShippingModifiers;
use Lunar\Facades\Taxes;
use Lunar\Models\Currency;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use MakesJsonApiRequests;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();

        Config::set('auth.providers.users', [
            'driver' => 'eloquent',
            'model' => \Dystore\Stripe\Tests\Stubs\Users\User::class,
        ]);

        Taxes::extend(
            'test',
            fn (Application $app) => $app->make(TestTaxDriver::class),
        );

        Currency::factory()->create([
            'code' => 'EUR',
            'decimal_places' => 2,
        ]);

        App::get(ShippingModifiers::class)->add(TestShippingModifier::class);

        activity()->disableLogging();
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
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

            // Lunar Stripe
            \Lunar\Stripe\StripePaymentsServiceProvider::class,

            // Livewire
            \Livewire\LivewireServiceProvider::class,

            // Lunar API
            \Dystcz\LunarApi\LunarApiServiceProvider::class,
            \Dystcz\LunarApi\JsonApiServiceProvider::class,

            // Stripe webhooks
            \Spatie\WebhookClient\WebhookClientServiceProvider::class,
            \Spatie\StripeWebhooks\StripeWebhooksServiceProvider::class,

            // Lunar API Stripe Adapter
            \Dystore\Stripe\LunarApiStripeAdapterServiceProvider::class,
        ];
    }

    /**
     * @param  Application  $app
     */
    public function getEnvironmentSetUp($app): void
    {
        $app->useEnvironmentPath(__DIR__.'/..');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);

        tap($app['config'], function (Repository $config) {
            /**
             * Lunar configuration
             */
            $config->set('lunar.urls.generator', TestUrlGenerator::class);
            $config->set('lunar.taxes.driver', 'test');

            /**
             * App configuration
             */
            $config->set('database.default', 'sqlite');
            $config->set('database.migrations', 'migrations');
            $config->set('database.connections.sqlite', [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]);

            $config->set('services.stripe', [
                'public_key' => env('STRIPE_PUBLIC_KEY'),
                'key' => env('STRIPE_SECRET_KEY'),
                'webhooks' => [
                    'payment_intent' => env('STRIPE_WEBHOOK_SECRET'),
                ],
            ]);
            $config->set('dystore.stripe.automatic_payment_methods', false);

            // Default payment driver
            $config->set('lunar.payments.default', 'stripe');
            $config->set('lunar.payments.types', [
                'stripe' => [
                    'driver' => 'stripe',
                    'authorized' => 'payment-received',
                ],
            ]);

            // Stripe webhooks
            $config->set('stripe-webhooks.verify_signature', false);
            $config->set('stripe-webhooks.connection', false);
        });

    }

    /**
     * Determine the Stripe signature.
     */
    protected function determineStripeSignature(array $payload, ?string $configKey = null): string
    {
        $secret = Config::get('services.stripe.webhooks.'.($configKey ?? 'payment_intent'));

        $timestamp = time();

        $timestampedPayload = $timestamp.'.'.json_encode($payload);

        $signature = hash_hmac('sha256', $timestampedPayload, $secret);

        return "t={$timestamp},v1={$signature}";
    }

    /**
     * Define database migrations.
     */
    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();
    }

    /**
     * Set up the database.
     */
    protected function setUpDatabase(): void
    {
        $migration = include __DIR__.'/../vendor/spatie/laravel-webhook-client/database/migrations/create_webhook_calls_table.php.stub';

        $migration->up();
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
