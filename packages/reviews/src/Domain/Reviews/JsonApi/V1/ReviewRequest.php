<?php

namespace Dystcz\LunarApiReviews\Domain\Reviews\JsonApi\V1;

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
        return [
            'rating' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],
            'comment' => [
                'nullable',
                'string',
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
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string,string>
     */
    public function messages(): array
    {
        return [
            // 'email.required' => __('lunar-api-product-notifications::validations.store_product_notification.email.required'),
            // 'email.email' => __('lunar-api-product-notifications::validations.store_product_notification.email.email'),
            // 'email.unique' => __('lunar-api-product-notifications::validations.store_product_notification.email.unique'),
        ];
    }
}
