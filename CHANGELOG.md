# Changelog

## 1.0.1

- Added CustomerGroups domain
- Added `currency` relation to `prices`
- Added `customer_group` relation to `prices`
- `PaymentOptions` can now be set as hidden (can be used for authorization, but won't be listed via API)
- The `OrderCreated` event is dispatched from `OrderObserver` right after the order is created
- Added `CartCheckedOut` event which is dispatched from `CheckoutCart` action

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
