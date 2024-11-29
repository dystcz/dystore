<?php

namespace Dystcz\LunarApiStripeAdapter\Managers;

use Illuminate\Support\Facades\Config;
use Lunar\Models\CartAddress;
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
    protected function buildIntent(int $value, string $currencyCode, ?CartAddress $shipping, array $opts = []): PaymentIntent
    {
        $intentData = [
            'amount' => $value,
            'currency' => $currencyCode,
            'capture_method' => Config::get('lunar.stripe.policy', 'automatic'),
            'shipping' => [
                'name' => "{$shipping->first_name} {$shipping->last_name}",
                'address' => [
                    'city' => $shipping->city,
                    'country' => $shipping->country->iso2,
                    'line1' => $shipping->line_one,
                    'line2' => $shipping->line_two,
                    'postal_code' => $shipping->postcode,
                    'state' => $shipping->state,
                ],
            ],
            ...$opts,
        ];

        $intentData = array_merge(
            $intentData,
            Config::get('lunar-api.stripe.automatic_payment_methods', true)
            ? ['automatic_payment_methods' => ['enabled' => true]]
            : ['payment_method_types' => Config::get('lunar-api.stripe.payment_method_types', ['card'])]
        );

        return PaymentIntent::create($intentData);
    }
}
