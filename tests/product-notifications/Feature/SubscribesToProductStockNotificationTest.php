<?php

use Dystore\ProductNotifications\Domain\ProductNotifications\Factories\ProductNotificationFactory;
use Dystore\ProductNotifications\Domain\ProductNotifications\Models\ProductNotification;
use Dystore\ProductNotifications\Tests\Stubs\Users\User;
use Dystore\ProductNotifications\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Database\Factories\ProductVariantFactory;

use function Pest\Faker\fake;

uses(TestCase::class, RefreshDatabase::class);

test('user can subscribe to product stock notification', function () {
    /** @var TestCase $this */
    $productVariant = ProductVariantFactory::new()->create();

    $this->actingAs(User::factory()->create());

    $data = [
        'type' => 'product_notifications',
        'attributes' => [
            'email' => $email = fake()->email,
            'purchasable_id' => $productVariant->id,
            'purchasable_type' => $productVariant::class,
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('product_notifications')
        ->withData($data)
        ->post('/api/v1/product_notifications');

    $id = $response
        ->assertCreatedWithServerId('http://localhost/api/v1/product_notifications', $data)
        ->id();

    $this->assertDatabaseHas(
        (new ProductNotification)->getTable(),
        [
            'id' => $id,
            'purchasable_id' => $productVariant->id,
            'purchasable_type' => $productVariant::class,
            'email' => $email,
        ]
    );
});

it('doesnt accept duplicate emails', function () {
    /** @var TestCase $this */
    $this->actingAs(User::factory()->create());

    $notification = ProductNotificationFactory::new()
        ->for(
            ProductVariantFactory::new()->state(['stock' => 0]),
            'purchasable'
        )
        ->create();

    $data = [
        'type' => 'product_notifications',
        'attributes' => [
            'email' => $notification->email,
            'purchasable_id' => $notification->purchasable_id,
            'purchasable_type' => $notification->purchasable_type,
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('product_notifications')
        ->withData($data)
        ->post('/api/v1/product_notifications');

    $expected = [
        'source' => ['pointer' => '/data/attributes/email'],
        'status' => '422',
        'detail' => __('dystore-product-notifications::validations.store_product_notification.email.unique'),
    ];

    $response->assertError(422, $expected);
});

test('user can subscribe again to the same product when previously notified', function () {
    /** @var TestCase $this */
    $this->actingAs(User::factory()->create());

    $notification = ProductNotificationFactory::new()
        ->for(
            ProductVariantFactory::new()->state(['stock' => 0]),
            'purchasable'
        )
        ->isSent()
        ->create();

    $data = [
        'type' => 'product_notifications',
        'attributes' => [
            'email' => $notification->email,
            'purchasable_id' => $notification->purchasable_id,
            'purchasable_type' => $notification->purchasable_type,
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('product_notifications')
        ->withData($data)
        ->post('/api/v1/product_notifications');

    $response
        ->assertCreatedWithServerId('http://localhost/api/v1/product_notifications', $data)
        ->id();
});

test('unauthenticated user can subscribe to product stock notification', function () {
    /** @var TestCase $this */
    $productVariant = ProductVariantFactory::new()->create();

    $data = [
        'type' => 'product_notifications',
        'attributes' => [
            'email' => $email = fake()->email,
            'purchasable_id' => $productVariant->id,
            'purchasable_type' => $productVariant::class,
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('product_notifications')
        ->withData($data)
        ->post('/api/v1/product_notifications');

    $id = $response
        ->assertCreatedWithServerId('http://localhost/api/v1/product_notifications', $data)
        ->id();

    $this->assertDatabaseHas(
        (new ProductNotification)->getTable(),
        [
            'id' => $id,
            'purchasable_id' => $productVariant->id,
            'purchasable_type' => $productVariant::class,
            'email' => $email,
        ]
    );
});
