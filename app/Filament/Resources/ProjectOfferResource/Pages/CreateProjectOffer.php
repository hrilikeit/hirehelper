<?php

namespace App\Filament\Resources\ProjectOfferResource\Pages;

use App\Filament\Resources\ProjectOfferResource;
use App\Support\AdminAccess;
use Filament\Resources\Pages\CreateRecord;

class CreateProjectOffer extends CreateRecord
{
    protected static string $resource = ProjectOfferResource::class;

    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (AdminAccess::isSalesManager(auth()->user()) && empty($data['sales_manager_id'])) {
            $data['sales_manager_id'] = auth()->id();
        }

        if (! empty($data['status']) && in_array($data['status'], ['active', 'closed'], true) && empty($data['accepted_at'])) {
            $data['accepted_at'] = now();
        }

        $data['sent_at'] = $data['sent_at'] ?? now();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
