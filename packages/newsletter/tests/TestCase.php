<?php

namespace Dystcz\LunarNewsletter\Tests;

use Cartalyst\Converter\Laravel\ConverterServiceProvider;
use Dystcz\LunarApi\LunarApiServiceProvider;
use Dystcz\LunarNewsletter\LunarNewsletterServiceProvider;
use Dystcz\LunarNewsletter\Tests\Stubs\JsonApi\Server;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Application;
use LaravelJsonApi\Testing\MakesJsonApiRequests;
use LaravelJsonApi\Testing\TestExceptionHandler;
use Lunar\LunarServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Newsletter\NewsletterServiceProvider;

abstract class TestCase extends Orchestra
{
    use MakesJsonApiRequests;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @param  Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        config()->set('lunar-api.additional_servers', [Server::class]);

        return [
            LunarNewsletterServiceProvider::class,

            // Laravel JsonApi
            \LaravelJsonApi\Encoder\Neomerx\ServiceProvider::class,
            \LaravelJsonApi\Laravel\ServiceProvider::class,
            \LaravelJsonApi\Spec\ServiceProvider::class,

            // Lunar Api
            LunarApiServiceProvider::class,

            // Lunar core
            LunarServiceProvider::class,
            ConverterServiceProvider::class,

            // Spatie Newsletter
            NewsletterServiceProvider::class,
        ];
    }

    /**
     * @param  Application  $app
     */
    public function getEnvironmentSetUp($app)
    {
        config()->set('newsletter.driver', \Spatie\Newsletter\Drivers\MailChimpDriver::class);
        config()->set('newsletter.driver_arguments.endpoint', '');
    }

    protected function resolveApplicationExceptionHandler($app): void
    {
        $app->singleton(
            ExceptionHandler::class,
            TestExceptionHandler::class
        );
    }
}
