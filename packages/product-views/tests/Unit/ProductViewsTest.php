<?php

use Dystcz\LunarApiProductViews\LunarApiProductViews;
use Dystcz\LunarApiProductViews\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

uses(TestCase::class, RefreshDatabase::class);

it('can record a view', function () {
    /** @var TestCase $this */
    $productId = 1;
    app(LunarApiProductViews::class)->record($productId);
    app(LunarApiProductViews::class)->record($productId);

    expect(Redis::zRange("product:views:{$productId}", 0, -1))
        ->toHaveCount(2);
})->group('product-views');

it('removes old entries', function () {
    /** @var TestCase $this */
    $productId = 2;

    Redis::zAdd("product:views:{$productId}", time() - 60 * 60, Str::uuid()->toString());

    expect(Redis::zRange("product:views:{$productId}", 0, -1))
        ->toHaveCount(1);

    app(LunarApiProductViews::class)->record($productId);

    expect(Redis::zRange("product:views:{$productId}", 0, -1))
        ->toHaveCount(1);
})->group('product-views');

it('returns a list of product\'s ids sorted by most viewed', function () {
    /** @var TestCase $this */
    app(LunarApiProductViews::class)->record(3);
    app(LunarApiProductViews::class)->record(4);
    app(LunarApiProductViews::class)->record(4);

    $sorted = app(LunarApiProductViews::class)->sorted();

    expect($sorted)->toBe([4, 3]);
})->group('product-views');
