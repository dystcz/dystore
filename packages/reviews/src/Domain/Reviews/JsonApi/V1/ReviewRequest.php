<?php

namespace Dystcz\LunarApiReviews\Domain\Reviews\JsonApi\V1;

use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;
use LaravelJsonApi\Validation\Rule as JsonApiRule;

class ReviewRequest extends ResourceRequest
{
    /**
     * Get the validation rules for the resource.
     */
    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string'],
            'purchasable_id' => ['required', 'integer'],
            'purchasable_type' => ['required', 'string'],
            'published_at' => ['nullable', JsonApiRule::dateTime()],
        ];
    }
}
