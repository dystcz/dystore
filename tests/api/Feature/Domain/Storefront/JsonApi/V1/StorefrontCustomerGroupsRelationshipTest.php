<?php

use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Lunar\Base\StorefrontSessionInterface;
use Lunar\Managers\StorefrontSessionManager;
use Lunar\Models\CustomerGroup;

uses(TestCase::class, RefreshDatabase::class)
    ->group('storefront');

it('can read storefront session customer groups', function () {
    /** @var TestCase $this */

    /** @var StorefrontSessionManager $storefrontSession */
    $storefrontSession = App::make(StorefrontSessionInterface::class);

    /** @var Collection $customerGroups */
    $customerGroups = CustomerGroup::factory()->count(3)->create();

    $storefrontSession->setCustomerGroups($customerGroups);

    $response = $this
        ->jsonApi()
        ->expects('customer_groups')
        ->get(serverUrl('/storefronts/lunar_storefront/customer_groups'));

    $response->assertSuccessful()
        ->assertFetchedMany([
            ...$customerGroups->map(function (CustomerGroup $customerGroup) {
                return [
                    'type' => 'customer_groups',
                    'id' => $customerGroup->getRouteKey(),
                ];
            })->all(),
        ]);
});
