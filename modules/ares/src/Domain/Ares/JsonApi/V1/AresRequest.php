<?php

namespace Modules\DystoreAres\Domain\Ares\JsonApi\V1;

use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class AresRequest extends ResourceRequest
{
    /**
     * Get the validation rules for the resource.
     *
     * @return array<string,array>
     */
    public function rules(): array
    {
        return [
            'company_in' => [
                'required',
                'string',
                'min:8',
                'max:8',
            ],
        ];
    }

    /**
     * Get the validation messages.
     *
     * @return array<string,string>
     */
    public function messages(): array
    {
        return [
            'company_in.required' => 'IČ je povinné pole.',
            'company_in.string' => 'IČ musí být řetězec.',
            'company_in.min' => 'IČ musí mít 8 číslic.',
            'company_in.max' => 'IČ musí mít 8 číslic.',
        ];
    }
}
