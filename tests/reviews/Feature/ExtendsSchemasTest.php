<?php

use Dystore\Api\Domain\Products\Models\Product;
use Dystore\Api\Domain\ProductVariants\Models\ProductVariant;
use Dystore\Reviews\Domain\Reviews\Models\Review;
use Dystore\Tests\Reviews\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class)
    ->group('reviews', 'manifests', 'extending');

it('reviews extend "ProductSchema" with reviews relationship', function () {
    /** @var TestCase $this */
    $product = Product::factory()
        ->has(
            ProductVariant::factory()
                ->has(
                    Review::factory()->count(3),
                    'reviews',
                ),
            'variants'
        )
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('reviews')
        ->get(serverUrl("/products/{$product->getRouteKey()}/reviews"));

    $response->assertFetchedMany($product->reviews);
});

it('reviews extend "ProductVariantSchema" with reviews relationship', function () {
    /** @var TestCase $this */
    $productVariant = ProductVariant::factory()
        ->has(
            Review::factory()->count(3),
            'reviews',
        )
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('reviews')
        ->get(serverUrl("/product_variants/{$productVariant->getRouteKey()}/reviews"));

    $response->assertFetchedMany($productVariant->reviews);
});
