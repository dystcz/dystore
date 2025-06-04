<?php

namespace Dystore\Api\Base\Repositories\ResourceRepository;

use Dystore\Api\Base\Repositories\Storage;

/**
 * @property class-string<\Dystore\Api\Domain\JsonApi\Resources\JsonApiResource> $resource
 */
class ResourceStorage extends Storage
{
    final public function __construct(
        protected string $resource,
        protected AttributeRepository $attributes = new AttributeRepository,
        protected RelationshipRepository $relationships = new RelationshipRepository,
    ) {
        //
    }

    /**
     * @param  class-string<\Dystore\Api\Domain\JsonApi\Resources\JsonApiResource>  $resource
     */
    public static function fromResource(string $resource): static
    {
        return new static(
            resource: $resource,
            attributes: new AttributeRepository($resource::defaultAttributes()),
            relationships: new RelationshipRepository($resource::defaultRelationships()),
        );
    }

    public function attributes(): AttributeRepository
    {
        return $this->attributes;
    }

    public function relationships(): RelationshipRepository
    {
        return $this->relationships;
    }
}
