<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectArchiveResource\Pages\EditProjectArchive;
use App\Filament\Resources\ProjectArchiveResource\Pages\ListProjectArchives;
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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectArchiveResource extends Resource
{
    protected static ?string $model = ClientProject::class;

    protected static ?string $modelLabel = 'Archived project';

    protected static ?string $pluralModelLabel = 'Projects Archive';

    protected static ?string $navigationLabel = 'Projects Archive';

    protected static ?string $recordTitleAttribute = 'title';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-archive-box';

    protected static string | UnitEnum | null $navigationGroup = 'Sales & Delivery';

    protected static ?int $navigationSort = 14;

    protected static ?string $slug = 'projects-archive';

    protected static bool $shouldSkipAuthorization = true;

    public static function canAccess(): bool
    {
        return AdminAccess::canAccessNonSalesResource(auth()->user());
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereIn('status', ['archived', 'completed', 'cancelled']);
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
                    Placeholder::make('freelancer_display')
                        ->label('Freelancer')
                        ->content(function (?ClientProject $record) {
                            $offer = $record?->offers()->latest()->first();
                            return $offer ? $offer->freelancer_display_name . ' — $' . number_format((float) $offer->hourly_rate, 2) . '/hr' : '—';
                        }),
                    Placeholder::make('total_paid_display')
                        ->label('Total paid')
                        ->content(function (?ClientProject $record) {
                            if (! $record) return '$0.00';
                            $total = \App\Models\Invoice::where('client_project_id', $record->id)
                                ->where('status', 'paid')
                                ->sum('amount');
                            return '$' . number_format((float) $total, 2);
                        }),
                ])
                ->columns(2),

            Section::make('Status')
                ->schema([
                    Select::make('status')
                        ->options([
                            'active' => 'Active (restore to Projects Active)',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled',
                            'archived' => 'Archived',
                        ])
                        ->required(),
                    Textarea::make('acceptance_notes')
                        ->rows(5)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('user.name')->label('Client')->searchable()->sortable(),
                TextColumn::make('title')->label('Project')->searchable()->sortable()->limit(42),
                TextColumn::make('offers_summary')
                    ->label('Freelancer')
                    ->state(function (ClientProject $record) {
                        $offer = $record->offers()->latest()->first();
                        return $offer ? $offer->freelancer_display_name : '—';
                    }),
                TextColumn::make('total_paid')
                    ->label('Total paid')
                    ->state(function (ClientProject $record) {
                        $total = \App\Models\Invoice::where('client_project_id', $record->id)
                            ->where('status', 'paid')
                            ->sum('amount');
                        return '$' . number_format((float) $total, 2);
                    }),
                TextColumn::make('status')->badge()->sortable()
                    ->color(fn (string $state) => match ($state) {
                        'archived' => 'gray',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('updated_at')->dateTime('M j, Y g:i A')->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->visible(fn () => AdminAccess::isSuperAdmin(auth()->user())),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjectArchives::route('/'),
            'edit' => EditProjectArchive::route('/{record}/edit'),
        ];
    }
}
