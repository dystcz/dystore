<?php

use Dystcz\LunarApi\Domain\Products\Models\Product;
use Dystcz\LunarApi\Domain\ProductVariants\Models\ProductVariant;
use Dystore\Reviews\Domain\Reviews\Models\Review;
use Dystore\Reviews\Tests\Stubs\Users\User;
use Dystore\Reviews\Tests\Stubs\Users\UserFactory;
use Dystore\Reviews\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

uses(TestCase::class, RefreshDatabase::class);

it('requires logged in user to create a review', function () {
    /** @var TestCase $this */

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
        ->jsonApi()
        ->expects('reviews')
        ->withData($data)
        ->post('/api/v1/reviews');

    $response->assertErrorStatus([
        'detail' => 'Unauthenticated.',
        'status' => '401',
        'title' => 'Unauthorized',
    ]);
})->group('reviews');

it('can save a review with name and meta', function () {
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
            'name' => $review->name,
            'purchasable_id' => $review->purchasable_id,
            'purchasable_type' => $review->purchasable_type,
            'meta' => $review->meta,
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
        'name' => $review->name,
        'meta' => json_encode($review->meta),
    ]);
})->group('reviews');

it('requires a rating and comment, but also a name in order to be saved', function () {

    /** @var TestCase $this */
    Config::set('dystore.reviews.domains.reviews.settings.name_required', true);

    $product = Product::factory()->create();

    /** @var User $user */
    $user = UserFactory::new()->create();

    $data = [
        'type' => 'reviews',
        'attributes' => [
            'purchasable_id' => $product->id,
            'purchasable_type' => $product->getMorphClass(),
        ],
    ];

    $response = $this
        ->actingAs($user)
        ->jsonApi()
        ->expects('reviews')
        ->withData($data)
        ->post('/api/v1/reviews');

    $response
        ->assertErrors(422, [
            ['detail' => __('dystore-reviews::validations.rating.required'), 'status' => '422'],
            ['detail' => __('dystore-reviews::validations.name.required'), 'status' => '422'],
        ]);

})->group('reviews');

it('can store anonymous review when configured', function () {
    /** @var TestCase $this */
    Config::get('dystore.reviews.domains.reviews.settings.auth_required', false);

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
        ->jsonApi()
        ->expects('reviews')
        ->withData($data)
        ->post('/api/v1/reviews');

    $id = $response
        ->assertCreatedWithServerId('http://localhost/api/v1/reviews', $data)
        ->id();

    $this->assertDatabaseHas($review->getTable(), [
        'id' => $id,
        'user_id' => null,
        'purchasable_id' => $review->purchasable_id,
        'purchasable_type' => $review->purchasable_type,
        'comment' => $review->comment,
        'rating' => $review->rating,
    ]);
})->todo()->group('reviews');

it('can create a review for a product', function () {
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
})->group('reviews');

it('can create a review for a product variant', function () {
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
})->group('reviews');
