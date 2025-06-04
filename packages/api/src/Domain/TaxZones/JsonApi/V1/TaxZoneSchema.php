<?php

namespace Dystore\Api\Domain\TaxZones\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use Lunar\Models\Contracts\TaxZone;

class TaxZoneSchema extends Schema
{
    /**
     * {@inheritDoc}
     */
    public static string $model = TaxZone::class;

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
