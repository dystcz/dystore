<?php

namespace Dystore\Api\Domain\Attributes\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use Dystore\Api\Support\Models\Actions\SchemaType;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsTo;
use LaravelJsonApi\Eloquent\Resources\Relation;
use Lunar\Models\Contracts\Attribute;
use Lunar\Models\Contracts\AttributeGroup;

class AttributeSchema extends Schema
{
    /**
     * {@inheritDoc}
     */
    public static string $model = Attribute::class;

    /**
     * {@inheritDoc}
     */
    public static function defaultIncludePaths(): array
    {
        return [
            'attribute_group',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFields(): array
    {
        return [
            static::idField(),

            fn () => BelongsTo::make('attribute_group', 'attributeGroup')
                ->retainFieldName()
                ->type(SchemaType::get(AttributeGroup::class))
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),
        ];
    }
}
