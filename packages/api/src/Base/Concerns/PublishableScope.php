<?php

namespace Dystore\Api\Base\Concerns;

use Carbon\Carbon;
use Dystore\Api\Base\Enums\PublishedStatus;
use Illuminate\Database\Eloquent\Builder;

trait PublishableScope
{
    /**
     * Scope published.
     */
    public function published(): self
    {
        /** @var Builder */
        $builder = $this;

        return $builder
            ->where(
                'status',
                PublishedStatus::PUBLISHED,
            )
            ->where(fn (Builder $query) => $query
                ->where('published_at', '<=', Carbon::now())
                ->orWhere('published_at', null)
            );
    }
}
