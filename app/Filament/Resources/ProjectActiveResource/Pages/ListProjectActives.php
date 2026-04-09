<?php

namespace App\Filament\Resources\ProjectActiveResource\Pages;

use App\Filament\Resources\ProjectActiveResource;
use App\Filament\Resources\ProjectActiveResource\Widgets\ProjectActiveOverview;
use Filament\Resources\Pages\ListRecords;

class ListProjectActives extends ListRecords
{
    protected static string $resource = ProjectActiveResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            ProjectActiveOverview::class,
        ];
    }
}
