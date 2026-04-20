<?php

namespace App\Filament\Resources\ConversationResource\Pages;

use App\Filament\Resources\ConversationResource;
use App\Mail\UnreadMessageMail;
use App\Models\EmailLog;
use App\Models\ProjectMessage;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Mail;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class EditConversation extends EditRecord
{
    protected static string $resource = ConversationResource::class;

    protected static ?string $title = 'Conversation';

    public function mount(int|string $record): void
    {
        parent::mount($record);

        // Mark all client messages as read by admin
        $this->record->messages()
            ->where('sender_type', 'client')
            ->whereNull('admin_read_at')
            ->update(['admin_read_at' => now()]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('notifyUnread')
                ->label('Notify by email')
                ->color('warning')
                ->icon('heroicon-o-envelope')
                ->requiresConfirmation()
                ->modalHeading('Send unread-messages email')
                ->modalDescription('Send the client an email letting them know they have unread messages from the freelancer.')
                ->action(function () {
                    $project = $this->record;
                    $client = $project->user;

                    if (! $client) {
                        Notification::make()->title('No client on this project.')->danger()->send();
                        return;
                    }

                    // Find latest freelancer message to reference in the email
                    $latest = $project->messages()
                        ->where('sender_type', 'freelancer')
                        ->orderByDesc('sent_at')
                        ->first();

                    if (! $latest) {
                        Notification::make()->title('No freelancer messages exist yet to notify about.')->warning()->send();
                        return;
                    }

                    try {
                        $mailable = new UnreadMessageMail(
                            message: $latest,
                            projectTitle: $project->title ?? 'Your project',
                            messagesUrl: route('workspace.messages'),
                        );

                        $emailLog = EmailLog::record(
                            userId: $client->id,
                            emailType: 'unread_message',
                            subject: 'New message on "' . ($project->title ?? 'Your project') . '"',
                            toEmail: $client->email,
                            projectId: $project->id,
                        );

                        $mailable->with('emailLogId', $emailLog->id);
                        Mail::to($client->email)->send($mailable);
                        $emailLog->update(['body' => $mailable->render()]);

                        Notification::make()
                            ->title('Notification email sent to ' . $client->email)
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        report($e);
                        Notification::make()
                            ->title('Failed to send: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('sendReply')
                ->label('Send reply')
                ->color('primary')
                ->icon('heroicon-o-paper-airplane')
                ->form([
                    TextInput::make('reply_as_name')
                        ->label('Reply as (name)')
                        ->default(fn () => $this->getFreelancerName())
                        ->required(),
                    Textarea::make('reply_message')
                        ->label('Message')
                        ->rows(6)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $project = $this->record;
                    $offer = $project->offers()
                        ->whereIn('status', ['active', 'pending', 'accepted'])
                        ->latest()
                        ->first()
                        ?? $project->offers()->latest()->first();

                    ProjectMessage::create([
                        'client_project_id' => $project->id,
                        'project_offer_id'  => $offer?->id,
                        'sender_type'       => 'freelancer',
                        'sender_name'       => trim((string) $data['reply_as_name']) ?: $this->getFreelancerName(),
                        'message'           => (string) $data['reply_message'],
                        'sent_at'           => now(),
                    ]);

                    Notification::make()->title('Reply sent.')->success()->send();

                    $this->redirect(ConversationResource::getUrl('edit', ['record' => $project->id]));
                }),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Conversation')
                ->schema([
                    Placeholder::make('thread_html')
                        ->hiddenLabel()
                        ->content(fn () => new HtmlString(view('filament.conversation.thread', [
                            'project' => $this->record,
                        ])->render()))
                        ->columnSpanFull(),
                ]),
        ])->columns(1);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return [];
    }

    protected function getSavedNotification(): ?Notification
    {
        return null;
    }

    protected function handleRecordUpdate($record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return $record;
    }

    protected function getFreelancerName(): string
    {
        $project = $this->record;
        $offer = $project->offers()
            ->whereIn('status', ['active', 'pending', 'accepted'])
            ->latest()
            ->first()
            ?? $project->offers()->latest()->first();

        return (string) ($offer?->freelancer_display_name ?: 'Freelancer');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
