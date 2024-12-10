<?php

use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Lunar\Base\StorefrontSessionInterface;
use Lunar\Managers\StorefrontSessionManager;
use Lunar\Models\Customer;

uses(TestCase::class, RefreshDatabase::class)
    ->group('storefront');

it('can read storefront session customer', function () {
    /** @var TestCase $this */

    /** @var StorefrontSessionManager $storefrontSession */
    $storefrontSession = App::make(StorefrontSessionInterface::class);

    /** @var Customer $customer */
    $customer = Customer::factory()->create();

    $storefrontSession->setCustomer($customer);

    $response = $this
        ->jsonApi()
        ->expects('customers')
        ->get(serverUrl('/storefronts/lunar_storefront/customer'));

    $response->assertSuccessful()
        ->assertFetchedOne([
            'type' => 'customers',
            'id' => $storefrontSession->getCustomer()->getRouteKey(),
        ]);
});
