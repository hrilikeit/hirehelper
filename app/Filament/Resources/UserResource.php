<?php

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use App\Support\AdminAccess;
use BackedEnum;
use UnitEnum;
use App\Models\LoginToken;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'Client';

    protected static ?string $pluralModelLabel = 'Clients';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

    protected static string | UnitEnum | null $navigationGroup = 'Sales & Delivery';

    protected static ?int $navigationSort = 10;

    protected static ?string $slug = 'clients';

    public static function canAccess(): bool
    {
        return AdminAccess::canAccessNonSalesResource(auth()->user());
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function canViewAny(): bool
    {
        return AdminAccess::canViewClients(auth()->user());
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return AdminAccess::canEditClients(auth()->user());
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role', UserRole::Client->value)
            ->latest();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Client account')
                ->schema([
                    TextInput::make('name')->required(),
                    TextInput::make('email')->email()->required(),
                    TextInput::make('company'),
                    TextInput::make('phone'),
                    Toggle::make('notify_messages'),
                    Toggle::make('notify_reports'),
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
                TextColumn::make('company')->searchable()->toggleable(),
                TextColumn::make('phone')->toggleable(),
                TextColumn::make('projects_count')
                    ->label('Projects')
                    ->counts('projects'),
                TextColumn::make('created_at')->dateTime('M j, Y g:i A')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('loginAsClient')
                    ->label('Login')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('success')
                    ->visible(fn () => AdminAccess::isSuperAdmin(auth()->user()))
                    ->requiresConfirmation()
                    ->modalHeading('Login as this client')
                    ->modalDescription('A one-time login link will be generated (valid for 5 minutes). The client\'s password will not be changed.')
                    ->action(function (User $record) {
                        $token = LoginToken::createFor($record->id, auth()->id());
                        $url = route('login-token', ['token' => $token->token]);

                        Notification::make()
                            ->title('Login link generated')
                            ->body($url)
                            ->success()
                            ->persistent()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->visible(fn () => AdminAccess::isSuperAdmin(auth()->user())),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
