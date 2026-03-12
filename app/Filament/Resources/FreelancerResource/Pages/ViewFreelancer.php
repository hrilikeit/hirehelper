<?php

namespace App\Filament\Resources\FreelancerResource\Pages;

use App\Filament\Resources\FreelancerResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewFreelancer extends ViewRecord
{
    protected static string $resource = FreelancerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
