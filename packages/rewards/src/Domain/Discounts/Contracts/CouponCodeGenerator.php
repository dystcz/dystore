<?php

namespace Dystcz\LunarRewards\Domain\Discounts\Contracts;

interface CouponCodeGenerator
{
    /**
     * Generate discount code.
     */
    public function generate(): string;
}
