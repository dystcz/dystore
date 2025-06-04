<?php

namespace Dystore\Api\Domain\AttributeGroups\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use Lunar\Models\Contracts\AttributeGroup;

class AttributeGroupSchema extends Schema
{
    /**
     * {@inheritDoc}
     */
    public static string $model = AttributeGroup::class;

    /**
     * {@inheritDoc}
     */
    public static function defaultFields(): array
    {
        return [
            static::idField(),
        ];
    }
}
