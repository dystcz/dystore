<?php

use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Lunar\Base\StorefrontSessionInterface;
use Lunar\Managers\StorefrontSessionManager;
use Lunar\Models\Customer;

uses(TestCase::class, RefreshDatabase::class)
    ->group('storefront');

it('can update storefront session customer', function () {
    /** @var TestCase $this */

    /** @var Customer $customer */
    $customer = Customer::factory()->create();

    $response = $this
        ->jsonApi()
        ->expects('storefronts')
        ->includePaths(
            'customer',
        )
        ->get(serverUrl('/storefronts/lunar_storefront'));

    /** @var StorefrontSessionManager $storefrontSession */
    $storefrontSession = App::make(StorefrontSessionInterface::class);

    $response->assertSuccessful()
        ->assertFetchedOne([
            'type' => 'storefronts',
            'id' => $storefrontSession->getSessionKey(),
        ]);

    $customerId = $response->json('data.relationships.customer.data.id');

    $response = $this
        ->jsonApi()
        ->expects('storefronts')
        ->withData([
            'type' => 'customers',
            'id' => (string) $customer->getRouteKey(),
        ])
        ->patch(serverUrl('/storefronts/lunar_storefront/relationships/customer'));

    $newCustomerId = $response->json('data.id');

    $response->assertSuccessful();

    expect($customerId)->not->toBe($newCustomerId);
});
