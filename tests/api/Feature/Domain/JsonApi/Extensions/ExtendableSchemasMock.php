<?php

namespace Dystore\Tests\Api\Feature\Domain\JsonApi\Extensions;

use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\Where;
use Lunar\Models\Contracts\Product;

class ExtendableSchemasMock extends Schema
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

    public static function resource(): string
    {
        return ProductResourceMock::class;
    }

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

            Str::make('ahoj'),

        ];
    }

    public static function defaultFilters(): array
    {
        return [
            Where::make('ahoj'),

        ];
    }

    public static function defaultSortables(): array
    {
        return [
            'ahoj',

             
        ];
    }
}
