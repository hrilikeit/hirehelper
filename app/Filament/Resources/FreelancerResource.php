<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FreelancerResource\Pages\CreateFreelancer;
use App\Filament\Resources\FreelancerResource\Pages\EditFreelancer;
use App\Filament\Resources\FreelancerResource\Pages\ListFreelancers;
use App\Filament\Resources\FreelancerResource\Pages\ViewFreelancer;
use App\Models\Freelancer;
use App\Models\User;
use App\Support\AdminAccess;
use BackedEnum;
use UnitEnum;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

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
            Section::make('Public profile')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, $set) {
                            $set('slug', Str::slug((string) $state));
                        }),
                    TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true),
                    TextInput::make('title')->required(),
                    TextInput::make('headline')->label('Profile headline'),
                    TextInput::make('specialization'),
                    TextInput::make('hourly_rate')->numeric()->required()->prefix('$'),
                    TextInput::make('country'),
                    TextInput::make('city'),
                    TextInput::make('location')->helperText('Legacy location field used by the client workspace cards.'),
                    TextInput::make('english_level'),
                    TextInput::make('timezone'),
                    TextInput::make('availability'),
                    TextInput::make('years_experience')->numeric()->minValue(0),
                    TextInput::make('average_rating')->numeric()->minValue(0)->maxValue(5),
                    TextInput::make('review_count')->numeric()->minValue(0),
                    TextInput::make('completed_jobs')->numeric()->minValue(0),
                    TextInput::make('avatar')->placeholder('avatar-ava.svg'),
                    Select::make('status')
                        ->options([
                            'active' => 'Active',
                            'paused' => 'Paused',
                            'archived' => 'Archived',
                        ])
                        ->required(),
                    Toggle::make('is_featured'),
                ])
                ->columns(3),

            Section::make('Expertise')
                ->schema([
                    TagsInput::make('skills'),
                    TagsInput::make('tools'),
                    Textarea::make('overview')->rows(4)->columnSpanFull(),
                    Textarea::make('bio')->rows(6)->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Links and internal notes')
                ->schema([
                    TextInput::make('portfolio_url')->url(),
                    TextInput::make('linkedin_url')->url(),
                    TextInput::make('github_url')->url(),
                    TextInput::make('intro_video_url')->url(),
                    Select::make('added_by_user_id')
                        ->label('Added by')
                        ->options(fn () => User::query()
                            ->whereIn('role', ['superadmin', 'admin', 'sales_manager'])
                            ->orderBy('name')
                            ->pluck('name', 'id'))
                        ->searchable(),
                    Textarea::make('internal_notes')->rows(5)->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Freelancer profile')
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('title'),
                    TextEntry::make('headline'),
                    TextEntry::make('specialization'),
                    TextEntry::make('display_location')->label('Location'),
                    TextEntry::make('hourly_rate')->money('USD'),
                    TextEntry::make('years_experience')->label('Years experience'),
                    TextEntry::make('availability'),
                    TextEntry::make('english_level')->label('English'),
                    TextEntry::make('timezone'),
                    TextEntry::make('average_rating')->label('Rating'),
                    TextEntry::make('review_count')->label('Reviews'),
                    TextEntry::make('completed_jobs')->label('Completed jobs'),
                    TextEntry::make('skills')
                        ->formatStateUsing(fn ($state): string => implode(', ', $state ?? []))
                        ->columnSpanFull(),
                    TextEntry::make('tools')
                        ->formatStateUsing(fn ($state): string => implode(', ', $state ?? []))
                        ->columnSpanFull(),
                    TextEntry::make('overview')->columnSpanFull(),
                    TextEntry::make('bio')->columnSpanFull(),
                    TextEntry::make('portfolio_url')
                        ->url(fn ($state): ?string => filled($state) ? $state : null)
                        ->openUrlInNewTab(),
                    TextEntry::make('linkedin_url')
                        ->url(fn ($state): ?string => filled($state) ? $state : null)
                        ->openUrlInNewTab(),
                    TextEntry::make('github_url')
                        ->url(fn ($state): ?string => filled($state) ? $state : null)
                        ->openUrlInNewTab(),
                    TextEntry::make('intro_video_url')
                        ->url(fn ($state): ?string => filled($state) ? $state : null)
                        ->openUrlInNewTab(),
                    TextEntry::make('addedBy.name')->label('Added by'),
                    TextEntry::make('internal_notes')->columnSpanFull(),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('title')->searchable()->limit(30),
                TextColumn::make('country')->toggleable(),
                TextColumn::make('average_rating')->label('Rating')->sortable(),
                TextColumn::make('review_count')->label('Reviews')->sortable(),
                TextColumn::make('hourly_rate')->money('USD')->sortable(),
                TextColumn::make('addedBy.name')->label('Added by')->toggleable(),
                TextColumn::make('status')->badge()->sortable(),
                IconColumn::make('is_featured')->boolean(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'paused' => 'Paused',
                        'archived' => 'Archived',
                    ]),
            ])
            ->defaultSort('name')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
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
