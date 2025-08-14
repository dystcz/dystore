<?php

use Dystore\Api\Domain\ProductVariants\Models\ProductVariant;
use Dystore\Reviews\Domain\Reviews\Models\Review;
use Dystore\Tests\Reviews\Stubs\Users\User;
use Dystore\Tests\Reviews\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class)
    ->group('reviews');

it('can list reviews through a product variant', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    $review = Review::factory()
        ->for(ProductVariant::factory(), 'purchasable')
        ->create();

    $response = $this
        ->actingAs($user)
        ->jsonApi()
        ->expects('reviews')
        ->get(serverUrl("/variants/{$review->purchasable->getRouteKey()}/reviews"));

    $response->assertFetchedMany([$review]);
});
