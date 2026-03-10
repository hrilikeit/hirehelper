<?php

namespace App\Filament\Resources\ClientProjectResource\Pages;

use App\Filament\Resources\ClientProjectResource;
use App\Support\AdminAccess;
use Filament\Resources\Pages\CreateRecord;

class CreateClientProject extends CreateRecord
{
    protected static string $resource = ClientProjectResource::class;

    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (AdminAccess::isSalesManager(auth()->user()) && empty($data['sales_manager_id'])) {
            $data['sales_manager_id'] = auth()->id();
        }

        if (! empty($data['status']) && ! in_array($data['status'], ['draft', 'pending'], true) && empty($data['accepted_at'])) {
            $data['accepted_at'] = now();
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
