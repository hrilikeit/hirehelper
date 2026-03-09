<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHireRequestRequest;
use App\Models\HireRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HireRequestController extends Controller
{
    public function create(): View
    {
        return view('site.start-hiring');
    }

    public function store(StoreHireRequestRequest $request): RedirectResponse
    {
        $data = $request->validated();

        HireRequest::create([
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
