<?php

namespace Dystcz\LunarRewards\Tests\Stubs\Carts;

use Dystcz\LunarRewards\Domain\Rewards\Contracts\Rewardable;
use Dystcz\LunarRewards\Domain\Rewards\Traits\HasRewardPointsBalance;
use Lunar\Models\Cart as LunarCart;

class Cart extends LunarCart implements Rewardable
{
    use HasRewardPointsBalance;
}
