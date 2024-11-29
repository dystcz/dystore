<?php

namespace Dystcz\LunarApiReviews\Domain\Reviews\Observers;

use Dystcz\LunarApiReviews\Domain\Reviews\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class ReviewObserver
{
    /**
     * Handle the Review "creating" event.
     */
    public function creating(Review $review): void
    {
        if (Config::get('lunar-api.reviews.domains.reviews.settings.auth_required', true)) {
            $review->user_id = $review->user_id ?: Auth::user()?->id;
        }
    }
}
