<?php

namespace App\Filament\Resources\ClientProjectResource\Pages;

use App\Filament\Resources\ClientProjectResource;
use App\Mail\ContractActiveMail;
use App\Models\EmailLog;
use App\Models\EmailSetting;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

class EditClientProject extends EditRecord
{
    protected static string $resource = ClientProjectResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! empty($data['status']) && ! in_array($data['status'], ['draft', 'pending'], true) && empty($data['accepted_at'])) {
            $data['accepted_at'] = now();
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('acceptActivate')
                ->label('Accept & Activate')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->modalHeading('Accept & Activate Project')
                ->modalDescription('This will move the project to Projects Active, activate the offer, and send the client a "Contract Active" email.')
                ->visible(fn () => in_array($this->record->status, ['draft', 'pending'], true))
                ->action(function () {
                    $project = $this->record;

                    // Activate project
                    $project->update([
                        'status' => 'active',
                        'accepted_at' => $project->accepted_at ?? now(),
                    ]);

                    // Activate the offer
                    $offer = $project->offers()->whereIn('status', ['pending', 'accepted'])->latest()->first();
                    if ($offer) {
                        $offer->update([
                            'status' => 'active',
                            'activated_at' => $offer->activated_at ?? now(),
                        ]);
                    }

                    // Send "Contract Active" email to client
                    $client = $project->user;
                    if ($client && $offer && EmailSetting::isActive('contract_active')) {
                        try {
                            $mailable = new ContractActiveMail(
                                offer: $offer,
                                userName: $client->name,
                                projectUrl: route('workspace.project-active'),
                            );

                            $emailLog = EmailLog::record(
                                userId: $client->id,
                                emailType: 'contract_active',
                                subject: 'Your contract is now active',
                                toEmail: $client->email,
                                projectId: $project->id,
                                offerId: $offer->id,
                            );

                            $mailable->with('emailLogId', $emailLog->id);
                            Mail::to($client->email)->send($mailable);
                            $emailLog->update(['body' => $mailable->render()]);
                        } catch (\Throwable $e) {
                            report($e);
                        }
                    }

                    Notification::make()
                        ->title('Project activated! Moved to Projects Active.')
                        ->success()
                        ->send();

                    $this->redirect(ClientProjectResource::getUrl('index'));
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
