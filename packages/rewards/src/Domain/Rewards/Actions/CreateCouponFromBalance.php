<?php

namespace Dystcz\LunarRewards\Domain\Rewards\Actions;

use Carbon\Carbon;
use Dystcz\LunarRewards\Domain\Discounts\Contracts\CouponCodeGenerator;
use Dystcz\LunarRewards\Domain\Rewards\Calculators\RewardValueCalculator;
use Dystcz\LunarRewards\Domain\Rewards\Contracts\Rewardable;
use Dystcz\LunarRewards\Domain\Rewards\DataTypes\Reward;
use Dystcz\LunarRewards\Facades\LunarRewards;
use Illuminate\Support\Facades\Config;
use Lunar\DataTypes\Price;
use Lunar\DiscountTypes\AmountOff;
use Lunar\Models\Currency;
use Lunar\Models\Discount;

class CreateCouponFromBalance extends PointsAction
{
    protected Currency $defaultCurrency;

    public function __construct(
        protected CouponCodeGenerator $generator
    ) {
        $this->defaultCurrency = $this->getDefaultCurrency();
    }

    /**
     * Create coupon from reward points.
     */
    public function handle(Rewardable $model, Currency $currency, ?Reward $points = null): Discount
    {
        $points = $points ?? LunarRewards::balanceManager($model)->getReward();

        $price = $this->calculateCouponValue($points, $currency);

        $coupon = $this->createCoupon($model, $price);

        return $coupon;
    }

    /**
     * Calculate coupon value.
     */
    protected function calculateCouponValue(Reward $points, Currency $currency): Price
    {
        return RewardValueCalculator::for($points, $currency)
            ->calculate();
    }

    /**
     * Get default currency.
     */
    protected function getDefaultCurrency(): Currency
    {
        return Currency::getDefault();
    }

    /**
     * Create coupon.
     */
    protected function createCoupon(Rewardable $model, Price $price): Discount
    {
        $prefix = trim(Config::get('lunar-rewards.rewards.coupon_name_prefix', 'Reward Points'));
        $suffix = "for {$model->getMorphClass()}::{$model->getKey()}";
        $name = implode(' ', [$prefix, $suffix]);
        $code = $this->generator->generate();

        $discount = Discount::create([
            'type' => AmountOff::class,
            'name' => $name,
            'max_uses' => 1,
            'handle' => $code,
            'coupon' => $code,
            'data' => [
                'fixed_value' => true,
                'fixed_values' => [
                    $price->currency->code => $price->decimal,
                ],
            ],
            'starts_at' => Carbon::now(),
        ]);

        return $discount;
    }
}
