<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FreelancerResource\Pages\CreateFreelancer;
use App\Filament\Resources\FreelancerResource\Pages\EditFreelancer;
use App\Filament\Resources\FreelancerResource\Pages\ListFreelancers;
use App\Models\Freelancer;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class FreelancerResource extends Resource
{
    protected static ?string $model = Freelancer::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?int $navigationSort = 12;

    protected static bool $shouldSkipAuthorization = true;

    protected static ?string $slug = 'freelancers';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set) {
                                $set('slug', Str::slug((string) $state));
                            }),
                        TextInput::make('slug')->required()->unique(ignoreRecord: true),
                        TextInput::make('title')->required(),
                        TextInput::make('hourly_rate')->numeric()->required()->prefix('$'),
                        TextInput::make('location'),
                        TextInput::make('avatar')->placeholder('avatar-ava.svg'),
                        Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'paused' => 'Paused',
                            ])
                            ->required(),
                        Toggle::make('is_featured'),
                        TagsInput::make('skills'),
                        Textarea::make('overview')->rows(6)->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('title')->searchable()->limit(30),
                TextColumn::make('hourly_rate')->money('USD')->sortable(),
                TextColumn::make('location')->toggleable(),
                TextColumn::make('status')->badge()->sortable(),
                IconColumn::make('is_featured')->boolean(),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFreelancers::route('/'),
            'create' => CreateFreelancer::route('/create'),
            'edit' => EditFreelancer::route('/{record}/edit'),
        ];
    }
}
