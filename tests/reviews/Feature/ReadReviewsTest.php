<?php

use Carbon\Carbon;
use Dystore\Api\Base\Enums\PublishedStatus;
use Dystore\Api\Domain\Media\Factories\MediaFactory;
use Dystore\Api\Domain\ProductVariants\Models\ProductVariant;
use Dystore\Reviews\Domain\Reviews\Models\Review;
use Dystore\Tests\Reviews\Stubs\Users\User;
use Dystore\Tests\Reviews\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Product;

uses(TestCase::class, RefreshDatabase::class)
    ->group('reviews');

it('can show a single review', function () {
    /** @var TestCase $this */
    /** @var Review $review */
    $review = Review::factory()
        ->for(ProductVariant::factory(), 'purchasable')
        ->create([
            'published_at' => Carbon::now(),
            'status' => PublishedStatus::PUBLISHED,
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

it('can show a review with images included', function () {
    /** @var TestCase $this */
    /** @var Review $review */
    $review = Review::factory()
        ->for(ProductVariant::factory(), 'purchasable')
        ->has(MediaFactory::new(), 'images')
        ->create([
            'published_at' => Carbon::now(),
            'status' => PublishedStatus::PUBLISHED,
        ]);

    $self = 'http://localhost/api/v1/reviews/'.$review->getRouteKey();

    $response = $this
        ->jsonApi()
        ->expects('reviews')
        ->includePaths('images')
        ->get($self);

    $response
        ->assertSuccessful()
        ->assertFetchedOne($review)
        ->assertIsIncluded('media', $review->images->first());
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
