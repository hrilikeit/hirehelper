<?php

namespace App\Filament\Resources\ProjectOfferResource\Pages;

use App\Filament\Resources\ProjectOfferResource;
use Filament\Resources\Pages\ListRecords;

class ListProjectOffers extends ListRecords
{
    protected static string $resource = ProjectOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
