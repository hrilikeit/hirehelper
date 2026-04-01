<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectOfferResource\Pages\CreateProjectOffer;
use App\Filament\Resources\ProjectOfferResource\Pages\EditProjectOffer;
use App\Filament\Resources\ProjectOfferResource\Pages\ListProjectOffers;
use App\Models\ClientProject;
use App\Models\Freelancer;
use App\Models\ProjectOffer;
use App\Models\User;
use App\Support\AdminAccess;
use BackedEnum;
use UnitEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectOfferResource extends Resource
{
    protected static ?string $model = ProjectOffer::class;

    protected static ?string $modelLabel = 'Offer';

    protected static ?string $pluralModelLabel = 'Offers';

    protected static ?string $recordTitleAttribute = 'role';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string | UnitEnum | null $navigationGroup = 'Sales & Delivery';

    protected static ?int $navigationSort = 12;

    protected static ?string $slug = 'project-offers';

    public static function canAccess(): bool
    {
        return AdminAccess::canAccessNonSalesResource(auth()->user());
    }

    public static function getEloquentQuery(): Builder
    {
        return AdminAccess::scopeOffers(parent::getEloquentQuery(), auth()->user());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
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
                            'active' => 'Active / accepted',
                            'closed' => 'Closed',
                        ])
                        ->required(),
                    Select::make('payment_status')
                        ->options([
                            'non_active' => 'Non active',
                            'billing_added' => 'Billing added',
                            'active' => 'Active',
                            'settled' => 'Settled',
                        ])
                        ->default('non_active')
                        ->required(),
                    TextInput::make('billing_method'),
                    TextInput::make('external_reference'),
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
                    Textarea::make('notes')
                        ->rows(6)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.title')->label('Project')->searchable()->limit(32),
                TextColumn::make('freelancer.name')->label('Freelancer')->searchable()->sortable(),
                TextColumn::make('role')->searchable()->toggleable(),
                TextColumn::make('hourly_rate')->money('USD')->sortable(),
                TextColumn::make('weekly_limit')->label('Hrs / week'),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('payment_status')->badge()->label('Payment')->sortable(),
                TextColumn::make('salesManager.name')->label('Sales')->toggleable(),
                TextColumn::make('projectManager.name')->label('PM')->toggleable(),
                TextColumn::make('sent_at')->dateTime('M j, Y g:i A')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'closed' => 'Closed',
                    ]),
                SelectFilter::make('payment_status')
                    ->options([
                        'non_active' => 'Non active',
                        'billing_added' => 'Billing added',
                        'active' => 'Active',
                        'settled' => 'Settled',
                    ]),
                SelectFilter::make('sales_manager_id')
                    ->label('Sales manager')
                    ->options(fn () => User::query()->whereIn('role', ['superadmin', 'admin', 'sales_manager'])->orderBy('name')->pluck('name', 'id')),
                SelectFilter::make('project_manager_id')
                    ->label('Project manager')
                    ->options(fn () => User::query()->whereIn('role', ['superadmin', 'admin', 'project_manager'])->orderBy('name')->pluck('name', 'id')),
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
            'create' => CreateProjectOffer::route('/create'),
            'edit' => EditProjectOffer::route('/{record}/edit'),
        ];
    }
}
