<?php

namespace Dystore\Api\Domain\ProductAssociations\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use Dystore\Api\Support\Models\Actions\SchemaType;
use LaravelJsonApi\Eloquent\Fields\Relations\HasOne;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\Where;
use LaravelJsonApi\Eloquent\Resources\Relation;
use Lunar\Models\Contracts\Product;
use Lunar\Models\Contracts\ProductAssociation;

class ProductAssociationSchema extends Schema
{
    /**
     * {@inheritDoc}
     */
    public static string $model = ProductAssociation::class;

    /**
     * {@inheritDoc}
     */
    public static function defaultIncludePaths(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFields(): array
    {
        return [
            static::idField(),

            Str::make('type'),

            HasOne::make('target')
                ->type(SchemaType::get(Product::class))
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),

            HasOne::make('parent')
                ->type(SchemaType::get(Product::class))
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFilters(): array
    {
        return [
            Where::make('type'),
        ];
    }
}
