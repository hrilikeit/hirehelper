@extends('workspace.layouts.base', ['activeNav' => 'messages'])

@section('content')
<div class="container">
    @include('workspace.partials.flash')

    <div class="page-heading">
        <div>
            <span class="badge"><span class="dot"></span> Signed in</span>
            <h1>Messages</h1>
            <p>The client inbox keeps all project communication, status updates, and hiring actions in one quieter workspace.</p>
        </div>
    </div>

    <div class="split-layout">
        <section class="project-card">
            @if ($offer)
                <div class="avatar-line" style="margin-bottom:18px">
                    <img src="{{ $offer->freelancer->avatar_url }}" alt="{{ $offer->freelancer->name }}">
                    <div>
                        <strong>{{ $offer->freelancer->name }}</strong>
                        <span>{{ $offer->role }}</span>
                    </div>
                </div>

                <div class="separator"></div>

                <div class="chat-card" style="background:#f8fbff;border-style:dashed">
                    <strong>Workspace note</strong>
                    <p class="muted" style="margin:8px 0 0">
                        @if ($offer->status === 'active')
                            This room is live. Use it for clarifications, check-ins, and contract-related updates.
                        @else
                            The offer is still pending. You can message the freelancer before activating the contract.
                        @endif
                    </p>
                </div>

                <div style="display:grid;gap:16px;margin-top:18px">
                    @forelse ($messages as $message)
                        <div class="setting-row" style="align-items:flex-start">
                            <div>
                                <strong>{{ $message->sender_name }}</strong>
                                <span>{{ $message->message }}</span>
                            </div>
                            <span class="badge">{{ optional($message->sent_at)->format('M j') ?: 'Today' }}</span>
                        </div>
                    @empty
                        <p class="empty">No messages yet. Start the conversation below.</p>
                    @endforelse
                </div>

                <div class="separator"></div>

                <form method="post" action="{{ route('workspace.messages.store') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="message">New message</label>
                        <textarea class="textarea" id="message" name="message" rows="5" placeholder="Share the next step, request an update, or confirm the details." required>{{ old('message') }}</textarea>
                    </div>
                    <div class="form-actions">
                        <span class="muted small">Messages stay inside this workspace.</span>
                        <button class="button button-primary" type="submit">Send message</button>
                    </div>
                </form>
            @else
                <div class="chat-card" style="background:#f8fbff;border-style:dashed">
                    <strong>Workspace note</strong>
                    <p class="muted" style="margin:8px 0 0">Create a project brief and send an offer before the message room becomes active.</p>
                </div>
                <div style="margin-top:18px">
                    <a class="button button-primary" href="{{ route('workspace.hire-flow') }}">Create project</a>
                </div>
            @endif
        </section>

        <aside class="sidebar-card">
            <h3 style="font-size:30px;letter-spacing:-.04em;margin:0 0 16px">Project snapshot</h3>
            @if ($project && $offer)
                <div class="setting-list">
                    <div class="setting-row">
                        <div>
                            <strong>{{ $project->title }}</strong>
                            <span>Current specialty: {{ $project->specialty }}</span>
                        </div>
                    </div>
                    <div class="setting-row">
                        <div>
                            <strong>Timeframe</strong>
                            <span>{{ $project->timeframe }}</span>
                        </div>
                    </div>
                    <div class="setting-row">
                        <div>
                            <strong>Rate</strong>
                            <span>${{ number_format((float) $offer->hourly_rate, 2) }} / hr</span>
                        </div>
                    </div>
                </div>

                <div class="inline-actions" style="margin-top:20px">
                    <a class="button button-primary" href="{{ $offer->status === 'active' ? route('workspace.project-active') : route('workspace.project-pending') }}">Open contract</a>
                    <a class="button button-secondary" href="{{ route('workspace.reports') }}">View reports</a>
                </div>
            @else
                <p class="empty">Project details appear here after you create your first offer.</p>
            @endif
        </aside>
    </div>
</div>
@endsection
