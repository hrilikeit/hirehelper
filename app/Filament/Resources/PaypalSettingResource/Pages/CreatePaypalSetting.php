<?php

namespace App\Filament\Resources\PaypalSettingResource\Pages;

use App\Filament\Resources\PaypalSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaypalSetting extends CreateRecord
{
    protected static string $resource = PaypalSettingResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
