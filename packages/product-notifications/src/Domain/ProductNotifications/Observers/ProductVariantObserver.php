<?php

namespace Dystcz\LunarProductNotification\Domain\ProductNotifications\Observers;

use Dystcz\LunarProductNotification\Domain\ProductNotifications\Actions\NotifySubscribedUsers;
use Lunar\Models\ProductVariant;

class ProductVariantObserver
{
    public $afterCommit = true;

    public function updated(ProductVariant $productVariant): void
    {
        if ($productVariant->wasChanged('stock')) {
            if ($productVariant->getOriginal('stock') !== 0) {
                return;
            }

            if ($productVariant->stock > 0) {
                app(NotifySubscribedUsers::class)->handle($productVariant);
            }
        }
    }
}
