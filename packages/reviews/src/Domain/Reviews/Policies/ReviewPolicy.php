<?php

namespace Dystore\Reviews\Domain\Reviews\Policies;

use Dystore\Reviews\Domain\Reviews\Models\Review;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ReviewPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?Authenticatable $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?Authenticatable $user, Review $review): bool
    {
        return true;
    }

    /**
     * Determine if the given user can create posts.
     */
    public function create(?Authenticatable $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Authenticatable $user, Review $review): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Authenticatable $user, Review $review): bool
    {
        return true;
    }

    /**
     * Determine whether the user can publish the review.
     */
    public function publish(Authenticatable $user, Review $review): bool
    {
        return true;
    }

    /**
     * Determine whether the user can unpublish the review.
     */
    public function unpublish(Authenticatable $user, Review $review): bool
    {
        return true;
    }
}
