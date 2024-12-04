<?php

use Dystcz\LunarApi\Domain\ProductVariants\Models\ProductVariant;
use Dystore\Reviews\Domain\Reviews\Models\Review;
use Dystore\Reviews\Tests\Stubs\Users\User;
use Dystore\Reviews\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

it('can as purchasable read reviews', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    $review = Review::factory()
        ->for(ProductVariant::factory(), 'purchasable')
        ->create();

    $response = $this
        ->actingAs($user)
        ->jsonApi()
        ->expects('reviews')
        ->get('/api/v1/variants/'.$review->purchasable->getRouteKey().'/reviews');

    $response->assertFetchedMany([$review]);
})->todo();
