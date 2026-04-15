<?php

namespace App\Filament\Resources\ProjectArchiveResource\Pages;

use App\Filament\Resources\ProjectArchiveResource;
use Filament\Resources\Pages\EditRecord;

class EditProjectArchive extends EditRecord
{
    protected static string $resource = ProjectArchiveResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
