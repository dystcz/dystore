# Dystore Stripe Adapter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dystcz/dystore-stripe.svg?style=flat-square)](https://packagist.org/packages/dystcz/dystore-stripe)
![GitHub Actions](https://github.com/dystcz/dystore-stripe/actions/workflows/tests.yaml/badge.svg)

[![Total Downloads](https://img.shields.io/packagist/dt/dystcz/dystore-stripe.svg?style=flat-square)](https://packagist.org/packages/dystcz/dystore-stripe)

This package provides a Stripe payment adapter for [Dystore API](https://github.com/dystcz/dystore-api).
It can authorize your payments and handle incoming Stripe webhooks.

## Getting started

Should be as easy as:

1. Install the package
2. Fill in your env variables
3. Accept payments

### Installation

You can install the package via composer:

```bash
composer require dystcz/dystore-stripe
```

You can publish the config file with:

```bash
php artisan vendor:publish --provider="Dystore\Stripe\StripeServiceProvider" --tag="dystore.stripe"
```

This will publish two configuration files:

1. `config/dystore/stripe.php` - contains the payment adapter configuration
2. `config/stripe-webhook.php` - contains the webhook configuration

### Configuration

#### Setting up the webhooks

You can configure the Stripe webhooks in the `config/stripe-webhook.php` file.
This package builds on top of Spatie's [laravel-stripe-webhooks](https://github.com/spatie/laravel-stripe-webhooks?tab=readme-ov-file)
package, so you can use the same configuration.
For more configuration options, please refer to the [documentation](https://github.com/spatie/laravel-stripe-webhooks?tab=readme-ov-file)
of the package.

#### Setting up environment variables

Do not forget to fill in your `.env` file with the following variables:

```dotenv
STRIPE_PUBLIC_KEY=pk_live_...
STRIPE_SECRET_KEY=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...
STRIPE_WEBHOOK_CONNECTION=redis
STRIPE_WEBHOOK_QUEUE=priority
STRIPE_SIGNATURE_VERIFY=true
```

### Stripe events and their webhook handlers

Here is a list of Stripe events which are currently handled by this package.
You can easily add your own handlers and register
them in the `config/stripe-webhook.php` file.

You can add a couple of useful methods to your handlers
by extending the `WebhookHandler` class.

#### Currently handled events

| Event                           | Webhook handler class          | Description                                                                                              |
| ------------------------------- | ------------------------------ | -------------------------------------------------------------------------------------------------------- |
| `payment_intent.succeeded`      | `HandlePaymentIntentSucceeded` | Dispatches `OrderPaymentCanceled` event.                                                                 |
| `payment_intent.payment_failed` | `HandlePaymentIntentFailed`    | Dispatches `OrderPaymentFailed` event.                                                                   |
| `payment_intent.canceled`       | `HandlePaymentIntentCanceled`  | Authorizes the payment via `AuthorizeStripePayment` class which dispatches the `OrderPaymentSuccessful`. |

You can listen to these and [others](https://github.com/dystcz/dystore-api/tree/26c9dedeecddf89a9d2aed418cf965525e393e40/src/Domain/Orders/Events)
events in your application and handle them accordingly.

> [!NOTE]
> All other events are handled by `HandleOtherEvent` class
> which does nothing by default, but you can easily swap the default
> handler for your own in the config.

### Advanced usage

If you ever need to implement custom logic, you can use the methods listed below.

```php
$payment = App::make(StripePaymentAdapter::class);

// Get payment driver
$payment->getDriver();

// Get payment type
$payment->getType();

// Create a payment intent
$payment->createIntent($cart)

// Handle a webhook (validate and authorize payment)
$payment->handleWebhook($request)
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Jakub Theimer](https://github.com/dystcz)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
