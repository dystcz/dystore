<?php

use Dystore\Api\Domain\Products\Models\Product;
use Dystore\Api\Domain\ProductVariants\Factories\ProductVariantFactory;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

it('can read highest price through relationship', function () {
    /** @var TestCase $this */
    $variant = ProductVariantFactory::new()
        ->for(Product::factory(), 'product')
        ->withPrice()
        ->withPrice()
        ->withPrice()
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('prices')
        ->get(serverUrl("/product_variants/{$variant->getRouteKey()}/highest_price"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($variant->prices->sortByDesc('price')->first())
        ->assertDoesntHaveIncluded();
})->group('product_variants');
