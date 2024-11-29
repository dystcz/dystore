<?php

namespace Dystcz\LunarRewards\Domain\Rewards\Contracts;

use Dystcz\LunarRewards\Domain\Rewards\DataTypes\Reward;
use Lunar\DataTypes\Price;
use Lunar\Models\Currency;

interface RewardValueCalculator
{
    /**
     * Set the reward and currency for calculating the value.
     */
    public function __construct(Reward $reward, Currency $currency);

    /**
     * Calculate the value from the reward points.
     */
    public function calculate(): Price;
}
