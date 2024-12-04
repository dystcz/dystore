<?php

use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Lunar\Base\StorefrontSessionInterface;
use Lunar\Managers\StorefrontSessionManager;
use Lunar\Models\Channel;

uses(TestCase::class, RefreshDatabase::class)
    ->group('storefront');

it('can update storefront session channel', function () {
    /** @var TestCase $this */

    /** @var Channel $channel */
    $channel = Channel::factory()->create([
        'default' => false,
    ]);

    $response = $this
        ->jsonApi()
        ->expects('storefronts')
        ->includePaths(
            'channel',
        )
        ->get(serverUrl('/storefronts/lunar_storefront'));

    /** @var StorefrontSessionManager $storefrontSession */
    $storefrontSession = App::make(StorefrontSessionInterface::class);

    $response->assertSuccessful()
        ->assertFetchedOne([
            'type' => 'storefronts',
            'id' => $storefrontSession->getSessionKey(),
        ]);

    $channelId = $response->json('data.relationships.channel.data.id');

    $response = $this
        ->jsonApi()
        ->expects('storefronts')
        ->withData([
            'type' => 'channels',
            'id' => (string) $channel->getRouteKey(),
        ])
        ->patch(serverUrl('/storefronts/lunar_storefront/relationships/channel'));

    $newChannelId = $response->json('data.id');

    $response->assertSuccessful();

    expect($channelId)->not->toBe($newChannelId);
});
