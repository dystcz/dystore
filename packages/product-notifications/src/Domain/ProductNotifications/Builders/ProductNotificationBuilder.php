<?php

namespace Dystcz\LunarApiProductNotification\Domain\ProductNotifications\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * @method self unsent()
 */
class ProductNotificationBuilder extends Builder
{
    /**
     * Scope unsent notifications.
     */
    public function unsent(): self
    {
        return $this->where('sent_at', '=', null);
    }
}
