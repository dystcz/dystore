# Changelog

## 1.0.0-beta.1

### Changes

### ⚠️ Breaking changes

1. Changed relationship names and routes, because Schemas now use type naming
   derived from snake_cased, pluralized morph aliases,
   relationship names and thus routes had to change as well.

   **Relationships:**

   `product-notifications` → `product_notifications`<br>

   **Routes:**

   `/product-notifications` → `/product_notifications`<br>
