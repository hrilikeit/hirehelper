<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcbaSettingResource\Pages\CreateAcbaSetting;
use App\Filament\Resources\AcbaSettingResource\Pages\EditAcbaSetting;
use App\Filament\Resources\AcbaSettingResource\Pages\ListAcbaSettings;
use App\Models\AcbaSetting;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AcbaSettingResource extends Resource
{
    protected static ?string $model = AcbaSetting::class;

    protected static ?string $modelLabel = 'ACBA card setting';

    protected static ?string $pluralModelLabel = 'ACBA card settings';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';

    protected static string | UnitEnum | null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 11;

    protected static ?string $slug = 'acba-settings';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('ACBA / ArCa card gateway')
                ->description('Store separate test and live gateway credentials here. The values are encrypted in the database and the client billing page will use the active environment.')
                ->schema([
                    TextInput::make('name')
                        ->default('Primary ACBA gateway')
                        ->required()
                        ->maxLength(255),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->inline(false),

                    Toggle::make('is_live')
                        ->label('Use live ACBA / ArCa gateway')
                        ->inline(false)
                        ->helperText('Leave off for test mode. Turn on only when the live bank credentials are ready.'),

                    TextInput::make('test_base_url')
                        ->label('Test REST base URL')
                        ->required()
                        ->default('https://ipaytest.arca.am:8445/payment/rest')
                        ->columnSpanFull(),

                    TextInput::make('test_username')
                        ->label('Test API Username')
                        ->maxLength(255),

                    TextInput::make('test_password')
                        ->label('Test API Password')
                        ->password()
                        ->revealable()
                        ->maxLength(255),

                    TextInput::make('live_base_url')
                        ->label('Live REST base URL')
                        ->required()
                        ->default('https://ipay.arca.am/payment/rest')
                        ->columnSpanFull(),

                    TextInput::make('live_username')
                        ->label('Live API Username')
                        ->maxLength(255),

                    TextInput::make('live_password')
                        ->label('Live API Password')
                        ->password()
                        ->revealable()
                        ->maxLength(255),

                    TextInput::make('verification_amount')
                        ->label('Verification amount')
                        ->numeric()
                        ->required()
                        ->default('0.01')
                        ->helperText('Temporary verification amount sent when the client adds a card billing method.'),

                    Select::make('verification_currency')
                        ->label('Verification currency')
                        ->required()
                        ->default('USD')
                        ->options([
                            'USD' => 'USD',
                            'AMD' => 'AMD',
                            'EUR' => 'EUR',
                            'RUB' => 'RUB',
                        ]),
                ])
                ->columns(2),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('ACBA / ArCa configuration')
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('environment_label')->label('Environment'),
                    TextEntry::make('is_active')->label('Active')->badge(),
                    TextEntry::make('test_username')->label('Test username')->placeholder('—'),
                    TextEntry::make('live_username')->label('Live username')->placeholder('—'),
                    TextEntry::make('verification_amount')->label('Verification amount'),
                    TextEntry::make('verification_currency')->label('Currency'),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('environment_label')->label('Environment'),
                IconColumn::make('is_active')->label('Active')->boolean(),
                TextColumn::make('verification_amount')
                    ->label('Verification')
                    ->formatStateUsing(fn ($state, $record) => number_format((float) $state, 2) . ' ' . $record->verification_currency),
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
            'index' => ListAcbaSettings::route('/'),
            'create' => CreateAcbaSetting::route('/create'),
            'edit' => EditAcbaSetting::route('/{record}/edit'),
        ];
    }
}
