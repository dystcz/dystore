<?php

namespace Dystore\Reviews\Domain\Reviews\Filament\Resources\Review\Pages;

use Dystore\Reviews\Domain\Reviews\Filament\Resources\Review\ReviewResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReview extends EditRecord
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
