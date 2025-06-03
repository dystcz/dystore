<?php

namespace Dystore\Api\Base\Repositories\SchemaRepository;

use Dystore\Api\Base\Repositories\Storage;

class SchemaStorage extends Storage
{
    final public function __construct(
        public readonly EagerLoadRepository $with = new EagerLoadRepository,
        public readonly IncludePathRepository $includePaths = new IncludePathRepository,
        public readonly FieldRepository $fields = new FieldRepository,
        public readonly SparseFieldRepository $sparseFields = new SparseFieldRepository,
        public readonly FilterRepository $filters = new FilterRepository,
        public readonly SortableRepository $sortables = new SortableRepository,
        public readonly RelatedRepository $showRelated = new RelatedRepository,
        public readonly RelationshipRepository $showRelationships = new RelationshipRepository,
    ) {
        //
    }

    /**
     * Get the repository for eager loading.
     *
     * @param  class-string<\Dystore\Api\Domain\JsonApi\Contracts\Schema>  $schema
     */
    public static function fromSchema(string $schema): self
    {
        return new self(
            with: new EagerLoadRepository($schema::defaultWith()),
            includePaths: new IncludePathRepository($schema::defaultIncludePaths()),
            fields: new FieldRepository($schema::defaultFields()),
            sparseFields: new SparseFieldRepository($schema::defaultSparseFields()),
            filters: new FilterRepository($schema::defaultFilters()),
            sortables: new SortableRepository($schema::defaultSortables()),
            showRelated: new RelatedRepository($schema::defaultShowRelated()),
            showRelationships: new RelationshipRepository($schema::defaultShowRelationships()),
        );
    }

    public function with(): EagerLoadRepository
    {
        return $this->with;
    }

    public function includePaths(): IncludePathRepository
    {
        return $this->includePaths;
    }

    public function fields(): FieldRepository
    {
        return $this->fields;
    }

    public function sparseFields(): SparseFieldRepository
    {
        return $this->sparseFields;
    }

    public function filters(): FilterRepository
    {
        return $this->filters;
    }

    public function sortables(): SortableRepository
    {
        return $this->sortables;
    }

    public function showRelated(): RelatedRepository
    {
        return $this->showRelated;
    }

    public function showRelationships(): RelationshipRepository
    {
        return $this->showRelationships;
    }
}
