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

it('can update storefront session channel', function () {
    /** @var TestCase $this */

    /** @var Collection $customerGroups */
    $customerGroups = CustomerGroup::factory()
        ->count(3)
        ->create([
            'default' => false,
        ]);

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
})->todo();
