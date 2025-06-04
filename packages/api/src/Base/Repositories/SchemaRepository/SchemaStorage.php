<?php

namespace Dystore\Api\Base\Repositories\SchemaRepository;

use Dystore\Api\Base\Repositories\Storage;
use Illuminate\Support\Arr;
use LaravelJsonApi\Contracts\Schema\Field;
use LaravelJsonApi\Eloquent\Contracts\Filter;
use LaravelJsonApi\Eloquent\Contracts\SortField;

class SchemaStorage extends Storage
{
    final public function __construct(
        protected string $schema,
        protected string $type,
        protected EagerLoadRepository $with = new EagerLoadRepository,
        protected IncludePathRepository $includePaths = new IncludePathRepository,
        protected FieldRepository $fields = new FieldRepository,
        protected SparseFieldRepository $sparseFields = new SparseFieldRepository,
        protected FilterRepository $filters = new FilterRepository,
        protected SortableRepository $sortables = new SortableRepository,
        protected RelatedRepository $showRelated = new RelatedRepository,
        protected RelationshipRepository $showRelationships = new RelationshipRepository,
    ) {
        //
    }

    /**
     * Get the repository for eager loading.
     *
     * @param  class-string<\Dystore\Api\Domain\JsonApi\Contracts\Schema>  $schema
     */
    public static function fromSchema(string $schema): static
    {
        return new static(
            schema: $schema,
            type: $schema::type(),
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

    /**
     * @return class-string<\Dystore\Api\Domain\JsonApi\Contracts\Schema>
     */
    public function schema(): string
    {
        return $this->schema;
    }

    public function with(): EagerLoadRepository
    {
        return $this->with;
    }

    /**
     * @param  string|string[]  $with
     */
    public function setWith(string|array $with): SchemaStorage
    {
        $this->with = new EagerLoadRepository(Arr::wrap($with));

        return $this;
    }

    /**
     * @param  string|string[]  $with
     */
    public function addWith(string|array $with): SchemaStorage
    {
        $this->setWith(
            $this->with()->merge(array_unique(Arr::wrap($with)))->toArray()
        );

        return $this;
    }

    /**
     * @param  string|string[]  $with
     */
    public function removeWith(string|array $with): SchemaStorage
    {
        $this->setWith(
            $this->with()->reject(fn (string $item) => in_array($item, Arr::wrap($with)))->toArray()
        );

        return $this;
    }

    public function includePaths(): IncludePathRepository
    {
        return $this->includePaths;
    }

    /**
     * @param  string[]  $includePaths
     */
    public function setIncludePaths(string ...$includePaths): SchemaStorage
    {
        $this->includePaths = new IncludePathRepository(...$includePaths);

        return $this;
    }

    /**
     * @param  string|string[]  $includePaths
     */
    public function addIncludePaths(string|array $includePaths): SchemaStorage
    {
        $this->setIncludePaths(
            $this->includePaths()->merge(array_unique(Arr::wrap($includePaths)))->toArray()
        );

        return $this;
    }

    /**
     * @param  string|string[]  $includePaths
     */
    public function removeIncludePaths(string ...$includePaths): SchemaStorage
    {
        $this->setIncludePaths(
            $this->includePaths()->reject(fn (string $item) => in_array($item, Arr::wrap($includePaths)))->toArray()
        );

        return $this;
    }

    public function fields(): FieldRepository
    {
        return $this->fields;
    }

    /**
     * @param  Field[]  $fields
     */
    public function setFields(array $fields): SchemaStorage
    {
        $this->fields = new FieldRepository($fields);

        return $this;
    }

    /**
     * @param  Field|Field[]  $fields
     */
    public function addFields(Field|array $fields): SchemaStorage
    {
        $this->fields()->push(...Arr::wrap($fields));

        return $this;
    }

    /**
     * @param  string|string[]  $names
     */
    public function removeFields(string|array $names): SchemaStorage
    {
        $this->setFields(
            $this->fields()->reject(fn (Field $field) => in_array($field->name(), Arr::wrap($names)))->toArray()
        );

        return $this;
    }

    public function sparseFields(): SparseFieldRepository
    {
        return $this->sparseFields;
    }

    /**
     * @param  string|string[]  $sparseFields
     */
    public function setSparseFields(string|array $sparseFields): SchemaStorage
    {
        $this->sparseFields = new SparseFieldRepository(Arr::wrap($sparseFields));

        return $this;
    }

    /**
     * @param  string|string[]  $sparseFields
     */
    public function addSparseFields(string|array $sparseFields): SchemaStorage
    {
        $this->sparseFields()->push(Arr::wrap($sparseFields));

        return $this;
    }

    /**
     * @param  string|string[]  $sparseFields
     */
    public function removeSparseFields(string|array $sparseFields): SchemaStorage
    {
        $this->setSparseFields(
            $this->sparseFields()->reject(fn (string $field) => in_array($field, Arr::wrap($sparseFields)))->toArray()
        );

        return $this;
    }

    public function filters(): FilterRepository
    {
        return $this->filters;
    }

    /**
     * @param  Filter[]  $filters
     */
    public function setFilters(array $filters): SchemaStorage
    {
        $this->filters = new FilterRepository(...$filters);

        return $this;
    }

    /**
     * @param  Filter|Filter[]  $filters
     */
    public function addFilters(Filter|array $filters): SchemaStorage
    {
        $this->filters()->push(...Arr::wrap($filters));

        return $this;
    }

    /**
     * @param  string|string[]  $keys
     */
    public function removeFilters(string|array $keys): SchemaStorage
    {
        $this->setFilters(
            $this->filters()->reject(fn (Filter $filter) => in_array($filter->key(), Arr::wrap($keys)))->toArray()
        );

        return $this;
    }

    public function sortables(): SortableRepository
    {
        return $this->sortables;
    }

    /**
     * @param  string|SortField|SortField[]  $sortables
     */
    public function setSortables(string|SortField|array $sortables): SchemaStorage
    {
        $this->sortables = new SortableRepository(Arr::wrap($sortables));

        return $this;
    }

    /**
     * @param  string|SortField|SortField[]  $sortables
     */
    public function addSortables(string|SortField|array $sortables): SchemaStorage
    {
        $this->sortables()->push(...Arr::wrap($sortables));

        return $this;
    }

    /**
     * @param  string|string[]  $names
     */
    public function removeSortables(string|array $names): SchemaStorage
    {
        $this->setSortables(
            $this->sortables()->reject(fn (SortField $sortable) => in_array($sortable->sortField(), Arr::wrap($names)))->toArray()
        );

        return $this;
    }

    public function showRelated(): RelatedRepository
    {
        return $this->showRelated;
    }

    /**
     * @param  string|string[]  $related
     */
    public function setShowRelated(string|array $related): SchemaStorage
    {
        $this->showRelated = new RelatedRepository(Arr::wrap($related));

        return $this;
    }

    /**
     * @param  string|string[]  $related
     */
    public function addShowRelated(string|array $related): SchemaStorage
    {
        $this->showRelated()->push(...Arr::wrap($related));

        return $this;
    }

    /**
     * @param  string|string[]  $related
     */
    public function removeShowRelated(string|array $related): SchemaStorage
    {
        $this->setShowRelated(
            $this->showRelated()->reject(fn (string $item) => in_array($item, Arr::wrap($related)))->toArray()
        );

        return $this;
    }

    public function showRelationships(): RelationshipRepository
    {
        return $this->showRelationships;
    }

    /**
     * @param  string|string[]  $relationships
     */
    public function setShowRelationships(string|array $relationships): SchemaStorage
    {
        $this->showRelationships = new RelationshipRepository(Arr::wrap($relationships));

        return $this;
    }

    /**
     * @param  string|string[]  $relationships
     */
    public function addShowRelationships(string|array $relationships): SchemaStorage
    {
        $this->showRelationships()->push(...Arr::wrap($relationships));

        return $this;
    }

    /**
     * @param  string|string[]  $relationships
     */
    public function removeShowRelationships(string|array $relationships): SchemaStorage
    {
        $this->setShowRelationships(
            $this->showRelationships()->reject(fn (string $item) => in_array($item, Arr::wrap($relationships)))->toArray()
        );

        return $this;
    }
}
