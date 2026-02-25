<?php

namespace Dystore\Reviews\Domain\Reviews\Filament\Resources\Review\Pages;

use Dystore\Reviews\Domain\Reviews\Filament\Resources\Review\ReviewResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReview extends CreateRecord
{
    protected static string $resource = ReviewResource::class;
}
