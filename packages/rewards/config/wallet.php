<?php

return [
    'default_currency' => 'RP',

    'balance' => [
        'accounting_statuses' => [
            \O21\LaravelWallet\Enums\TransactionStatus::SUCCESS,
            \O21\LaravelWallet\Enums\TransactionStatus::ON_HOLD,
        ],
        'extra_values' => [
            // enable value_pending calculation
            'pending' => false,
            // enable value_on_hold calculation
            'on_hold' => false,
        ],
        'max_scale' => 8,
        'log_states' => false,
    ],

    'models' => [
        'balance' => \Dystcz\LunarRewards\Domain\Rewards\Models\Balance::class,
        'balance_state' => \Dystcz\LunarRewards\Domain\Rewards\Models\BalanceState::class,
        'transaction' => \Dystcz\LunarRewards\Domain\Rewards\Models\Transaction::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Table Names
    |--------------------------------------------------------------------------
    |
    | Specify the table names to use for the rewards system.
    | Note that the table names are prefixed with the default Lunar table prefix.
    | Eg. 'balances' will be 'lunar_balances' or wharever your table prefix is.
    |
    */
    'table_names' => [
        'balances' => 'wallet_balances',
        'balance_states' => 'wallet_balance_states',
        'transactions' => 'wallet_transactions',
    ],

    'processors' => [
        'deposit' => \O21\LaravelWallet\Transaction\Processors\DepositProcessor::class,
        'charge' => \O21\LaravelWallet\Transaction\Processors\ChargeProcessor::class,
        'transfer' => \O21\LaravelWallet\Transaction\Processors\TransferProcessor::class,
    ],
];
