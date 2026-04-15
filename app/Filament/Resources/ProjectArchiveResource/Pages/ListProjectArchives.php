<?php

namespace App\Filament\Resources\ProjectArchiveResource\Pages;

use App\Filament\Resources\ProjectArchiveResource;
use Filament\Resources\Pages\ListRecords;

class ListProjectArchives extends ListRecords
{
    protected static string $resource = ProjectArchiveResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
