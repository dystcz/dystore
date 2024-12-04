<?php

use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\CustomerGroup;

uses(TestCase::class, RefreshDatabase::class)
    ->group('customer_groups');

it('can read a customer group', function () {
    /** @var TestCase $this */
    $customerGroup = CustomerGroup::factory()->create();

    $response = $this
        ->jsonApi()
        ->expects('customer_groups')
        ->get(serverUrl("/customer_groups/{$customerGroup->getRouteKey()}"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($customerGroup);
});
