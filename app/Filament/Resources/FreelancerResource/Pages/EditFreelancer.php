<?php

namespace App\Filament\Resources\FreelancerResource\Pages;

use App\Filament\Resources\FreelancerResource;
use App\Models\Freelancer;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFreelancer extends EditRecord
{
    protected static string $resource = FreelancerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['slug'] = Freelancer::generateUniqueSlug(
            (string) ($data['name'] ?? $this->record->name ?? 'freelancer'),
            $this->record->getKey(),
        );
        $data['status'] = $data['status'] ?? $this->record->status ?? 'active';
        $data['avatar'] = $data['avatar'] ?? $this->record->avatar ?? 'avatar-jade.svg';
        $data['is_featured'] = (bool) ($data['is_featured'] ?? $this->record->is_featured ?? false);
        $data['added_by_user_id'] = $data['added_by_user_id'] ?? $this->record->added_by_user_id ?? auth()->id();
        $data['overview'] = trim((string) ($data['bio'] ?? ''));

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->syncReviewMetrics();
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
