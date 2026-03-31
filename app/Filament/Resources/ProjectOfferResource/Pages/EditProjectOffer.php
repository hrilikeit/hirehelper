<?php

namespace App\Filament\Resources\ProjectOfferResource\Pages;

use App\Filament\Resources\ProjectOfferResource;
use App\Mail\ContractActiveMail;
use App\Mail\PaymentFailedMail;
use App\Mail\WeeklyTrackedHoursMail;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

class EditProjectOffer extends EditRecord
{
    protected static string $resource = ProjectOfferResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! empty($data['status']) && in_array($data['status'], ['active', 'closed'], true) && empty($data['accepted_at'])) {
            $data['accepted_at'] = now();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $offer = $this->record;

        // Send "Contract Active" email when status changes to active
        if ($offer->wasChanged('status') && $offer->status === 'active') {
            $client = $offer->project?->user;
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
                    $offer = $this->record;
                    $client = $offer->project?->user;

                    if (! $client) {
                        Notification::make()->title('No client found for this offer.')->danger()->send();
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
                    $offer = $this->record;
                    $client = $offer->project?->user;

                    if (! $client) {
                        Notification::make()->title('No client found for this offer.')->danger()->send();
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
