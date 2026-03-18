<?php

namespace App\Filament\Resources\AcbaSettingResource\Pages;

use App\Filament\Resources\AcbaSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAcbaSettings extends ListRecords
{
    protected static string $resource = AcbaSettingResource::class;

    protected function getHeaderActions(): array
    {
        return AcbaSettingResource::canCreate()
            ? [CreateAction::make()]
            : [];
    }
}
