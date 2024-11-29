<?php

return [
    'rating' => [
        'required' => 'Rating is required.',
        'integer' => 'Rating must be an integer.',
        'min' => 'Rating must be at least 1.',
        'max' => 'Rating may not be greater than 5.',
    ],

    'name' => [
        'required' => 'Please enter your name.',
        'string' => 'Name must be a string.',
    ],

    'comment' => [
        'string' => 'Comment must be a string.',
    ],

    'purchasable_id' => [
        'required' => 'Purchasable ID is required.',
        'integer' => 'Purchasable ID must be an integer.',
    ],

    'purchasable_type' => [
        'required' => 'Purchasable type is required.',
        'string' => 'Purchasable type must be a string.',
    ],
];
