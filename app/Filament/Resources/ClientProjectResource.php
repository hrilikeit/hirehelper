<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientProjectResource\Pages\CreateClientProject;
use App\Filament\Resources\ClientProjectResource\Pages\EditClientProject;
use App\Filament\Resources\ClientProjectResource\Pages\ListClientProjects;
use App\Filament\Resources\ClientProjectResource\RelationManagers\TimesheetsRelationManager;
use App\Models\ClientBillingMethod;
use App\Models\ClientProject;
use App\Models\User;
use App\Support\AdminAccess;
use BackedEnum;
use UnitEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
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

    protected static ?string $modelLabel = 'Pending project';

    protected static ?string $pluralModelLabel = 'Projects Pending';

    protected static ?string $navigationLabel = 'Projects Pending';

    protected static ?string $recordTitleAttribute = 'title';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-briefcase';

    protected static string | UnitEnum | null $navigationGroup = 'Sales & Delivery';

    protected static ?int $navigationSort = 11;

    protected static ?string $slug = 'client-projects';

    public static function getEloquentQuery(): Builder
    {
        return AdminAccess::scopeProjects(
            parent::getEloquentQuery()->whereIn('status', ['draft', 'pending']),
            auth()->user()
        );
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Project details')
                ->schema([
                    Placeholder::make('client_name')
                        ->label('Client')
                        ->content(fn (?ClientProject $record) => $record?->user?->name ?? '—'),
                    Placeholder::make('title_display')
                        ->label('Title')
                        ->content(fn (?ClientProject $record) => $record?->title ?? '—')
                        ->columnSpanFull(),
                    Placeholder::make('description_display')
                        ->label('Description')
                        ->content(fn (?ClientProject $record) => $record?->description ?? '—')
                        ->columnSpanFull(),
                    Placeholder::make('experience_display')
                        ->label('Experience level')
                        ->content(fn (?ClientProject $record) => $record?->experience_level ?? '—'),
                    Placeholder::make('timeframe_display')
                        ->label('Timeframe')
                        ->content(fn (?ClientProject $record) => $record?->timeframe ?? '—'),
                    Placeholder::make('specialty_display')
                        ->label('Specialty')
                        ->content(fn (?ClientProject $record) => $record?->specialty ?? '—'),
                    Placeholder::make('external_ref_display')
                        ->label('External reference')
                        ->content(fn (?ClientProject $record) => $record?->external_reference ?: '—'),
                    Placeholder::make('payment_method_display')
                        ->label('Payment method')
                        ->content(function (?ClientProject $record) {
                            if (! $record?->user_id) {
                                return '—';
                            }
                            $method = ClientBillingMethod::where('user_id', $record->user_id)
                                ->where('is_default', true)
                                ->first();
                            return $method ? $method->display_label . ($method->verified_at ? ' (verified)' : ' (not verified)') : 'Not added';
                        }),
                    Placeholder::make('sales_display')
                        ->label('Sales manager')
                        ->content(fn (?ClientProject $record) => $record?->salesManager?->name ?? '—'),
                    Placeholder::make('pm_display')
                        ->label('Project manager')
                        ->content(fn (?ClientProject $record) => $record?->projectManager?->name ?? '—'),
                ])
                ->columns(2),

            Section::make('Status & Acceptance')
                ->schema([
                    Select::make('status')
                        ->options([
                            'draft' => 'Draft',
                            'pending' => 'Pending',
                            'active' => 'Active (move to Projects Active)',
                            'cancelled' => 'Cancelled',
                        ])
                        ->required(),
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
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('title')->searchable()->sortable()->limit(42),
                TextColumn::make('user.name')->label('Client')->searchable()->sortable(),
                TextColumn::make('user.country')->label('Country')->toggleable(),
                TextColumn::make('specialty')->badge()->toggleable(),
                TextColumn::make('offers_summary')
                    ->label('Freelancer')
                    ->state(function (ClientProject $record) {
                        $offer = $record->offers()->whereIn('status', ['active', 'pending'])->first();
                        return $offer ? $offer->freelancer_display_name : '—';
                    }),
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
            'index' => ListClientProjects::route('/'),
            'create' => CreateClientProject::route('/create'),
            'edit' => EditClientProject::route('/{record}/edit'),
        ];
    }
}
