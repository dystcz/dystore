<?php

namespace Dystcz\LunarRewards;

use Dystcz\LunarRewards\Domain\Rewards\Actions\ChargePoints;
use Dystcz\LunarRewards\Domain\Rewards\Actions\DepositPoints;
use Dystcz\LunarRewards\Domain\Rewards\Actions\TransferPoints;
use Dystcz\LunarRewards\Domain\Rewards\Contracts\Rewardable;
use Dystcz\LunarRewards\Domain\Rewards\DataTypes\Reward;
use Dystcz\LunarRewards\Domain\Rewards\Managers\PointBalanceManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use O21\LaravelWallet\Models\Transaction;

class LunarRewards
{
    protected DepositPoints $deposit;

    protected ChargePoints $charge;

    protected TransferPoints $transfer;

    public function __construct()
    {
        $this->deposit = App::make(DepositPoints::class);
        $this->charge = App::make(ChargePoints::class);
        $this->transfer = App::make(TransferPoints::class);
    }

    /**
     * Deposit points to the model.
     */
    public function deposit(Rewardable $to, Reward $points): Transaction
    {
        return $this->deposit->handle($to, $points);
    }

    /**
     * Charge points from the model.
     */
    public function charge(Rewardable $from, Reward $points): Transaction
    {
        return $this->charge->handle($from, $points);
    }

    /**
     * Transfer points from one model to another.
     */
    public function transfer(Rewardable $from, Rewardable $to, Reward $points): Transaction
    {
        return $this->transfer->handle($from, $to, $points);
    }

    /**
     * Get list of transactions.
     *
     * @return Collection<Transaction>
     */
    public function transactions(Rewardable $model): Collection
    {
        return $this->balanceManager($model)->getTransactions();
    }

    /**
     * Get the balance of the model.
     */
    public function balance(Rewardable $model): int
    {
        return $this->balanceManager($model)->getValue();
    }

    /**
     * Get the balance manager of the model.
     */
    public function balanceManager(Rewardable $model): PointBalanceManager
    {
        return PointBalanceManager::of($model);
    }
}
