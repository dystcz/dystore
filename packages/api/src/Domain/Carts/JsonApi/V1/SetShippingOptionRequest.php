<?php

namespace Dystcz\LunarApi\Domain\Carts\JsonApi\V1;

use Dystcz\LunarApi\Domain\Addresses\Http\Enums\AddressType;
use Illuminate\Validation\Rule;
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
            'address_type' => [
                'required',
                'string',
                Rule::in([
                    AddressType::SHIPPING->value,
                    AddressType::BILLING->value,
                ]),
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
            'address_type.required' => __('lunar-api::validations.cart_addresses.address_type.required'),
            'address_type.string' => __('lunar-api::validations.cart_addresses.address_type.string'),
            'address_type.in' => __('lunar-api::validations.cart_addresses.address_type.in', [
                'types' => implode(', ', [
                    AddressType::SHIPPING->value,
                    AddressType::BILLING->value,
                ]),
            ]),
        ];
    }
}
