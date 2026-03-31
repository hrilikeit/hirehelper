<?php

namespace App\Observers;

use App\Mail\UnreadMessageMail;
use App\Models\ProjectMessage;
use Illuminate\Support\Facades\Mail;

class ProjectMessageObserver
{
    public function created(ProjectMessage $message): void
    {
        // When sender is freelancer or system, notify the client
        if (in_array($message->sender_type, ['freelancer', 'system'], true)) {
            $this->notifyClient($message);
        }
    }

    protected function notifyClient(ProjectMessage $message): void
    {
        $project = $message->project;
        if (! $project) {
            return;
        }

        $client = $project->user;
        if (! $client || ! $client->notify_messages) {
            return;
        }

        try {
            Mail::to($client->email)->send(new UnreadMessageMail(
                message: $message,
                projectTitle: $project->title ?? 'Your project',
                messagesUrl: route('workspace.messages'),
            ));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
