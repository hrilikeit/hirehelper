<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientProjectResource\Pages\CreateClientProject;
use App\Filament\Resources\ClientProjectResource\Pages\EditClientProject;
use App\Filament\Resources\ClientProjectResource\Pages\ListClientProjects;
use App\Models\ClientProject;
use App\Models\User;
use App\Support\AdminAccess;
use BackedEnum;
use UnitEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientProjectResource extends Resource
{
    protected static ?string $model = ClientProject::class;

    protected static ?string $modelLabel = 'Hiring project';

    protected static ?string $pluralModelLabel = 'Hiring projects';

    protected static ?string $recordTitleAttribute = 'title';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-briefcase';

    protected static string | UnitEnum | null $navigationGroup = 'Sales & Delivery';

    protected static ?int $navigationSort = 11;

    protected static ?string $slug = 'client-projects';

    public static function getEloquentQuery(): Builder
    {
        return AdminAccess::scopeProjects(parent::getEloquentQuery(), auth()->user());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Project details')
                ->schema([
                    Select::make('user_id')
                        ->label('Client')
                        ->options(fn () => User::query()->where('role', 'client')->orderBy('name')->pluck('name', 'id'))
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
                    TextInput::make('external_reference')->label('External reference'),
                    Select::make('status')
                        ->options([
                            'draft' => 'Draft',
                            'pending' => 'Pending',
                            'active' => 'Active / accepted',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled',
                        ])
                        ->required(),
                    Select::make('sales_manager_id')
                        ->label('Sales manager')
                        ->options(fn () => User::query()
                            ->whereIn('role', ['superadmin', 'admin', 'sales_manager'])
                            ->orderBy('name')
                            ->pluck('name', 'id'))
                        ->searchable(),
                    Select::make('project_manager_id')
                        ->label('Project manager')
                        ->options(fn () => User::query()
                            ->whereIn('role', ['superadmin', 'admin', 'project_manager'])
                            ->orderBy('name')
                            ->pluck('name', 'id'))
                        ->searchable(),
                    Textarea::make('acceptance_notes')
                        ->rows(5)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable()->limit(42),
                TextColumn::make('user.name')->label('Client')->searchable()->sortable(),
                TextColumn::make('specialty')->badge(),
                TextColumn::make('salesManager.name')->label('Sales')->toggleable(),
                TextColumn::make('projectManager.name')->label('PM')->toggleable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('updated_at')->dateTime('M j, Y g:i A')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('sales_manager_id')
                    ->label('Sales manager')
                    ->options(fn () => User::query()->whereIn('role', ['superadmin', 'admin', 'sales_manager'])->orderBy('name')->pluck('name', 'id')),
                SelectFilter::make('project_manager_id')
                    ->label('Project manager')
                    ->options(fn () => User::query()->whereIn('role', ['superadmin', 'admin', 'project_manager'])->orderBy('name')->pluck('name', 'id')),
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
            'create' => CreateClientProject::route('/create'),
            'edit' => EditClientProject::route('/{record}/edit'),
        ];
    }
}
