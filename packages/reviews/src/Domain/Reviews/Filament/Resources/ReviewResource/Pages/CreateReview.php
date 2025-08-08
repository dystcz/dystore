<?php

namespace Dystore\Reviews\Domain\Reviews\Filament\Resources\ReviewResource\Pages;

use Dystore\Reviews\Domain\Reviews\Filament\Resources\ReviewResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReview extends CreateRecord
{
    protected static string $resource = ReviewResource::class;
}
