# Lunar API 🪬

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dystcz/lunar-api.svg?style=flat-square)](https://packagist.org/packages/dystcz/lunar-api)
[![Total Downloads](https://img.shields.io/packagist/dt/dystcz/lunar-api.svg?style=flat-square)](https://packagist.org/packages/dystcz/lunar-api)
![GitHub Actions](https://github.com/dystcz/lunar-api/actions/workflows/tests.yaml/badge.svg)

## What's going on here?

This package introduces an API layer for Lunar ecommerce package.

### Some perks

- focus on extensibility
- wannabe ecosystem of [complementary packages](#lunar-api-compatible-packages) with emphasis on the plug and play spirit 
- already running in production 🤫
- built on top of [Laravel JSON:API](https://github.com/laravel-json-api/laravel)

## Getting started guide

### Requirements

- PHP ^8.2
- Laravel 10
- [Lunar requirements](https://docs.lunarphp.io/core/installation.html#server-requirements)

### Installation

You can install the package via composer

```bash
composer require dystcz/lunar-api
```

Publish config files

> You will probably need them pretty bad

```bash
php artisan vendor:publish --provider="Dystcz\LunarApi\LunarApiServiceProvider" --tag="lunar-api"
```

### Let's hit your first endpoint

Just visit `/api/v1/products` and you should see a list of products.

Hopefully everything went smooth so far. If not, please create an issue.

You can view the rest of the API routes by running `php artisan route:list --name=v1` in your terminal.

### Useful resource

- [Postman request collection](postman_collection.json) 🚧
, so you can test the API with ease
- [Laravel JSON:API Docs](https://laraveljsonapi.io/docs/3.0/)
, if you plan to extend the API

### Testing

```bash
composer test
```

### Lunar API compatible packages

- [Reviews](https://github.com/dystcz/lunar-api-reviews) (Adds user reviews functionality)
- [Product Views](https://github.com/dystcz/lunar-api-product-views)
 (Store unique product views in Redis)
- [Product Stock Notifications](https://github.com/dystcz/lunar-api-product-notifications)
 (Notify users when product is in stock again)
- [Newsletter](https://github.com/dystcz/lunar-api-newsletter)
 (Newsletter sign up with support for Mailchimp / Mailcoach / Brevo)
- [Stripe Payment Adapter](https://github.com/dystcz/lunar-api-stripe-adapter)
- [Mollie Payment Adapter](https://github.com/pixelpillow/lunar-api-mollie-adapter)
- [PayPal Adapter](https://github.com/dystcz/lunar-api-paypal-adapter) [WIP]

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email dev@dy.st instead of using the issue tracker.

## Credits

- [All Contributors](../../contributors)
- [Lunar](https://github.com/lunarphp/lunar) for providing awesome e-commerce package
- [Laravel JSON:API](https://github.com/laravel-json-api/laravel)
 which is a brilliant JSON:API layer for Laravel applications

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
