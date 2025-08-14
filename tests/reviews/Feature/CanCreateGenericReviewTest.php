<?php

use Dystore\Reviews\Domain\Reviews\Models\Review;
use Dystore\Tests\Reviews\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

uses(TestCase::class, RefreshDatabase::class)
    ->group('reviews');

it('can store anonymous review without purchasable when configured', function () {
    /** @var TestCase $this */
    Config::set('dystore.reviews.domains.reviews.settings.auth_required', false);
    Config::set('dystore.reviews.domains.reviews.settings.purchasable_required', false);

    /** @var Review $review */
    $review = Review::factory()->make();

    $data = [
        'type' => 'reviews',
        'attributes' => [
            'comment' => $review->comment,
            'rating' => $review->rating,
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
        'comment' => $review->comment,
        'rating' => $review->rating,
    ]);
})->group('reviews');
