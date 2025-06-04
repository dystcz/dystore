<?php

namespace Dystore\Tests\Api\Feature\Domain\JsonApi\Stubs;

use Dystore\Api\Domain\JsonApi\Core\Schema\Schema;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\Where;
use Lunar\Models\Contracts\Product;

class BaseSchemaMock extends Schema
{
    /**
     * {@inheritDoc}
     */
    public static string $model = Product::class;

    protected array $with = [
        'something',
    ];

    protected array $showRelated = [
        'one',
    ];

    protected array $showRelationship = [
        'apple',
    ];

    public static function defaultIncludePaths(): array
    {
        return [
            'include-one',
            'include-two',
        ];
    }

    public static function defaultFields(): array
    {
        return [
            ID::make(),

            Str::make('nazdar'),
        ];
    }

    public static function defaultFilters(): array
    {
        return [
            Where::make('bazar'),
        ];
    }

    public static function defaultSortables(): array
    {
        return [
            'cus',
        ];
    }
}
