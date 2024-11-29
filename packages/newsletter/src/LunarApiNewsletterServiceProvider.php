<?php

namespace Dystcz\LunarApiNewsletter;

use Dystcz\LunarApi\Base\Facades\SchemaManifestFacade;
use Dystcz\LunarApiNewsletter\Domain\Newsletter\JsonApi\V1\NewsletterSchema;
use Illuminate\Support\ServiceProvider;

class LunarApiNewsletterServiceProvider extends ServiceProvider
{
    protected $root = __DIR__.'/..';

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->registerConfig();

        $this->registerSchemas();

        $this->loadTranslationsFrom(
            "{$this->root}/lang",
            'lunar-api-newsletter',
        );

        // Register the main class to use with the facade
        $this->app->singleton('lunar-api-newsletter', function () {
            return new LunarApiNewsletter;
        });
    }

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom("{$this->root}/routes/api.php");

        if ($this->app->runningInConsole()) {
            $this->publishConfig();
            $this->publishTranslations();
        }
    }

    /**
     * Register config files.
     */
    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(
            "{$this->root}/config/newsletter.php",
            'lunar-api.newsletter',
        );
    }

    /**
     * Publish config files.
     */
    protected function publishConfig(): void
    {
        $this->publishes([
            "{$this->root}/config/newsletter.php" => config_path('lunar-api.newsletter.php'),
        ], 'lunar-api-newsletter');
    }

    /**
     * Publish translations.
     */
    protected function publishTranslations(): void
    {
        $this->publishes([
            "{$this->root}/lang" => $this->app->langPath('vendor/lunar-api-newsletter'),
        ], 'lunar-api.translations');
    }

    /**
     * Register schemas.
     */
    public function registerSchemas(): void
    {
        SchemaManifestFacade::registerSchema(NewsletterSchema::class);
    }
}
