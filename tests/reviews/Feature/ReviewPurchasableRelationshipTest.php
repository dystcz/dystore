<?php

use Dystore\Api\Base\Enums\PublishedStatus;
use Dystore\Api\Domain\Products\Models\Product;
use Dystore\Api\Domain\ProductVariants\Models\ProductVariant;
use Dystore\Reviews\Domain\Reviews\Models\Review;
use Dystore\Tests\Reviews\Stubs\Users\User;
use Dystore\Tests\Reviews\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class)
    ->group('reviews');

it('can list product reviews', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    $review = Review::factory()
        ->for(Product::factory(), 'purchasable')
        ->state([
            'status' => PublishedStatus::PUBLISHED,
            'published_at' => now(),
        ])
        ->create();

    $response = $this
        ->actingAs($user)
        ->jsonApi()
        ->expects('reviews')
        ->get(serverUrl("/products/{$review->purchasable->getRouteKey()}/reviews"));

    $response->assertFetchedMany([$review]);
});

it('can list product variant reviews', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    $review = Review::factory()
        ->for(ProductVariant::factory(), 'purchasable')
        ->state([
            'status' => PublishedStatus::PUBLISHED,
            'published_at' => now(),
        ])
        ->create();

    $response = $this
        ->actingAs($user)
        ->jsonApi()
        ->expects('reviews')
        ->get(serverUrl("/product_variants/{$review->purchasable->getRouteKey()}/reviews"));

    $response->assertFetchedMany([$review]);
});
