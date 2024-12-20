<?php

namespace Dystore\Api\Domain\Products\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Builder<Model>
 */
class ProductBuilder extends Builder
{
    /**
     * Scope a query to only include published models.
     */
    public function published(): self
    {
        return $this->where('status', '!=', 'draft');
    }
}
