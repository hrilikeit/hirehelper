<?php

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Filament\Resources\AdminUserResource\Pages\CreateAdminUser;
use App\Filament\Resources\AdminUserResource\Pages\EditAdminUser;
use App\Filament\Resources\AdminUserResource\Pages\ListAdminUsers;
use App\Models\User;
use App\Support\AdminAccess;
use BackedEnum;
use UnitEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AdminUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'Team user';

    protected static ?string $pluralModelLabel = 'Team users';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-shield-check';

    protected static string | UnitEnum | null $navigationGroup = 'Access Control';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'users';

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function canViewAny(): bool
    {
        return AdminAccess::canViewInternalUsers(auth()->user());
    }

    public static function canCreate(): bool
    {
        return AdminAccess::canManageInternalUsers(auth()->user());
    }

    public static function canEdit($record): bool
    {
        return AdminAccess::canManageInternalUsers(auth()->user());
    }

    public static function canDelete($record): bool
    {
        return AdminAccess::canManageInternalUsers(auth()->user());
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('role', UserRole::internalValues())
            ->latest();
    }

    public static function form(Schema $schema): Schema
    {
        $user = auth()->user();

        return $schema->components([
            Section::make('User account')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    TextInput::make('phone')
                        ->maxLength(50),
                    TextInput::make('job_title')
                        ->label('Job title')
                        ->maxLength(255),
                    Select::make('role')
                        ->options(UserRole::internalOptions(AdminAccess::isSuperAdmin($user)))
                        ->required(),
                    Toggle::make('is_active')
                        ->default(true),
                    TextInput::make('avatar_url')
                        ->label('Avatar URL')
                        ->url(),
                    TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->minLength(8)
                        ->same('passwordConfirmation'),
                    TextInput::make('passwordConfirmation')
                        ->password()
                        ->revealable()
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->dehydrated(false)
                        ->label('Confirm password'),
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
                TextColumn::make('role')->badge()->formatStateUsing(fn (string $state): string => UserRole::tryFrom($state)?->label() ?? ucfirst(str_replace('_', ' ', $state))),
                TextColumn::make('job_title')->label('Job title')->toggleable(),
                IconColumn::make('is_active')->boolean()->label('Active'),
                TextColumn::make('created_at')->dateTime('M j, Y g:i A')->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options(UserRole::internalOptions()),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn (User $record): bool => AdminAccess::canManageInternalUsers(auth()->user()) && ! $record->isRole('superadmin')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdminUsers::route('/'),
            'create' => CreateAdminUser::route('/create'),
            'edit' => EditAdminUser::route('/{record}/edit'),
        ];
    }
}
