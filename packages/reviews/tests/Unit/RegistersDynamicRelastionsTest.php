<?php

use Dystcz\LunarApi\Domain\Products\Models\Product;
use Dystcz\LunarApi\Domain\ProductVariants\Models\ProductVariant;
use Dystcz\LunarApiReviews\Tests\TestCase;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

test('Product has reviews relation', function () {
    $model = new Product();

    expect($model->reviews())->toBeInstanceOf(HasManyThrough::class);
});

test('ProductVariant has reviews relation', function () {
    $model = new ProductVariant();

    expect($model->reviews())->toBeInstanceOf(MorphMany::class);
});
