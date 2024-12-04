<?php

namespace Dystore\Reviews\Domain\Reviews\Contacts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface HasReviews
{
    public function reviews(): MorphMany;
}
