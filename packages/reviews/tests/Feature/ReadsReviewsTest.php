<?php

use Carbon\Carbon;
use Dystcz\LunarApi\Domain\ProductVariants\Models\ProductVariant;
use Dystcz\LunarApiReviews\Domain\Reviews\Models\Review;
use Dystcz\LunarApiReviews\Tests\Stubs\Users\User;
use Dystcz\LunarApiReviews\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Product;

uses(TestCase::class, RefreshDatabase::class);

it('can list product variant reviews', function () {
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

    $response
        ->assertSuccessful()
        ->assertFetchedMany($reviews);
});

it('can list product reviews', function () {
    /** @var TestCase $this */
    $reviews = Review::factory()
        ->for(Product::factory(), 'purchasable')
        ->count(4)
        ->create(['published_at' => Carbon::now()]);

    $self = 'http://localhost/api/v1/reviews';

    $response = $this
        ->jsonApi()
        ->expects('reviews')
        ->get($self);

    $response
        ->assertSuccessful()
        ->assertFetchedMany($reviews);
});

it('can show a single review', function () {
    /** @var TestCase $this */
    /** @var Review $review */
    $review = Review::factory()
        ->for(ProductVariant::factory(), 'purchasable')
        ->create([
            'published_at' => Carbon::now(),
        ]);

    $self = 'http://localhost/api/v1/reviews/'.$review->getRouteKey();

    $response = $this
        ->jsonApi()
        ->expects('reviews')
        ->get($self);

    $response
        ->assertSuccessful()
        ->assertFetchedOne($review);
});

it('can show unpublished reviews belonging to logged in user', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    $reviews = Review::factory()
        ->for(Product::factory(), 'purchasable')
        ->count(3)
        ->create([
            'user_id' => $user->id,
        ]);

    $self = 'http://localhost/api/v1/reviews';

    $response = $this
        ->actingAs($user)
        ->jsonApi()
        ->expects('reviews')
        ->get($self);

    $response
        ->assertSuccessful()
        ->assertFetchedMany($reviews);
});

it('does not show unpublished reviews', function () {
    /** @var TestCase $this */
    $reviews = Review::factory()
        ->for(Product::factory(), 'purchasable')
        ->count(3);

    $self = 'http://localhost/api/v1/reviews';

    $response = $this
        ->jsonApi()
        ->expects('reviews')
        ->get($self);

    $response
        ->assertSuccessful()
        ->assertFetchedNone();
});
