<?php

namespace App\Filament\Resources\HireRequestResource\Pages;

use App\Filament\Resources\HireRequestResource;
use Filament\Resources\Pages\EditRecord;

class EditHireRequest extends EditRecord
{
    protected static string $resource = HireRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
