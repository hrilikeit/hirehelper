<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MilestoneProjectResource\Pages\EditMilestoneProject;
use App\Filament\Resources\MilestoneProjectResource\Pages\ListMilestoneProjects;
use App\Models\Freelancer;
use App\Models\Milestone;
use App\Models\MilestoneProject;
use App\Support\AdminAccess;
use BackedEnum;
use UnitEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class MilestoneProjectResource extends Resource
{
    protected static ?string $model = MilestoneProject::class;

    protected static ?string $modelLabel = 'Milestone';

    protected static ?string $pluralModelLabel = 'Milestones';

    protected static ?string $navigationLabel = 'Milestones';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-flag';

    protected static string | UnitEnum | null $navigationGroup = 'Sales & Delivery';

    protected static ?int $navigationSort = 15;

    protected static ?string $slug = 'milestones';

    protected static bool $shouldSkipAuthorization = true;

    public static function canAccess(): bool
    {
        return AdminAccess::canAccessNonSalesResource(auth()->user());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Project details')
                ->schema([
                    TextInput::make('title')
                        ->label('Project title')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('description')
                        ->label('Project description')
                        ->rows(3),
                    Select::make('freelancer_id')
                        ->label('Freelancer')
                        ->options(Freelancer::orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                    Select::make('status')
                        ->options([
                            'draft' => 'Draft',
                            'active' => 'Active',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled',
                        ])
                        ->default('draft'),
                    Placeholder::make('client_link')
                        ->label('Client link')
                        ->content(function (?MilestoneProject $record) {
                            if (! $record) return 'Save the project first to generate a link.';
                            $url = $record->public_url;
                            return new HtmlString('<a href="' . e($url) . '" target="_blank" style="color:#4b4ff5;word-break:break-all">' . e($url) . '</a>');
                        })
                        ->columnSpanFull(),
                    Placeholder::make('client_assigned')
                        ->label('Client')
                        ->content(function (?MilestoneProject $record) {
                            if (! $record?->user) return 'No client assigned yet (will be assigned when client opens the link and registers).';
                            return $record->user->name . ' (' . $record->user->email . ')';
                        })
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Milestones')
                ->schema([
                    Repeater::make('milestones')
                        ->relationship()
                        ->schema([
                            TextInput::make('name')
                                ->label('Milestone name')
                                ->required()
                                ->columnSpan(2),
                            Textarea::make('description')
                                ->label('Description')
                                ->rows(2)
                                ->columnSpan(2),
                            TextInput::make('amount')
                                ->label('Amount ($)')
                                ->numeric()
                                ->required()
                                ->prefix('$')
                                ->step(0.01),
                            Select::make('status')
                                ->options([
                                    'pending' => 'Pending',
                                    'funded' => 'Funded',
                                    'released' => 'Released',
                                ])
                                ->default('pending'),
                        ])
                        ->columns(2)
                        ->orderColumn('sort_order')
                        ->defaultItems(1)
                        ->addActionLabel('Add milestone')
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => ($state['name'] ?? 'New milestone') . ' — $' . number_format((float) ($state['amount'] ?? 0), 2))
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('title')->label('Project')->searchable()->sortable()->limit(40),
                TextColumn::make('freelancer.name')->label('Freelancer')->sortable(),
                TextColumn::make('user.name')->label('Client')->sortable()
                    ->default('—'),
                TextColumn::make('milestones_count')
                    ->label('Milestones')
                    ->counts('milestones'),
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('usd')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'active' => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('created_at')->label('Created')->dateTime('M j, Y')->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->recordUrl(null)
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMilestoneProjects::route('/'),
            'create' => MilestoneProjectResource\Pages\CreateMilestoneProject::route('/create'),
            'edit' => EditMilestoneProject::route('/{record}/edit'),
        ];
    }
}
