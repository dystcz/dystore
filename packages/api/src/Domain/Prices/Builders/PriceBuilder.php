<?php

namespace Dystore\Api\Domain\Prices\Builders;

use Dystore\Api\Domain\Storefront\Managers\StorefrontSessionManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\App;
use Lunar\Base\StorefrontSessionInterface;

/**
 * @param  StorefrontSessionManager  $storefront
 *
 * @method PriceBuilder inCurrency(?string $column, ?string $table)
 * @method PriceBuilder inCustomerGroups(?string $column, ?string $table)
 * @method PriceBuilder basePrices(?string $table)
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
}
