<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FreelancerResource\Pages\CreateFreelancer;
use App\Filament\Resources\FreelancerResource\Pages\EditFreelancer;
use App\Filament\Resources\FreelancerResource\Pages\ListFreelancers;
use App\Filament\Resources\FreelancerResource\Pages\ViewFreelancer;
use App\Models\Freelancer;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class FreelancerResource extends Resource
{
    protected static ?string $model = Freelancer::class;

    protected static ?string $modelLabel = 'Freelancer persona';

    protected static ?string $pluralModelLabel = 'Freelancer personas';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-sparkles';

    protected static string | UnitEnum | null $navigationGroup = 'Talent';

    protected static ?int $navigationSort = 20;

    protected static ?string $slug = 'freelancers';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Required freelancer details')
                ->description('This is the simplified sales form. Only the fields below are required.')
                ->schema([
                    Hidden::make('slug'),
                    Hidden::make('status')->default('active'),
                    Hidden::make('is_featured')->default(false),
                    Hidden::make('added_by_user_id')->default(fn (): ?int => auth()->id()),

                    TextInput::make('name')
                        ->label('Freelancer full name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('contact_email')
                        ->label('Freelancer email')
                        ->email()
                        ->maxLength(255),

                    FileUpload::make('avatar')
                        ->label('Freelancer picture')
                        ->image()
                        ->disk('public')
                        ->directory('freelancers')
                        ->visibility('public')
                        ->helperText('Optional. Upload a freelancer photo. If left empty, the default avatar is used.')
                        ->afterStateHydrated(function (FileUpload $component, $state): void {
                            if (filled($state) && ! str_contains((string) $state, '/')) {
                                $component->state(null);
                            }
                        }),

                    TextInput::make('title')
                        ->label('Title')
                        ->required()
                        ->maxLength(255),

                    Select::make('average_rating')
                        ->label('Stars')
                        ->options([
                            1 => '1',
                            2 => '2',
                            3 => '3',
                            4 => '4',
                            5 => '5',
                        ])
                        ->required(),

                    TextInput::make('hourly_rate')
                        ->label('Rate')
                        ->numeric()
                        ->minValue(0)
                        ->required()
                        ->prefix('$'),

                    TextInput::make('total_earned')
                        ->label('Total earned')
                        ->numeric()
                        ->minValue(0)
                        ->required()
                        ->prefix('$'),

                    TextInput::make('years_experience')
                        ->label('Experience')
                        ->numeric()
                        ->minValue(0)
                        ->required(),

                    Textarea::make('bio')
                        ->label('Description')
                        ->required()
                        ->rows(6)
                        ->columnSpanFull(),
                ])
                ->columns(3),

            Section::make('Reviews')
                ->description('Sales person can add up to 15 reviews.')
                ->schema([
                    Repeater::make('reviews')
                        ->relationship()
                        ->addActionLabel('Add new review')
                        ->maxItems(15)
                        ->itemLabel(fn (array $state): string => filled($state['review_title'] ?? null) ? (string) $state['review_title'] : 'Review')
                        ->schema([
                            TextInput::make('review_title')
                                ->label('Review title')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(2),

                            Select::make('stars')
                                ->label('Stars')
                                ->options([
                                    1 => '1',
                                    2 => '2',
                                    3 => '3',
                                    4 => '4',
                                    5 => '5',
                                ])
                                ->required(),

                            DatePicker::make('date_from')
                                ->label('From')
                                ->required(),

                            DatePicker::make('date_to')
                                ->label('To')
                                ->required(),

                            TextInput::make('hours')
                                ->label('Hours')
                                ->numeric()
                                ->minValue(0)
                                ->required(),

                            TextInput::make('rate')
                                ->label('Rate')
                                ->numeric()
                                ->minValue(0)
                                ->required()
                                ->prefix('$'),

                            Textarea::make('review_text')
                                ->label('Review text')
                                ->required()
                                ->rows(4)
                                ->columnSpanFull(),
                        ])
                        ->columns(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Freelancer profile')
                ->schema([
                    TextEntry::make('name')->label('Freelancer full name'),
                    TextEntry::make('contact_email')->label('Freelancer email')->placeholder('—'),
                    TextEntry::make('title'),
                    TextEntry::make('hourly_rate')->label('Rate')->money('USD'),
                    TextEntry::make('total_earned')->label('Total earned')->money('USD'),
                    TextEntry::make('years_experience')->label('Experience'),
                    TextEntry::make('average_rating')->label('Stars'),
                    TextEntry::make('review_count')->label('Reviews'),
                    TextEntry::make('bio')->label('Description')->columnSpanFull(),
                    TextEntry::make('addedBy.name')->label('Added by'),
                    TextEntry::make('status')->badge(),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Freelancer')->searchable()->sortable(),
                TextColumn::make('contact_email')->label('Email')->searchable()->toggleable(),
                TextColumn::make('title')->searchable()->limit(30),
                TextColumn::make('hourly_rate')->label('Rate')->money('USD')->sortable(),
                TextColumn::make('total_earned')->label('Total earned')->money('USD')->sortable(),
                TextColumn::make('years_experience')->label('Experience')->sortable(),
                TextColumn::make('average_rating')->label('Stars')->sortable(),
                TextColumn::make('review_count')->label('Reviews')->sortable(),
                TextColumn::make('addedBy.name')->label('Added by')->toggleable(),
                TextColumn::make('status')->badge()->sortable(),
                IconColumn::make('is_featured')->label('Featured')->boolean()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'paused' => 'Paused',
                        'archived' => 'Archived',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFreelancers::route('/'),
            'create' => CreateFreelancer::route('/create'),
            'view' => ViewFreelancer::route('/{record}'),
            'edit' => EditFreelancer::route('/{record}/edit'),
        ];
    }
}
