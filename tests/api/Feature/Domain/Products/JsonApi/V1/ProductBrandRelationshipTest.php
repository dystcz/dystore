<?php

use Dystore\Api\Domain\Products\Models\Product;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Brand;

uses(TestCase::class, RefreshDatabase::class);

it('can read brand through relationship', function () {
    /** @var TestCase $this */
    $product = Product::factory()
        ->has(Brand::factory())
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('brands')
        ->get(serverUrl("/products/{$product->getRouteKey()}/brand"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($product->brand)
        ->assertDoesntHaveIncluded();
})->group('products');
