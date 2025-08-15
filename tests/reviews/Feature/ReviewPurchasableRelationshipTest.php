<?php

use Carbon\Carbon;
use Dystore\Api\Base\Enums\PublishedStatus;
use Dystore\Api\Domain\Products\Models\Product;
use Dystore\Api\Domain\ProductVariants\Models\ProductVariant;
use Dystore\Reviews\Domain\Reviews\Models\Review;
use Dystore\Tests\Reviews\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class)
    ->group('reviews');

it('can list product reviews', function () {
    /** @var TestCase $this */
    $review = Review::factory()
        ->for(Product::factory(), 'purchasable')
        ->state([
            'status' => PublishedStatus::PUBLISHED,
            'published_at' => Carbon::now(),
        ])
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('reviews')
        ->get(serverUrl("/products/{$review->purchasable->getRouteKey()}/reviews"));

    $response
        ->assertSuccessful()
        ->assertFetchedMany([$review]);
});

it('can list product variant reviews through a product', function () {
    /** @var TestCase $this */
    $reviews = Review::factory()
        ->for(ProductVariant::factory(), 'purchasable')
        ->count(5)
        ->create([
            'status' => PublishedStatus::PUBLISHED,
            'published_at' => Carbon::now(),
        ]);

    $product = $reviews->first()->purchasable->product;

    $response = $this
        ->jsonApi()
        ->expects('reviews')
        ->get(serverUrl("/products/{$product->getRouteKey()}/product_variant_reviews"));

    $response
        ->assertSuccessful()
        ->assertFetchedMany($reviews);
});

it('can list product variant reviews', function () {
    /** @var TestCase $this */
    $review = Review::factory()
        ->for(ProductVariant::factory(), 'purchasable')
        ->state([
            'status' => PublishedStatus::PUBLISHED,
            'published_at' => Carbon::now(),
        ])
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('reviews')
        ->get(serverUrl("/product_variants/{$review->purchasable->getRouteKey()}/reviews"));

    $response
        ->assertSuccessful()
        ->assertFetchedMany([$review]);
});
