<?php

namespace Dystore\Reviews\Domain\Reviews\Traits;

use Dystore\Reviews\Domain\Reviews\Builders\ReviewBuilder;
use Dystore\Reviews\Domain\Reviews\Models\Review;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait InteractsWithReviews
{
    /**
     * Return reviews relation.
     */
    public function reviews(): MorphMany
    {
        /** @var Model $this */
        return $this->morphMany(Review::class, 'purchasable');
    }

    /**
     * Return average rating.
     */
    public function reviewRating(): Attribute
    {
        return Attribute::make(
            get: function (): ?float {
                /** @var Model $this */
                if ($this->relationLoaded('reviews')) {
                    return $this->reviews?->avg('rating');
                }

                /** @var ReviewBuilder $reviewBuilder */
                $reviewBuilder = $this->reviews();

                return $reviewBuilder->avg('rating');
            }
        );
    }
}
