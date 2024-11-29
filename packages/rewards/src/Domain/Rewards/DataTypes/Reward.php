<?php

namespace Dystcz\LunarRewards\Domain\Rewards\DataTypes;

use Illuminate\Support\Facades\App;
use Lunar\DataTypes\Price;
use Lunar\Models\Currency;

class Reward extends Price
{
    public Currency $currency;

    /**
     * Initialise the Price datatype.
     *
     * @param  mixed  $value
     */
    public function __construct(
        public $value,
        public int $unitQty = 1
    ) {
        $this->currency = App::make('lunar-rewards-currency');

        parent::__construct($value, $this->currency, $unitQty);
    }
}
