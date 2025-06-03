<?php

namespace Dystore\Api\Base\Repositories\ResourceRepository;

use Dystore\Api\Base\Repositories\Storage;

class ResourceStorage extends Storage
{
    final public function __construct(
        public readonly AttributeRepository $attributes = new AttributeRepository,
        public readonly RelationshipRepository $relationships = new RelationshipRepository,
    ) {
        //
    }

    /**
     * @param  class-string<\Dystore\Api\Domain\JsonApi\Resources\JsonApiResource>  $resource
     */
    public static function fromResource(string $resource): static
    {
        return new static(
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
