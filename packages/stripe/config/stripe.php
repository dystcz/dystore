<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sripe payment driver
    |--------------------------------------------------------------------------
    |
    | Drivers for lunar are set in lunar.payments.types.
    | When stripe is set as a driver, this adapter will be used.
    |
    */
    'driver' => 'stripe',

    /*
    |--------------------------------------------------------------------------
    | Sripe payment type
    |--------------------------------------------------------------------------
    |
    | This key serves is an identification for this adapter.
    | That means that stripe driver is handled by this adapter if configured.
    |
    */
    'type' => 'stripe',

    /**
     * Automatic payment methods
     *
     * Enable automatic payment methods.
     */
    'automatic_payment_methods' => true,
];
