# Lunar API Paypal Adapter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dystcz/lunar-api-paypal-adapter.svg?style=flat-square)](https://packagist.org/packages/dystcz/lunar-api-paypal-adapter)
![GitHub Actions](https://github.com/dystcz/lunar-api-paypal-adapter/actions/workflows/tests.yaml/badge.svg)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/dystcz/lunar-api-paypal-adapter/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/dystcz/lunar-api-paypal-adapter/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/dystcz/lunar-api-paypal-adapter.svg?style=flat-square)](https://packagist.org/packages/dystcz/lunar-api-paypal-adapter)

TODO: Write description

## Installation

You can install the package via composer:

```bash
composer require dystcz/lunar-api-paypal-adapter
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="lunar-api-paypal-adapter-config"
```

This is the contents of the published config file:

```php
return [
    'driver' => 'paypal',
    'type' => 'card',
];
```

## Usage

```php
// Create a payment intent
App::make(PaypalPaymentAdapter::class)->createIntent($cart)

// Handle a webhook (validate and authorize payment)
App::make(PaypalPaymentAdapter::class)->handleWebhook($request)
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
