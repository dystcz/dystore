<?php

namespace Dystore\Api\Domain\Tags\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use Dystore\Api\Support\Models\Actions\SchemaType;
use LaravelJsonApi\Eloquent\Fields\Relations\MorphTo;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\Where;
use LaravelJsonApi\Eloquent\Filters\WhereIn;
use Lunar\Models\Contracts\Tag;
use Lunar\Models\Contracts\Url;

class TagSchema extends Schema
{
    /**
     * {@inheritDoc}
     */
    public static string $model = Tag::class;

    /**
     * {@inheritDoc}
     */
    public static function defaultIncludePaths(): array
    {
        return [
            // 'taggables',

        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFields(): array
    {
        return [
            static::idField(),

            Str::make('value'),

            // MorphTo::make('taggables', 'taggables')
            //     ->type(SchemaType::get(Url::class)),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFilters(): array
    {
        return [
            Where::make('value'),
            WhereIn::make('values', 'value')->delimiter(','),
        ];
    }
}
