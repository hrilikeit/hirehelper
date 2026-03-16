<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaypalSettingResource\Pages\CreatePaypalSetting;
use App\Filament\Resources\PaypalSettingResource\Pages\EditPaypalSetting;
use App\Filament\Resources\PaypalSettingResource\Pages\ListPaypalSettings;
use App\Models\PaypalSetting;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PaypalSettingResource extends Resource
{
    protected static ?string $model = PaypalSetting::class;

    protected static ?string $modelLabel = 'PayPal setting';

    protected static ?string $pluralModelLabel = 'PayPal settings';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';

    protected static string | UnitEnum | null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 10;

    protected static ?string $slug = 'paypal-settings';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('PayPal API credentials')
                ->description('Use the same credentials style you use in the iThire project. The values are stored encrypted in the database.')
                ->schema([
                    TextInput::make('name')
                        ->default('Primary PayPal account')
                        ->required()
                        ->maxLength(255),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->inline(false),

                    Toggle::make('is_live')
                        ->label('Use live PayPal API')
                        ->inline(false)
                        ->helperText('Leave off for sandbox/testing. Turn on only when you are ready to use live PayPal credentials.'),

                    TextInput::make('api_username')
                        ->label('PayPal API Username')
                        ->maxLength(255),

                    TextInput::make('api_password')
                        ->label('PayPal API Password')
                        ->password()
                        ->revealable()
                        ->maxLength(255),

                    TextInput::make('client_id')
                        ->label('PayPal Client ID')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('client_secret')
                        ->label('PayPal Client Secret')
                        ->password()
                        ->revealable()
                        ->required()
                        ->maxLength(255),

                    TextInput::make('webhook_id')
                        ->label('PayPal Webhook ID')
                        ->helperText('Optional for now. Add it later if you also wire PayPal webhook verification.')
                        ->maxLength(255),
                ])
                ->columns(2),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('PayPal configuration')
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('environmentLabel')->label('Environment'),
                    TextEntry::make('is_active')->label('Active')->badge(),
                    TextEntry::make('api_username')->label('API Username')->placeholder('—'),
                    TextEntry::make('client_id')->label('Client ID')->placeholder('—'),
                    TextEntry::make('webhook_id')->label('Webhook ID')->placeholder('—'),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('environmentLabel')->label('Environment')->sortable(),
                IconColumn::make('is_active')->label('Active')->boolean(),
                TextColumn::make('updated_at')->label('Updated')->since()->sortable(),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function canCreate(): bool
    {
        return static::getModel()::query()->count() === 0;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaypalSettings::route('/'),
            'create' => CreatePaypalSetting::route('/create'),
            'edit' => EditPaypalSetting::route('/{record}/edit'),
        ];
    }
}
