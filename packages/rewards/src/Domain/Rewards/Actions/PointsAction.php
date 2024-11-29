<?php

namespace Dystcz\LunarRewards\Domain\Rewards\Actions;

use Illuminate\Support\Facades\App;
use O21\LaravelWallet\Contracts\TransactionCreator;
use O21\LaravelWallet\Transaction\Creator;

abstract class PointsAction
{
    /** @var Creator */
    protected TransactionCreator $transactionCreator;

    public function __construct()
    {
        $this->transactionCreator = App::make(TransactionCreator::class);
    }
}
