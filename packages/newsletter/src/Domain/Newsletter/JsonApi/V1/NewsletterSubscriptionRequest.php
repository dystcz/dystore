<?php

namespace Dystore\Newsletter\Domain\Newsletter\JsonApi\V1;

use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class NewsletterSubscriptionRequest extends ResourceRequest
{
    /**
     * Get the validation rules for the resource.
     *
     * @return array<string,array<int,mixed>>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email:filter',
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
            'email.required' => __('dystore-newsletter::validations.newsletter_subscription.email.required'),
            'email.email' => __('dystore-newsletter::validations.newsletter_subscription.email.email'),
        ];
    }
}
