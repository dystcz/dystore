<?php

namespace Dystore\Newsletter;

use Dystcz\LunarApi\Base\Facades\SchemaManifestFacade;
use Dystore\Newsletter\Domain\Newsletter\JsonApi\V1\NewsletterSchema;
use Illuminate\Support\ServiceProvider;

class NewsletterServiceProvider extends ServiceProvider
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
            'dystore-newsletter',
        );

        // Register the main class to use with the facade
        $this->app->singleton('dystore-newsletter', function () {
            return new Newsletter;
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
            'dystore.newsletter',
        );
    }

    /**
     * Publish config files.
     */
    protected function publishConfig(): void
    {
        $this->publishes([
            "{$this->root}/config/newsletter.php" => config_path('dystore.newsletter.php'),
        ], 'dystore-newsletter');
    }

    /**
     * Publish translations.
     */
    protected function publishTranslations(): void
    {
        $this->publishes([
            "{$this->root}/lang" => $this->app->langPath('vendor/dystore-newsletter'),
        ], 'dystore-newsletter.translations');
    }

    /**
     * Register schemas.
     */
    public function registerSchemas(): void
    {
        SchemaManifestFacade::registerSchema(NewsletterSchema::class);
    }
}
