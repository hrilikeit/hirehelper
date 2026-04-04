<?php

namespace App\Filament\Resources\ProjectActiveResource\Pages;

use App\Filament\Resources\ProjectActiveResource;
use App\Mail\ContractActiveMail;
use App\Mail\PaymentFailedMail;
use App\Mail\WeeklyTrackedHoursMail;
use App\Models\EmailSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

class EditProjectActive extends EditRecord
{
    protected static string $resource = ProjectActiveResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! empty($data['status']) && ! in_array($data['status'], ['draft', 'pending'], true) && empty($data['accepted_at'])) {
            $data['accepted_at'] = now();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $project = $this->record;
        $offer = $project->offers()->where('status', 'active')->first();

        if (! $offer) {
            return;
        }

        // Send "Contract Active" email when project transitions to active
        if ($project->wasChanged('status') && $project->status === 'active' && EmailSetting::isActive('contract_active')) {
            $client = $project->user;
            if ($client) {
                try {
                    Mail::to($client->email)->send(new ContractActiveMail(
                        offer: $offer,
                        userName: $client->name,
                        projectUrl: route('workspace.project-active'),
                    ));

                    Notification::make()
                        ->title('Contract active email sent to ' . $client->email)
                        ->success()
                        ->send();
                } catch (\Throwable $e) {
                    report($e);
                    Notification::make()
                        ->title('Failed to send contract active email')
                        ->danger()
                        ->send();
                }
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendPaymentFailed')
                ->label('Send Payment Failed Email')
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle')
                ->requiresConfirmation()
                ->modalHeading('Send Payment Failed Email')
                ->modalDescription('This will send a payment failed notification to the client.')
                ->action(function () {
                    $project = $this->record;
                    $offer = $project->offers()->where('status', 'active')->first();
                    $client = $project->user;

                    if (! $client || ! $offer) {
                        Notification::make()->title('No client or active offer found.')->danger()->send();
                        return;
                    }

                    if (! EmailSetting::isActive('payment_failed')) {
                        Notification::make()->title('Payment Failed email is disabled in settings.')->warning()->send();
                        return;
                    }

                    try {
                        Mail::to($client->email)->send(new PaymentFailedMail(
                            offer: $offer,
                            userName: $client->name,
                            billingUrl: route('workspace.billing-method'),
                            amount: '$' . number_format($offer->weekly_amount, 2),
                        ));

                        Notification::make()
                            ->title('Payment failed email sent to ' . $client->email)
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        report($e);
                        Notification::make()
                            ->title('Failed to send email: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('sendWeeklyHours')
                ->label('Send Weekly Hours Email')
                ->color('info')
                ->icon('heroicon-o-clock')
                ->form([
                    TextInput::make('hours')
                        ->label('Hours tracked this week')
                        ->numeric()
                        ->required()
                        ->minValue(0.1)
                        ->step(0.1),
                    TextInput::make('week_label')
                        ->label('Week label (e.g. "Mar 24 – Mar 30")')
                        ->required()
                        ->default(now()->startOfWeek()->format('M j') . ' – ' . now()->endOfWeek()->format('M j')),
                ])
                ->action(function (array $data) {
                    $project = $this->record;
                    $offer = $project->offers()->where('status', 'active')->first();
                    $client = $project->user;

                    if (! $client || ! $offer) {
                        Notification::make()->title('No client or active offer found.')->danger()->send();
                        return;
                    }

                    if (! EmailSetting::isActive('weekly_tracked_hours')) {
                        Notification::make()->title('Weekly Tracked Hours email is disabled in settings.')->warning()->send();
                        return;
                    }

                    try {
                        Mail::to($client->email)->send(new WeeklyTrackedHoursMail(
                            offer: $offer,
                            userName: $client->name,
                            hoursTracked: (float) $data['hours'],
                            weekLabel: $data['week_label'],
                            reportsUrl: route('workspace.reports'),
                        ));

                        Notification::make()
                            ->title('Weekly hours email sent to ' . $client->email)
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        report($e);
                        Notification::make()
                            ->title('Failed to send email: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
