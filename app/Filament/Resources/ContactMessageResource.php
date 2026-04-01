<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages\EditContactMessage;
use App\Filament\Resources\ContactMessageResource\Pages\ListContactMessages;
use App\Models\ContactMessage;
use App\Support\AdminAccess;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $modelLabel = 'Contact message';

    protected static ?string $pluralModelLabel = 'Contact messages';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-envelope';

    protected static ?int $navigationSort = 10;

    protected static bool $shouldSkipAuthorization = true;

    protected static ?string $slug = 'contact-messages';

    public static function canAccess(): bool
    {
        return AdminAccess::canAccessNonSalesResource(auth()->user());
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Message details')
                    ->schema([
                        TextInput::make('name')->disabled()->saved(false),
                        TextInput::make('email')->email()->disabled()->saved(false),
                        TextInput::make('company')->disabled()->saved(false),
                        TextInput::make('topic')->disabled()->saved(false),
                        Textarea::make('message')->rows(8)->disabled()->saved(false)->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Admin')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'new' => 'New',
                                'reviewing' => 'Reviewing',
                                'replied' => 'Replied',
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
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->sortable(),
                TextColumn::make('topic')->badge(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('company')->toggleable(),
                TextColumn::make('created_at')->dateTime('M j, Y g:i A')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => ListContactMessages::route('/'),
            'edit' => EditContactMessage::route('/{record}/edit'),
        ];
    }
}
