<?php

namespace Dystore\Api\Domain\JsonApi\Contracts;

use LaravelJsonApi\Contracts\Schema\Schema as BaseSchemaContract;

interface Schema extends BaseSchemaContract
{
    /**
     * Get the default eager loading for the schema.
     */
    public static function defaultWith(): array;

    /**
     * Get the default include paths for the schema.
     */
    public static function defaultIncludePaths(): array;

    /**
     * Get the default fields for the schema.
     */
    public static function defaultFields(): array;

    /**
     * Get the default sparse fields for the schema.
     */
    public static function defaultSparseFields(): array;

    /**
     * Get the default filters for the schema.
     */
    public static function defaultFilters(): array;

    /**
     * Get the default sortables for the schema.
     */
    public static function defaultSortables(): array;

    /**
     * Allow specific related resources to be accessed.
     */
    public function showRelated(): array;

    /**
     * Get the default allowed related resource.
     */
    public static function defaultShowRelated(): array;

    /**
     * Allow specific relationships to be accessed.
     */
    public function showRelationships(): array;

    /**
     * Get the default allowed relationships.
     */
    public static function defaultShowRelationships(): array;
}
