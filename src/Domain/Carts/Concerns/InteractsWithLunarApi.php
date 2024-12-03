<?php

namespace Dystcz\LunarApi\Domain\Carts\Concerns;

use Dystcz\LunarApi\Domain\Carts\Events\CartCreated;
use Dystcz\LunarApi\Domain\Carts\Factories\CartFactory;
use Dystcz\LunarApi\Hashids\Traits\HashesRouteKey;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lunar\Models\Contracts\Cart as CartContract;

trait InteractsWithLunarApi
{
    use HashesRouteKey;
    use InteractsWithPaymentOptions;

    /**
     * Boot the trait.
     */
    public static function bootInteractsWithLunarApi(): void
    {
        static::retrieved(function (CartContract $model) {
            /** @var \Lunar\Models\Cart $model */
            $model->cachableProperties = [
                ...$model->cachableProperties,
                'paymentOption',
                'paymentSubTotal',
                'paymentTaxTotal',
                'paymentTotal',
                'paymentTaxBreakdown',
            ];
        });
    }

    /**
     * Get the event map for the model.
     *
     * @return array<string,class-string>
     */
    public function dispatchesEvents(): array
    {
        return [
            ...$this->dispatchesEvents,
            'created' => CartCreated::class,
        ];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): CartFactory
    {
        return CartFactory::new();
    }

    /**
     * @param  Builder<Model>  $query
     */
    public function scopeActive(Builder $query): Builder
    {
        // NOTE: When cart has order, it is considered inactive
        // @see scopeActive method on \Lunar\Models\Cart
        return $query->whereDoesntHave('orders');
    }
}
