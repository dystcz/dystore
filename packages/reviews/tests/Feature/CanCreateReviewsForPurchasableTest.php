<?php

use Dystcz\LunarApi\Domain\Products\Models\Product;
use Dystcz\LunarApi\Domain\ProductVariants\Models\ProductVariant;
use Dystcz\LunarApiReviews\Domain\Reviews\Models\Review;
use Dystcz\LunarApiReviews\Tests\Stubs\Users\User;
use Dystcz\LunarApiReviews\Tests\Stubs\Users\UserFactory;
use Dystcz\LunarApiReviews\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

it('can create a review for a Product', function () {
    /** @var TestCase $this */

    /** @var User $user */
    $user = UserFactory::new()->create();

    /** @var Review $review */
    $review = Review::factory()
        ->for(Product::factory(), 'purchasable')
        ->make();

    $data = [
        'type' => 'reviews',
        'attributes' => [
            'comment' => $review->comment,
            'rating' => $review->rating,
            'purchasable_id' => $review->purchasable_id,
            'purchasable_type' => $review->purchasable_type,
        ],
    ];

    $response = $this
        ->actingAs($user)
        ->jsonApi()
        ->expects('reviews')
        ->withData($data)
        ->post('/api/v1/reviews');

    $id = $response
        ->assertCreatedWithServerId('http://localhost/api/v1/reviews', $data)
        ->id();

    $this->assertDatabaseHas($review->getTable(), [
        'id' => $id,
        'user_id' => $user->id,
        'purchasable_id' => $review->purchasable_id,
        'purchasable_type' => $review->purchasable_type,
        'comment' => $review->comment,
        'rating' => $review->rating,
    ]);
});

it('can create a review for a ProductVariant', function () {
    /** @var TestCase $this */

    /** @var User $user */
    $user = UserFactory::new()->create();

    /** @var Review $review */
    $review = Review::factory()
        ->for(ProductVariant::factory(), 'purchasable')
        ->make();

    $data = [
        'type' => 'reviews',
        'attributes' => [
            'comment' => $review->comment,
            'rating' => $review->rating,
            'purchasable_id' => $review->purchasable_id,
            'purchasable_type' => $review->purchasable_type,
        ],
    ];

    $response = $this
        ->actingAs($user)
        ->jsonApi()
        ->expects('reviews')
        ->withData($data)
        ->post('/api/v1/reviews');

    $id = $response
        ->assertCreatedWithServerId('http://localhost/api/v1/reviews', $data)
        ->id();

    $this->assertDatabaseHas($review->getTable(), [
        'id' => $id,
        'user_id' => $user->id,
        'purchasable_id' => $review->purchasable_id,
        'purchasable_type' => $review->purchasable_type,
        'comment' => $review->comment,
        'rating' => $review->rating,
    ]);
});
