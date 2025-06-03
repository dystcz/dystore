<?php

namespace Dystore\Api;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class JsonApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        \LaravelJsonApi\Laravel\LaravelJsonApi::defaultResource(
            Domain\JsonApi\Resources\JsonApiResource::class,
        );
        \LaravelJsonApi\Laravel\LaravelJsonApi::defaultAuthorizer(
            Domain\JsonApi\Authorizers\Authorizer::class,
        );
        \LaravelJsonApi\Laravel\LaravelJsonApi::defaultQuery(
            Domain\JsonApi\Queries\Query::class,
        );
        \LaravelJsonApi\Laravel\LaravelJsonApi::defaultCollectionQuery(
            Domain\JsonApi\Queries\CollectionQuery::class,
        );
        \LaravelJsonApi\Laravel\LaravelJsonApi::withCountQueryParameter(
            'with_count',
        );
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        // Register custom repository
        $this->app->bind(
            \LaravelJsonApi\Eloquent\Repository::class,
            fn () => Domain\JsonApi\Eloquent\Repository::class,
        );

        // Register the JSON:API manifest.
        $this->app->singleton(
            \Dystore\Api\Base\Contracts\JsonApiManifest::class,
            fn (Application $app) => $app->make(\Dystore\Api\Base\Manifests\JsonApiManifest::class),
        );
    }
}
