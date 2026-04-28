<?php

namespace App\Filament\Resources\MilestoneProjectResource\Pages;

use App\Filament\Resources\MilestoneProjectResource;
use Filament\Resources\Pages\EditRecord;

class EditMilestoneProject extends EditRecord
{
    protected static string $resource = MilestoneProjectResource::class;

    protected function afterSave(): void
    {
        $this->record->recalculateTotal();
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
