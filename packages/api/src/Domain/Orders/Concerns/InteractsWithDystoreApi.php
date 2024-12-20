<?php

namespace Dystore\Api\Domain\Orders\Concerns;

use Dystore\Api\Domain\Orders\Factories\OrderFactory;
use Dystore\Api\Domain\PaymentOptions\Casts\PaymentBreakdown;
use Dystore\Api\Hashids\Traits\HashesRouteKey;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Config;
use Lunar\Base\Casts\Price;
use Lunar\Models\Transaction;

trait InteractsWithDystoreApi
{
    use HashesRouteKey;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string,string>
     */
    protected function casts(): array
    {
        /** @var \Lunar\Models\Order $this */
        return [
            ...$this->casts,
            ...parent::casts(),
            'payment_total' => Price::class,
            'payment_breakdown' => PaymentBreakdown::class,
        ];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): OrderFactory
    {
        return OrderFactory::new();
    }

    /**
     * Return product lines relationship.
     */
    public function productLines(): HasMany
    {
        /** @var \Lunar\Models\Order $this */
        return $this
            ->lines()
            ->whereNotIn(
                'type',
                Config::get('dystore.general.purchasable.non_eloquent_types', []),
            );
    }

    /**
     * Return payment lines relationship.
     */
    public function paymentLines(): HasMany
    {
        /** @var \Lunar\Models\Order $this */
        return $this->lines()->where('type', 'payment');
    }

    /**
     * Get the latest transaction for the order.
     */
    public function latestTransaction(): HasOne
    {
        /** @var \Lunar\Models\Order $this */
        return $this
            ->hasOne(Transaction::modelClass())
            ->latestOfMany();
    }
}
