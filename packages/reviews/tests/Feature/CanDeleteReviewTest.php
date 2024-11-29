<?php

use Dystcz\LunarApi\Domain\ProductVariants\Models\ProductVariant;
use Dystcz\LunarApiReviews\Domain\Reviews\Models\Review;
use Dystcz\LunarApiReviews\Tests\Stubs\Users\User;
use Dystcz\LunarApiReviews\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Hub\Models\Staff;

uses(TestCase::class, RefreshDatabase::class);

it('can delete a review', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    $review = Review::factory()
        ->for(ProductVariant::factory(), 'purchasable')
        ->for($user)
        ->create();

    $response = $this
        ->actingAs($user)
        ->jsonApi()
        ->delete('/api/v1/reviews/'.$review->getKey());

    $response->assertNoContent();

    $this->assertDatabaseMissing($review->getTable(), [
        'id' => $review->getKey(),
    ]);
});

it('allows only admin or owner to delete a review', function () {
    /** @var TestCase $this */
    // delete review as random user
    $randomUser = User::factory()->create();
    $reviewOwner = User::factory()->create();

    $review = Review::factory()
        ->for(ProductVariant::factory(), 'purchasable')
        ->for($reviewOwner)
        ->create();

    $response = $this
        ->actingAs($randomUser)
        ->jsonApi()
        ->delete('/api/v1/reviews/'.$review->getRouteKey());

    $response->assertErrorStatus([
        'detail' => 'This action is unauthorized.',
        'status' => '403',
        'title' => 'Forbidden',
    ]);

    // delete review as admin
    $adminUser = Staff::factory()->create(['admin' => true]);

    $response = $this
        ->actingAs($adminUser)
        ->jsonApi()
        ->delete('/api/v1/reviews/'.$review->getRouteKey());

    $response->assertNoContent();
});
