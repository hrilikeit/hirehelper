<?php

namespace App\Filament\Resources\ProjectActiveResource\Pages;

use App\Filament\Resources\ProjectActiveResource;
use App\Mail\ContractActiveMail;
use App\Mail\PaymentFailedMail;
use App\Mail\WeeklyTrackedHoursMail;
use App\Models\EmailLog;
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
        $offer = $project->offers()->whereIn('status', ['active', 'pending', 'accepted'])->first();

        if (! $offer) {
            return;
        }

        // Send "Contract Active" email when project transitions to active
        if ($project->wasChanged('status') && $project->status === 'active' && EmailSetting::isActive('contract_active')) {
            $client = $project->user;
            if ($client) {
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
            Action::make('checkPayPal')
                ->label('Check PayPal')
                ->color('success')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    $project = $this->record;
                    $offer = $project->offers()->whereIn('status', ['active', 'pending', 'accepted'])->first();

                    if (! $offer) {
                        Notification::make()->title('No offer found for this project.')->danger()->send();
                        return;
                    }

                    $subscription = \App\Models\WeeklySubscription::where('project_offer_id', $offer->id)->latest()->first();

                    if (! $subscription) {
                        Notification::make()->title('No PayPal subscription linked to this offer yet.')->warning()->send();
                        return;
                    }

                    if (! filled($subscription->paypal_subscription_id)) {
                        Notification::make()->title('Subscription has no PayPal ID yet (not yet approved).')->warning()->send();
                        return;
                    }

                    try {
                        $service = app(\App\Services\PayPalSubscriptionService::class);
                        $synced = $service->sync($subscription);

                        Notification::make()
                            ->title('PayPal sync complete')
                            ->body('Status: ' . ($synced->paypal_subscription_status ?: '—') . ' • Next billing: ' . ($synced->next_billing_at?->format('M j, Y') ?: '—'))
                            ->success()
                            ->send();

                        // Refresh form so the placeholders re-render with new data
                        $this->refreshFormData([
                            'paypal_status_display',
                            'paypal_payer_email_display',
                            'paypal_next_billing_display',
                            'total_paid_display',
                            'total_pending_display',
                        ]);
                    } catch (\Throwable $e) {
                        report($e);
                        Notification::make()
                            ->title('PayPal sync failed: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('sendPaymentFailed')
                ->label('Send Payment Failed Email')
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle')
                ->requiresConfirmation()
                ->modalHeading('Send Payment Failed Email')
                ->modalDescription('This will send a payment failed notification to the client.')
                ->action(function () {
                    $project = $this->record;
                    $offer = $project->offers()->whereIn('status', ['active', 'pending', 'accepted'])->first();
                    $client = $project->user;

                    if (! $client) {
                        Notification::make()->title('No client found for this project.')->danger()->send();
                        return;
                    }

                    if (! $offer) {
                        Notification::make()->title('No active offer/freelancer assigned to this project. Assign a freelancer first.')->danger()->send();
                        return;
                    }

                    if (! EmailSetting::isActive('payment_failed')) {
                        Notification::make()->title('Payment Failed email is disabled in settings.')->warning()->send();
                        return;
                    }

                    try {
                        // Calculate actual outstanding balance from unpaid timesheets
                        $outstandingBalance = \App\Models\Timesheet::where('project_offer_id', $offer->id)
                            ->where('status', 'pending')
                            ->sum('amount');

                        $mailable = new PaymentFailedMail(
                            offer: $offer,
                            userName: $client->name,
                            billingUrl: route('workspace.billing-method'),
                            amount: '$' . number_format((float) $outstandingBalance, 2),
                        );

                        // Create log first so we can embed tracking pixel
                        $emailLog = EmailLog::record(
                            userId: $client->id,
                            emailType: 'payment_failed',
                            subject: 'Payment failed',
                            toEmail: $client->email,
                            projectId: $project->id,
                            offerId: $offer->id,
                        );

                        // Attach emailLogId for the tracking pixel in the layout
                        $mailable->with('emailLogId', $emailLog->id);
                        Mail::to($client->email)->send($mailable);

                        // Store rendered body after send
                        $emailLog->update(['body' => $mailable->render()]);

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
                    $offer = $project->offers()->whereIn('status', ['active', 'pending', 'accepted'])->first();
                    $client = $project->user;

                    if (! $client) {
                        Notification::make()->title('No client found for this project.')->danger()->send();
                        return;
                    }

                    if (! $offer) {
                        Notification::make()->title('No active offer/freelancer assigned to this project. Assign a freelancer first.')->danger()->send();
                        return;
                    }

                    if (! EmailSetting::isActive('weekly_tracked_hours')) {
                        Notification::make()->title('Weekly Tracked Hours email is disabled in settings.')->warning()->send();
                        return;
                    }

                    try {
                        $mailable = new WeeklyTrackedHoursMail(
                            offer: $offer,
                            userName: $client->name,
                            hoursTracked: (float) $data['hours'],
                            weekLabel: $data['week_label'],
                            reportsUrl: route('workspace.reports'),
                        );

                        $emailLog = EmailLog::record(
                            userId: $client->id,
                            emailType: 'weekly_tracked_hours',
                            subject: 'Weekly tracked hours — ' . $data['week_label'],
                            toEmail: $client->email,
                            projectId: $project->id,
                            offerId: $offer->id,
                        );

                        $mailable->with('emailLogId', $emailLog->id);
                        Mail::to($client->email)->send($mailable);
                        $emailLog->update(['body' => $mailable->render()]);

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
