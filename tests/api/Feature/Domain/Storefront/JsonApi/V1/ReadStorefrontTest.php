<?php

use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Lunar\Base\StorefrontSessionInterface;
use Lunar\Managers\StorefrontSessionManager;

uses(TestCase::class, RefreshDatabase::class)
    ->group('storefront');

it('can read storefront session', function () {
    /** @var TestCase $this */
    $response = $this
        ->jsonApi()
        ->expects('storefronts')
        ->includePaths(
            'channel',
            'customer',
            'currency',
            'customer_groups'
        )
        ->get(serverUrl('/storefronts'));

    /** @var StorefrontSessionManager $storefrontSession */
    $storefrontSession = App::make(StorefrontSessionInterface::class);

    $response->assertSuccessful()
        ->assertFetchedOne([
            'type' => 'storefronts',
            'id' => $storefrontSession->getSessionKey(),
        ])
        ->assertIncluded([
            [
                'type' => 'channels',
                'id' => $storefrontSession->getChannel()->getKey(),
            ],
            [
                'type' => 'currencies',
                'id' => $storefrontSession->getCurrency()->getKey(),
            ],
            [
                'type' => 'customer_groups',
                'id' => $storefrontSession->getCustomerGroups()->first()->getKey(),
            ],
        ]);
});
