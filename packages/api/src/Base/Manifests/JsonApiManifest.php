<?php

namespace Dystore\Api\Base\Manifests;

use Dystore\Api\Base\Contracts\JsonApiManifest as JsonApiManifestContract;
use Dystore\Api\Base\Repositories\ResourceRepository\ResourceStorage;
use Dystore\Api\Base\Repositories\SchemaRepository\SchemaStorage;
use Dystore\Api\Support\Config\Collections\DomainConfigCollection;
use Illuminate\Support\Arr;

class JsonApiManifest implements JsonApiManifestContract
{
    protected array $schemas = [];

    protected array $resources = [];

    public function __construct()
    {
        $this->registerBaseSchemas();
        $this->registerBaseResources();
    }

    private function registerBaseSchemas(): void
    {
        $config = DomainConfigCollection::fromConfig('dystore.domains');

        $config->getSchemas()->each(fn (string $schema) => $this->addSchema($schema));
    }

    private function registerBaseResources(): void
    {
        $config = DomainConfigCollection::fromConfig('dystore.domains')
            ->getResources()
            ->each(fn (string $resource) => $this->addResource($resource));
    }

    public function schemas(): array
    {
        return $this->schemas;
    }

    public function getSchemas(): array
    {
        return array_map(
            fn (SchemaStorage $storage) => $storage->schema(),
            $this->schemas()
        );
    }

    /**
     * @param  class-string<\Dystore\Api\Domain\JsonApi\Eloquent\Schema|\Dystore\Api\Domain\JsonApi\Core\Schema\Schema>  $schema
     */
    public function schema(string $schema): ?SchemaStorage
    {
        return Arr::get($this->schemas(), $schema::type());
    }

    public function schemaByType(string $type): ?SchemaStorage
    {
        return Arr::get($this->schemas(), $type);
    }

    public function addSchema(string $schema, ?SchemaStorage $storage = null): self
    {
        $this->schemas[$schema::type()] = $storage ?? SchemaStorage::fromSchema($schema);

        return $this;
    }

    public function resources(): array
    {
        return $this->resources;
    }

    public function resource(string $resource): ?ResourceStorage
    {
        return Arr::get($this->resources(), $resource);
    }

    public function addResource(string $resource, ?ResourceStorage $storage = null): self
    {
        $this->resources[$resource] = $storage ?? ResourceStorage::fromResource($resource);

        return $this;
    }
}
