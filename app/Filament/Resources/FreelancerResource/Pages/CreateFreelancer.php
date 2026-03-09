<?php

namespace App\Filament\Resources\FreelancerResource\Pages;

use App\Filament\Resources\FreelancerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFreelancer extends CreateRecord
{
    protected static string $resource = FreelancerResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
