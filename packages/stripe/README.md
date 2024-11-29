# Lunar API Stripe Adapter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dystcz/lunar-api-stripe-adapter.svg?style=flat-square)](https://packagist.org/packages/dystcz/lunar-api-stripe-adapter)
![GitHub Actions](https://github.com/dystcz/lunar-api-stripe-adapter/actions/workflows/tests.yaml/badge.svg)

[![Total Downloads](https://img.shields.io/packagist/dt/dystcz/lunar-api-stripe-adapter.svg?style=flat-square)](https://packagist.org/packages/dystcz/lunar-api-stripe-adapter)

TODO: Write description

## Installation

You can install the package via composer:

```bash
composer require dystcz/lunar-api-stripe-adapter
```

You can publish the config file with:

```bash
php artisan vendor:publish --provider="Dystcz\LunarApiStripeAdapter\LunarApiStripeAdapterServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
return [
    'driver' => 'stripe',
    'type' => 'card',
];
```

## Usage

```php
// Create a payment intent
App::make(StripePaymentAdapter::class)->createIntent($cart)

// Handle a webhook (validate and authorize payment)
App::make(StripePaymentAdapter::class)->handleWebhook($request)
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

- [Jakub Theimer](https://github.com/dystcz)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
