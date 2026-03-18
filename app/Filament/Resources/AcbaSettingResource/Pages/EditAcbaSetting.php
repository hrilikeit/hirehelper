<?php

namespace App\Filament\Resources\AcbaSettingResource\Pages;

use App\Filament\Resources\AcbaSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAcbaSetting extends EditRecord
{
    protected static string $resource = AcbaSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
