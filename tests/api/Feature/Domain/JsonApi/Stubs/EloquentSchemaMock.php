<?php

namespace Dystore\Tests\Api\Feature\Domain\JsonApi\Stubs;

use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use Dystore\Tests\Api\Feature\Domain\JsonApi\Base\ProductResourceMock;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\Where;
use Lunar\Models\Contracts\Tag;

class EloquentSchemaMock extends Schema
{
    /**
     * {@inheritDoc}
     */
    public static string $model = Tag::class;

    protected array $with = [
        'something',
    ];

    protected array $showRelated = [
        'one',
    ];

    protected array $showRelationships = [
        'apple',
    ];

    /**
     * {@inheritDoc}
     */
    public static function resource(): string
    {
        return ProductResourceMock::class;
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultIncludePaths(): array
    {
        return [
            'include-one',
            'include-two',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFields(): array
    {
        return [
            ID::make(),

            Str::make('ahoj'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFilters(): array
    {
        return [
            Where::make('ahoj'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultSortables(): array
    {
        return [
            'ahoj',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function authorizable(): bool
    {
        return false;
    }
}
