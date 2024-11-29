<?php

namespace Dystcz\LunarApiProductNotification\Domain\ProductNotifications\JsonApi\V1;

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class ProductNotificationRequest extends ResourceRequest
{
    /**
     * Get the validation rules for the resource.
     *
     * @return array<string,array<int,mixed>>
     */
    public function rules(): array
    {
        $attributes = $this->data['attributes'];

        return [
            'email' => [
                'required',
                'email',
                Rule::unique(config('lunar.database.table_prefix').'product_notifications')
                    ->where(fn ($query) => $query->where([
                        ['email', $attributes['email']],
                        ['purchasable_id', $attributes['purchasable_id']],
                        ['purchasable_type', $attributes['purchasable_type']],
                        ['sent_at', null],
                    ])
                    ),
            ],
            'purchasable_id' => ['required', 'integer'],
            'purchasable_type' => ['required', 'string'],
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
            'email.required' => __('lunar-api-product-notifications::validations.store_product_notification.email.required'),
            'email.email' => __('lunar-api-product-notifications::validations.store_product_notification.email.email'),
            'email.unique' => __('lunar-api-product-notifications::validations.store_product_notification.email.unique'),
        ];
    }
}
