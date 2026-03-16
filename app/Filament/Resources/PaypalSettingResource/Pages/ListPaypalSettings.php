<?php

namespace App\Filament\Resources\PaypalSettingResource\Pages;

use App\Filament\Resources\PaypalSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPaypalSettings extends ListRecords
{
    protected static string $resource = PaypalSettingResource::class;

    protected function getHeaderActions(): array
    {
        return PaypalSettingResource::canCreate()
            ? [CreateAction::make()]
            : [];
    }
}
