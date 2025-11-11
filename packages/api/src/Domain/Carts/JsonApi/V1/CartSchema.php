<?php

namespace Dystore\Api\Domain\Carts\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Eloquent\Fields\CartPricing;
use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use Dystore\Api\Support\Models\Actions\SchemaType;
use LaravelJsonApi\Eloquent\Fields\ArrayHash;
use LaravelJsonApi\Eloquent\Fields\Boolean;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsTo;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Fields\Relations\HasOne;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Resources\Relation;
use Lunar\Models\Contracts\Cart;
use Lunar\Models\Contracts\CartAddress;
use Lunar\Models\Contracts\CartLine;
use Lunar\Models\Contracts\Customer;
use Lunar\Models\Contracts\Order;

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
            'cart_lines.purchasable.default_url',
            'cart_lines.purchasable.images',
            'cart_lines.purchasable.prices',
            'cart_lines.purchasable.price',
            'cart_lines.purchasable.lowest_price',
            'cart_lines.purchasable.highest_price',
            'cart_lines.purchasable.product',
            'cart_lines.purchasable.product.collections',
            'cart_lines.purchasable.product.default_url',
            'cart_lines.purchasable.product.images',
            'cart_lines.purchasable.product.product_type',
            'cart_lines.purchasable.product.thumbnail',
            'cart_lines.purchasable.product_option_values',
            'cart_lines.purchasable.product_option_values.product_option',
            'cart_lines.purchasable.thumbnail',
            'cart_lines.purchasable.values',

            'order',
            'order.product_lines',
            'order.product_lines.purchasable',
            'order.product_lines.purchasable.thumbnail',
            'order.product_lines.purchasable.default_url',
            'order.product_lines.purchasable.product',
            'order.product_lines.purchasable.product.thumbnail',
            'order.product_lines.purchasable.product.default_url',
            'order.product_lines.purchasable.product_option_values',

            'cart_addresses',
            'cart_addresses.country',

            'shipping_address',
            'shipping_address.country',

            'billing_address',
            'billing_address.country',

            'customer',
            'customer.addresses',
            'customer.addresses.country',

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

            CartPricing::make('pricing'),

            Str::make('coupon_code'),

            Str::make('payment_option'),

            Str::make('shipping_option')->hidden(), // NOTE: Attributes used for setting shipping options to current session cart

            Str::make('address_type')->hidden(),

            Boolean::make('create_user')->hidden(), // NOTE: Attributes used for determining if user should be created during checkout

            Boolean::make('agree')->hidden(),

            ArrayHash::make('meta'),

            BelongsTo::make('customer', 'customer')
                ->type(SchemaType::get(Customer::class))
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),

            HasOne::make('order', 'draftOrder')
                ->type(SchemaType::get(Order::class))
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),

            HasMany::make('cart_lines', 'lines')
                ->type(SchemaType::get(CartLine::class))
                ->retainFieldName()
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),

            HasMany::make('cart_addresses', 'addresses')
                ->type(SchemaType::get(CartAddress::class))
                ->retainFieldName()
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),

            HasOne::make('shipping_address', 'shippingAddress')
                ->type(SchemaType::get(CartAddress::class))
                ->retainFieldName()
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),

            HasOne::make('billing_address', 'billingAddress')
                ->type(SchemaType::get(CartAddress::class))
                ->retainFieldName()
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),

            ...parent::fields(),
        ];
    }
}
