<?php

use Dystcz\LunarApi\Domain\Products\Factories\ProductFactory;
use Dystcz\LunarApi\Domain\ProductVariants\Factories\ProductVariantFactory;
use Dystcz\LunarApiReviews\Domain\Reviews\Factories\ReviewFactory;
use Dystcz\LunarApiReviews\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

it('reviews extend ProductSchema with reviews relationship', function () {
    $product = ProductFactory::new()
        ->has(
            ProductVariantFactory::new()->has(ReviewFactory::new()->count(3), 'reviews'),
            'variants'
        )
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('reviews')
        ->get("/api/v1/products/{$product->getRouteKey()}/reviews");

    $response->assertFetchedMany($product->reviews);
});

it('reviews extend ProductVariantSchema with reviews relationship', function () {
    $productVariant = ProductVariantFactory::new()
        ->has(ReviewFactory::new()->count(3), 'reviews')
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('reviews')
        ->get("/api/v1/variants/{$productVariant->getRouteKey()}/reviews");

    $response->assertFetchedMany($productVariant->reviews);
});