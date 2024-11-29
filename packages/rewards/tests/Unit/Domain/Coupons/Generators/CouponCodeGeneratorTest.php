<?php

use Dystcz\LunarRewards\Domain\Discounts\Contracts\CouponCodeGenerator as CouponCodeGeneratorContract;
use Dystcz\LunarRewards\Domain\Discounts\Generators\CouponCodeGenerator;
use Dystcz\LunarRewards\Tests\Stubs\Generators\TestCouponCodeGenerator;
use Dystcz\LunarRewards\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Lunar\Models\Discount;

uses(TestCase::class, RefreshDatabase::class);

it('can generate a coupon code', function () {
    /** @var TestCase $this */

    /** @var CouponCodeGenerator $generator */
    $generator = App::make(CouponCodeGeneratorContract::class);

    $code = $generator->generate();

    $this->assertIsString($code);
    $this->assertEquals(CouponCodeGenerator::LENGTH, strlen($code));

})->group('rewards', 'discounts', 'generators');

it('can recursively generate unique coupon code', function () {
    /** @var TestCase $this */
    App::singleton(CouponCodeGeneratorContract::class, fn () => new TestCouponCodeGenerator);

    /** @var CouponCodeGenerator $generator */
    $generator = App::make(CouponCodeGeneratorContract::class);

    Discount::factory()->create(['coupon' => '10']);
    Discount::factory()->create(['coupon' => '11']);
    Discount::factory()->create(['coupon' => '13']);
    Discount::factory()->create(['coupon' => '14']);
    Discount::factory()->create(['coupon' => '15']);
    Discount::factory()->create(['coupon' => '17']);
    Discount::factory()->create(['coupon' => '18']);
    Discount::factory()->create(['coupon' => '19']);
    Discount::factory()->create(['coupon' => '20']);

    $ranTimes = 0;
    $code = null;

    while (true) {
        $ranTimes = Cache::get('coupon-generator-test');
        if ($ranTimes > 1) {
            break;
        } else {
            Cache::forget('coupon-generator-test');
        }

        $code = $generator->generate();
    }

    $this->assertGreaterThan(1, $ranTimes);
    $this->assertTrue(in_array($code, ['12', '16']));
    $this->assertIsString($code);
    $this->assertEquals(TestCouponCodeGenerator::LENGTH, strlen($code));

})->group('rewards', 'discounts', 'generators');
