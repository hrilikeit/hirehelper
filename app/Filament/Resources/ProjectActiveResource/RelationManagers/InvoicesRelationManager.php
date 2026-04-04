<?php

namespace App\Filament\Resources\ProjectActiveResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    protected static ?string $title = 'Invoices & Payments';

    protected static ?string $recordTitleAttribute = 'invoice_number';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono'),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'weekly' => 'success',
                        'bonus' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('description')
                    ->limit(40),
                TextColumn::make('hours')
                    ->label('Hours')
                    ->suffix('h')
                    ->placeholder('—'),
                TextColumn::make('hourly_rate')
                    ->label('Rate')
                    ->money('usd')
                    ->placeholder('—'),
                TextColumn::make('amount')
                    ->money('usd')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'refunded' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('payment_method')
                    ->label('Method')
                    ->placeholder('—'),
                TextColumn::make('period_start')
                    ->label('Period')
                    ->date('M j')
                    ->description(fn ($record) => $record->period_end ? '– ' . $record->period_end->format('M j') : null)
                    ->placeholder('—'),
                TextColumn::make('paid_at')
                    ->label('Paid')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->placeholder('Not paid'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
