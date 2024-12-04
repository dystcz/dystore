<?php

use Dystore\Api\Domain\ProductVariants\Models\ProductVariant;
use Dystore\Reviews\Domain\Reviews\Models\Review;
use Dystore\Reviews\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Hub\Models\Staff;

uses(TestCase::class, RefreshDatabase::class);

it('can publish a review', function () {
    $review = Review::factory()
        ->for(ProductVariant::factory(), 'purchasable')
        ->create([
            'published_at' => null,
        ]);

    $response = $this
        ->actingAs($review->user)
        ->jsonApi()
        ->expects('reviews')
        ->post("/api/v1/reviews/{$review->getRouteKey()}/-actions/publish");

    $response->assertFetchedOne($review);

    expect($response->json('data.attributes.published_at'))
        ->toBe($review->fresh()->published_at->format('Y-m-d H:i:s'));
})->skip('Currently not supported by the API');

it('can unpublish a review', function () {
    $user = Staff::factory()->create(['admin' => true]);

    $review = Review::factory()
        ->for(ProductVariant::factory(), 'purchasable')
        ->create();

    $response = $this
        ->actingAs($user)
        ->jsonApi()
        ->expects('reviews')
        ->delete("http://localhost/api/v1/reviews/{$review->getRouteKey()}/-actions/unpublish");

    $response->assertNoContent();

    expect($review->fresh()->published_at)->toBe(null);
})->skip('Currently not supported by the API');
