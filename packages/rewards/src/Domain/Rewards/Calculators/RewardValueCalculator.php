<?php

namespace Dystcz\LunarRewards\Domain\Rewards\Calculators;

use Dystcz\LunarRewards\Domain\Rewards\Contracts\RewardValueCalculator as RewardValueCalculatorContract;
use Dystcz\LunarRewards\Domain\Rewards\DataTypes\Reward;
use Illuminate\Support\Facades\Config;
use Lunar\DataTypes\Price;
use Lunar\Models\Currency;

class RewardValueCalculator implements RewardValueCalculatorContract
{
    protected float $rewardCoefficient;

    protected float $valueCoefficient;

    protected Reward $reward;

    protected Currency $currency;

    /**
     * {@inheritDoc}
     */
    public function __construct(Reward $reward, Currency $currency)
    {
        $this->reward = $reward;

        $this->currency = $currency;

        $this->setRewardCoefficient(Config::get('lunar-rewards.rewards.reward_coefficient', 1));
        $this->setValueCoefficient(Config::get('lunar-rewards.rewards.value_coefficient', 1));
    }

    /**
     * {@inheritDoc}
     */
    public function calculate(): Price
    {
        if (! $this->getReward()) {
            throw new \Exception('No reward set for calculating the value.');
        }

        if (! $this->getCurrency()) {
            throw new \Exception('No currency set for calculating the value.');
        }

        $rewardCoefficient = $this->getRewardCoefficient();
        $valueCoefficient = $this->getValueCoefficient();
        $exchangeRate = $this->getCurrency()->exchange_rate;

        $value = (int) round($this->getReward()->value * $exchangeRate / ($rewardCoefficient * $valueCoefficient) * 100);

        return new Price($value, $this->getCurrency());
    }

    /**
     * Static constructor.
     */
    public static function for(Reward $reward, Currency $currency): self
    {
        return new static($reward, $currency);
    }

    /**
     * Get the reward.
     */
    public function getReward(): Reward
    {
        return $this->reward;
    }

    /**
     * Get the currency.
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * Set the value coefficient.
     */
    public function setValueCoefficient(int|float $valueCoefficient): self
    {
        $this->valueCoefficient = (float) $valueCoefficient;

        return $this;
    }

    /**
     * Get the value coefficient.
     */
    public function getValueCoefficient(): float
    {
        return $this->valueCoefficient;
    }

    /**
     * Set the reward points coefficient.
     */
    public function setRewardCoefficient(int|float $rewardCoefficient): self
    {
        $this->rewardCoefficient = (float) $rewardCoefficient;

        return $this;
    }

    /**
     * Get the reward points coefficient.
     */
    public function getRewardCoefficient(): float
    {
        return $this->rewardCoefficient;
    }
}
