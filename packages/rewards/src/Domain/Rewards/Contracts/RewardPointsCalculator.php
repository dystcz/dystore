<?php

namespace Dystcz\LunarRewards\Domain\Rewards\Contracts;

use Lunar\Models\Cart;
use Lunar\Models\Order;

interface RewardPointsCalculator
{
    /**
     * Set the model on which the reward points are calculated.
     */
    public function __construct(Order|Cart $model);

    /**
     * Calculate the reward points for the given order.
     */
    public function calculate(): int;
}
