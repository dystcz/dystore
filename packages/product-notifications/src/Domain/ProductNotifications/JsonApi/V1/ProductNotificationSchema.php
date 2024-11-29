<?php

namespace Dystcz\LunarProductNotification\Domain\ProductNotifications\JsonApi\V1;

use Dystcz\LunarProductNotification\Domain\ProductNotifications\Models\ProductNotification;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Number;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Schema;

class ProductNotificationSchema extends Schema
{
    /**
     * The model the schema corresponds to.
     */
    public static string $model = ProductNotification::class;

    /**
     * Get the resource fields.
     */
    public function fields(): array
    {
        return [
            ID::make(),

            Str::make('email'),

            Number::make('purchasable_id'),
            Str::make('purchasable_type'),
        ];
    }

    /**
     * Get the JSON:API resource type.
     */
    public static function type(): string
    {
        return 'product-notifications';
    }
}
