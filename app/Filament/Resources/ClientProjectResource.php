<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientProjectResource\Pages\EditClientProject;
use App\Filament\Resources\ClientProjectResource\Pages\ListClientProjects;
use App\Models\ClientProject;
use App\Models\User;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientProjectResource extends Resource
{
    protected static ?string $model = ClientProject::class;

    protected static ?string $modelLabel = 'Project';

    protected static ?string $pluralModelLabel = 'Projects';

    protected static ?string $recordTitleAttribute = 'title';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?int $navigationSort = 13;

    protected static bool $shouldSkipAuthorization = true;

    protected static ?string $slug = 'client-projects';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Project')
                    ->schema([
                        Select::make('user_id')
                            ->label('Client')
                            ->options(fn () => User::query()->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        TextInput::make('title')->required()->columnSpanFull(),
                        Textarea::make('description')->rows(8)->required()->columnSpanFull(),
                        Select::make('experience_level')
                            ->options([
                                'Entry' => 'Entry',
                                'Intermediate' => 'Intermediate',
                                'Expert' => 'Expert',
                            ])
                            ->required(),
                        TextInput::make('timeframe')->required(),
                        TextInput::make('specialty')->required(),
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'pending' => 'Pending',
                                'active' => 'Active',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable()->limit(45),
                TextColumn::make('user.name')->label('Client')->searchable()->sortable(),
                TextColumn::make('specialty')->badge(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('updated_at')->dateTime('M j, Y g:i A')->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClientProjects::route('/'),
            'edit' => EditClientProject::route('/{record}/edit'),
        ];
    }
}
