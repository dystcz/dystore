<?php

use Dystcz\LunarApiReviews\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Admin\Models\Staff;
use Lunar\Database\Factories\ProductFactory;
use Lunar\Database\Factories\ProductVariantFactory;
use Lunar\Models\Currency;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Currency::factory()->create([
        'default' => true,
    ]);
});

test('product show page contains reviews slot component', function () {
    $product = ProductFactory::new()
        ->has(ProductVariantFactory::new(), 'variants')
        ->create();

    $staff = Staff::factory()->create(['admin' => true]);

    $this
        ->actingAs($staff, 'staff')
        ->get('/hub/products/'.$product->id)
        ->assertSeeLivewire('lunar-api-reviews::reviews-slot');
})->skip('Need to implement slots in Filament');
