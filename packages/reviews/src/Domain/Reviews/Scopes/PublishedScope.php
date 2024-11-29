<?php

namespace Dystcz\LunarApiReviews\Domain\Reviews\Scopes;

use Dystcz\LunarApiReviews\Domain\Reviews\Builders\ReviewBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class PublishedScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        /** @var ReviewBuilder $builder */
        $builder->published();
    }
}
