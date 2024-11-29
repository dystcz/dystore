<?php

namespace Dystcz\LunarRewards\Domain\Rewards\Models;

use Illuminate\Support\Facades\Config;
use O21\LaravelWallet\Models\Transaction as WalletTransaction;

class Transaction extends WalletTransaction
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $tablePrefix = Config::get('lunar.database.table_prefix', 'lunar_');
        $tableName = Config::get('wallet.table_names.transactions', 'wallet_transactions');

        $this->setTable("{$tablePrefix}{$tableName}");
    }
}
