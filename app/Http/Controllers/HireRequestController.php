<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHireRequestRequest;
use App\Models\HireRequest;
use App\Support\PublicSubmissionMailer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class HireRequestController extends Controller
{
    public function create(): View
    {
        return view('site.start-hiring');
    }

    public function store(StoreHireRequestRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $hireRequest = HireRequest::create([
            'category' => $data['category'],
            'project_title' => $data['projectTitle'],
            'needs' => $data['needs'],
            'outcome' => $data['outcome'],
            'timeline' => $data['timeline'],
            'budget' => $data['budget'],
            'team' => $data['team'],
            'context' => $data['context'] ?? null,
            'name' => $data['name'],
            'email' => $data['email'],
            'company' => $data['company'] ?? null,
            'website' => $data['website'] ?? null,
            'source' => $data['source'] ?? null,
            'status' => 'new',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        try {
            PublicSubmissionMailer::send(
                subject: 'Website hiring request: ' . Str::limit($hireRequest->project_title, 80),
                view: 'emails.support.hire-request',
                data: [
                    'hireRequest' => $hireRequest,
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
            ->route('hire.received')
            ->with('request_summary', [
                'category' => $data['category'],
                'timeline' => $data['timeline'],
                'budget' => $data['budget'],
                'email' => $data['email'],
            ]);
    }

    public function thankYou(): View
    {
        return view('site.request-received');
    }
}
