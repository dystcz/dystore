<?php

namespace Dystore\Api\Domain\ProductTypes\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use Dystore\Api\Support\Models\Actions\SchemaType;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Fields\Str;
use Lunar\Models\Contracts\Attribute;
use Lunar\Models\Contracts\ProductType;

class ProductTypeSchema extends Schema
{
    /**
     * {@inheritDoc}
     */
    public static string $model = ProductType::class;

    /**
     * {@inheritDoc}
     */
    public static function defaultIncludePaths(): array
    {
        return [
            // 'mapped_attributes',
            // 'mapped_attributes.attribute_group',

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

            // HasMany::make('mapped_attributes', 'mappedAttributes')
            //     ->type(SchemaType::get(Attribute::class))
            //     ->serializeUsing(
            //         static fn ($relation) => $relation->withoutLinks()
            //     ),

        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFilters(): array
    {
        return [];
    }
}
