<?php

namespace Dystcz\LunarRewards\Domain\Rewards\Actions;

use Dystcz\LunarRewards\Domain\Rewards\Contracts\Rewardable;
use Dystcz\LunarRewards\Domain\Rewards\DataTypes\Reward;
use O21\LaravelWallet\Models\Transaction;

class TransferPoints extends PointsAction
{
    /**
     * Tranfer points between two rewardable models.
     */
    public function handle(Rewardable $from, Rewardable $to, Reward $points): Transaction
    {
        $transaction = $this->transactionCreator
            ->amount($points->value)
            ->currency($points->currency->code)
            ->processor('transfer')
            ->from($from)
            ->to($to)
            ->commit();

        return $transaction;
    }
}
