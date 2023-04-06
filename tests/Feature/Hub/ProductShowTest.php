<?php

use Dystcz\LunarApiReviews\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Database\Factories\ProductFactory;
use Lunar\Database\Factories\ProductVariantFactory;
use Lunar\Hub\Models\Staff;
use Lunar\Models\Currency;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Currency::factory()->create([
        'default' => true,
    ]);
});

test('product_show_page_contains_reviews_slot_component', function () {
    $product = ProductFactory::new()
        ->has(ProductVariantFactory::new(), 'variants')
        ->create();

    $staff = Staff::factory()->create(['admin' => true]);

    $this
        ->actingAs($staff, 'staff')
        ->get('/hub/products/'.$product->id)
        ->assertSeeLivewire('lunar-reviews::reviews-slot');
});
