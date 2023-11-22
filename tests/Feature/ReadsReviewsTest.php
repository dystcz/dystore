<?php

use Carbon\Carbon;
use Dystcz\LunarApi\Domain\ProductVariants\Models\ProductVariant;
use Dystcz\LunarApiReviews\Domain\Reviews\Models\Review;
use Dystcz\LunarApiReviews\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

it('can list reviews', function () {
    /** @var TestCase $this */
    $reviews = Review::factory()
        ->for(ProductVariant::factory(), 'purchasable')
        ->count(5)
        ->create(['published_at' => Carbon::now()]);

    $self = 'http://localhost/api/v1/reviews';

    $response = $this
        ->jsonApi()
        ->expects('reviews')
        ->get($self);

    $response->assertFetchedMany($reviews);
});

it('can show a single review', function () {
    /** @var TestCase $this */
    /** @var Review $review */
    $review = Review::factory()
        ->for(ProductVariant::factory(), 'purchasable')
        ->create();

    $self = 'http://localhost/api/v1/reviews/'.$review->getRouteKey();

    $response = $this
        ->jsonApi()
        ->expects('reviews')
        ->get($self);

    $response->assertFetchedOne($review);
});
