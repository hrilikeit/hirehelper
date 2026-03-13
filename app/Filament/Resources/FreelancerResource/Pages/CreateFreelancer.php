<?php

namespace App\Filament\Resources\FreelancerResource\Pages;

use App\Filament\Resources\FreelancerResource;
use App\Models\Freelancer;
use Filament\Resources\Pages\CreateRecord;

class CreateFreelancer extends CreateRecord
{
    protected static string $resource = FreelancerResource::class;

    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = Freelancer::generateUniqueSlug((string) ($data['name'] ?? 'freelancer'));
        $data['status'] = $data['status'] ?? 'active';
        $data['avatar'] = filled($data['avatar'] ?? null) ? $data['avatar'] : 'avatar-jade.svg';
        $data['is_featured'] = (bool) ($data['is_featured'] ?? false);
        $data['added_by_user_id'] = $data['added_by_user_id'] ?? auth()->id();
        $data['overview'] = trim((string) ($data['bio'] ?? ''));

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->syncReviewMetrics();
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
