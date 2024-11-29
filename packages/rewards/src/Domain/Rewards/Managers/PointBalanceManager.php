<?php

namespace Dystcz\LunarRewards\Domain\Rewards\Managers;

use Dystcz\LunarRewards\Domain\Rewards\Contracts\Rewardable;
use Dystcz\LunarRewards\Domain\Rewards\DataTypes\Reward;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use O21\LaravelWallet\Models\Balance;
use O21\LaravelWallet\Models\Transaction;
use O21\LaravelWallet\Numeric;

class PointBalanceManager
{
    protected Balance $balance;

    public function __construct(
        protected Rewardable $model,
    ) {
        $this->balance = $model->balance();
    }

    /**
     * Create a new instance of the action.
     */
    public static function of(Rewardable $model): self
    {
        return new static($model);
    }

    /**
     * Get the model.
     */
    public function model(): Rewardable
    {
        return $this->model;
    }

    /**
     * Get balance model.
     */
    public function balance(): Balance
    {
        return $this->balance;
    }

    /**
     * Get transactions query.
     */
    public function getTransactionsQuery(): Builder
    {
        return $this
            ->balance()
            ->transactions();
    }

    /**
     * Get transactions.
     *
     * @return Collection<Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this
            ->getTransactionsQuery()
            ->get();
    }

    /**
     * Determine if the model has enough points.
     */
    public function hasEnoughPoints(Reward $points): bool
    {
        return $this
            ->model()
            ->isEnoughFunds((string) $points, $points->currency->code);
    }

    /**
     * Get balance.
     */
    public function get(): Numeric
    {
        return $this->balance->value;
    }

    /**
     * Get balance reward.
     */
    public function getReward(): Reward
    {
        return new Reward((int) $this->get()->get());
    }

    /**
     * Get balance value.
     */
    public function getValue(): int
    {
        return $this->getReward()->value;
    }

    /**
     * Get pending.
     */
    public function getPending(): Numeric
    {
        return new Numeric($this->balance->value_pending);
    }

    /**
     * Get pending reward.
     */
    public function getPendingReward(): Reward
    {
        return new Reward((int) $this->getPending()->get());
    }

    /**
     * Get pending value.
     */
    public function getPendingValue(): int
    {
        return $this->getPendingReward()->value;
    }

    /**
     * Get on hold value.
     */
    public function getOnHold(): Numeric
    {
        return new Numeric($this->balance->value_on_hold);
    }

    /**
     * Get on hold reward.
     */
    public function getOnHoldReward(): Reward
    {
        return new Reward((int) $this->getOnHold()->get());
    }

    /**
     * Get on hold value.
     */
    public function getOnHoldValue(): int
    {
        return $this->getOnHoldReward()->value;
    }

    /**
     * Get sent.
     */
    public function getSent(): Numeric
    {
        return $this->balance->sent;
    }

    /**
     * Get sent reward.
     */
    public function getSentReward(): Reward
    {
        return new Reward((int) $this->getSent()->get());
    }

    /**
     * Get sent value.
     */
    public function getSentValue(): int
    {
        return $this->getSentReward()->value;
    }

    /**
     * Get received.
     */
    public function getReceived(): Numeric
    {
        return $this->balance->received;
    }

    /**
     * Get received reward.
     */
    public function getReceivedReward(): Reward
    {
        return new Reward((int) $this->getReceived()->get());
    }

    /**
     * Get received value.
     */
    public function getReceivedValue(): int
    {
        return $this->getReceivedReward()->value;
    }
}
