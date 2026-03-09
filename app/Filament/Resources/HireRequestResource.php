<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HireRequestResource\Pages\EditHireRequest;
use App\Filament\Resources\HireRequestResource\Pages\ListHireRequests;
use App\Models\HireRequest;
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

class HireRequestResource extends Resource
{
    protected static ?string $model = HireRequest::class;

    protected static ?string $modelLabel = 'Hire request';

    protected static ?string $pluralModelLabel = 'Hire requests';

    protected static ?string $recordTitleAttribute = 'project_title';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?int $navigationSort = 9;

    protected static bool $shouldSkipAuthorization = true;

    protected static ?string $slug = 'hire-requests';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Project request')
                    ->schema([
                        TextInput::make('project_title')->label('Project title')->disabled()->saved(false),
                        TextInput::make('category')->disabled()->saved(false),
                        Textarea::make('needs')->rows(8)->disabled()->saved(false)->columnSpanFull(),
                        TextInput::make('outcome')->disabled()->saved(false),
                        TextInput::make('timeline')->disabled()->saved(false),
                        TextInput::make('budget')->disabled()->saved(false),
                        TextInput::make('team')->disabled()->saved(false),
                        Textarea::make('context')->rows(4)->disabled()->saved(false)->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Client')
                    ->schema([
                        TextInput::make('name')->disabled()->saved(false),
                        TextInput::make('email')->email()->disabled()->saved(false),
                        TextInput::make('company')->disabled()->saved(false),
                        TextInput::make('website')->url()->disabled()->saved(false),
                        TextInput::make('source')->disabled()->saved(false),
                    ])
                    ->columns(2),
                Section::make('Admin')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'new' => 'New',
                                'reviewing' => 'Reviewing',
                                'contacted' => 'Contacted',
                                'qualified' => 'Qualified',
                                'closed' => 'Closed',
                            ])
                            ->required(),
                        Textarea::make('admin_notes')->rows(6)->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Technical')
                    ->schema([
                        TextInput::make('ip_address')->disabled()->saved(false),
                        Textarea::make('user_agent')->rows(3)->disabled()->saved(false)->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project_title')->label('Project')->searchable()->sortable()->limit(40),
                TextColumn::make('category')->badge(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('created_at')->dateTime('M j, Y g:i A')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHireRequests::route('/'),
            'edit' => EditHireRequest::route('/{record}/edit'),
        ];
    }
}
