<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages\CreateService;
use App\Filament\Resources\ServiceResource\Pages\EditService;
use App\Filament\Resources\ServiceResource\Pages\ListServices;
use App\Models\Freelancer;
use App\Models\Service;
use App\Support\AdminAccess;
use BackedEnum;
use UnitEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $modelLabel = 'Service';

    protected static ?string $pluralModelLabel = 'Services';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-briefcase';

    protected static string | UnitEnum | null $navigationGroup = 'Talent';

    protected static ?int $navigationSort = 22;

    protected static ?string $slug = 'services';

    protected static bool $shouldSkipAuthorization = true;

    public static function canAccess(): bool
    {
        return AdminAccess::canAccessNonSalesResource(auth()->user());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Service details')
                ->schema([
                    Select::make('freelancer_id')
                        ->label('Freelancer')
                        ->options(fn () => Freelancer::query()
                            ->where('status', 'active')
                            ->orderBy('name')
                            ->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                    TextInput::make('name')
                        ->label('Service name')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('description')
                        ->rows(4)
                        ->columnSpanFull(),
                    TextInput::make('monthly_price')
                        ->label('Monthly price ($)')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->step(0.01),
                    TextInput::make('active_users')
                        ->label('Number of users already using')
                        ->numeric()
                        ->default(0)
                        ->minValue(0),
                    TextInput::make('star_rating')
                        ->label('Star rating')
                        ->numeric()
                        ->default(5.00)
                        ->minValue(0)
                        ->maxValue(5)
                        ->step(0.01),
                    Select::make('status')
                        ->options([
                            'active' => 'Active',
                            'inactive' => 'Inactive',
                        ])
                        ->default('active')
                        ->required(),
                ])
                ->columns(2),

            Section::make('Service link')
                ->schema([
                    Placeholder::make('service_url')
                        ->label('Public URL')
                        ->content(fn (?Service $record) => $record?->slug
                            ? url('/services/' . $record->slug)
                            : 'Save the service to generate the link'),
                ])
                ->hiddenOn('create'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('freelancer.name')->label('Freelancer')->searchable()->sortable(),
                TextColumn::make('monthly_price')
                    ->label('Price')
                    ->formatStateUsing(fn ($state) => '$' . number_format((float) $state, 2) . '/mo')
                    ->sortable(),
                TextColumn::make('active_users')->label('Users')->sortable(),
                TextColumn::make('star_rating')->label('Rating')->sortable(),
                TextColumn::make('subscriptions_count')
                    ->label('Subscribers')
                    ->counts('subscriptions')
                    ->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('slug')
                    ->label('Link')
                    ->formatStateUsing(fn ($state) => url('/services/' . $state))
                    ->copyable()
                    ->limit(40),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->visible(fn () => AdminAccess::isSuperAdmin(auth()->user())),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'edit' => EditService::route('/{record}/edit'),
        ];
    }
}
