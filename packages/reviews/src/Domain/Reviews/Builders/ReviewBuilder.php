<?php

namespace Dystore\Reviews\Domain\Reviews\Builders;

use Carbon\Carbon;
use Dystore\Api\Base\Enums\PublishedStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

/**
 * @extends Builder<Model>
 *
 * @method ReviewBuilder published()
 */
class ReviewBuilder extends Builder
{
    /**
     * Scope a query to only include published models.
     */
    public function published(): self
    {
        return $this
            ->where(
                'status',
                PublishedStatus::PUBLISHED,
            )
            ->where('published_at', '!=', null)
            ->where('published_at', '<=', Carbon::now())
            ->orWhere(function (Builder $query) {
                $query
                    ->when(
                        Auth::check() && Config::get(
                            'dystore.reviews.domains.reviews.settings.include_unpublished_auth_user_reviews',
                            false,
                        ),
                        fn (Builder $query) => $query->where('user_id', Auth::id()),
                    );
            });
    }
}
