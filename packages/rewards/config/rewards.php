<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Reward Points Calculator
    |--------------------------------------------------------------------------
    |
    | Specify which class to use for calculating reward points.
    |
    */
    'reward_point_calculator' => \Dystcz\LunarRewards\Domain\Rewards\Calculators\RewardPointsCalculator::class,

    /*
    |--------------------------------------------------------------------------
    | Reward Points Coefficient
    |--------------------------------------------------------------------------
    |
    | Specify the coefficient to use for calculating reward points.
    | The coefficient multiplies the order total to calculate the reward points.
    |
    */
    'reward_point_coefficient' => 1,
];
