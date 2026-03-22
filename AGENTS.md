# AGENTS.md

## Project Overview

Dystore is a PHP monorepo of e-commerce packages built on top of Lunar and Laravel JSON:API.
It follows a domain-driven design with a contract-based architecture enabling full swappability
of components. Requires PHP 8.2+, Laravel 11/12, and ext-redis.

## Monorepo Structure

```
packages/           # 6 packages: api, newsletter, product-notifications, product-views, reviews, stripe
  api/src/
    Base/           # Shared base classes (Concerns, Contracts, Data, Enums, Facades, Traits)
    Domain/         # ~38 domain folders (Products, Carts, Orders, etc.)
      {Entity}/
        Actions/    Contracts/    Concerns/    Enums/    Events/
        Factories/  Http/Controllers/  Http/Routing/  JsonApi/V1/
        Models/     Policies/     Builders/    Scopes/   Pipelines/
    Support/        # Base Action class, Config, Models
tests/              # Mirrors packages/ structure: tests/api/, tests/newsletter/, etc.
  Pest.php          # Global test helpers (serverUrl, decodeHashedId, etc.)
```

## Build / Lint / Test Commands

```bash
# Install dependencies
composer install

# Run all tests
composer test                       # or: vendor/bin/pest

# Run a single test suite
vendor/bin/pest --testsuite api
vendor/bin/pest --testsuite newsletter
vendor/bin/pest --testsuite reviews
# Available suites: api, newsletter, product-notifications, product-views, reviews, stripe

# Run a single test file
vendor/bin/pest tests/api/Feature/Domain/Products/JsonApi/V1/ListProductsTest.php

# Run tests matching a name/description
vendor/bin/pest --filter="can list bare products"

# Run tests by group
vendor/bin/pest --group=products
vendor/bin/pest --group=checkout

# Run with hashids config
composer test-hashids

# Static analysis (PHPStan level 0 via Larastan)
composer analyse                    # or: vendor/bin/phpstan analyse

# Code formatting (Laravel Pint, laravel preset)
composer format                     # or: vendor/bin/pint

# Artisan via testbench
composer artisan                    # or: vendor/bin/testbench
```

Tests use SQLite in-memory (`DB_CONNECTION=testing`). The product-views suite requires Redis.

## Code Style Guidelines

### Formatting (enforced by Pint + php-cs-fixer)

- **Preset**: Laravel (via `pint.json`) with `explicit_string_variable: true`
- **Indentation**: 4 spaces for PHP; 2 spaces for JSON, YAML, JS/TS/Vue
- **Strings**: Single quotes. No spaces around `.` concatenation (`$a.$b`)
- **Not operator**: Space after `!` (`! $value`, not `!$value`)
- **Trailing commas**: Required in multiline arrays and argument lists
- **Imports**: Alphabetically sorted, one per line, no leading slash
- **Line endings**: LF, final newline required
- **No `declare(strict_types=1)`** -- this is intentional, do not add it

### Naming Conventions

| Element | Convention | Example |
|---------|-----------|---------|
| Classes | PascalCase with role suffix | `ProductsController`, `ProductPolicy`, `ProductSchema` |
| Models | Entity name, no suffix | `Product`, `Cart`, `Order` |
| Actions | Verb phrase, no suffix | `CheckoutCart`, `CreateUser`, `IsPurchasable` |
| Contracts | Same name as implementation, in `Contracts/` dir | `Contracts\Product`, `Contracts\CheckoutCart` |
| Traits | `InteractsWith*`, `Has*`, `CanBe*`, `Handles*` | `InteractsWithDystoreApi`, `HasRelationships` |
| Enums | PascalCase name, UPPER_SNAKE_CASE cases | `Availability::IN_STOCK`, `OrderStatus::PENDING_PAYMENT` |
| Events | Past tense | `CartCheckedOut`, `OrderPaymentFailed` |
| Methods | camelCase | `isAlwaysPurchasable()`, `getAuthGuard()` |
| Boolean methods | `is` prefix | `isInStock()`, `isDraft()`, `isPreorderable()` |
| Getter methods | `get` prefix | `getAvailability()`, `getDriver()` |
| Test files | `{Action}{Entity}Test.php` | `ListProductsTest.php`, `ReadProductTest.php` |

### Types and Type Hints

- Return types required on all methods (`void`, `self`, `static`, concrete types)
- Parameter types required on all parameters
- Use constructor promotion for DTOs, Events, ValueObjects
- Use `?Type` for nullable types (not `Type|null`)
- Use union types where appropriate (`Collection|Model`, `string|Closure`)
- Use `/** @var Type $var */` inline annotations for type narrowing in method bodies
- Use `@method`, `@property` PHPDoc on classes for IDE support

### Imports

- Alphabetical order (auto-enforced by Pint)
- One `use` statement per class
- No blank lines between import groups
- No leading backslash on imports

### Architecture Patterns

**Contract-based binding**: Every controller, model, and action has a contract interface.
Implementations are bound in service providers. Routes reference contracts, not concrete classes.

```php
// ServiceProvider binding
$this->app->bind(ProductsController::class, Controllers\ProductsController::class);
```

**Models**: Extend Lunar base model + implement contract + single aggregating trait:
```php
class Product extends LunarProduct implements ProductContract {
    use InteractsWithDystoreApi;
}
```

**Actions**: Single-purpose invocable classes with a `handle()` method:
```php
class CheckoutCart extends Action implements CheckoutCartContract {
    public function handle(CartContract $cart, ?CheckoutCartRequest $request = null): OrderContract { ... }
}
```

**Pipelines**: Stages implement `handle(CartContract $cart, Closure $next): mixed`.

### Error Handling

- Domain exceptions (e.g. `CartException`) are caught and re-thrown as `ValidationException`
- Use `abort(404)` for HTTP not-found responses
- Use `LogicException` for configuration/setup errors
- No broad try/catch blocks -- error handling is minimal and targeted
- Rely on Laravel's exception handler

### Testing Conventions

- **Framework**: Pest PHP (not PHPUnit class-based)
- **Setup**: `uses(TestCase::class, RefreshDatabase::class)->group('...')`
- **Naming**: `it('can list products', ...)` or `test('product has prices', ...)`
- **Inline types**: Start test closures with `/** @var TestCase $this */` when needed
- **Assertions**: Mix of Pest `expect()` and PHPUnit `$this->assert*()` methods
- **JSON:API testing**: Use `$this->jsonApi()->expects('type')->get(serverUrl('/path'))`
- **Groups**: Tag tests with `->group('products')`, `->group('checkout')`, etc.
- **Global helpers**: `serverUrl()`, `decodeHashedId()`, `mapModelsToResponseData()`
  defined in `tests/Pest.php`

### PHPDoc

- Use `{@inheritDoc}` for overridden methods
- Summary lines should end with a period
- No `@access` or `@package` tags
- Use `@phpstan-consistent-constructor` where needed

### CI Pipeline

- Tests run on PHP 8.3/8.4 with Laravel 11/12 against all 6 test suites
- PHPStan static analysis at level 0 with Larastan
- Pint auto-fixes code style on push (auto-committed with "Fix styling" message)
