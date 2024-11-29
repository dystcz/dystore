<?php

namespace Dystcz\LunarRewards\Domain\Rewards\Calculators;

use Dystcz\LunarRewards\Domain\Rewards\Contracts\RewardPointsCalculator as RewardPointsCalculatorContract;
use Illuminate\Support\Facades\Config;
use Lunar\Models\Cart;
use Lunar\Models\Order;

class RewardPointsCalculator implements RewardPointsCalculatorContract
{
    protected int|float $coefficient;

    protected Order|Cart $model;

    /**
     * {@inheritDoc}
     */
    public function __construct(Order|Cart $model)
    {
        $this->model = $model;

        $this->setCoefficient(Config::get('lunar-rewards.reward_point_coefficient', 1));
    }

    /**
     * {@inheritDoc}
     */
    public function calculate(): int
    {
        if (! $this->getModel()) {
            throw new \Exception('No model set for calculating reward points.');
        }

        $currency = $this->getModel()->currency;
        $exchangeRate = $currency->exchange_rate;
        $subTotal = $this->getModel()->sub_total ?? $this->getModel()->subTotal ?? null;
        $subTotalValue = $subTotal ? $subTotal->value : 0;
        $coefficient = $this->getCoefficient();

        $points = (int) round($subTotalValue * $exchangeRate * $coefficient / 100);

        return $points;
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
    public function setCoefficient(int|float $coefficient): self
    {
        $this->coefficient = (float) $coefficient;

        return $this;
    }

    /**
     * Get the coefficient used for calculating reward points.
     */
    public function getCoefficient(): float
    {
        return $this->coefficient;
    }
}
