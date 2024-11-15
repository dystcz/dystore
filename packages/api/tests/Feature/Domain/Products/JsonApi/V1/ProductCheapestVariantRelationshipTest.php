<?php

use Dystore\Api\Domain\Products\Models\Product;
use Dystore\Api\Domain\ProductVariants\Factories\ProductVariantFactory;
use Dystore\Api\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

it('can read cheapest variant through relationship', function () {
    /** @var TestCase $this */
    $product = Product::factory()
        ->has(ProductVariantFactory::new()->withPrice()->count(4), 'variants')
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('product_variants')
        ->get(serverUrl("/products/{$product->getRouteKey()}/cheapest_product_variant"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne(
            $product->variants->sortBy(
                fn ($variant) => $variant->prices->sortBy('price')->first()->price,
            )->first(),
        )
        ->assertDoesntHaveIncluded();
})->group('products');
