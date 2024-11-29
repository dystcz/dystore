<?php

namespace Dystcz\LunarProductNotification\Domain\ProductNotifications\Actions;

use Dystcz\LunarProductNotification\Domain\ProductNotifications\Notifications\ProductRestockedNotification;
use Illuminate\Support\Facades\Notification;
use Lunar\Models\ProductVariant;

class NotifySubscribedUsers
{
    public function handle(ProductVariant $productVariant): void
    {
        $notifiables = $productVariant->notifications()->unsent()->get();

        Notification::send(
            $notifiables,
            new ProductRestockedNotification($productVariant)
        );

        $notifiables->each->update(['sent_at' => now()]);
    }
}
