<?php

namespace Dystore\Tests\Stripe;

use Cartalyst\Converter\Laravel\ConverterServiceProvider;
use Dystore\Api\ApiServiceProvider;
use Dystore\Api\JsonApiServiceProvider;
use Dystore\Stripe\Jobs\Webhooks\HandleOtherEvent;
use Dystore\Stripe\Jobs\Webhooks\HandlePaymentIntentCanceled;
use Dystore\Stripe\Jobs\Webhooks\HandlePaymentIntentCreated;
use Dystore\Stripe\Jobs\Webhooks\HandlePaymentIntentFailed;
use Dystore\Stripe\Jobs\Webhooks\HandlePaymentIntentSucceeded;
use Dystore\Stripe\Jobs\Webhooks\WebhookProfile;
use Dystore\Stripe\StripeServiceProvider;
use Dystore\Tests\Api\Stubs\Carts\Modifiers\TestShippingModifier;
use Dystore\Tests\Api\Stubs\Lunar\TestTaxDriver;
use Dystore\Tests\Api\Stubs\Lunar\TestUrlGenerator;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Kalnoy\Nestedset\NestedSetServiceProvider;
use LaravelJsonApi\Testing\MakesJsonApiRequests;
use LaravelJsonApi\Testing\TestExceptionHandler;
use Livewire\LivewireServiceProvider;
use Lunar\Base\ShippingModifiers;
use Lunar\Facades\Taxes;
use Lunar\LunarServiceProvider;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Stripe\StripePaymentsServiceProvider;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Spatie\LaravelBlink\BlinkServiceProvider;
use Spatie\LaravelRay\RayServiceProvider;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;
use Spatie\StripeWebhooks\StripeWebhooksServiceProvider;
use Spatie\WebhookClient\WebhookClientServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
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
        ]);

        CustomerGroup::factory()->create([
            'default' => true,
        ]);

        App::get(ShippingModifiers::class)->add(TestShippingModifier::class);

        Artisan::call('vendor:publish', [
            '--provider' => 'Spatie\WebhookClient\WebhookClientServiceProvider',
            '--tag' => 'webhook-client-migrations',
        ]);

        activity()->disableLogging();
    }

    /**
     * @param  Application  $app
     */
    public function getEnvironmentSetUp($app): void
    {
        $app->useEnvironmentPath(__DIR__.'/../..');
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
                    'lunar' => env('STRIPE_WEBHOOK_SECRET'),
                ],
            ]);
            $config->set('dystore.stripe.automatic_payment_methods', false);
            $config->set('dystore.stripe.eshop_id', 'Dystore');
            $config->set('dystore.stripe.handle_eshop_ids', ['*']);

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
            $config->set('stripe-webhooks.connection', 'sync');
            $config->set('stripe-webhooks.default_job', HandleOtherEvent::class);
            $config->set('stripe-webhooks.profile', WebhookProfile::class);
            $config->set('stripe-webhooks.jobs', [
                'payment_intent_created' => HandlePaymentIntentCreated::class,
                'payment_intent_succeeded' => HandlePaymentIntentSucceeded::class,
                'payment_intent_payment_failed' => HandlePaymentIntentFailed::class,
                'payment_intent_canceled' => HandlePaymentIntentCanceled::class,
                'payment_intent_payment_failed' => HandlePaymentIntentFailed::class,
            ]);
        });

    }

    /**
     * Get package providers.
     *
     * @param  Application  $app
     * @return array<int, class-string<ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [
            // Ray
            RayServiceProvider::class,

            // Laravel JsonApi
            \LaravelJsonApi\Encoder\Neomerx\ServiceProvider::class,
            \LaravelJsonApi\Laravel\ServiceProvider::class,
            \LaravelJsonApi\Spec\ServiceProvider::class,

            // Lunar core
            LunarServiceProvider::class,
            MediaLibraryServiceProvider::class,
            ActivitylogServiceProvider::class,
            ConverterServiceProvider::class,
            NestedSetServiceProvider::class,
            BlinkServiceProvider::class,

            // Lunar Stripe
            StripePaymentsServiceProvider::class,

            // Livewire
            LivewireServiceProvider::class,

            // Dystore API
            ApiServiceProvider::class,
            JsonApiServiceProvider::class,

            // Stripe webhooks
            WebhookClientServiceProvider::class,
            StripeWebhooksServiceProvider::class,

            // Dystore Stripe
            StripeServiceProvider::class,
        ];
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
