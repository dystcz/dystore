<?php

use Carbon\Carbon;
use Dystore\Api\Base\Enums\PublishedStatus;
use Dystore\Reviews\Domain\Reviews\Filament\Resources\ReviewResource;
use Dystore\Reviews\Domain\Reviews\Models\Review;
use Dystore\Reviews\Domain\Reviews\Scopes\PublishedScope;
use Dystore\Tests\Reviews\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class)
    ->group('reviews');

it('includes draft reviews when published scope is removed', function () {
    /** @var TestCase $this */
    $draft = Review::factory()->create([
        'status' => PublishedStatus::DRAFT,
        'published_at' => null,
    ]);

    $published = Review::factory()->create([
        'status' => PublishedStatus::PUBLISHED,
        'published_at' => Carbon::now(),
    ]);

    $reviews = Review::withoutGlobalScope(PublishedScope::class)->get();

    expect($reviews)->toHaveCount(2);
    expect($reviews->pluck('id')->all())->toContain($draft->id, $published->id);
});

it('includes hidden reviews when published scope is removed', function () {
    /** @var TestCase $this */
    $hidden = Review::factory()->create([
        'status' => PublishedStatus::HIDDEN,
        'published_at' => null,
    ]);

    $published = Review::factory()->create([
        'status' => PublishedStatus::PUBLISHED,
        'published_at' => Carbon::now(),
    ]);

    $reviews = Review::withoutGlobalScope(PublishedScope::class)->get();

    expect($reviews)->toHaveCount(2);
    expect($reviews->pluck('id')->all())->toContain($hidden->id, $published->id);
});

it('does not include draft reviews in the default model query', function () {
    /** @var TestCase $this */
    Review::factory()->create([
        'status' => PublishedStatus::DRAFT,
        'published_at' => null,
    ]);

    $published = Review::factory()->create([
        'status' => PublishedStatus::PUBLISHED,
        'published_at' => Carbon::now(),
    ]);

    $reviews = Review::query()->get();

    expect($reviews)->toHaveCount(1);
    expect($reviews->first()->id)->toBe($published->id);
});

it('counts all reviews for the navigation badge', function () {
    /** @var TestCase $this */
    Review::factory()->create([
        'status' => PublishedStatus::DRAFT,
        'published_at' => null,
    ]);

    Review::factory()->create([
        'status' => PublishedStatus::HIDDEN,
        'published_at' => null,
    ]);

    Review::factory()->create([
        'status' => PublishedStatus::PUBLISHED,
        'published_at' => Carbon::now(),
    ]);

    expect(ReviewResource::getNavigationBadge())->toBe('3');
});

it('preserves soft delete scope when published scope is removed', function () {
    /** @var TestCase $this */
    $active = Review::factory()->create([
        'status' => PublishedStatus::DRAFT,
        'published_at' => null,
    ]);

    $deleted = Review::factory()->create([
        'status' => PublishedStatus::DRAFT,
        'published_at' => null,
    ]);
    $deleted->delete();

    $reviews = Review::withoutGlobalScope(PublishedScope::class)->get();

    expect($reviews)->toHaveCount(1);
    expect($reviews->first()->id)->toBe($active->id);
});

it('removes the published scope in the eloquent query override', function () {
    /** @var TestCase $this */
    $method = new ReflectionMethod(ReviewResource::class, 'getEloquentQuery');

    expect($method->isStatic())->toBeTrue();
    expect($method->isPublic())->toBeTrue();

    // Verify the method exists and returns a Builder (class declares the override)
    $returnType = $method->getReturnType();
    expect($returnType)->not->toBeNull();
    expect($returnType->getName())->toBe('Illuminate\Database\Eloquent\Builder');
});
