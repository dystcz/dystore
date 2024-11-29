<?php

namespace Dystcz\LunarNewsletter\Domain\Newsletter\JsonApi\V1;

use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class NewsletterSubscriptionRequest extends ResourceRequest
{
    /**
     * Get the validation rules for the resource.
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

    public function messages()
    {
        return [
            'email.required' => 'Please enter an email address',
            'email.email' => 'Please enter a valid email address',
        ];
    }
}
