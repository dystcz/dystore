# Changelog

## 1.0.15 - 2026-07-29

- Add nested `collection_groups` filter on products for multi-group collection filtering [#103](https://github.com/dystcz/dystore/pull/103)
- Show all reviews in Filament admin by removing `PublishedScope` [#102](https://github.com/dystcz/dystore/pull/102)
- Add product and variant availability scopes [#101](https://github.com/dystcz/dystore/pull/101)
- Add order relation to reviews [#100](https://github.com/dystcz/dystore/pull/100)
- Update registering users without password [#99](https://github.com/dystcz/dystore/pull/99)
- Add delete user endpoint [#98](https://github.com/dystcz/dystore/pull/98)
- Add check if account exists endpoint [#97](https://github.com/dystcz/dystore/pull/97)
- Update Pest testing framework to `^4.0` [#96](https://github.com/dystcz/dystore/pull/96)
- Make all `WhereIdIn` filters comma-delimited
- Add and use visible scope for products
- Allow replacing attributes with single media conversion
- Fix media definitions and srcset
- Fix review Filament resource
- Fix review schema morph types
- Fix review contracts namespace typo
- Update order policy
- Load user before checking if already attached to cart

## 1.0.14

- Update to Lunar 1.3.0 [#95](https://github.com/dystcz/dystore/pull/95)
- Update cart address validation messages
- Update password confirmation validation
- Update belongs to many through relationship
- Fix `variantValues` relationship on products
- Add order signature to meta in response after checkout
- Constraint to product option values with variants
- Do not force pagination
- Scope only products with published status [#94](https://github.com/dystcz/dystore/pull/94)
- Update order during checkout [#93](https://github.com/dystcz/dystore/pull/93)
- Add filters to product variants [#92](https://github.com/dystcz/dystore/pull/92)
- Order product variant values by position [#90](https://github.com/dystcz/dystore/pull/90), [#91](https://github.com/dystcz/dystore/pull/91)
- Add filters to product options and product option values [#89](https://github.com/dystcz/dystore/pull/89)
- Fix variant values relationship on products [#88](https://github.com/dystcz/dystore/pull/88)
- Refactor `variantValues` relationship [#86](https://github.com/dystcz/dystore/pull/86), [#87](https://github.com/dystcz/dystore/pull/87)
- Add pivot product position [#86](https://github.com/dystcz/dystore/pull/86)
- Abort 404 when accessing draft product [#85](https://github.com/dystcz/dystore/pull/85)
- Update to Lunar 1.1.0 [#84](https://github.com/dystcz/dystore/pull/84)
- Set customer groups before setting customer to cart [#83](https://github.com/dystcz/dystore/pull/83)
- Update user customer policies [#82](https://github.com/dystcz/dystore/pull/82)
- Fix n+1 queries when including prices to resources [#81](https://github.com/dystcz/dystore/pull/81)
- Add set customer endpoint which works with cart from session [#80](https://github.com/dystcz/dystore/pull/80)
- Handle cart-customer relationship [#79](https://github.com/dystcz/dystore/pull/79)
- Make rating required in request configurable [#78](https://github.com/dystcz/dystore/pull/78)
- Fix review request validation [#76](https://github.com/dystcz/dystore/pull/76), [#77](https://github.com/dystcz/dystore/pull/77)

## 1.0.13

- Update to Lunar 1.0.1 [#74](https://github.com/dystcz/dystore/pull/74)
- Add filters and sorts to reviews [#75](https://github.com/dystcz/dystore/pull/75)

## 1.0.12

- Update to Lunar 1.0.0 [#73](https://github.com/dystcz/dystore/pull/73)
- Add product variant reviews relation to products [#72](https://github.com/dystcz/dystore/pull/72)
- Add images relation to reviews [#71](https://github.com/dystcz/dystore/pull/71)
- Fix reviews [#70](https://github.com/dystcz/dystore/pull/70)
- Enhance reviews [#67](https://github.com/dystcz/dystore/pull/67), [#68](https://github.com/dystcz/dystore/pull/68)
- Add Filament reviews resource and media library plugin
- Add publishable scope and concern to review model
- Add dynamic name attribute to product variant model
- Make purchasable relation nullable for more general reviews

## 1.0.11

- Enhance reviews [#67](https://github.com/dystcz/dystore/pull/67)

## 1.0.10

- Update to Lunar 1.0.0-beta.25 [#66](https://github.com/dystcz/dystore/pull/66)

## 1.0.9

- Scope only published product associations [#64](https://github.com/dystcz/dystore/pull/64)
- Scope products by customer groups [#65](https://github.com/dystcz/dystore/pull/65)
- Add custom reset password notification
- Use user contract namespace
- Use contract namespace for new password controller
- Fix password reset flow [#62](https://github.com/dystcz/dystore/pull/62)

## 1.0.8

- Fix API headers middleware priority [#61](https://github.com/dystcz/dystore/pull/61)

## 1.0.7

- Update to Lunar 1.0.0-beta.22 [#60](https://github.com/dystcz/dystore/pull/60)
- Add price relation to product variants [#57](https://github.com/dystcz/dystore/pull/57)
- Fix price relation hotfix [#58](https://github.com/dystcz/dystore/pull/58)
- Add order lines prices include paths [#59](https://github.com/dystcz/dystore/pull/59)
- Add auto documentation for facades [#54](https://github.com/dystcz/dystore/pull/54)
- Update policies

### ⚠️ Breaking changes

- `ResourceManifestFacade` has been renamed to `ResourceManifest`
- `SchemaManifestFacade` has been renamed to `SchemaManifest`

## 1.0.6

- Update routing with `HasRoutes` concern [#53](https://github.com/dystcz/dystore/pull/53)
- Add `SetApiHeaders` middleware
- Add webhook route to `HasRoutes` concern
- Update product and product variant prices [#52](https://github.com/dystcz/dystore/pull/52)
- Refactor obtaining prices with or without tax
- Set priceable when prices included to product variants
- Set product relation when most expensive variant included
- Add more options to include paths
- Use published scope
- Update product schema [#50](https://github.com/dystcz/dystore/pull/50)
- Add `WhereHas` tags filter to product schema
- Add hidden sortable `created_at`

## 1.0.5

- Add Ecomail newsletter driver [#49](https://github.com/dystcz/dystore/pull/49)

## 1.0.4

- Re-enable hashids support [#48](https://github.com/dystcz/dystore/pull/48)
- Update to Lunar 1.0.0-beta.20 [#47](https://github.com/dystcz/dystore/pull/47)

## 1.0.3

- Set `customer_id` when creating a cart [#44](https://github.com/dystcz/dystore/pull/44)
- Handle customer `customer_groups` relationship [#43](https://github.com/dystcz/dystore/pull/43)
- Add `customer_groups` relation to customer
- Add `withCustomerGroup` method

## 1.0.2

- Removed Lunar pipeline overrides [#37](https://github.com/dystcz/dystore/pull/37)
- Updated Laravel JSON:API versions [#36](https://github.com/dystcz/dystore/pull/36)
- Payment prices activity log errors workaround [#35](https://github.com/dystcz/dystore/pull/35)
- Add product variant builder

## 1.0.1

- Added CustomerGroups domain
- Added `currency` relation to `prices`
- Added `customer_group` relation to `prices`
- `PaymentOptions` can now be set as hidden (can be used for authorization, but won't be listed via API)
- The `OrderCreated` event is dispatched from `OrderObserver` right after the order is created
- Processing Stripe webhooks is now controlled by custom `WebhookProfile` which checks `eshop_id`
  (if configured) in payment intent metadata and either dispatches webhook handlers or discards the webhook calls
- Added `CartCheckedOut` event which is dispatched from `CheckoutCart` action
- Fixed pricing and prices relations in [#33](https://github.com/dystcz/dystore/pull/33)
- Added default `api-pricing` middleware to `config/general.php` which scopes prices to currency and customer groups set in storefront session
- Added custom storefront session manager
- Refactored authorization actions and payment methods
- Updated Laravel JSON:API to v5
- Updated to Lunar 1.0.0-beta.10

### ⚠️ Breaking changes

- `CartCheckedOut` event is dispatched after checkout instead of `OrderCreated` event

## 1.0.0

- Monorepo 🎉

## 1.0.0-beta.4

### Changes

- Removed relationship links from responses by default

## 1.0.0-beta.3

### Changes

- Added complete `User` model
- Added create (register) user endpoint (`POST` `/users`)
- Added update user endpoint (`PATCH` `/users`)
- Added change user password endpoint (`PATCH` `/users/-actions/change-password`)
- Added `AuthUser` json-api proxy
- Added custom `ProxySchema`
- Added login endpoint for logged in user (`POST` `/auth/-actions/login`)
- Added logout endpoint for logged in user (`POST` `/auth/-actions/logout`)
- Added "me" endpoint for logged in user (`GET` `/auth/-actions/me`)
- Added "my orders" endpoint for logged in user (`/auth/-actions/me/orders`)
- Added "register without password" endpoint (`POST` `/auth/-actions/register-without-password`)
- Added forgotten password endpoint (`POST` `/auth/-actions/forgot-password`)
- Added reset password endpoint (`POST` `/auth/-actions/reset-password`)
- Added create new password endpoint (`GET` `/auth/-actions/reset-password/{token}`)
- Added `variantValues` relationship to `Product` model
- Added `product_option_values` relationship to `ProductSchema` (scoped to variant values using `variantValues` relationship)
- Added `product_option_handle` to `ProductOptionValueSchema`

### ⚠️ Breaking changes

- Renamed `lunar_model` to `model_contract` in `domains.php` config file

## 1.0.0-beta.2

### Changes

- Fixed attribute mapping for `collections`
- Added tests for `collections` `default_url` relationship and includes
- Fixed dynamic relationships
- Added configurable auth guard `/Dystcz/LunarApi/Facades/LunarApi::authGuard($guard)`
- Updated policies to grant more privileges to Filament admins
- Added `product_options` relationship for `products`

### ⚠️ Breaking changes

1. Changed relationship names.

    **Relationships:**

    `product_options.values` → `product_options.product_option_values`<br>
    `product_variants.values` → `product_variants.product_option_values`<br>

## 1.0.0-beta.1

### Changes

- Model logic extracted to traits
- Added contracts for all models
- Added `images` relationship route for `collections`
- Added countable relationship tests

### ⚠️ Breaking changes

1. Changed relationship names and routes, because Schemas now use type naming
   derived from snake_cased, pluralized morph aliases,
   relationship names and thus routes had to change as well.

    **Relationships:**

    `associations` → `product_associations`<br>
    `cheapest_variant` → `cheapest_product_variant`<br>
    `inverse_associations` → `inverse_product_associations`<br>
    `most_expensive_variant` → `most_expensive_product_variant`<br>
    `other_variants` → `other_product_variants`<br>
    `variants` → `product_variants`

    **Routes:**

    `/cart-addresses` → `/cart_addresses`<br>
    `/orders/{order}/order-lines` → `/orders/{order}/order_lines`<br>
    `/products/{product}/relationships/lowest-price` → `/products/{product}/relationships/lowest_price`<br>
    ...

2. Changed withCount query parameter
   `?withCount=` → `?with_count=`

## 0.8.8

### Changes

- Carts do not get automatically created when fetching them unless configured with `lunar.cart.auto_create = true`. However, they are created on demand by adding a first `CartLine` to a `Cart`.
- Added custom `CartSessionAuthListener` which merges current cart in the session with previously associated user cart and returns the updated user cart.
- Added `CreateEmptyCartAddresses` action from a listener with the same name.

### ⚠️ Breaking changes

- Empty `CartAddress`es are not created automatically with `Cart` anymore. You will have to create them manually by calling the endpoint below or using your own listener for the `CartCreated` event.

#### New endpoints

| Description                 | Related Model / Entity | Endpoint                                 | Method |
| --------------------------- | ---------------------- | ---------------------------------------- | ------ |
| Create empty cart addresses | `Cart`                 | `/carts/-actions/create-empty-addresses` | `post` |

## 0.8.7

### ⚠️ Breaking changes

#### Endpoints

| Description           | Related Model | Endpoint change                                      | Method change      |
| --------------------- | ------------- | ---------------------------------------------------- | ------------------ |
| Set coupon action     | `Cart`        | `/apply-coupon` → `/set-coupon`                      | `patch` → `post`   |
| Unset coupon action   | `Cart`        | `/remove-coupon` → `/unset-coupon`                   | `delete` → `post`  |
| Set shipping option   | `CartAddress` | `/attach-shipping-option` → `/set-shipping-option`   | ---                |
| Unset shipping option | `CartAddress` | `/detach-shipping-option` → `/unset-shipping-option` | `delete` → `patch` |

### Purchasable payment options 🆕

In the same fashion as shipping options, purchasable payment options are now available.

#### Super quick guide

1. Create a custom `PaymentModifier`
2. Add `PaymentOption`s in the modifier handle method by calling `PaymentManifest@addOption`
3. Register the modifier in a service provider like so: `App::get(PaymentModifiers::class)->add(PaymentModifier::class);`
4. You should now see your payment options when calling the `/payment-options` endpoint

#### Endpoints for setting and unsetting payment options

| Description                    | Related Model / Entity | Endpoint                               | Method |
| ------------------------------ | ---------------------- | -------------------------------------- | ------ |
| List available payment options | `PaymentOption`        | `/payment-options`                     | `get`  |
| Set payment option             | `Cart`                 | `/carts/-actions/set-payment-option`   | `post` |
| Unset payment option           | `Cart`                 | `/carts/-actions/unset-payment-option` | `post` |

## 0.8.3

- Find order redundancy by @theimerj in [https://github.com/dystcz/dystore-api/pull/91](https://github.com/dystcz/dystore-api/pull/91).
  Added more actions which can find order by payment intent id.
  This increases the success rate of identifying the order
  connected with the payment intent.
  Especially useful when data integrity is not ideal.
