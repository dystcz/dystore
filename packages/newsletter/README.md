# Lunar API Newsletter

This [lunar-api](https://github.com/dystcz/lunar-api) compatible package exposes an API endpoint which
allows you to subscribe to newsletter lists by providing an email address
using the [spatie/laravel-newsletter](https://github.com/spatie/laravel-newsletter) package.

This initial version only takes an email address and subscribes to a list. There may be more endpoints added upon request in the future.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dystcz/dystore-newsletter.svg?style=flat-square)](https://packagist.org/packages/dystcz/dystore-newsletter)
[![Total Downloads](https://img.shields.io/packagist/dt/dystcz/dystore-newsletter.svg?style=flat-square)](https://packagist.org/packages/dystcz/dystore-newsletter)
![GitHub Actions](https://github.com/dystcz/dystore-newsletter/actions/workflows/tests.yaml/badge.svg)

## Installation

You can install the package via composer:

```bash
composer require dystcz/dystore-newsletter
```

To publish the [laravel-newsletter](https://github.com/spatie/laravel-newsletter) config file to `config/newsletter.php` run:

```bash
php artisan vendor:publish --tag="newsletter-config"
```

The full configuration can be found here: [spatie/laravel-newsletter](https://github.com/spatie/laravel-newsletter/blob/main/config/newsletter.php)

### Using Brevo

To use Brevo, install this extra package.

```bash
composer require getbrevo/brevo-php "1.x.x"
```

The `driver` key of the `newsletter` config file must be set to `Dystore\Newsletter\Drivers\BrevoDriver::class`.

Next, you must provide values for the API key and `list.subscribers.id`. You'll find these values in [Brevo settings](https://app.brevo.com/settings/keys/api).

The `endpoint` config value can be set to an empty string.

### Using MailChimp

To use MailChimp, install this extra package.

```bash
composer require drewm/mailchimp-api
```

The `driver` key of the `newsletter` config file must be set to `Spatie\Newsletter\Drivers\MailChimpDriver::class`.

Next, you must provide values for the API key and `list.subscribers.id`. You'll find these values in the MailChimp UI.

The `endpoint` config value can be set to an empty string.

### Using Mailcoach

To let this package work with Mailcoach, you need to install the Mailcoach SDK.

```bash
composer require spatie/mailcoach-sdk-php
```

Next, you must provide values for the API key, endpoint and `list.subscribers.id` in the config file. You'll find the API key and endpoint in the [Mailcoach](https://mailcoach.app) settings screen. The value for `list.subscribers.id` must be the UUID of an email list on Mailcoach. You'll find this value on the settings screen of an email list

## Usage

Make a `POST` request here `/api/v1/newsletters/-actions/subscribe` with the following data:

```php
$data = [
    'type' => 'newsletters',
    'attributes' => [
        'email' => $email,
    ],
];
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email jakub@dy.st instead of using the issue tracker.

## Credits

- [Jakub Theimer](https://github.com/dystcz)
- [Spatie](https://github.com/spatie)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
