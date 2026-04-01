<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailSettingResource\Pages\ListEmailSettings;
use App\Models\EmailSetting;
use App\Support\AdminAccess;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class EmailSettingResource extends Resource
{
    protected static ?string $model = EmailSetting::class;

    protected static ?string $modelLabel = 'Email';

    protected static ?string $pluralModelLabel = 'Emails';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-envelope';

    protected static string | UnitEnum | null $navigationGroup = 'Access Control';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'email-settings';

    public static function canAccess(): bool
    {
        return AdminAccess::isSuperAdmin(auth()->user());
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('key')
                    ->label('Key')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('description')
                    ->limit(60)
                    ->toggleable(),
                ToggleColumn::make('is_active')
                    ->label('Active'),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('name', 'asc')
            ->paginated(false);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmailSettings::route('/'),
        ];
    }
}
