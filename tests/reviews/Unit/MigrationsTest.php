<?php

namespace Dystore\Tests\Reviews\Unit;

use Dystore\Tests\Reviews\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

uses(TestCase::class, RefreshDatabase::class);

test('reviews table exists', function () {
    /** @var TestCase $this */
    expect(Schema::hasTable(Config::get('lunar.database.table_prefix').'reviews'))->toBeTrue();
});

test('reviews table has expected structure', function () {
    /** @var TestCase $this */
    $columns = [
        'id',
        'purchasable_type',
        'purchasable_id',
        'rating',
        'comment',
        'published_at',
        'created_at',
        'updated_at',
    ];

    expect(Schema::hasColumns(Config::get('lunar.database.table_prefix').'reviews', $columns), 1)->toBeTrue();
});
