<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentLinkResource\Pages\CreatePaymentLink;
use App\Filament\Resources\PaymentLinkResource\Pages\EditPaymentLink;
use App\Filament\Resources\PaymentLinkResource\Pages\ListPaymentLinks;
use App\Models\Freelancer;
use App\Models\PaymentLink;
use App\Services\PayPalCheckoutService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Throwable;

class PaymentLinkResource extends Resource
{
    protected static ?string $model = PaymentLink::class;

    protected static ?string $modelLabel = 'Payment Link';

    protected static ?string $pluralModelLabel = 'Payment Links';

    protected static ?string $recordTitleAttribute = 'slug';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-link';

    protected static \UnitEnum|string|null $navigationGroup = 'Payments';

    protected static ?int $navigationSort = 10;

    protected static bool $shouldSkipAuthorization = true;

    protected static ?string $slug = 'payment-links';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payment link')
                    ->schema([
                        Select::make('freelancer_id')
                            ->label('Freelancer')
                            ->options(fn () => Freelancer::query()->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('freelancer.name')
                    ->label('Freelancer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('description')
                    ->limit(40)
                    ->tooltip(fn (?string $state): ?string => $state),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('public_url')
                    ->label('Payment link')
                    ->copyable()
                    ->copyMessage('Payment link copied')
                    ->limit(38),
                TextColumn::make('paypal_order_status')
                    ->label('PayPal order')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('paid_at')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
                TextColumn::make('paypal_payer_email')
                    ->label('Payer')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('openLink')
                    ->label('Open link')
                    ->url(fn (PaymentLink $record): string => $record->public_url, shouldOpenInNewTab: true),
                Action::make('syncPayPal')
                    ->label('Sync PayPal')
                    ->action(fn (PaymentLink $record) => static::syncPayPalStatus($record)),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentLinks::route('/'),
            'create' => CreatePaymentLink::route('/create'),
            'edit' => EditPaymentLink::route('/{record}/edit'),
        ];
    }

    protected static function syncPayPalStatus(PaymentLink $record): void
    {
        if (! filled(config('paypal.client_id')) || ! filled(config('paypal.secret'))) {
            Notification::make()
                ->title('PayPal is not configured yet.')
                ->warning()
                ->send();

            return;
        }

        if (! filled($record->paypal_order_id)) {
            Notification::make()
                ->title('No PayPal order exists for this link yet.')
                ->warning()
                ->send();

            return;
        }

        try {
            app(PayPalCheckoutService::class)->syncPaymentLink($record);

            Notification::make()
                ->title('PayPal status refreshed.')
                ->success()
                ->send();
        } catch (Throwable $throwable) {
            report($throwable);

            Notification::make()
                ->title('Could not refresh PayPal status.')
                ->danger()
                ->send();
        }
    }
}
