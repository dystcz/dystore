<?php

namespace Dystore\Api\Domain\Prices\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Lunar\Models\Price;

class ApiPricingScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        /** @var \Dystore\Api\Domain\Prices\Builders\PriceBuilder $builder */
        $builder
            ->inCurrency(
                'currency_id',
                (new Price)->getTable()
            )
            ->inCustomerGroups(
                'customer_group_id',
                (new Price)->getTable()
            );
    }
}
