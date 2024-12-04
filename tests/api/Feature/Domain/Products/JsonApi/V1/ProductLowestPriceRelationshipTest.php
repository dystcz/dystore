<?php

use Dystore\Api\Domain\Products\Factories\ProductFactory;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

it('can read lowest price through relationship', function () {
    /** @var TestCase $this */
    $product = ProductFactory::new()
        ->withPrices(3)
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('prices')
        ->get(serverUrl("/products/{$product->getRouteKey()}/lowest_price"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($product->prices->sortBy('price')->first())
        ->assertDoesntHaveIncluded();
})->group('products');
