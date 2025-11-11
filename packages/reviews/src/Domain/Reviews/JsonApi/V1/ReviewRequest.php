<?php

namespace Dystore\Reviews\Domain\Reviews\JsonApi\V1;

use Illuminate\Support\Facades\Config;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;
use LaravelJsonApi\Validation\Rule as JsonApiRule;

class ReviewRequest extends ResourceRequest
{
    /**
     * Get the validation rules for the resource.
     *
     * @return array<string,array<int,mixed>>
     */
    public function rules(): array
    {
        $rules = [
            'rating' => [
                Config::get('dystore.reviews.domains.reviews.settings.rating_required', false)
                ? 'required'
                : 'nullable',
                'numeric',
                'min:1',
                'max:5',
            ],
            'name' => [
                Config::get('dystore.reviews.domains.reviews.settings.name_required', false)
                ? 'required'
                : 'nullable',
                'string',
            ],
            'comment' => [
                'nullable',
                'string',
            ],
            'meta' => [
                'nullable',
                'array',
            ],
            'purchasable_id' => [
                Config::get('dystore.reviews.domains.reviews.settings.purchasable_required', false)
                ? 'required'
                : 'nullable',
                'numeric',
            ],
            'purchasable_type' => [
                Config::get('dystore.reviews.domains.reviews.settings.purchasable_required', false)
                ? 'required'
                : 'nullable',
                'string',
            ],
            'published_at' => [
                'nullable',
                JsonApiRule::dateTime(),
            ],
        ];

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string,string>
     */
    public function messages(): array
    {
        return [
            'rating.required' => __('dystore-reviews::validations.reviews.rating.required'),
            'rating.integer' => __('dystore-reviews::validations.reviews.rating.integer'),
            'rating.min' => __('dystore-reviews::validations.reviews.rating.min'),
            'rating.max' => __('dystore-reviews::validations.reviews.rating.max'),
            'name.required' => __('dystore-reviews::validations.reviews.name.required'),
            'name.string' => __('dystore-reviews::validations.reviews.name.string'),
            'comment.string' => __('dystore-reviews::validations.reviews.comment.string'),
            'purchasable_id.required' => __('dystore-reviews::validations.reviews.purchasable_id.required'),
            'purchasable_id.numeric' => __('dystore-reviews::validations.reviews.purchasable_id.numeric'),
            'purchasable_type.required' => __('dystore-reviews::validations.reviews.purchasable_type.required'),
            'purchasable_type.string' => __('dystore-reviews::validations.reviews.purchasable.string'),
        ];
    }
}
