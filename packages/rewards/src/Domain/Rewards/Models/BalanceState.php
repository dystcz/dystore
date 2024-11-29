<?php

namespace Dystcz\LunarRewards\Domain\Rewards\Models;

use Illuminate\Support\Facades\Config;
use O21\LaravelWallet\Models\BalanceState as WalletBalanceState;

class BalanceState extends WalletBalanceState
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $tablePrefix = Config::get('lunar.database.table_prefix', 'lunar_');
        $tableName = Config::get('wallet.table_names.balance_states', 'wallet_balance_states');

        $this->setTable("{$tablePrefix}{$tableName}");
    }
}
