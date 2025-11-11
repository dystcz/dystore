<?php

return [
    'reviews' => [
        'rating' => [
            'required' => 'Hodnocení je povinné.',
            'integer' => 'Hodnocení musí být celé číslo.',
            'min' => 'Hodnocení musí být alespoň 1.',
            'max' => 'Hodnocení nesmí být větší než 5.',
        ],

        'name' => [
            'required' => 'Prosím, zadejte své jméno.',
            'string' => 'Jméno musí být řetězec.',
        ],

        'comment' => [
            'string' => 'Komentář musí být řetězec.',
        ],

        'purchasable_id' => [
            'required' => 'ID položky je povinné.',
            'integer' => 'ID položky musí být celé číslo.',
        ],

        'purchasable_type' => [
            'required' => 'Typ položky je povinný.',
            'string' => 'Typ položky musí být řetězec.',
        ],
    ],
];
