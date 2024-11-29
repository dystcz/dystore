<?php

use Dystcz\LunarApiProductNotification\Domain\ProductNotifications\Factories\ProductNotificationFactory;
use Dystcz\LunarApiProductNotification\Domain\ProductNotifications\Models\ProductNotification;
use Dystcz\LunarApiProductNotification\Tests\Stubs\Users\User;
use Dystcz\LunarApiProductNotification\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Database\Factories\ProductVariantFactory;

use function Pest\Faker\fake;

uses(TestCase::class, RefreshDatabase::class);

test('user can subscribe to product stock notification', function () {
    $productVariant = ProductVariantFactory::new()->create();

    $this->actingAs(User::factory()->create());

    $data = [
        'type' => 'product-notifications',
        'attributes' => [
            'email' => $email = fake()->email,
            'purchasable_id' => $productVariant->id,
            'purchasable_type' => $productVariant::class,
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('product-notifications')
        ->withData($data)
        ->post('/api/v1/product-notifications');

    $id = $response
        ->assertCreatedWithServerId('http://localhost/api/v1/product-notifications', $data)
        ->id();

    $this->assertDatabaseHas(
        (new ProductNotification())->getTable(),
        [
            'id' => $id,
            'purchasable_id' => $productVariant->id,
            'purchasable_type' => $productVariant::class,
            'email' => $email,
        ]
    );
});

it('doesnt accept duplicate emails', function () {
    $this->actingAs(User::factory()->create());

    $notification = ProductNotificationFactory::new()
        ->for(
            ProductVariantFactory::new()->state(['stock' => 0]),
            'purchasable'
        )
        ->create();

    $data = [
        'type' => 'product-notifications',
        'attributes' => [
            'email' => $notification->email,
            'purchasable_id' => $notification->purchasable_id,
            'purchasable_type' => $notification->purchasable_type,
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('product-notifications')
        ->withData($data)
        ->post('/api/v1/product-notifications');

    $expected = [
        'source' => ['pointer' => '/data/attributes/email'],
        'status' => '422',
        'detail' => __('lunar-api-product-notifications::validations.store_product_notification.email.unique'),
    ];

    $response->assertError(422, $expected);
});

test('user can subscribe again to the same product when previously notified', function () {

    $this->actingAs(User::factory()->create());

    $notification = ProductNotificationFactory::new()
        ->for(
            ProductVariantFactory::new()->state(['stock' => 0]),
            'purchasable'
        )
        ->isSent()
        ->create();

    $data = [
        'type' => 'product-notifications',
        'attributes' => [
            'email' => $notification->email,
            'purchasable_id' => $notification->purchasable_id,
            'purchasable_type' => $notification->purchasable_type,
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('product-notifications')
        ->withData($data)
        ->post('/api/v1/product-notifications');

    $response
        ->assertCreatedWithServerId('http://localhost/api/v1/product-notifications', $data)
        ->id();
});

test('unauthenticated user can subscribe to product stock notification', function () {
    $productVariant = ProductVariantFactory::new()->create();

    $data = [
        'type' => 'product-notifications',
        'attributes' => [
            'email' => $email = fake()->email,
            'purchasable_id' => $productVariant->id,
            'purchasable_type' => $productVariant::class,
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('product-notifications')
        ->withData($data)
        ->post('/api/v1/product-notifications');

    $id = $response
        ->assertCreatedWithServerId('http://localhost/api/v1/product-notifications', $data)
        ->id();

    $this->assertDatabaseHas(
        (new ProductNotification())->getTable(),
        [
            'id' => $id,
            'purchasable_id' => $productVariant->id,
            'purchasable_type' => $productVariant::class,
            'email' => $email,
        ]
    );
});
