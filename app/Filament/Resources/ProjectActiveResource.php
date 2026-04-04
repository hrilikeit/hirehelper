<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientProjectResource\RelationManagers\TimesheetsRelationManager;
use App\Filament\Resources\ProjectActiveResource\Pages\EditProjectActive;
use App\Filament\Resources\ProjectActiveResource\Pages\ListProjectActives;
use App\Filament\Resources\ProjectActiveResource\RelationManagers\EmailLogsRelationManager;
use App\Filament\Resources\ProjectActiveResource\RelationManagers\InvoicesRelationManager;
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
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectActiveResource extends Resource
{
    protected static ?string $model = ClientProject::class;

    protected static ?string $modelLabel = 'Active project';

    protected static ?string $pluralModelLabel = 'Projects Active';

    protected static ?string $navigationLabel = 'Projects Active';

    protected static ?string $recordTitleAttribute = 'title';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-check-badge';

    protected static string | UnitEnum | null $navigationGroup = 'Sales & Delivery';

    protected static ?int $navigationSort = 12;

    protected static ?string $slug = 'projects-active';

    public static function getEloquentQuery(): Builder
    {
        return AdminAccess::scopeActiveProjects(
            parent::getEloquentQuery()->whereIn('status', ['active', 'accepted', 'completed']),
            auth()->user()
        );
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Client')
                ->schema([
                    Placeholder::make('client_name')
                        ->label('Name')
                        ->content(fn (?ClientProject $record) => $record?->user?->name ?? '—'),
                    Placeholder::make('client_email')
                        ->label('Email')
                        ->content(fn (?ClientProject $record) => $record?->user?->email ?? '—'),
                    Placeholder::make('client_company')
                        ->label('Company')
                        ->content(fn (?ClientProject $record) => $record?->user?->company ?: '—'),
                    Placeholder::make('client_country')
                        ->label('Country')
                        ->content(fn (?ClientProject $record) => $record?->user?->country ?: 'Not detected'),
                    Placeholder::make('client_last_login')
                        ->label('Last login')
                        ->content(fn (?ClientProject $record) => $record?->user?->last_login_at?->diffForHumans() ?: 'Never'),
                    Placeholder::make('client_registered')
                        ->label('Registered')
                        ->content(fn (?ClientProject $record) => $record?->user?->created_at?->format('M j, Y g:i A') ?? '—'),
                ])
                ->columns(3),

            Section::make('Project details')
                ->schema([
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
                    Placeholder::make('freelancer_display')
                        ->label('Freelancer')
                        ->content(function (?ClientProject $record) {
                            $offer = $record?->offers()->where('status', 'active')->first();
                            return $offer ? $offer->freelancer_display_name . ' — $' . number_format((float) $offer->hourly_rate, 2) . '/hr' : '—';
                        }),
                    Placeholder::make('sales_display')
                        ->label('Sales manager')
                        ->content(fn (?ClientProject $record) => $record?->salesManager?->name ?? '—'),
                ])
                ->columns(2),

            Section::make('Status')
                ->schema([
                    Select::make('status')
                        ->options([
                            'active' => 'Active',
                            'accepted' => 'Accepted',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled',
                        ])
                        ->required(),
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
                TextColumn::make('user.country')->label('Country')->toggleable(),
                TextColumn::make('offers_summary')
                    ->label('Freelancer')
                    ->state(function (ClientProject $record) {
                        $offer = $record->offers()->where('status', 'active')->first();
                        return $offer ? $offer->freelancer_display_name : '—';
                    }),
                TextColumn::make('projectManager.name')->label('PM')->toggleable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('user.last_login_at')
                    ->label('Last login')
                    ->since()
                    ->toggleable(),
                TextColumn::make('updated_at')->dateTime('M j, Y g:i A')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'accepted' => 'Accepted',
                        'completed' => 'Completed',
                    ]),
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
        return [
            TimesheetsRelationManager::class,
            InvoicesRelationManager::class,
            EmailLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjectActives::route('/'),
            'edit' => EditProjectActive::route('/{record}/edit'),
        ];
    }
}
