<?php

namespace Dystore\Stripe\Managers;

use Illuminate\Support\Facades\Config;
use Lunar\Stripe\Managers\StripeManager as LunarStripeManager;
use Stripe\PaymentIntent;

class StripeManager extends LunarStripeManager
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Build the intent
     */
    protected function buildIntent(int $value, string $currencyCode, array $opts = []): PaymentIntent
    {
        $params = [
            'amount' => $value,
            'currency' => $currencyCode,
            Config::get('dystore.stripe.automatic_payment_methods', true)
            ? ['automatic_payment_methods' => ['enabled' => true]]
            : ['payment_method_types' => Config::get('dystore.stripe.payment_method_types', ['card'])],
            'capture_method' => config('lunar.stripe.policy', 'automatic'),
        ];

        return PaymentIntent::create([
            ...$params,
            ...$opts,
        ]);
    }
}
