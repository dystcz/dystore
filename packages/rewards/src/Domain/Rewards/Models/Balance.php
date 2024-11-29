<?php

namespace Dystcz\LunarRewards\Domain\Rewards\Models;

use Illuminate\Support\Facades\Config;
use O21\LaravelWallet\Models\Balance as WalletBalance;

class Balance extends WalletBalance
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $tablePrefix = Config::get('lunar.database.table_prefix', 'lunar_');
        $tableName = Config::get('wallet.table_names.balances', 'wallet_balances');

        $this->setTable("{$tablePrefix}{$tableName}");
    }
}
