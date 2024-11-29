<?php

return [
    'driver' => 'stripe',

    'type' => 'stripe',

    'automatic_payment_methods' => true,

    'payment_method_types' => ['card'],

    'payment_intent_status_map' => [
        'payment_intent.succeeded' => 'succeeded',
        'payment_intent.canceled' => 'canceled',
        'payment_intent.payment_failed' => 'failed',
    ],

    /*
    |--------------------------------------------------------------------------
    | Capture method policy
    |--------------------------------------------------------------------------
    |
    | Here is where you can set whether you want to capture and charge payments
    | straight away, or create the Payment Intent and release them at a later date.
    |
    | automatic - Capture the payment straight away.
    | manual - Don't take payment straight away and capture later.
    |
    */
    'capture_method' => 'automatic',
];
