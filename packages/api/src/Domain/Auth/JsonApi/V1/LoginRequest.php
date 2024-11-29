<?php

namespace Dystcz\LunarApi\Domain\Auth\JsonApi\V1;

use Dystcz\LunarApi\Facades\LunarApi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class LoginRequest extends ResourceRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules for the resource.
     *
     * @return array<string,array>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email',
            ],
            'password' => [
                'required',
                'string',
            ],
        ];
    }

    /**
     * Get the validation messages for the request.
     *
     * @return array<string,string>
     */
    public function messages(): array
    {
        return [
            'email.required' => __('lunar-api::validations.auth.email.required'),
            'email.string' => __('lunar-api::validations.auth.email.string'),
            'email.email' => __('lunar-api::validations.auth.email.email'),
            'password.required' => __('lunar-api::validations.auth.password.required'),
            'password.string' => __('lunar-api::validations.auth.password.string'),
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! Auth::guard(LunarApi::getAuthGuard())->attempt([
                'email' => $this->input('data.attributes.email'),
                'password' => $this->input('data.attributes.password'),
            ])) {
                $validator->errors()->add(
                    'password',
                    __('lunar-api::validations.auth.attempt.failed'),
                );
            }
        });
    }
}
