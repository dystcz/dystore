<?php

namespace Dystore\Reviews\Domain\Reviews\Observers;

use Dystore\Reviews\Domain\Reviews\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class ReviewObserver
{
    /**
     * Handle the Review "creating" event.
     */
    public function creating(Review $review): void
    {
        if (Config::get('dystore.reviews.domains.reviews.settings.auth_required', true)) {
            $review->user_id = $review->user_id ?: Auth::user()?->id;
        }
    }
}
