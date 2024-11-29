<?php

return [
    'driver' => 'stripe',
    'type' => 'stripe',

    'payment_intent_status_map' => [
        'payment_intent.succeeded' => 'succeeded',
        'payment_intent.canceled' => 'canceled',
        'payment_intent.payment_failed' => 'failed',
    ],
];
