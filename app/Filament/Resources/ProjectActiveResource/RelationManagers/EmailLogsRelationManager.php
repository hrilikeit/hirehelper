<?php

namespace App\Filament\Resources\ProjectActiveResource\RelationManagers;

use App\Models\EmailLog;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

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
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('viewEmail')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading(fn (EmailLog $record) => $record->subject ?: 'Email Preview')
                    ->modalContent(function (EmailLog $record): HtmlString {
                        if (filled($record->body)) {
                            return new HtmlString(
                                '<div style="max-height:500px;overflow-y:auto;border:1px solid #e5e7eb;border-radius:8px;padding:0">'
                                . '<iframe srcdoc="' . e($record->body) . '" style="width:100%;height:500px;border:none"></iframe>'
                                . '</div>'
                            );
                        }

                        return new HtmlString(
                            '<div style="text-align:center;padding:48px 24px;color:#6b7280">'
                            . '<p style="font-size:14px">Email content is not available for this message.</p>'
                            . '<p style="font-size:12px;margin-top:8px">Email body storage was added after this email was sent.</p>'
                            . '</div>'
                        );
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ]);
    }
}
