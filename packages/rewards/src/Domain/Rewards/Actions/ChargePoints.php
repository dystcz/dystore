<?php

namespace Dystcz\LunarRewards\Domain\Rewards\Actions;

use Dystcz\LunarRewards\Domain\Rewards\Contracts\Rewardable;
use Dystcz\LunarRewards\Domain\Rewards\DataTypes\Reward;
use O21\LaravelWallet\Models\Transaction;

class ChargePoints extends PointsAction
{
    /**
     * Charge points from the model.
     */
    public function handle(Rewardable $from, Reward $points): Transaction
    {
        $transaction = $this->transactionCreator
            ->amount($points->value)
            ->currency($points->currency->code)
            ->processor('charge')
            ->from($from)
            ->commit();

        return $transaction;
    }
}
