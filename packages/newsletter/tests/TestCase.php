<?php

namespace Dystcz\LunarApiNewsletter\Tests;

use Dystcz\LunarApiNewsletter\LunarApiNewsletterServiceProvider;
use Dystcz\LunarApiNewsletter\Tests\Stubs\JsonApi\Server;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;
use LaravelJsonApi\Testing\MakesJsonApiRequests;
use LaravelJsonApi\Testing\TestExceptionHandler;
use Orchestra\Testbench\TestCase as Orchestra;

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
        Config::set('lunar-api.additional_servers', [Server::class]);

        return [
            LunarApiNewsletterServiceProvider::class,

            // Laravel JsonApi
            \LaravelJsonApi\Encoder\Neomerx\ServiceProvider::class,
            \LaravelJsonApi\Laravel\ServiceProvider::class,
            \LaravelJsonApi\Spec\ServiceProvider::class,

            // Lunar Api
            \Dystcz\LunarApi\LunarApiServiceProvider::class,

            // Lunar core
            \Lunar\LunarServiceProvider::class,
            \Cartalyst\Converter\Laravel\ConverterServiceProvider::class,

            // Spatie Newsletter
            \Spatie\Newsletter\NewsletterServiceProvider::class,
        ];
    }

    /**
     * @param  Application  $app
     */
    public function getEnvironmentSetUp($app)
    {
        Config::set('newsletter.driver', \Spatie\Newsletter\Drivers\MailChimpDriver::class);
        Config::set('newsletter.driver_arguments.endpoint', '');
    }

    protected function resolveApplicationExceptionHandler($app): void
    {
        $app->singleton(
            ExceptionHandler::class,
            TestExceptionHandler::class
        );
    }
}
