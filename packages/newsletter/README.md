# [WIP] Lunar Newsletter

This [lunar-api](https://github.com/dystcz/lunar-api) compatible package is basically an API wrapper 
of [spatie/laravel-newsletter](https://github.com/spatie/laravel-newsletter) for the [lunar](https://github.com/lunarphp/lunar) backend
which allows your users to **easily sign-up** to Mailchimp or Mailcoach or other email marketing services.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dystcz/lunar-newsletter.svg?style=flat-square)](https://packagist.org/packages/dystcz/lunar-newsletter)
[![Total Downloads](https://img.shields.io/packagist/dt/dystcz/lunar-newsletter.svg?style=flat-square)](https://packagist.org/packages/dystcz/lunar-newsletter)
![GitHub Actions](https://github.com/dystcz/lunar-newsletter/actions/workflows/main.yml/badge.svg)

Add the possibility to sign up to newsletter lists to your Lunar backend

## Installation

You can install the package via composer:

```bash
composer require dystcz/lunar-newsletter

```
To publish the [laravel-newsletter](https://github.com/spatie/laravel-newsletter) config file to `config/newsletter.php` run:

```bash
php artisan vendor:publish --tag="newsletter-config"
```

You car read more about the configuration options here: [spatie/laravel-newsletter](https://github.com/spatie/laravel-newsletter)

## Usage

Just install the package and hit `/api/v1/newsletters/-actions/subscribe`

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

-   [Jakub Theimer](https://github.com/dystcz)
-   [Spatie](https://github.com/spatie)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

