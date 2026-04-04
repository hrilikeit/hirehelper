<?php

namespace App\Filament\Resources\ClientProjectResource\RelationManagers;

use App\Models\Timesheet;
use App\Models\WeeklySubscription;
use App\Services\PayPalSubscriptionService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TimesheetsRelationManager extends RelationManager
{
    protected static string $relationship = 'timesheets';

    protected static ?string $title = 'Weekly Hours';

    protected static ?string $recordTitleAttribute = 'week_start';

    public function form(Schema $schema): Schema
    {
        $ownerRecord = $this->getOwnerRecord();
        $offers = $ownerRecord->offers()->with('freelancer')->get();

        $offerOptions = $offers->mapWithKeys(function ($offer) {
            $label = $offer->freelancer_display_name . ' — $' . number_format((float) $offer->hourly_rate, 2) . '/hr';
            return [$offer->id => $label];
        })->toArray();

        return $schema->components([
            Select::make('project_offer_id')
                ->label('Offer / Freelancer')
                ->options($offerOptions)
                ->required()
                ->default($offers->first()?->id),
            DatePicker::make('week_start')
                ->label('Week starting (Sunday)')
                ->required()
                ->default(Timesheet::weekStartFor(now())->toDateString()),
            TextInput::make('sun')->label('Sun')->numeric()->default(0)->step(0.25),
            TextInput::make('mon')->label('Mon')->numeric()->default(0)->step(0.25),
            TextInput::make('tue')->label('Tue')->numeric()->default(0)->step(0.25),
            TextInput::make('wed')->label('Wed')->numeric()->default(0)->step(0.25),
            TextInput::make('thu')->label('Thu')->numeric()->default(0)->step(0.25),
            TextInput::make('fri')->label('Fri')->numeric()->default(0)->step(0.25),
            TextInput::make('sat')->label('Sat')->numeric()->default(0)->step(0.25),
            Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'paid' => 'Paid',
                    'discarded' => 'Discarded',
                ])
                ->default('pending'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('week_start')
                    ->label('Week')
                    ->date('M j, Y')
                    ->sortable(),
                TextColumn::make('offer.freelancer_display_name')
                    ->label('Freelancer'),
                TextColumn::make('sun')->label('Sun'),
                TextColumn::make('mon')->label('Mon'),
                TextColumn::make('tue')->label('Tue'),
                TextColumn::make('wed')->label('Wed'),
                TextColumn::make('thu')->label('Thu'),
                TextColumn::make('fri')->label('Fri'),
                TextColumn::make('sat')->label('Sat'),
                TextColumn::make('total_hours')
                    ->label('Total')
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('usd')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'discarded' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->defaultSort('week_start', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->label('Add Week')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['total_hours'] = (float) ($data['sun'] ?? 0) + (float) ($data['mon'] ?? 0)
                            + (float) ($data['tue'] ?? 0) + (float) ($data['wed'] ?? 0)
                            + (float) ($data['thu'] ?? 0) + (float) ($data['fri'] ?? 0)
                            + (float) ($data['sat'] ?? 0);

                        $offer = \App\Models\ProjectOffer::find($data['project_offer_id']);
                        $rate = $offer ? (float) $offer->hourly_rate : 0;
                        $data['amount'] = round($data['total_hours'] * $rate, 2);

                        return $data;
                    }),
            ])
            ->recordActions([
                Action::make('markPaid')
                    ->label('Mark Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Timesheet $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Mark as Paid')
                    ->modalDescription('This will manually mark the timesheet week as paid.')
                    ->action(function (Timesheet $record) {
                        $record->update(['status' => 'paid']);

                        Notification::make()
                            ->title('Marked as paid')
                            ->body('Week of ' . $record->week_start->format('M j, Y') . ' — $' . number_format((float) $record->amount, 2))
                            ->success()
                            ->send();
                    }),
                Action::make('checkPayPal')
                    ->label('Check PayPal')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->visible(fn (Timesheet $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Auto-check PayPal')
                    ->modalDescription('This will query PayPal to see if the subscription payment was collected for this week.')
                    ->action(function (Timesheet $record) {
                        $offer = $record->offer;

                        if (! $offer) {
                            Notification::make()
                                ->title('No linked offer')
                                ->danger()
                                ->send();
                            return;
                        }

                        $subscription = WeeklySubscription::where('project_offer_id', $offer->id)
                            ->where('status', 'active')
                            ->latest()
                            ->first();

                        if (! $subscription) {
                            // Also try user-level subscription
                            $subscription = WeeklySubscription::where('user_id', $offer->project?->user_id)
                                ->where('status', 'active')
                                ->latest()
                                ->first();
                        }

                        if (! $subscription) {
                            Notification::make()
                                ->title('No active PayPal subscription found')
                                ->warning()
                                ->send();
                            return;
                        }

                        try {
                            $service = app(PayPalSubscriptionService::class);
                            $paid = $service->hasPaymentForWeek($subscription, $record->week_start);

                            if ($paid) {
                                $record->update(['status' => 'paid']);

                                Notification::make()
                                    ->title('Payment confirmed')
                                    ->body('PayPal payment found for week of ' . $record->week_start->format('M j, Y'))
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('No payment found')
                                    ->body('PayPal has not yet collected a payment for this week.')
                                    ->warning()
                                    ->send();
                            }
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('PayPal check failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['total_hours'] = (float) ($data['sun'] ?? 0) + (float) ($data['mon'] ?? 0)
                            + (float) ($data['tue'] ?? 0) + (float) ($data['wed'] ?? 0)
                            + (float) ($data['thu'] ?? 0) + (float) ($data['fri'] ?? 0)
                            + (float) ($data['sat'] ?? 0);

                        $offer = \App\Models\ProjectOffer::find($data['project_offer_id']);
                        $rate = $offer ? (float) $offer->hourly_rate : 0;
                        $data['amount'] = round($data['total_hours'] * $rate, 2);

                        return $data;
                    }),
                DeleteAction::make(),
            ]);
    }
}
