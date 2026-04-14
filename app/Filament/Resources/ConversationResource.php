<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConversationResource\Pages\EditConversation;
use App\Filament\Resources\ConversationResource\Pages\ListConversations;
use App\Models\ClientProject;
use App\Support\AdminAccess;
use BackedEnum;
use UnitEnum;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConversationResource extends Resource
{
    protected static ?string $model = ClientProject::class;

    protected static ?string $modelLabel = 'Conversation';

    protected static ?string $pluralModelLabel = 'Conversations';

    protected static ?string $recordTitleAttribute = 'title';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string | UnitEnum | null $navigationGroup = 'Talent';

    protected static ?int $navigationSort = 23;

    protected static ?string $slug = 'conversations';

    protected static bool $shouldSkipAuthorization = true;

    public static function canAccess(): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        // Only projects that actually have at least one message
        return parent::getEloquentQuery()->whereHas('messages');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Project')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('latest_offer_freelancer')
                    ->label('Freelancer')
                    ->getStateUsing(function (ClientProject $record) {
                        $offer = $record->offers()
                            ->whereIn('status', ['active', 'pending', 'accepted'])
                            ->latest()
                            ->first()
                            ?? $record->offers()->latest()->first();
                        return $offer?->freelancer_display_name ?: '—';
                    }),
                TextColumn::make('last_message_preview')
                    ->label('Last message')
                    ->getStateUsing(function (ClientProject $record) {
                        $msg = $record->messages()->orderByDesc('sent_at')->first();
                        if (! $msg) return '—';
                        $who = $msg->sender_type === 'client' ? 'Client' : 'Freelancer';
                        return $who . ': ' . \Illuminate\Support\Str::limit((string) $msg->message, 60);
                    }),
                TextColumn::make('messages_count')
                    ->label('Msgs')
                    ->counts('messages')
                    ->badge(),
                TextColumn::make('last_message_at')
                    ->label('Last activity')
                    ->getStateUsing(fn (ClientProject $record) => $record->messages()->max('sent_at'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->label('Open')
                    ->url(fn (ClientProject $record) => static::getUrl('edit', ['record' => $record])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConversations::route('/'),
            'edit' => EditConversation::route('/{record}/edit'),
        ];
    }
}
