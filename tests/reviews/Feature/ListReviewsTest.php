<?php

use Carbon\Carbon;
use Dystore\Api\Base\Enums\PublishedStatus;
use Dystore\Api\Domain\ProductVariants\Models\ProductVariant;
use Dystore\Reviews\Domain\Reviews\Models\Review;
use Dystore\Tests\Reviews\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Product;

uses(TestCase::class, RefreshDatabase::class)
    ->group('reviews');

it('can list product variant reviews', function () {
    /** @var TestCase $this */
    $reviews = Review::factory()
        ->for(ProductVariant::factory(), 'purchasable')
        ->count(5)
        ->create([
            'published_at' => Carbon::now(),
            'status' => PublishedStatus::PUBLISHED,
        ]);

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
        ->create([
            'published_at' => Carbon::now(),
            'status' => PublishedStatus::PUBLISHED,
        ]);

    $self = 'http://localhost/api/v1/reviews';

    $response = $this
        ->jsonApi()
        ->expects('reviews')
        ->get($self);

    $response
        ->assertSuccessful()
        ->assertFetchedMany($reviews);
});
