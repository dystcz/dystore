<?php

use Dystore\Api\Domain\Products\Models\Product;
use Dystore\Api\Domain\ProductVariants\Factories\ProductVariantFactory;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

it('can read product through relationship', function () {
    /** @var TestCase $this */
    $variants = ProductVariantFactory::new()
        ->for(Product::factory(), 'product')
        ->count(2)
        ->create();

    $variant = $variants->first();

    $response = $this
        ->jsonApi()
        ->expects('products')
        ->get(serverUrl("/product_variants/{$variant->getRouteKey()}/product"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($variant->product)
        ->assertDoesntHaveIncluded();
})->group('product_variants');
