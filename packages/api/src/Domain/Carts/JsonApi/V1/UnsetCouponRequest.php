<?php

namespace Dystore\Api\Domain\Carts\JsonApi\V1;

use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class UnsetCouponRequest extends ResourceRequest
{
    /**
     * Get the validation rules for the resource.
     *
     * @return array<string,array<int,mixed>>
     */
    public function rules(): array
    {
        return [
            //
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
            'coupon_code.required' => __('dystore::validations.carts.coupon_code.required'),
            'coupon_code.string' => __('dystore::validations.carts.coupon_code.string'),
        ];
    }
}
