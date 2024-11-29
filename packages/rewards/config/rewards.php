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
    | The coefficient multiplies the value to calculate the reward points.
    | Default: sub total * reward point coefficient (10)
    |
    */
    'reward_coefficient' => 10,

    /*
    |--------------------------------------------------------------------------
    | Reward Value Coefficient
    |--------------------------------------------------------------------------
    |
    | Specify the coefficient to use for calculating value from reward points.
    | The coefficient divides the reward points to calculate the value.
    | Default: points / (reward point coefficient * value coefficient) (10 * 10 = 100)
    |
    */
    'value_coefficient' => 10,

    /*
    |--------------------------------------------------------------------------
    | Coupon Code Generator
    |--------------------------------------------------------------------------
    |
    | Specify which class to for generating discount codes.
    |
    */
    'coupon_code_generator' => \Dystcz\LunarRewards\Domain\Discounts\Generators\CouponCodeGenerator::class,

    /*
    |--------------------------------------------------------------------------
    | Coupon Name Prefix
    |--------------------------------------------------------------------------
    |
    | Set the prefix for the coupon code name.
    |
    */
    'coupon_name_prefix' => 'Reward Points Coupon',
];
