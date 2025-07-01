<?php

namespace Dystore\Api\Domain\CartLines\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Resources\JsonApiResource;
use Illuminate\Http\Request;

/**
 * @param  \Dystore\Api\Domain\CartLines\Models\CartLine  $resource
 */
class CartLineResource extends JsonApiResource
{
    /**
     * Get the resource's attributes.
     *
     * @param  Request|null  $request
     */
    public function attributes($request): iterable
    {
        return parent::attributes($request);
    }
}
