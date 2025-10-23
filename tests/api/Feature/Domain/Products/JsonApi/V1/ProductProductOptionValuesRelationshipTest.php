<?php

use Dystore\Api\Domain\Products\Models\Product;
use Dystore\Api\Domain\ProductVariants\Models\ProductVariant;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;

uses(TestCase::class, RefreshDatabase::class);

it('can list product options values through relationship', function () {
    /** @var TestCase $this */
    $productOption = ProductOption::factory()->create();

    $product = Product::factory()
        ->hasAttached($productOption, ['position' => 1], 'productOptions')
        ->create();

    $values = ProductOptionValue::factory()
        ->for($productOption, 'option')
        ->count(4)
        ->create();

    $variant = ProductVariant::factory()
        ->for($product, 'product')
        ->hasAttached($values, [], 'values')
        ->count(4)
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('product_option_values')
        ->get(serverUrl("/products/{$product->getRouteKey()}/product_option_values"));

    $this->assertEquals(
        $product->variantValues->pluck('id')->sort()->values(),
        $values->pluck('id')->sort()->values()
    );

    $response
        ->assertSuccessful()
        ->assertFetchedMany($product->variantValues)
        ->assertDoesntHaveIncluded();
})->group('products');

it('can show product with product option values included', function () {
    /** @var TestCase $this */
    $productOption = ProductOption::factory()->create();

    $product = Product::factory()
        ->hasAttached($productOption, ['position' => 1], 'productOptions')
        ->create();

    $values = ProductOptionValue::factory()
        ->for($productOption, 'option')
        ->count(4)
        ->create();

    $variant = ProductVariant::factory()
        ->for($product, 'product')
        ->hasAttached($values, [], 'values')
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('products')
        ->get(serverUrl("/products/{$product->getRouteKey()}?include=product_option_values"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($product);

    foreach ($product->variantValues as $value) {
        $response->assertIsIncluded('product_option_values', $value);
    }
})->group('products');

it('includes product option values in included section when using include parameter', function () {
    /** @var TestCase $this */
    $productOption = ProductOption::factory()->create();

    $product = Product::factory()
        ->hasAttached($productOption, ['position' => 1], 'productOptions')
        ->create();

    $values = ProductOptionValue::factory()
        ->for($productOption, 'option')
        ->count(3)
        ->create();

    ProductVariant::factory()
        ->for($product, 'product')
        ->hasAttached($values, [], 'values')
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('products')
        ->get(serverUrl("/products/{$product->getRouteKey()}?include=product_option_values"));

    $response->assertSuccessful();

    // Verify the relationship data contains references to all values
    $relationshipData = $response->json('data.relationships.product_option_values.data');
    expect($relationshipData)->toBeArray();
    expect($relationshipData)->toHaveCount(3);

    // Extract IDs from relationship data
    $relationshipIds = collect($relationshipData)->pluck('id')->sort()->values();
    $expectedIds = $values->pluck('id')->map(fn ($id) => (string) $id)->sort()->values();
    expect($relationshipIds->toArray())->toBe($expectedIds->toArray());

    // Verify the included section exists and contains all values
    $included = $response->json('included');
    expect($included)->toBeArray();
    expect($included)->toHaveCount(3);

    // Verify each value is properly included with correct type and attributes
    foreach ($values as $value) {
        $includedValue = collect($included)->firstWhere('id', (string) $value->id);
        expect($includedValue)->not->toBeNull();
        expect($includedValue['type'])->toBe('product_option_values');
        expect($includedValue['attributes']['name'])->toBe($value->translate('name'));
    }
})->group('products', 'includes');

it('can count product option values', function () {
    /** @var TestCase $this */
    $product = Product::factory()
        ->hasAttached(
            ProductOption::factory()
                ->count(2)
                ->has(ProductOptionValue::factory()->count(3), 'values'),
            ['position' => 1],
        )
        ->create();

    $values = $product->productOptions->map(fn (ProductOption $option) => $option->values->first());

    $variant = ProductVariant::factory()
        ->for($product, 'product')
        ->hasAttached($values, [], 'values')
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('products')
        ->get(serverUrl("/products/{$product->getRouteKey()}?with_count=product_option_values"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($product);

    expect($response->json('data.relationships.product_option_values.meta.count'))->toBe(2);
})->group('products', 'counts');

it('returns distinct product option values even when same value is used across multiple variants', function () {
    /** @var TestCase $this */

    // Create a product with product options and values
    $product = Product::factory()
        ->hasAttached(
            ProductOption::factory()
                ->count(2)
                ->has(ProductOptionValue::factory()->count(3), 'values'),
            ['position' => 1],
        )
        ->create();

    // Get the first value from the first option - this will be shared across variants
    $sharedValue = $product->productOptions->first()->values->first();

    // Get unique values from second option for each variant
    $uniqueValue1 = $product->productOptions->last()->values->get(0);
    $uniqueValue2 = $product->productOptions->last()->values->get(1);
    $uniqueValue3 = $product->productOptions->last()->values->get(2);

    // Create 3 variants, all using the same shared value plus a unique value
    ProductVariant::factory()
        ->for($product, 'product')
        ->hasAttached([$sharedValue, $uniqueValue1], [], 'values')
        ->create();

    ProductVariant::factory()
        ->for($product, 'product')
        ->hasAttached([$sharedValue, $uniqueValue2], [], 'values')
        ->create();

    ProductVariant::factory()
        ->for($product, 'product')
        ->hasAttached([$sharedValue, $uniqueValue3], [], 'values')
        ->create();

    // Get variant values
    $variantValues = $product->variantValues;

    // Should return exactly 4 distinct values (1 shared + 3 unique)
    expect($variantValues)->toHaveCount(4);

    // Check that each value appears only once
    $valueIds = $variantValues->pluck('id');
    expect($valueIds->unique())->toHaveCount(4);

    // Verify the shared value appears only once (not 3 times)
    expect($valueIds->filter(fn ($id) => $id === $sharedValue->id))->toHaveCount(1);

    // Verify all expected values are present
    expect($valueIds->sort()->values()->toArray())->toBe([
        $sharedValue->id,
        $uniqueValue1->id,
        $uniqueValue2->id,
        $uniqueValue3->id,
    ]);
})->group('products', 'distinct');

it('returns distinct values when querying through relationship', function () {
    /** @var TestCase $this */
    $product = Product::factory()
        ->hasAttached(
            ProductOption::factory()
                ->has(ProductOptionValue::factory()->count(5), 'values'),
            ['position' => 1],
        )
        ->create();

    $values = $product->productOptions->first()->values;

    // Create multiple variants with overlapping values
    ProductVariant::factory()
        ->for($product, 'product')
        ->hasAttached([$values->get(0), $values->get(1), $values->get(2)], [], 'values')
        ->create();

    ProductVariant::factory()
        ->for($product, 'product')
        ->hasAttached([$values->get(1), $values->get(2), $values->get(3)], [], 'values')
        ->create();

    ProductVariant::factory()
        ->for($product, 'product')
        ->hasAttached([$values->get(2), $values->get(3), $values->get(4)], [], 'values')
        ->create();

    // Should return exactly 5 distinct values (0,1,2,3,4)
    $variantValues = $product->variantValues;

    expect($variantValues)->toHaveCount(5);
    expect($variantValues->pluck('id')->sort()->values()->toArray())->toBe([
        $values->get(0)->id,
        $values->get(1)->id,
        $values->get(2)->id,
        $values->get(3)->id,
        $values->get(4)->id,
    ]);
})->group('products', 'distinct');

it('returns empty collection when product has no variants', function () {
    /** @var TestCase $this */
    $product = Product::factory()->create();

    $variantValues = $product->variantValues;

    expect($variantValues)->toBeEmpty();
})->group('products', 'distinct');

it('returns distinct values with eager loading', function () {
    /** @var TestCase $this */
    $product = Product::factory()
        ->hasAttached(
            ProductOption::factory()
                ->has(ProductOptionValue::factory()->count(3), 'values'),
            ['position' => 1],
        )
        ->create();

    $sharedValue = $product->productOptions->first()->values->first();
    $uniqueValue = $product->productOptions->first()->values->last();

    // Create 2 variants sharing a value
    ProductVariant::factory()
        ->for($product, 'product')
        ->hasAttached([$sharedValue, $uniqueValue], [], 'values')
        ->create();

    ProductVariant::factory()
        ->for($product, 'product')
        ->hasAttached([$sharedValue], [], 'values')
        ->create();

    // Eager load the relationship
    $productWithValues = Product::with('variantValues')->find($product->id);

    expect($productWithValues->variantValues)->toHaveCount(2);
    expect($productWithValues->variantValues->pluck('id')->sort()->values()->toArray())->toBe([
        $sharedValue->id,
        $uniqueValue->id,
    ]);
})->group('products', 'distinct', 'eager-loading');

it('returns distinct values with pagination', function () {
    /** @var TestCase $this */

    // Create a product with multiple options and values
    $product = Product::factory()
        ->hasAttached(
            ProductOption::factory()
                ->count(2)
                ->has(ProductOptionValue::factory()->count(5), 'values'),
            ['position' => 1],
        )
        ->create();

    $allValues = $product->productOptions->flatMap(fn (ProductOption $option) => $option->values);

    // Create multiple variants using various combinations of values
    // This will create duplicates if not handled properly
    ProductVariant::factory()
        ->for($product, 'product')
        ->hasAttached([$allValues->get(0), $allValues->get(1)], [], 'values')
        ->create();

    ProductVariant::factory()
        ->for($product, 'product')
        ->hasAttached([$allValues->get(0), $allValues->get(2)], [], 'values')
        ->create();

    ProductVariant::factory()
        ->for($product, 'product')
        ->hasAttached([$allValues->get(1), $allValues->get(3)], [], 'values')
        ->create();

    // Request first page with 2 items per page
    $response = $this
        ->jsonApi()
        ->expects('product_option_values')
        ->page(['number' => 1, 'size' => 2])
        ->get(serverUrl("/products/{$product->getRouteKey()}/product_option_values"));

    $response->assertSuccessful();

    // Should have exactly 2 items on first page
    expect($response->json('data'))->toHaveCount(2);

    // Verify no duplicate IDs on first page
    $firstPageIds = collect($response->json('data'))->pluck('id')->all();
    expect($firstPageIds)->toHaveCount(2);
    expect(array_unique($firstPageIds))->toHaveCount(2);

    // Request second page
    $response2 = $this
        ->jsonApi()
        ->expects('product_option_values')
        ->page(['number' => 2, 'size' => 2])
        ->get(serverUrl("/products/{$product->getRouteKey()}/product_option_values"));

    $response2->assertSuccessful();

    // Second page should also have distinct IDs
    $secondPageIds = collect($response2->json('data'))->pluck('id')->all();
    expect(array_unique($secondPageIds))->toHaveCount(count($secondPageIds));

    // Verify no overlap between pages
    $overlap = array_intersect($firstPageIds, $secondPageIds);
    expect($overlap)->toBeEmpty();
})->group('products', 'distinct', 'pagination');

it('returns correct total count with pagination', function () {
    /** @var TestCase $this */
    $product = Product::factory()
        ->hasAttached(
            ProductOption::factory()
                ->has(ProductOptionValue::factory()->count(10), 'values'),
            ['position' => 1],
        )
        ->create();

    $values = $product->productOptions->first()->values;

    // Create 3 variants, each using 5 values (with some overlap)
    ProductVariant::factory()
        ->for($product, 'product')
        ->hasAttached($values->take(5), [], 'values')
        ->create();

    ProductVariant::factory()
        ->for($product, 'product')
        ->hasAttached($values->slice(3, 5), [], 'values')
        ->create();

    ProductVariant::factory()
        ->for($product, 'product')
        ->hasAttached($values->slice(5, 5), [], 'values')
        ->create();

    // Should have exactly 10 distinct values (all values are used)
    $response = $this
        ->jsonApi()
        ->expects('product_option_values')
        ->page(['number' => 1, 'size' => 3])
        ->get(serverUrl("/products/{$product->getRouteKey()}/product_option_values"));

    $response->assertSuccessful();

    // Total should be 10, not 15 (which would be the case without distinct)
    expect($response->json('meta.page.total'))->toBe(10);
    expect($response->json('meta.page.lastPage'))->toBe(4); // 10 items / 3 per page = 4 pages
})->group('products', 'distinct', 'pagination');

it('returns correct count when calling count() method directly', function () {
    /** @var TestCase $this */
    $product = Product::factory()
        ->hasAttached(
            ProductOption::factory()
                ->has(ProductOptionValue::factory()->count(5), 'values'),
            ['position' => 1],
        )
        ->create();

    $values = $product->productOptions->first()->values;

    // Create 3 variants using same values - would be 15 rows without distinct
    ProductVariant::factory()
        ->for($product, 'product')
        ->hasAttached($values, [], 'values')
        ->create();

    ProductVariant::factory()
        ->for($product, 'product')
        ->hasAttached($values, [], 'values')
        ->create();

    ProductVariant::factory()
        ->for($product, 'product')
        ->hasAttached($values, [], 'values')
        ->create();

    // Direct count on relationship should return distinct count
    expect($product->variantValues()->count())->toBe(5);

    // Collection count should also be 5
    expect($product->variantValues->count())->toBe(5);
})->group('products', 'distinct', 'count');

it('returns correct count in JSON API meta matching total', function () {
    /** @var TestCase $this */
    $product = Product::factory()
        ->hasAttached(
            ProductOption::factory()
                ->has(ProductOptionValue::factory()->count(8), 'values'),
            ['position' => 1],
        )
        ->create();

    $values = $product->productOptions->first()->values;

    // Create multiple variants with overlapping values
    // This would create many duplicate rows if not handled properly
    ProductVariant::factory()
        ->for($product, 'product')
        ->hasAttached($values->take(4), [], 'values')
        ->create();

    ProductVariant::factory()
        ->for($product, 'product')
        ->hasAttached($values->slice(2, 4), [], 'values')
        ->create();

    ProductVariant::factory()
        ->for($product, 'product')
        ->hasAttached($values->slice(4, 4), [], 'values')
        ->create();

    // All 8 values are used across the 3 variants
    $response = $this
        ->jsonApi()
        ->expects('product_option_values')
        ->get(serverUrl("/products/{$product->getRouteKey()}/product_option_values"));

    $response->assertSuccessful();

    $meta = $response->json('meta');

    // The count should match the total (distinct count)
    expect($meta['count'])->toBe(8, 'Meta count should be distinct count');
    expect($meta['page']['total'])->toBe(8, 'Page total should be distinct count');
    expect($meta['count'])->toBe($meta['page']['total'], 'Count should equal total');
})->group('products', 'distinct', 'count', 'json-api');
