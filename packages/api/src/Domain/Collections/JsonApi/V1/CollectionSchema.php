<?php

namespace Dystore\Api\Domain\Collections\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Eloquent\Fields\AttributeData;
use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use Dystore\Api\Domain\JsonApi\Eloquent\Sorts\InDefaultOrder;
use Dystore\Api\Support\Models\Actions\SchemaType;
use LaravelJsonApi\Eloquent\Fields\Number;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsTo;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Fields\Relations\HasOne;
use LaravelJsonApi\Eloquent\Filters\Has;
use LaravelJsonApi\Eloquent\Filters\WhereHas;
use LaravelJsonApi\Eloquent\Filters\WhereNull;
use LaravelJsonApi\Eloquent\Resources\Relation;
use Lunar\Models\Contracts\Collection;
use Lunar\Models\Contracts\CollectionGroup;
use Lunar\Models\Contracts\Url;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CollectionSchema extends Schema
{
    /**
     * {@inheritDoc}
     */
    public static string $model = Collection::class;

    /**
     * {@inheritDoc}
     */
    protected $defaultSort = 'ordered';

    /**
     * {@inheritDoc}
     */
    public static function defaultWith(): array
    {
        return [
            'attributes',
            'attributes.attributeGroup',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultIncludePaths(): array
    {
        return [
            'default_url',
            'images',
            'thumbnail',
            'urls',

            'group',

            'products',
            'products.default_url',
            'products.images',
            'products.lowest_price',
            'products.prices',
            'products.thumbnail',
            'products.urls',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFields(): array
    {
        return [
            static::idField(),

            AttributeData::make('attribute_data')
                ->groupAttributes(),

            Number::make('parent_id', 'parent_id')
                ->hidden(),

            HasOne::make('default_url', 'defaultUrl')
                ->type(SchemaType::get(Url::class))
                ->retainFieldName()
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),

            HasMany::make('images', 'images')
                ->type(SchemaType::get(Media::class))
                ->canCount()
                ->countAs('images_count')
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),

            BelongsTo::make('group', 'group')
                ->type(SchemaType::get(CollectionGroup::class))
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),

            HasMany::make('products')
                ->canCount()
                ->countAs('products_count')
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),

            HasOne::make('thumbnail', 'thumbnail')
                ->type(SchemaType::get(Media::class))
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),

            HasMany::make('urls')
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultSortables(): array
    {
        return [
            InDefaultOrder::make('ordered'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFilters(): array
    {
        return [
            fn (Schema $schema) => WhereHas::make($schema, 'urls', 'url')->singular(),
            fn (Schema $schema) => WhereHas::make($schema, 'urls', 'urls'),
            fn (Schema $schema) => WhereHas::make($schema, 'group', 'group'),
            fn (Schema $schema) => WhereNull::make('root', 'parent_id'),
            fn (Schema $schema) => Has::make($schema, 'products', 'has_products'),
        ];
    }
}
