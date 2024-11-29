<?php

namespace Dystore\Api\Domain\ShippingOptions\JsonApi\V1;

use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class SetShippingOptionRequest extends ResourceRequest
{
    /**
     * Get the validation rules for the resource.
     *
     * @return array<string,array<int,mixed>>
     */
    public function rules(): array
    {
        return [
            'shipping_option' => [
                'required',
                'string',
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
            'shipping_option.required' => __('lunar-api::validations.shipping.set_shipping_option.shipping_option.required'),
            'shipping_option.string' => __('lunar-api::validations.shipping.set_shipping_option.shipping_option.string'),
        ];
    }
}
