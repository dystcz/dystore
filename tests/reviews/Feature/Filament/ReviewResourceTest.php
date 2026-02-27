<?php

use Dystore\Api\Base\Enums\PublishedStatus;
use Dystore\Reviews\Domain\Reviews\Filament\Resources\Review\Pages\CreateReview;
use Dystore\Reviews\Domain\Reviews\Filament\Resources\Review\Pages\EditReview;
use Dystore\Reviews\Domain\Reviews\Filament\Resources\Review\Pages\ListReviews;
use Dystore\Reviews\Domain\Reviews\Models\Review;
use Dystore\Tests\Reviews\Stubs\Users\User;
use Dystore\Tests\Reviews\TestCase;
use Filament\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Livewire\Livewire;

uses(TestCase::class)
    ->group('reviews', 'reviews.filament');

it('can render the list reviews page', function () {
    Livewire::test(ListReviews::class)
        ->assertSuccessful();
});

it('can render the create review page', function () {
    Livewire::test(CreateReview::class)
        ->assertSuccessful();
});

it('can render the edit review page', function () {
    $review = Review::factory()->create();

    Livewire::test(EditReview::class, ['record' => $review->getRouteKey()])
        ->assertSuccessful();
});

it('can list reviews in the table', function () {
    $reviews = Review::factory()
        ->count(5)
        ->create([
            'published_at' => now(),
            'status' => PublishedStatus::PUBLISHED,
        ]);

    Livewire::test(ListReviews::class)
        ->assertCanSeeTableRecords($reviews)
        ->assertCountTableRecords(5);
});

it('can see review columns in the table', function () {
    $review = Review::factory()->create([
        'name' => 'Test Review',
        'rating' => 5,
        'published_at' => now(),
        'status' => PublishedStatus::PUBLISHED,
    ]);

    Livewire::test(ListReviews::class)
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('rating')
        ->assertCanRenderTableColumn('status')
        ->assertCanRenderTableColumn('published_at');
});

it('has edit action in the table', function () {
    Livewire::test(ListReviews::class)
        ->assertTableActionExists(EditAction::class);
});

it('has delete bulk action in the table', function () {
    Livewire::test(ListReviews::class)
        ->assertTableBulkActionExists(DeleteBulkAction::class);
});

it('can delete a review from the table', function () {
    $review = Review::factory()->create();

    Livewire::test(ListReviews::class)
        ->callTableAction(DeleteAction::class, $review)
        ->assertSwalActionExecuted('delete');

    $this->assertModelMissing($review);
});

it('can render the edit form with correct fields', function () {
    $review = Review::factory()->create([
        'rating' => 4,
        'status' => PublishedStatus::DRAFT,
    ]);

    Livewire::test(EditReview::class, ['record' => $review->getRouteKey()])
        ->assertFormExists()
        ->assertFormFieldExists('name')
        ->assertFormFieldExists('rating')
        ->assertFormFieldExists('comment')
        ->assertFormFieldExists('status')
        ->assertFormFieldExists('published_at')
        ->assertFormFieldExists('purchasable')
        ->assertFormFieldExists('user_id');
});

it('can update a review', function () {
    $review = Review::factory()->create([
        'rating' => 3,
        'status' => PublishedStatus::DRAFT,
    ]);

    Livewire::test(EditReview::class, ['record' => $review->getRouteKey()])
        ->fillForm([
            'rating' => 5,
            'comment' => 'Updated comment',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $review->refresh();

    expect($review->rating)->toBe(5)
        ->and($review->comment)->toBe('Updated comment');
});

it('can create a review via the create form', function () {
    $user = User::factory()->create();

    Livewire::test(CreateReview::class)
        ->fillForm([
            'name' => 'New Review',
            'rating' => 5,
            'comment' => 'Great product!',
            'status' => PublishedStatus::PUBLISHED,
            'user_id' => $user->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('reviews', [
        'name' => 'New Review',
        'rating' => 5,
        'comment' => 'Great product!',
        'status' => PublishedStatus::PUBLISHED,
    ]);
});

it('validates required fields on create form', function () {
    Livewire::test(CreateReview::class)
        ->fillForm([
            'name' => null,
            'rating' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['rating' => 'required']);
});

it('validates required fields on edit form', function () {
    $review = Review::factory()->create();

    Livewire::test(EditReview::class, ['record' => $review->getRouteKey()])
        ->fillForm([
            'rating' => null,
        ])
        ->call('save')
        ->assertHasFormErrors(['rating' => 'required']);
});

it('can filter reviews by rating', function () {
    $fiveStar = Review::factory()->count(2)->create(['rating' => 5, 'published_at' => now(), 'status' => PublishedStatus::PUBLISHED]);
    $threeStar = Review::factory()->count(3)->create(['rating' => 3, 'published_at' => now(), 'status' => PublishedStatus::PUBLISHED]);

    Livewire::test(ListReviews::class)
        ->filterTable('rating', 5)
        ->assertCanSeeTableRecords($fiveStar)
        ->assertCanNotSeeTableRecords($threeStar);
});

it('can filter reviews by status', function () {
    $published = Review::factory()->count(2)->create(['status' => PublishedStatus::PUBLISHED, 'published_at' => now()]);
    $draft = Review::factory()->count(3)->create(['status' => PublishedStatus::DRAFT, 'published_at' => null]);

    Livewire::test(ListReviews::class)
        ->filterTable('status', PublishedStatus::PUBLISHED->value)
        ->assertCanSeeTableRecords($published)
        ->assertCanNotSeeTableRecords($draft);
});
