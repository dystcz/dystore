# Lunar Rewards

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dystcz/lunar-rewards.svg?style=flat-square)](https://packagist.org/packages/dystcz/lunar-api)
[![Total Downloads](https://img.shields.io/packagist/dt/dystcz/lunar-rewards.svg?style=flat-square)](https://packagist.org/packages/dystcz/lunar-api)
![GitHub Actions](https://github.com/dystcz/lunar-rewards/actions/workflows/tests.yaml/badge.svg)

## What's going on here?

This is a reward system package for [Lunar](https://github.com/lunarphp/lunar)
which allows you to add or subtract reward points from models (eg. `User`s).
You can give points to users for various actions like buying products, writing reviews, etc.
Your users can then spend these points on discounts, free products, etc.

Point balances are managed by the awesome [Laravel Wallet](https://github.com/021-projects/laravel-wallet) package.

### Example use cases

#### Getting rewards

```diff
+ Give points to a user for every paid order (can be calculated from order value, or fixed amount)
+ Give points to a user for writing a review
+ Give points to a user for referring a friend
+ Give points to a user for signing up
+ Give points to a user for completing a profile
```

#### Spending rewards

```diff
- Donating points to a charity (transfer points from user to a dedicated charity account)
- Redeeming points for a discount (applied on cart)
- Redeeming points for a coupon code with the value of points
- Moving users to a different customer group based on points
- Giving a free product for a certain amount of points
```

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

```bash
php artisan vendor:publish --provider="Dystcz\LunarRewards\LunarRewardsServiceProvider" --tag="lunar-rewards.config"
```

This will publish two configuration files:

1. `config/lunar-rewards/rewards.php` - contains the rewards configuration
2. `config/wallet.php` - contains the wallet configuration

Publish migrations

> Only in case you want to customize the database schema

```bash
php artisan vendor:publish --provider="Dystcz\LunarRewards\LunarRewardsServiceProvider" --tag="lunar-rewards.migrations"
```

### Configuration

If you want to dig deeper into the underlaying [laravel-wallet](https://github.com/021-projects/laravel-wallet) package configuration
please visit their [documentation](021-projects.github.io/laravel-wallet).
You might want to [configure the database table names](https://021-projects.github.io/laravel-wallet/8.x/configuration.html#table-names).

### Usage

#### Preparing your models

1. Implement the `Rewardable` interface in your model.
2. Add the `HasRewardPointsBalance` trait to your model.

```php
use Dystcz\LunarRewards\Domain\Rewards\Contracts\Rewardable;
use Dystcz\LunarRewards\Domain\Rewards\Traits\HasRewardPointsBalance;

class Model
class Model implements Rewardable
{
    use HasRewardPointsBalance;
}
```

#### Depositing / Giving points to a model

```php
use Dystcz\LunarRewards\Domain\Rewards\Actions\DepositPoints;
use Dystcz\LunarRewards\Domain\Rewards\DataTypes\Reward;
use Dystcz\LunarRewards\Facades\LunarRewards;

(new DepositPoints)->handle(to: $model, points: new Reward(100));

// or by calling the facade
LunarRewards::deposit(to: $model, points: new Reward(1000));
```

#### Charging points from a model

```php
use Dystcz\LunarRewards\Domain\Rewards\Actions\ChargePoints;
use Dystcz\LunarRewards\Domain\Rewards\DataTypes\Reward;
use Dystcz\LunarRewards\Facades\LunarRewards;

(new ChargePoints)->handle(from: $model, points: new Reward(100));

// or by calling the facade
LunarRewards::charge(from: $model, points: new Reward(1000));
```

#### Transferring points

```php
use Dystcz\LunarRewards\Domain\Rewards\Actions\TransferPoints;
use Dystcz\LunarRewards\Domain\Rewards\DataTypes\Reward;
use Dystcz\LunarRewards\Facades\LunarRewards;

(new TransferPoints)->handle(from: $model, to: $model2, points: new Reward(100));

// or by calling the facade
LunarRewards::transfer(from: $model, to: $model2, points: new Reward(1000));
```

#### Getting model points balance

```php
use Dystcz\LunarRewards\Domain\Rewards\Managers\PointBalanceManager;
use Dystcz\LunarRewards\Facades\LunarRewards;

$balance = PointBalanceManager::of($model);

// Points Balance
$balance->getValue(); // int
$balance->getReward(); // Dystcz\LunarRewards\Domain\Rewards\DataTypes\Reward

// Get balance by calling the facade
LunarRewards::balance($model); // int

// All Sent Points
$balance->getSent(); // int
$balance->getSentReward(); // Dystcz\LunarRewards\Domain\Rewards\DataTypes\Reward

// All Received Points
$balance->getReceived(); // int
$balance->getReceivedReward(); // Dystcz\LunarRewards\Domain\Rewards\DataTypes\Reward
```

#### Getting model points transactions

```php
use Dystcz\LunarRewards\Domain\Rewards\Managers\PointBalanceManager;

$balance = PointBalanceManager::of($model);

// All Received Points
$balance->getTransactions(); // Illuminate\Support\Collection<\Dystcz\LunarRewards\Domain\Rewards\Models\Transaction>
$balance->getTransactionsQuery(); // Illuminate\Database\Eloquent\Builder

// Or simply by calling the facade
```

#### Validating balances

```php
use Dystcz\LunarRewards\Domain\Rewards\Managers\PointBalanceManager;
use Dystcz\LunarRewards\Domain\Rewards\DataTypes\Reward;

$balance = PointBalanceManager::of($model);

// Check if model has enough points
$balance->hasEnoughPoints(new Reward(1000)); // bool
```

#### Creating coupons from balance

```php
use Dystcz\LunarRewards\Domain\Rewards\Actions\CreateCouponFromBalance;
use Dystcz\LunarRewards\Domain\Rewards\DataTypes\Reward;

$currency = $order->currency; // Lunar\Models\Currency

// Create a coupon with the value from the whole balance
$coupon = App::make(CreateCouponFromBalance::class)->handle(model: $model, currency: $currency);

// Create a coupon only for provided points
$coupon = App::make(CreateCouponFromBalance::class)->handle(
    model: $model,
    currency: $currency,
    points: new Reward(1000)
);
```

### Lunar API endpoints

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
- [Laravel Wallet](https://github.com/021-projects/laravel-wallet) for the points transaction engine
 which is a brilliant JSON:API layer for Laravel applications

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
