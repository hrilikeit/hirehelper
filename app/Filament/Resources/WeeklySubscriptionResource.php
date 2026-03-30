<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WeeklySubscriptionResource\Pages\ListWeeklySubscriptions;
use App\Models\WeeklySubscription;
use App\Services\PayPalSubscriptionService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Throwable;
use UnitEnum;

class WeeklySubscriptionResource extends Resource
{
    protected static ?string $model = WeeklySubscription::class;

    protected static ?string $modelLabel = 'Weekly Subscription';

    protected static ?string $pluralModelLabel = 'Weekly Subscriptions';

    protected static ?string $recordTitleAttribute = 'paypal_subscription_id';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-arrow-path';

    protected static string | UnitEnum | null $navigationGroup = 'Payments';

    protected static ?int $navigationSort = 20;

    protected static bool $shouldSkipAuthorization = true;

    protected static ?string $slug = 'weekly-subscriptions';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('offer.freelancer.name')
                    ->label('Freelancer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('weekly_amount')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('paypal_subscription_status')
                    ->label('PayPal Status')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('paypal_payer_email')
                    ->label('Payer email')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('next_billing_at')
                    ->label('Next billing')
                    ->dateTime('M j, Y')
                    ->sortable(),
                TextColumn::make('started_at')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('syncPayPal')
                    ->label('Sync')
                    ->action(function (WeeklySubscription $record) {
                        try {
                            app(PayPalSubscriptionService::class)->sync($record);
                            Notification::make()->title('Subscription synced.')->success()->send();
                        } catch (Throwable $e) {
                            report($e);
                            Notification::make()->title('Could not sync subscription.')->danger()->send();
                        }
                    }),
                Action::make('cancelSubscription')
                    ->label('Cancel')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (WeeklySubscription $record) {
                        try {
                            app(PayPalSubscriptionService::class)->cancel($record, 'Cancelled by admin');
                            Notification::make()->title('Subscription cancelled.')->success()->send();
                        } catch (Throwable $e) {
                            report($e);
                            Notification::make()->title('Could not cancel subscription.')->danger()->send();
                        }
                    })
                    ->visible(fn (WeeklySubscription $record) => in_array($record->status, ['active', 'pending_approval'])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWeeklySubscriptions::route('/'),
        ];
    }
}
