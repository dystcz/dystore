<?php

use Dystore\Tests\ProductViews\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;
use Lunar\Models\Product;

uses(TestCase::class, RefreshDatabase::class)
    ->group('product_views');

it('records a view each time the product is shown', function () {
    /** @var TestCase $this */
    $product = Product::factory()->create(['id' => 5]);

    $self = 'http://localhost/api/v1/products/'.$product->getRouteKey();

    $this
        ->jsonApi()
        ->expects('products')
        ->get($self);

    $hits = Redis::command('zcount', ["product:views:{$product->id}", -INF, +INF]);

    expect($hits)->toBe(1);

    $this
        ->jsonApi()
        ->expects('products')
        ->get($self);

    $hits = Redis::command('zcount', ["product:views:{$product->id}", -INF, +INF]);

    expect($hits)->toBe(2);
});
