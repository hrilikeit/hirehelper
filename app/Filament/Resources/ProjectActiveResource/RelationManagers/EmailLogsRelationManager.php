<?php

namespace App\Filament\Resources\ProjectActiveResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmailLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'emailLogs';

    protected static ?string $title = 'Emails Sent';

    protected static ?string $recordTitleAttribute = 'email_type';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => str_replace('_', ' ', ucfirst($state))),
                TextColumn::make('subject')
                    ->label('Subject')
                    ->limit(50),
                TextColumn::make('to_email')
                    ->label('To'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sent' => 'success',
                        'opened' => 'info',
                        'failed' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('opened_at')
                    ->label('Opened')
                    ->since()
                    ->placeholder('Not opened'),
                TextColumn::make('created_at')
                    ->label('Sent at')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
