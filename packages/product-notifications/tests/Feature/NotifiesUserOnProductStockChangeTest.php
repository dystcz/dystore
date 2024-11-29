<?php

use Dystcz\LunarProductNotification\Domain\ProductNotifications\Factories\ProductNotificationFactory;
use Dystcz\LunarProductNotification\Domain\ProductNotifications\Notifications\ProductRestockedNotification;
use Dystcz\LunarProductNotification\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Database\Factories\ProductVariantFactory;

uses(TestCase::class, RefreshDatabase::class);

it('notifies user on product stock change', function () {
    $notification = ProductNotificationFactory::new()
        ->for(
            ProductVariantFactory::new()->state(['stock' => 0]),
            'purchasable'
        )
        ->create();

    $productVariant = $notification->purchasable;

    Notification::fake();

    $productVariant->update(['stock' => 1]);

    Notification::assertSentTo(
        $notification,
        ProductRestockedNotification::class,
        function ($notification, $channels) use ($productVariant) {
            return $notification->productVariant->is($productVariant);
        }
    );
});

it('notifies the user only once', function () {
    $notification = ProductNotificationFactory::new()
        ->for(
            ProductVariantFactory::new()->state(['stock' => 0]),
            'purchasable'
        )
        ->create(['sent_at' => now()]);

    $productVariant = $notification->purchasable;

    Notification::fake();

    $productVariant->update(['stock' => 1]);

    Notification::assertNothingSent();
});
