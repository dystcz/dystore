<?php

use Dystcz\LunarApi\Domain\Products\Models\Product;
use Dystcz\LunarApi\Domain\ProductVariants\Models\ProductVariant;
use Dystore\Reviews\Tests\TestCase;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

test('product has reviews relation', function () {
    $model = new Product;

    expect($model->reviews())->toBeInstanceOf(MorphMany::class);
});

test('product has variantReviews relation', function () {
    $model = new Product;

    expect($model->variantReviews())->toBeInstanceOf(HasManyThrough::class);
});

test('product variant has reviews relation', function () {
    $model = new ProductVariant;

    expect($model->reviews())->toBeInstanceOf(MorphMany::class);
});
