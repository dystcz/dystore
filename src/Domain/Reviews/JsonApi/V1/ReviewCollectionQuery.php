<?php

namespace Dystcz\LunarApiReviews\Domain\Reviews\JsonApi\V1;

use Dystcz\LunarApi\Domain\Collections\JsonApi\V1\CollectionQuery;

class ReviewCollectionQuery extends CollectionQuery
{
    /**
     * Get the validation rules that apply to the request query parameters.
     *
     * @return array<string,array<int,mixed>>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
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
            ...parent::messages(),
        ];
    }
}