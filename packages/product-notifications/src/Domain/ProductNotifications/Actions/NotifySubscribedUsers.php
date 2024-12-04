<?php

namespace Dystore\ProductNotifications\Domain\ProductNotifications\Actions;

use Dystore\ProductNotifications\Domain\ProductNotifications\Notifications\ProductRestockedNotification;
use Illuminate\Support\Facades\Notification;
use Lunar\Models\Contracts\ProductVariant as ProductVariantContract;

class NotifySubscribedUsers
{
    public function handle(ProductVariantContract $productVariant): void
    {
        /** @var \Dystore\Api\Domain\ProductVariants\Models\ProductVariant $productVariant */
        $notifiables = $productVariant
            ->notifications()
            ->unsent()
            ->get();

        Notification::send(
            $notifiables,
            new ProductRestockedNotification($productVariant)
        );

        $notifiables->each->update(['sent_at' => now()]);
    }
}
