<?php

namespace Dystcz\LunarRewards\Facades;

use Dystcz\LunarRewards\Domain\Rewards\Contracts\Rewardable;
use Dystcz\LunarRewards\Domain\Rewards\DataTypes\Reward;
use Dystcz\LunarRewards\Domain\Rewards\Managers\PointBalanceManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \O21\LaravelWallet\Models\Transaction deposit(Rewardable $model, Reward $points)
 * @method static \O21\LaravelWallet\Models\Transaction charge(Rewardable $model, Reward $points)
 * @method static \O21\LaravelWallet\Models\Transaction transfer(Rewardable $from, Rewardable $to, Reward $points)
 * @method static \Illuminate\Support\Collection transactions(Rewardable $model)
 * @method static int balance(Rewardable $model)
 * @method static PointBalanceManager balanceManager(Rewardable $model)
 *
 * @see \Dystcz\LunarRewards\LunarRewards
 */
class LunarRewards extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'lunar-rewards';
    }
}
