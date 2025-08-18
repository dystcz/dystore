<?php

use Carbon\Carbon;
use Dystore\Api\Base\Enums\PublishedStatus;
use Dystore\Api\Domain\ProductVariants\Models\ProductVariant;
use Dystore\Reviews\Domain\Reviews\Models\Review;
use Dystore\Tests\Reviews\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Product;

uses(TestCase::class, RefreshDatabase::class)
    ->group('reviews');

it('can list generic reviews', function () {
    /** @var TestCase $this */
    $reviews = Review::factory()
        ->count(5)
        ->create([
            'published_at' => Carbon::now(),
            'status' => PublishedStatus::PUBLISHED,
        ]);

    $self = serverUrl('/reviews', true);

    $response = $this
        ->jsonApi()
        ->expects('reviews')
        ->get($self);

    $response
        ->assertSuccessful()
        ->assertFetchedMany($reviews);
});

it('includes total count in paginated review listings', function () {
    /** @var TestCase $this */
    $total = 9;
    Review::factory()
        ->count($total)
        ->create([
            'published_at' => Carbon::now(),
            'status' => PublishedStatus::PUBLISHED,
        ]);

    $self = serverUrl('/reviews', true);

    $response = $this
        ->jsonApi()
        ->expects('reviews')
        ->page(['size' => '5', 'number' => '2'])
        ->get($self);

    $response->assertSuccessful();
    expect($response->json('meta.page.total'))->toBe($total);
    expect($response->json('data'))->toHaveCount(4);
});

it('can list generic reviews together with reviews for a given purchasable', function () {
    /** @var TestCase $this */
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

    $generic = Review::factory()->count(3)->create([
        'published_at' => Carbon::now(),
        'status' => PublishedStatus::PUBLISHED,
    ]);

    $productSpecific = Review::factory()->for($product, 'purchasable')->count(2)->create([
        'published_at' => Carbon::now(),
        'status' => PublishedStatus::PUBLISHED,
    ]);

    $variantSpecific = Review::factory()->for($variant, 'purchasable')->count(2)->create([
        'published_at' => Carbon::now(),
        'status' => PublishedStatus::PUBLISHED,
    ]);

    $self = serverUrl('/reviews', true);

    // fetch for product
    $responseProduct = $this
        ->jsonApi()
        ->expects('reviews')
        ->filter(['purchasable_or_generic' => 'products:'.$product->getRouteKey()])
        ->get($self);
    $responseProduct->assertSuccessful();
    $idsProduct = collect($responseProduct->json('data.*.id'));
    expect($idsProduct->sort()->values()->all())
        ->toEqualCanonicalizing(
            $generic->pluck('id')->merge($productSpecific->pluck('id'))->map(fn ($id) => (string) $id)->sort()->values()->all()
        );

    // fetch for variant
    $responseVariant = $this
        ->jsonApi()
        ->expects('reviews')
        ->filter(['purchasable_or_generic' => 'product_variants:'.$variant->getRouteKey()])
        ->get($self);
    $responseVariant->assertSuccessful();
    $idsVariant = collect($responseVariant->json('data.*.id'));
    expect($idsVariant->sort()->values()->all())
        ->toEqualCanonicalizing(
            $generic->pluck('id')->merge($variantSpecific->pluck('id'))->map(fn ($id) => (string) $id)->sort()->values()->all()
        );
});

it('can list reviews sorted by published_at', function () {
    /** @var TestCase $this */
    $now = Carbon::now();
    $older = Review::factory()->create([
        'published_at' => $now->copy()->subDays(2),
        'status' => PublishedStatus::PUBLISHED,
    ]);
    $middle = Review::factory()->create([
        'published_at' => $now->copy()->subDay(),
        'status' => PublishedStatus::PUBLISHED,
    ]);
    $newer = Review::factory()->create([
        'published_at' => $now,
        'status' => PublishedStatus::PUBLISHED,
    ]);

    $self = serverUrl('/reviews', true);

    $response = $this
        ->jsonApi()
        ->expects('reviews')
        ->sort('published_at')
        ->get($self);

    $response->assertSuccessful();
    $response->assertFetchedManyInOrder([$older, $middle, $newer]);
});

it('only fetches generic reviews when generic filter is used', function () {
    /** @var TestCase $this */
    $generic = Review::factory()
        ->count(3)
        ->create([
            'published_at' => Carbon::now(),
            'status' => PublishedStatus::PUBLISHED,
        ]);

    $variantReviews = Review::factory()
        ->for(ProductVariant::factory(), 'purchasable')
        ->count(2)
        ->create([
            'published_at' => Carbon::now(),
            'status' => PublishedStatus::PUBLISHED,
        ]);

    $productReviews = Review::factory()
        ->for(Product::factory(), 'purchasable')
        ->count(2)
        ->create([
            'published_at' => Carbon::now(),
            'status' => PublishedStatus::PUBLISHED,
        ]);

    $self = serverUrl('/reviews', true);

    $response = $this
        ->jsonApi()
        ->expects('reviews')
        ->filter(['without_purchasable' => 'true'])
        ->get($self);

    $response->assertSuccessful();

    $fetchedIds = collect($response->json('data'))->pluck('id')->all();
    $genericIds = $generic->pluck('id')->map(fn ($id) => (string) $id)->all();

    expect($fetchedIds)->toEqualCanonicalizing($genericIds);
});

it('can list generic reviews in random order', function () {
    /** @var TestCase $this */
    $reviews = Review::factory()
        ->count(8)
        ->create([
            'published_at' => Carbon::now(),
            'status' => PublishedStatus::PUBLISHED,
        ]);

    $self = serverUrl('/reviews', true);

    $response1 = $this
        ->jsonApi()
        ->expects('reviews')
        ->sort('random')
        ->get($self);

    $response2 = $this
        ->jsonApi()
        ->expects('reviews')
        ->sort('random')
        ->get($self);

    $response1->assertSuccessful();
    $response2->assertSuccessful();

    $ids1 = $response1->json('data.*.id');
    $ids2 = $response2->json('data.*.id');

    // same set of IDs
    expect(collect($ids1)->sort()->values()->all())
        ->toEqual(collect($ids2)->sort()->values()->all());
    // order should differ with high probability
    expect($ids1)->not->toEqual($ids2);
});

it('can list product variant reviews', function () {
    /** @var TestCase $this */
    $reviews = Review::factory()
        ->for(ProductVariant::factory(), 'purchasable')
        ->count(5)
        ->create([
            'published_at' => Carbon::now(),
            'status' => PublishedStatus::PUBLISHED,
        ]);

    $self = serverUrl('/reviews', true);

    $response = $this
        ->jsonApi()
        ->expects('reviews')
        ->get($self);

    $response
        ->assertSuccessful()
        ->assertFetchedMany($reviews);
});

it('can list product reviews', function () {
    /** @var TestCase $this */
    $reviews = Review::factory()
        ->for(Product::factory(), 'purchasable')
        ->count(4)
        ->create([
            'published_at' => Carbon::now(),
            'status' => PublishedStatus::PUBLISHED,
        ]);

    $self = serverUrl('/reviews', true);

    $response = $this
        ->jsonApi()
        ->expects('reviews')
        ->get($self);

    $response
        ->assertSuccessful()
        ->assertFetchedMany($reviews);
});
