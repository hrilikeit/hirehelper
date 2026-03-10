<?php

namespace App\Filament\Resources\FreelancerResource\Pages;

use App\Filament\Resources\FreelancerResource;
use App\Support\AdminAccess;
use Filament\Resources\Pages\CreateRecord;

class CreateFreelancer extends CreateRecord
{
    protected static string $resource = FreelancerResource::class;

    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (AdminAccess::isSalesManager(auth()->user()) && empty($data['added_by_user_id'])) {
            $data['added_by_user_id'] = auth()->id();
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
