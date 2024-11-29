# Lunar Rewards

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dystcz/lunar-rewards.svg?style=flat-square)](https://packagist.org/packages/dystcz/lunar-api)
[![Total Downloads](https://img.shields.io/packagist/dt/dystcz/lunar-rewards.svg?style=flat-square)](https://packagist.org/packages/dystcz/lunar-api)
![GitHub Actions](https://github.com/dystcz/lunar-rewards/actions/workflows/tests.yaml/badge.svg)

## What's going on here?

This is a reward system package for [Lunar](https://github.com/lunarphp/lunar)
which allows your users to earn points for their purchases and redeem them for discounts.

## Getting started guide

### Requirements

- PHP ^8.2
- Laravel 10
- [Lunar requirements](https://docs.lunarphp.io/core/installation.html#server-requirements)

### Installation

You can install the package via composer

```bash
composer require dystcz/lunar-rewards
```

Publish config files

> You will probably need them pretty bad

```bash
php artisan vendor:publish --provider="Dystcz\LunarRewards\LunarRewardsServiceProvider" --tag="lunar-rewards"
```

Publish migrations

> Only in case you want to customize the database schema

```bash
php artisan vendor:publish --provider="Dystcz\LunarRewards\LunarRewardsServiceProvider" --tag="lunar-rewards.migrations"
```

### Testing

```bash
composer test
```

### Compatible packages

- [Lunar API](https://github.com/dystcz/lunar-api) (JSON:API layer for Lunar)

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
