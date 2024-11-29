<?php

namespace Dystcz\LunarRewards\Domain\Rewards\Calculators;

use Dystcz\LunarRewards\Domain\Rewards\Contracts\RewardPointsCalculator as RewardPointsCalculatorContract;
use Dystcz\LunarRewards\Domain\Rewards\DataTypes\Reward;
use Illuminate\Support\Facades\Config;
use Lunar\Models\Cart;
use Lunar\Models\Order;

class RewardPointsCalculator implements RewardPointsCalculatorContract
{
    protected float $rewardCoefficient;

    protected Order|Cart $model;

    /**
     * {@inheritDoc}
     */
    public function __construct(Order|Cart $model)
    {
        $this->model = $model;

        $this->setRewardCoefficient(Config::get('lunar-rewards.rewards.reward_coefficient', 1));
    }

    /**
     * {@inheritDoc}
     */
    public function calculate(): Reward
    {
        if (! $this->getModel()) {
            throw new \Exception('No model set for calculating reward points.');
        }

        $currency = $this->getModel()->currency;
        $exchangeRate = $currency->exchange_rate;
        $subTotal = $this->getModel()->sub_total ?? $this->getModel()->subTotal ?? null;
        $subTotalValue = $subTotal ? $subTotal->value : 0;
        $rewardCoefficient = $this->getRewardCoefficient();

        $points = (int) round($subTotalValue * $exchangeRate * $rewardCoefficient / 100);

        return new Reward($points);
    }

    /**
     * Create a new instance of the calculator for the given model.
     */
    public static function for(Order|Cart $model): self
    {
        return new static($model);
    }

    /**
     * Get the model on which the reward points are calculated.
     */
    public function getModel(): Order|Cart|null
    {
        return $this->model;
    }

    /**
     * Set the coefficient for calculating reward points.
     */
    public function setRewardCoefficient(int|float $rewardCoefficient): self
    {
        $this->rewardCoefficient = (float) $rewardCoefficient;

        return $this;
    }

    /**
     * Get the coefficient used for calculating reward points.
     */
    public function getRewardCoefficient(): float
    {
        return $this->rewardCoefficient;
    }
}
