<?php

namespace Dystore\Reviews\Domain\Reviews\Filament\Resources\ReviewResource\Pages;

use Dystore\Reviews\Domain\Reviews\Filament\Resources\ReviewResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReviews extends ListRecords
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
