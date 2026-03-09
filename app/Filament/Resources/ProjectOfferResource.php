<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectOfferResource\Pages\EditProjectOffer;
use App\Filament\Resources\ProjectOfferResource\Pages\ListProjectOffers;
use App\Models\ClientProject;
use App\Models\Freelancer;
use App\Models\ProjectOffer;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectOfferResource extends Resource
{
    protected static ?string $model = ProjectOffer::class;

    protected static ?string $modelLabel = 'Offer';

    protected static ?string $pluralModelLabel = 'Offers';

    protected static ?string $recordTitleAttribute = 'role';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?int $navigationSort = 14;

    protected static bool $shouldSkipAuthorization = true;

    protected static ?string $slug = 'project-offers';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Offer')
                    ->schema([
                        Select::make('client_project_id')
                            ->label('Project')
                            ->options(fn () => ClientProject::query()->orderBy('title')->pluck('title', 'id'))
                            ->searchable()
                            ->required(),
                        Select::make('freelancer_id')
                            ->label('Freelancer')
                            ->options(fn () => Freelancer::query()->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        TextInput::make('role')->required(),
                        TextInput::make('hourly_rate')->numeric()->required()->prefix('$'),
                        TextInput::make('weekly_limit')->numeric()->required(),
                        Toggle::make('manual_time'),
                        Toggle::make('multi_offer'),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'active' => 'Active',
                                'closed' => 'Closed',
                            ])
                            ->required(),
                        TextInput::make('billing_method'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.title')->label('Project')->searchable()->limit(35),
                TextColumn::make('freelancer.name')->label('Freelancer')->searchable()->sortable(),
                TextColumn::make('role')->searchable()->toggleable(),
                TextColumn::make('hourly_rate')->money('USD')->sortable(),
                TextColumn::make('weekly_limit')->label('Hrs / week'),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('billing_method')->toggleable(),
                TextColumn::make('sent_at')->dateTime('M j, Y g:i A')->sortable(),
            ])
            ->defaultSort('sent_at', 'desc')
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjectOffers::route('/'),
            'edit' => EditProjectOffer::route('/{record}/edit'),
        ];
    }
}
