<?php

namespace App\Filament\Resources\AcbaSettingResource\Pages;

use App\Filament\Resources\AcbaSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAcbaSetting extends CreateRecord
{
    protected static string $resource = AcbaSettingResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
