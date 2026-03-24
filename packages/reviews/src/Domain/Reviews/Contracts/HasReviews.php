<?php

namespace Dystore\Reviews\Domain\Reviews\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface HasReviews
{
    public function reviews(): MorphMany;
}
