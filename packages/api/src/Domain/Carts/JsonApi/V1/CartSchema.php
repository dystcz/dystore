<?php

namespace Dystcz\LunarApi\Domain\Carts\JsonApi\V1;

use Dystcz\LunarApi\Domain\JsonApi\Eloquent\Schema;
use LaravelJsonApi\Eloquent\Fields\ArrayHash;
use LaravelJsonApi\Eloquent\Fields\Boolean;
use LaravelJsonApi\Eloquent\Fields\Map;
use LaravelJsonApi\Eloquent\Fields\Number;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Fields\Relations\HasOne;
use LaravelJsonApi\Eloquent\Fields\Str;
use Lunar\Models\Cart;

class CartSchema extends Schema
{
    /**
     * {@inheritDoc}
     */
    public static string $model = Cart::class;

    /**
     * {@inheritDoc}
     */
    protected bool $selfLink = false;

    /**
     * {@inheritDoc}
     */
    public function includePaths(): iterable
    {
        return [
            'cart_lines',
            'cart_lines.purchasable',
            'cart_lines.purchasable.prices',
            'cart_lines.purchasable.product',
            'cart_lines.purchasable.product.thumbnail',

            'order',
            'order.product_lines',
            'order.product_lines.purchasable',
            'order.product_lines.purchasable.thumbnail',

            'cart_addresses',
            'cart_addresses.country',

            'shipping_address',
            'shipping_address.country',

            'billing_address',
            'billing_address.country',

            ...parent::includePaths(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function fields(): array
    {
        return [
            $this->idField(),

            Map::make('prices', [
                Number::make('sub_total', 'subTotal')
                    ->serializeUsing(
                        static fn ($value) => $value?->decimal,
                    ),
                Number::make('sub_total_discounted', 'subTotalDiscounted')
                    ->serializeUsing(
                        static fn ($value) => $value?->decimal,
                    ),
                Number::make('total', 'total')
                    ->serializeUsing(
                        static fn ($value) => $value?->decimal,
                    ),
                Number::make('shipping_sub_total', 'shippingSubTotal')
                    ->serializeUsing(
                        static fn ($value) => $value?->decimal,
                    ),
                Number::make('shipping_total', 'shippingTotal')
                    ->serializeUsing(
                        static fn ($value) => $value?->decimal,
                    ),
                Number::make('tax_total', 'taxTotal')
                    ->serializeUsing(
                        static fn ($value) => $value?->decimal,
                    ),
                Number::make('discount_total', 'discountTotal')
                    ->serializeUsing(
                        static fn ($value) => $value?->decimal,
                    ),
                ArrayHash::make('tax_breakdown', 'taxBreakdown'),
                ArrayHash::make('discount_breakdown', 'discountBreakdown'),
            ]),

            Str::make('coupon_code'),

            // Custom fields (not in the database)
            Boolean::make('create_user')
                ->hidden(),

            ArrayHash::make('meta'),

            HasOne::make('order', 'draftOrder')
                ->type('orders')
                ->serializeUsing(
                    static fn ($relation) => $relation->withoutLinks(),
                ),

            HasMany::make('cart_lines', 'lines')
                ->type('cart-lines')
                ->serializeUsing(
                    static fn ($relation) => $relation->withoutLinks(),
                ),

            HasMany::make('cart_addresses', 'addresses')
                ->type('cart-addresses')
                ->serializeUsing(
                    static fn ($relation) => $relation->withoutLinks(),
                ),

            HasOne::make('shipping_address', 'shippingAddress')
                ->type('cart-addresses')
                ->serializeUsing(
                    static fn ($relation) => $relation->withoutLinks(),
                ),

            HasOne::make('billing_address', 'billingAddress')
                ->type('cart-addresses')
                ->serializeUsing(
                    static fn ($relation) => $relation->withoutLinks(),
                ),

            ...parent::fields(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function authorizable(): bool
    {
        return false; // TODO: create policies
    }

    /**
     * {@inheritDoc}
     */
    public static function type(): string
    {
        return 'carts';
    }
}
