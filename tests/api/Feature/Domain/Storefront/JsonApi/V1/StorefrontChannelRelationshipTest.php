<?php

use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Lunar\Base\StorefrontSessionInterface;
use Lunar\Managers\StorefrontSessionManager;
use Lunar\Models\Channel;

uses(TestCase::class, RefreshDatabase::class)
    ->group('storefront');

it('can read storefront session channel', function () {
    /** @var TestCase $this */

    /** @var StorefrontSessionManager $storefrontSession */
    $storefrontSession = App::make(StorefrontSessionInterface::class);

    /** @var Channel $channel */
    $channel = Channel::factory()->create();

    $storefrontSession->setChannel($channel);

    $response = $this
        ->jsonApi()
        ->expects('channels')
        ->get(serverUrl('/storefronts/lunar_storefront/channel'));

    $response->assertSuccessful()
        ->assertFetchedOne([
            'type' => 'channels',
            'id' => $storefrontSession->getChannel()->getRouteKey(),
        ]);
});
