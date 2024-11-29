<?php

namespace Dystcz\LunarApiReviews\Domain\Hub\Components\Slots;

use Carbon\Carbon;
use Dystcz\LunarApiReviews\Domain\Reviews\Models\Review;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Lunar\Hub\Slots\AbstractSlot;
use Lunar\Hub\Slots\Traits\HubSlot;

class ReviewsSlot extends Component implements AbstractSlot
{
    use HubSlot;
    use WithPagination;

    public static function getName(): string
    {
        return 'lunar-api-reviews::reviews-slot';
    }

    public function getSlotHandle(): string
    {
        return 'reviews-slot';
    }

    public function getSlotInitialValue(): void {}

    public function getSlotPosition(): string
    {
        return 'bottom';
    }

    public function getSlotTitle(): string
    {
        return 'Reviews';
    }

    public function updateSlotModel(): void {}

    public function handleSlotSave($model, $data): void
    {
        $this->slotModel = $model;
    }

    public function toggle(Review $review): void
    {
        $review->update(['published_at' => $review->published_at ? null : Carbon::now()]);

        $this->setPage($this->page);
    }

    public function render(): View|Factory
    {
        return view('lunar-api-reviews::livewire.reviews-slot', [
            'reviews' => $this->slotModel->reviews()->paginate(10),
        ]);
    }
}
