<?php

namespace Dystore\Api\Domain\Prices\Builders;

use Dystore\Api\Domain\Storefront\Managers\StorefrontSessionManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\App;
use Lunar\Base\StorefrontSessionInterface;
use Lunar\Models\Contracts\CustomerGroup as CustomerGroupContract;

/**
 * @param  StorefrontSessionManager  $storefront
 *
 * @extends Builder<Model>
 */
class PriceBuilder extends Builder
{
    private StorefrontSessionInterface $storefront;

    public function __construct(QueryBuilder $query)
    {
        $this->storefront = App::make(StorefrontSessionInterface::class);

        parent::__construct($query);
    }

    public function inCurrency(): self
    {
        return $this->where(
            'currency_id',
            $this->storefront->getCurrency()->id
        );
    }

    public function inCustomerGroups(): self
    {
        $customerGroups = App::make(StorefrontSessionInterface::class)->getCustomerGroups();

        return $this->when(
            value: fn () => $this->storefront->getCustomerGroups()
                ->filter(fn (?CustomerGroupContract $group = null) => $group)
                ->isNotEmpty(),
            callback: fn (self $query) => $query
                ->whereIn(
                    'customer_group_id',
                    $customerGroups->pluck('id')->toArray()
                )
                ->orWhere(
                    'customer_group_id',
                    null
                ),
            default: fn (self $query) => $query->base(),
        );
    }

    public function base(): self
    {
        $table = $this->getModel()->getTable();

        return $this
            ->where("{$table}.min_quantity", 1)
            ->where("{$table}.customer_group_id", null);
    }
}
