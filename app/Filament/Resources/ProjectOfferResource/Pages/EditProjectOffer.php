<?php

namespace App\Filament\Resources\ProjectOfferResource\Pages;

use App\Filament\Resources\ProjectOfferResource;
use Filament\Resources\Pages\EditRecord;

class EditProjectOffer extends EditRecord
{
    protected static string $resource = ProjectOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
