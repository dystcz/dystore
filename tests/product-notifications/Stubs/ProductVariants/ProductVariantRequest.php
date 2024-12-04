<?php

namespace Dystore\Tests\ProductNotifications\Stubs\ProductVariants;

use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;
use LaravelJsonApi\Validation\Rule as JsonApiRule;

class ProductVariantRequest extends ResourceRequest
{
    /**
     * Get the validation rules for the resource.
     */
    public function rules(): array
    {
        return [
            'reviews' => [JsonApiRule::toMany()],
        ];
    }
}
