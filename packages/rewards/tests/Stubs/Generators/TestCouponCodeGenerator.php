<?php

namespace Dystcz\LunarRewards\Tests\Stubs\Generators;

use Dystcz\LunarRewards\Domain\Discounts\Generators\CouponCodeGenerator;
use Illuminate\Support\Facades\Cache;

class TestCouponCodeGenerator extends CouponCodeGenerator
{
    public const LENGTH = 2;

    /**
     * Generate code.
     */
    protected function generateCode(): string
    {
        Cache::increment('coupon-generator-test');

        return (string) random_int(10, 20);
    }
}
