<?php

namespace Dystore\Api\Base\Facades;

use Dystore\Api\Base\Contracts\JsonApiManifest as JsonApiManifestContract;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array schemas()
 * @method static \Dystore\Api\Base\Repositories\SchemaRepository\SchemaStorage|null schema(string $schema)
 * @method static \Dystore\Api\Base\Manifests\JsonApiManifest addSchema(string $schema, \Dystore\Api\Base\Repositories\SchemaRepository\SchemaStorage|null $storage = null)
 * @method static array resources()
 * @method static \Dystore\Api\Base\Repositories\ResourceRepository\ResourceStorage|null resource(string $resource)
 * @method static \Dystore\Api\Base\Manifests\JsonApiManifest addResource(string $resource, \Dystore\Api\Base\Repositories\ResourceRepository\ResourceStorage|null $storage = null)
 *
 * @see \Dystore\Api\Base\Manifests\JsonApiManifest
 */
class JsonApiManifest extends Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor(): string
    {
        return JsonApiManifestContract::class;
    }
}
