<?php

use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Lunar\Base\StorefrontSessionInterface;
use Lunar\Managers\StorefrontSessionManager;
use Lunar\Models\Currency;

uses(TestCase::class, RefreshDatabase::class)
    ->group('storefront');

it('can read storefront session currency', function () {
    /** @var TestCase $this */

    /** @var StorefrontSessionManager $storefrontSession */
    $storefrontSession = App::make(StorefrontSessionInterface::class);

    /** @var Currency $currency */
    $currency = Currency::factory()->create();

    $storefrontSession->setCurrency($currency);

    $response = $this
        ->jsonApi()
        ->expects('currencies')
        ->get(serverUrl('/storefronts/lunar_storefront/currency'));

    $response->assertSuccessful()
        ->assertFetchedOne([
            'type' => 'currencies',
            'id' => $storefrontSession->getCurrency()->getRouteKey(),
        ]);
});
