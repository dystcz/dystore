<?php

return [
    /*
     * Stripe will sign each webhook using a secret. You can find the used secret at the
     * webhook configuration settings: https://dashboard.stripe.com/account/webhooks.
     */
    'signing_secret' => env('STRIPE_WEBHOOK_SECRET'),

    /*
     * You can define a default job that should be run for all other Stripe event type
     * without a job defined in next configuration.
     * You may leave it empty to store the job in database but without processing it.
     */
    'default_job' => \Dystcz\LunarApiStripeAdapter\Jobs\Webhooks\HandleOtherEvent::class,

    /*
     * You can define the job that should be run when a certain webhook hits your application
     * here. The key is the name of the Stripe event type with the `.` replaced by a `_`.
     *
     * You can find a list of Stripe webhook types here:
     * https://stripe.com/docs/api#event_types.
     */
    'jobs' => [
        'payment_intent_created' => \Dystcz\LunarApiStripeAdapter\Jobs\Webhooks\HandlePaymentIntentCreated::class,
        'payment_intent_succeeded' => \Dystcz\LunarApiStripeAdapter\Jobs\Webhooks\HandlePaymentIntentSucceeded::class,
        'payment_intent_payment_failed' => \Dystcz\LunarApiStripeAdapter\Jobs\Webhooks\HandlePaymentIntentFailed::class,
        'payment_intent_cancelled' => \Dystcz\LunarApiStripeAdapter\Jobs\Webhooks\HandlePaymentIntentCancelled::class,
        // 'payment_intent_processing' => \Dystcz\LunarApiStripeAdapter\Jobs\Webhooks\HandlePaymentIntentProcessing::class,
        // 'payment_intent_requires_action' => \Dystcz\LunarApiStripeAdapter\Jobs\Webhooks\HandlePaymentIntentRequiresAction::class,
        // 'source_chargeable' => \Dystcz\LunarApiStripeAdapter\Jobs\Webhooks\HandleChargeableSource::class,
        // 'charge_failed' => \Dystcz\LunarApiStripeAdapter\Jobs\Webhooks\HandleChargeFailed::class,
    ],

    /*
     * The classname of the model to be used. The class should equal or extend
     * Spatie\WebhookClient\Models\WebhookCall.
     */
    'model' => \Spatie\WebhookClient\Models\WebhookCall::class,

    /**
     * This class determines if the webhook call should be stored and processed.
     */
    'profile' => \Spatie\StripeWebhooks\StripeWebhookProfile::class,

    /*
     * Specify a connection and or a queue to process the webhooks
     */
    'connection' => env('STRIPE_WEBHOOK_CONNECTION'),
    'queue' => env('STRIPE_WEBHOOK_QUEUE'),

    /*
     * When disabled, the package will not verify if the signature is valid.
     * This can be handy in local environments.
     */
    'verify_signature' => env('STRIPE_SIGNATURE_VERIFY', true),
];
