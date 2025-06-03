<?php

namespace Dystore\Api\Domain\Brands\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use Dystore\Api\Support\Models\Actions\SchemaType;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Fields\Relations\HasOne;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\Where;
use LaravelJsonApi\Eloquent\Filters\WhereHas;
use LaravelJsonApi\Eloquent\Filters\WhereIn;
use LaravelJsonApi\Eloquent\Resources\Relation;
use Lunar\Models\Contracts\Brand;
use Lunar\Models\Contracts\Url;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BrandSchema extends Schema
{
    /**
     * {@inheritDoc}
     */
    public static string $model = Brand::class;

    /**
     * {@inheritDoc}
     */
    public static function defaultIncludePaths(): array
    {
        return [
            'default_url',
            'urls',
            'thumbnail',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFields(): array
    {
        return [
            static::idField(),

            Str::make('name'),

            fn () => HasOne::make('default_url', 'defaultUrl')
                ->type(SchemaType::get(Url::class))
                ->retainFieldName()
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),

            fn () => HasOne::make('thumbnail')
                ->type(SchemaType::get(Media::class))
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),

            fn () => HasMany::make('urls')
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFilters(): array
    {
        return [
            Where::make('name'),
            WhereIn::make('names', 'name')->delimiter(','),
            fn (Schema $schema) => WhereHas::make($schema, 'urls', 'url')->singular(),
            fn (Schema $schema) => WhereHas::make($schema, 'urls', 'urls'),
        ];
    }
}
