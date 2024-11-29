<?php

namespace Dystcz\LunarApi\Domain\ProductOptions\JsonApi\V1;

use Dystcz\LunarApi\Domain\JsonApi\Eloquent\Schema;
use Dystcz\LunarApi\Support\Models\Actions\ModelType;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Fields\Str;
use Lunar\Models\Contracts\ProductOption;
use Lunar\Models\Contracts\ProductOptionValue;

class ProductOptionSchema extends Schema
{
    /**
     * {@inheritDoc}
     */
    public static string $model = ProductOption::class;

    /**
     * {@inheritDoc}
     */
    public function includePaths(): iterable
    {
        return [
            ...parent::includePaths(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function fields(): iterable
    {
        return [
            $this->idField(),

            Str::make('name')
                ->readOnly()
                ->extractUsing(
                    fn (ProductOption $model, string $attribute) => $model->translate($attribute),
                ),

            Str::make('label')
                ->readOnly()
                ->extractUsing(
                    fn (ProductOption $model, string $attribute) => $model->translate($attribute),
                ),

            Str::make('handle')
                ->readOnly(),

            HasMany::make('values', 'values')
                ->type(ModelType::get(ProductOptionValue::class))
                ->readOnly(),

            ...parent::fields(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function filters(): array
    {
        return [
            ...parent::filters(),
        ];
    }
}
