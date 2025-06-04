<?php

namespace Dystore\Api\Domain\JsonApi\Contracts;

use LaravelJsonApi\Contracts\Schema\Schema as BaseSchemaContract;

interface Schema extends BaseSchemaContract
{
    /**
     * Get the default eager loading for the schema.
     *
     * @return array<int,mixed>
     */
    public static function defaultWith(): array;

    /**
     * Get the default include paths for the schema.
     *
     * @return array<int,mixed>
     */
    public static function defaultIncludePaths(): array;

    /**
     * Get the default fields for the schema.
     *
     * @return array<int,mixed>
     */
    public static function defaultFields(): iterable;

    /**
     * Get the default sparse fields for the schema.
     *
     * @return array<int,mixed>
     */
    public static function defaultSparseFields(): iterable;

    /**
     * Get the default filters for the schema.
     *
     * @return array<int,mixed>
     */
    public static function defaultFilters(): iterable;

    /**
     * Get the default sortables for the schema.
     *
     * @return array<int,mixed>
     */
    public static function defaultSortables(): iterable;

    /**
     * Allow specific related resources to be accessed.
     *
     * @return array<int,mixed>
     */
    public function showRelated(): array;

    /**
     * Get the default allowed related resource.
     *
     * @return array<int,mixed>
     */
    public static function defaultShowRelated(): array;

    /**
     * Allow specific relationships to be accessed.
     *
     * @return array<int,mixed>
     */
    public function showRelationships(): array;

    /**
     * Get the default allowed relationships.
     *
     * @return array<int,mixed>
     */
    public static function defaultShowRelationships(): array;
}
