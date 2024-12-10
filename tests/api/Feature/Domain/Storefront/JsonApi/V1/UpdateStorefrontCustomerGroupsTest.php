<?php

use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Lunar\Base\StorefrontSessionInterface;
use Lunar\Managers\StorefrontSessionManager;
use Lunar\Models\CustomerGroup;

uses(TestCase::class, RefreshDatabase::class)
    ->group('storefront');

it('can update storefront customer groups', function () {
    /** @var TestCase $this */

    /** @var Collection $customerGroups */
    $customerGroups = CustomerGroup::factory()->count(3)->create();

    $response = $this
        ->jsonApi()
        ->expects('storefronts')
        ->includePaths(
            'customer_groups',
        )
        ->get(serverUrl('/storefronts/lunar_storefront'));

    /** @var StorefrontSessionManager $storefrontSession */
    $storefrontSession = App::make(StorefrontSessionInterface::class);

    $response->assertSuccessful()
        ->assertFetchedOne([
            'type' => 'storefronts',
            'id' => $storefrontSession->getSessionKey(),
        ]);

    $customerGroupIds = Arr::pluck($response->json('data.relationships.customer_groups.data'), 'id');

    $response = $this
        ->jsonApi()
        ->expects('storefronts')
        ->withData([
            ...$customerGroups->map(function (CustomerGroup $customerGroup) {
                return [
                    'type' => 'customer_groups',
                    'id' => (string) $customerGroup->getRouteKey(),
                ];
            })->all(),
        ])
        ->patch(serverUrl('/storefronts/lunar_storefront/relationships/customer_groups'));

    $newCustomerGroupIds = $response->json();

    $response->assertSuccessful();

    expect($customerGroupIds)->not->toBe($newCustomerGroupIds);
});
