<?php

namespace Dystore\Api\Base\Manifests;

use Dystore\Api\Base\Contracts\JsonApiManifest as JsonApiManifestContract;
use Dystore\Api\Base\Repositories\ResourceRepository;
use Dystore\Api\Base\Repositories\ResourceRepository\ResourceStorage;
use Dystore\Api\Base\Repositories\SchemaRepository;
use Dystore\Api\Base\Repositories\SchemaRepository\SchemaStorage;

class JsonApiManifest implements JsonApiManifestContract
{
    public function __construct(
        public SchemaRepository $schemas = new SchemaRepository,
        public ResourceRepository $resources = new ResourceRepository,
    ) {}

    public function schemas(): SchemaRepository
    {
        return $this->schemas;
    }

    public function schema(string $schema): ?SchemaStorage
    {
        return $this->schemas->get($schema);
    }

    public function addSchema(string $schema, ?SchemaStorage $storage = null): self
    {
        $storage = $storage ?: SchemaStorage::fromSchema($schema);

        $this->schemas->put($schema, $storage);

        return $this;
    }

    public function resources(): ResourceRepository
    {
        return $this->resources;
    }

    public function resource(string $resource): ?array
    {
        return $this->resources->get($resource);
    }

    public function addResource(string $resource, ?ResourceStorage $storage = null): self
    {
        $storage = $storage ?: ResourceStorage::fromResource($resource);

        $this->resources->put($resource, $storage);

        return $this;
    }
}
