<?php

namespace App\Filament\Resources\MilestoneProjectResource\Pages;

use App\Filament\Resources\MilestoneProjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMilestoneProject extends CreateRecord
{
    protected static string $resource = MilestoneProjectResource::class;

    protected function afterCreate(): void
    {
        $this->record->recalculateTotal();
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
