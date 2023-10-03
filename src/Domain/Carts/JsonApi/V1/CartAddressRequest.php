<?php

namespace Dystcz\LunarApi\Domain\Carts\JsonApi\V1;

use Closure;
use Dystcz\LunarApi\Domain\Carts\Models\CartAddress;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;
use Lunar\Facades\CartSession;

class CartAddressRequest extends ResourceRequest
{
    /**
     * Get the validation rules for the resource.
     */
    public function rules(): array
    {
        return [
            'title' => [
                'nullable',
                'string',
            ],
            'first_name' => [
                Rule::requiredIf($this->isShippingAddress()),
                'string',
            ],
            'last_name' => [
                Rule::requiredIf($this->isShippingAddress()),
                'string',
            ],
            'company_name' => [
                'nullable',
                'string',
            ],
            'line_one' => [
                Rule::requiredIf($this->isShippingAddress()),
                'string',
            ],
            'line_two' => [
                'nullable',
                'string',
            ],
            'line_three' => [
                'nullable',
                'string',
            ],
            'city' => [
                Rule::requiredIf($this->isShippingAddress()),
                'string',
            ],
            'state' => [
                'nullable',
                'string',
            ],
            'postcode' => [
                Rule::requiredIf($this->isShippingAddress()),
                'string',
            ],
            'delivery_instructions' => [
                'nullable',
                'string',
            ],
            'contact_email' => [
                Rule::requiredIf($this->isShippingAddress()),
                'string',
            ],
            'contact_phone' => [
                Rule::requiredIf($this->isShippingAddress()),
                'string',
            ],
            'shipping_option' => [
                'nullable',
                'string',
            ],
            'address_type' => [
                'required',
                'string',
                Rule::in(['shipping', 'billing']),
            ],

            'cart' => [\LaravelJsonApi\Validation\Rule::toOne(), 'required'],
            'country' => [\LaravelJsonApi\Validation\Rule::toOne(), 'required'],
        ];
    }

    /**
     * Determine if address type is shipping.
     */
    protected function isShippingAddress(): Closure
    {
        return function () {
            $this->input('data.attributes.address_type') === 'shipping';
        };
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        if (! $this->isUpdating()) {
            return;
        }

        $validator->after(function ($validator) {
            /** @var CartAddress $cartAddress */
            $cartAddress = $this->model();

            $newCartId = (int) $this->input('data.relationships.cart.data.id');

            if (! $newCartId) {
                return;
            }

            if (! in_array($newCartId, [$cartAddress->cart_id, CartSession::current()->id])) {
                $validator->errors()->add(
                    'cart',
                    'Cannot change the cart of a cart address.'
                );
            }
        });
    }
}