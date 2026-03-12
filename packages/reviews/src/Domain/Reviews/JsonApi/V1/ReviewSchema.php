<?php

namespace Dystore\Reviews\Domain\Reviews\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use Dystore\Api\Domain\JsonApi\Eloquent\Sorts\InRandomOrder;
use Dystore\Api\Domain\Products\JsonApi\V1\ProductSchema;
use Dystore\Api\Domain\ProductVariants\JsonApi\V1\ProductVariantSchema;
use Dystore\Api\Support\Models\Actions\SchemaType;
use Dystore\Reviews\Domain\Reviews\Builders\ReviewBuilder;
use Dystore\Reviews\Domain\Reviews\Contacts\Review;
use Dystore\Reviews\Domain\Reviews\JsonApi\Filters\PurchasableOrGeneric;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Http\Request;
use LaravelJsonApi\Eloquent\Fields\ArrayHash;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use LaravelJsonApi\Eloquent\Fields\Number;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsTo;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Fields\Relations\MorphTo;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\WhereIdIn;
use LaravelJsonApi\Eloquent\Filters\WhereIdNotIn;
use LaravelJsonApi\Eloquent\Filters\WhereNull;
use LaravelJsonApi\Eloquent\Resources\Relation;
use LaravelJsonApi\Eloquent\Sorting\SortColumn;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ReviewSchema extends Schema
{
    /**
     * The model the schema corresponds to.
     */
    public static string $model = Review::class;

    /**
     * {@inheritDoc}
     */
    protected array $with = [
        'user',
        'user.customers',
    ];

    /**
     * Default sort.
     */
    protected $defaultSort = '-published_at';

    /**
     * {@inheritDoc}
     */
    public function includePaths(): iterable
    {
        return [
            'user',
            'user.customers',

            ...parent::includePaths(),
        ];
    }

    /**
     * Build an index query for this resource.
     */
    public function indexQuery(?Request $request, Builder $query): Builder
    {
        /** @var ReviewBuilder $query */
        return $query->published();
    }

    /**
     * Build a "relatable" query for this resource.
     */
    public function relatableQuery(?Request $request, EloquentRelation $query): EloquentRelation
    {
        /** @var ReviewBuilder $query */
        return $query->published();
    }

    /**
     * Get the resource fields.
     *
     * @return array
     */
    public function fields(): iterable
    {
        return [
            $this->idField(),

            Str::make('name')
                ->extractUsing(
                    fn ($model, $column, $value) => $model->name,
                ),

            Str::make('comment'),

            Number::make('rating')
                ->sortable(),

            Number::make('purchasable_id')
                ->acceptStrings()
                ->serializeUsing(static function ($value) {
                    $raw = request()->input('data.attributes.purchasable_id');

                    return is_string($raw) ? (string) $value : $value;
                }),

            Str::make('purchasable_type'),

            ArrayHash::make('meta'),

            DateTime::make('published_at')
                ->serializeUsing(
                    static fn ($value) => $value?->format('Y-m-d H:i:s'),
                )
                ->sortable(),

            BelongsTo::make('user')
                ->serializeUsing(
                    static fn (Relation $relation) => $relation->withoutLinks(),
                ),

            MorphTo::make('purchasable', 'purchasable')
                ->types(
                    ProductSchema::type(),
                    ProductVariantSchema::type(),
                ),

            HasMany::make('images', 'images')
                ->type(SchemaType::get(Media::class))
                ->canCount()
                ->countAs('images_count')
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),

            ...parent::fields(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function sortables(): iterable
    {
        return [
            ...parent::sortables(),

            SortColumn::make('id', 'id'),
            SortColumn::make('published_at', 'published_at'),
            InRandomOrder::make('random'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function filters(): array
    {
        return [
            WhereIdIn::make($this),
            WhereIdNotIn::make($this, 'except'),
            WhereNull::make('without_purchasable', 'purchasable_type'),
            PurchasableOrGeneric::make('purchasable_or_generic'),

            ...parent::filters(),
        ];
    }
}
