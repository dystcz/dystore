<?php

namespace Dystore\Api\Domain\Orders\Actions;

use Dystore\Api\Domain\Payments\Data\PaymentIntent;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Lunar\Models\Contracts\Order as OrderContract;
use Lunar\Models\Order;

class FindOrderByIntent
{
    /**
     * Find order by intent.
     *
     * @throws ModelNotFoundException
     */
    public function __invoke(PaymentIntent $intent): ?OrderContract
    {
        return Order::modelClass()::query()
            ->where('meta->payment_intent', $intent->getId())
            ->firstOrFail();
    }
}
