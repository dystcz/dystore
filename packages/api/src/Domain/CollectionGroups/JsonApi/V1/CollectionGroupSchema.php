<?php

namespace Dystore\Api\Domain\CollectionGroups\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\Where;
use Lunar\Models\Contracts\CollectionGroup;

class CollectionGroupSchema extends Schema
{
    /**
     * {@inheritDoc}
     */
    public static string $model = CollectionGroup::class;

    /**
     * {@inheritDoc}
     */
    public static function defaultFields(): array
    {
        return [
            static::idField(),

            Str::make('name'),
            Str::make('handle'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFilters(): array
    {
        return [
            Where::make('name', 'name'),
            Where::make('handle', 'handle'),
        ];
    }
}
