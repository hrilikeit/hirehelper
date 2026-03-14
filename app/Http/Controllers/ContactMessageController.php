<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use App\Support\PublicSubmissionMailer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class ContactMessageController extends Controller
{
    public function create(): View
    {
        return view('site.contact');
    }

    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        $contactMessage = ContactMessage::create([
            ...$request->validated(),
            'status' => 'new',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        try {
            PublicSubmissionMailer::send(
                subject: 'Website contact form: ' . Str::limit($contactMessage->topic, 80),
                view: 'emails.support.contact-message',
                data: [
                    'contactMessage' => $contactMessage,
                    'supportInbox' => config('hirehelper.support_inbox'),
                ],
                files: [
                    $request->file('attachment'),
                    $request->file('attachments', []),
                    $request->file('files', []),
                ],
            );
        } catch (Throwable $exception) {
            report($exception);
        }

        return redirect()
            ->route('contact.show')
            ->with('success', 'Thank you. Your message has been sent.');
    }
}
