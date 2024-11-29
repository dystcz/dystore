<?php

namespace Dystcz\LunarApiReviews\Domain\Reviews\JsonApi\V1;

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
                'required',
                'integer',
                'min:1',
                'max:5',
            ],
            'name' => [
                'nullable',
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
                'required',
                'integer',
            ],
            'purchasable_type' => [
                'required',
                'string',
            ],
            'published_at' => [
                'nullable',
                JsonApiRule::dateTime(),
            ],
        ];

        if (Config::get('lunar-api.reviews.domains.reviews.settings.name_required', false)) {
            $rules['name'] = ['required', ...$rules['name']];
        }

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
            'rating.required' => __('lunar-api-reviews::validations.rating.required'),
            'rating.integer' => __('lunar-api-reviews::validations.rating.integer'),
            'rating.min' => __('lunar-api-reviews::validations.rating.min'),
            'rating.max' => __('lunar-api-reviews::validations.rating.max'),
            'name.required' => __('lunar-api-reviews::validations.name.required'),
            'name.string' => __('lunar-api-reviews::validations.name.string'),
            'comment.string' => __('lunar-api-reviews::validations.comment.string'),
            'purchasable_id.required' => __('lunar-api-reviews::validations.purchasable_id.required'),
            'purchasable_id.integer' => __('lunar-api-reviews::validations.surchasable_id.integer'),
            'purchasable_type.required' => __('lunar-api-reviews::validations.purchasable_type.required'),
            'purchasable_type.string' => __('lunar-api-reviews::validations.strchasable_type.string'),
        ];
    }
}
