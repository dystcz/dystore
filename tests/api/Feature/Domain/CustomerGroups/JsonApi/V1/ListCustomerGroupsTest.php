<?php

use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\CustomerGroup;

uses(TestCase::class, RefreshDatabase::class)
    ->group('customer_groups');

it('can list customer group', function () {
    /** @var TestCase $this */
    $customerGroup = CustomerGroup::factory()->count(3)->create();

    $response = $this
        ->jsonApi()
        ->expects('customer_groups')
        ->get(serverUrl('/customer_groups'));

    $response
        ->assertSuccessful();
})->todo();
