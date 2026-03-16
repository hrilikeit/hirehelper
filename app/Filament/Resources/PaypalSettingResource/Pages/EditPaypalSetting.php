<?php

namespace App\Filament\Resources\PaypalSettingResource\Pages;

use App\Filament\Resources\PaypalSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPaypalSetting extends EditRecord
{
    protected static string $resource = PaypalSettingResource::class;

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
