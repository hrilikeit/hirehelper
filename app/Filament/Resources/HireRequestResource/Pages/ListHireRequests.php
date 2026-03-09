<?php

namespace App\Filament\Resources\HireRequestResource\Pages;

use App\Filament\Resources\HireRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListHireRequests extends ListRecords
{
    protected static string $resource = HireRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
