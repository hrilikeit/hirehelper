@extends('workspace.layouts.base', ['activeNav' => 'projects'])

@section('content')
<div class="container">
    @include('workspace.partials.flash')

    @if (! $billingVerified)
        <div class="notice-banner" data-dismissible-notice>
            <div style="display:flex;align-items:center;gap:12px"><span class="notice-icon">⚠</span><span><strong>Critical notice:</strong> billing should be verified to keep contract payments running smoothly.</span></div>
            <div style="display:flex;align-items:center;gap:18px"><a href="{{ route('workspace.billing-method', ['offer' => $offer->id]) }}">Verify billing method</a><button class="link-button" type="button" data-dismiss-notice>Dismiss</button></div>
        </div>
    @endif

    <div class="breadcrumbs">
        <a href="{{ route('workspace.dashboard-live') }}">Dashboard</a><span>›</span><span>Project</span>
    </div>

    <div class="dual-col">
        <section class="project-card">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:18px;flex-wrap:wrap">
                <div>
                    <h1 style="font-size:48px;letter-spacing:-.05em;margin:0 0 20px">{{ $project->title }}</h1>
                    <div class="inline-actions">
                        <span class="badge">Time & payments</span>
                        <a class="cta-link" href="{{ route('workspace.project-pending') }}">Terms & settings</a>
                    </div>
                </div>
                <a class="cta-link" href="{{ route('workspace.reports') }}">Pay bonus</a>
            </div>

            <div class="separator"></div>
            <div class="timesheet-grid">
                <div class="timesheet-stat"><small>This week</small><strong>00:00 hrs</strong><div class="muted small">of {{ $offer->weekly_limit }} hrs / week limit</div></div>
                <div class="timesheet-stat"><small>Last week</small><strong>00:00 hrs</strong><div class="muted small">$0.00</div></div>
                <div class="timesheet-stat"><small>Since start</small><strong>00:00 hrs</strong><div class="muted small">$0.00</div></div>
            </div>

            <div class="separator"></div>
            <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
                <h2 style="font-size:30px;letter-spacing:-.04em;margin:0">Timesheet this week</h2>
                <div class="muted small">Amount: $0.00</div>
            </div>

            <div class="day-strip" style="margin-top:22px">
                @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                    <div class="day-box"><div class="day">{{ $day }}</div><div class="hours">00:00</div></div>
                @endforeach
            </div>

            <div class="separator"></div>
            <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
                <h2 style="font-size:30px;letter-spacing:-.04em;margin:0">All timesheets and earnings</h2>
                <span class="badge">Last 30 days</span>
            </div>
            <table class="table" style="margin-top:14px">
                <thead><tr><th>Date</th><th>Description</th><th>Status</th><th>Amount</th><th>Invoice</th></tr></thead>
                <tbody><tr><td colspan="5" class="muted">No transaction meets your selected criteria yet.</td></tr></tbody>
            </table>
        </section>

        <aside class="sidebar-card side-profile">
            <img src="{{ $offer->freelancer_display_avatar_url }}" alt="{{ $offer->freelancer_display_name }}">
            <h3>
                @if ($offer->freelancer && $offer->freelancer->slug)
                    <a href="{{ url('/freelancers/' . $offer->freelancer->slug) }}" style="color:inherit;text-decoration:none">{{ $offer->freelancer_display_name }}</a>
                @else
                    {{ $offer->freelancer_display_name }}
                @endif
            </h3>
            <div class="place">{{ $offer->freelancer_display_title }}</div>
            <a class="cta-link" href="{{ route('workspace.messages') }}">Send a message</a>
            <div class="status-block">
                <div>Start date: <strong>{{ optional($offer->activated_at)->format('M j, Y') ?: now()->format('M j, Y') }}</strong></div>
                <div>Contract status: <span class="status-pill status-active">Active</span></div>
                <div>Billing: <strong>{{ $billingMethod ?: 'Not added yet' }}</strong></div>
            </div>
            <div class="action-inline">
                <span>Pause contract</span>
                <span class="toggle"></span>
            </div>
            <a class="button button-primary" href="{{ route('workspace.reports') }}">Pay</a>
            <form method="post" action="{{ route('workspace.project.close') }}" style="width:100%">
                @csrf
                <input type="hidden" name="offer_id" value="{{ $offer->id }}">
                <button class="button button-secondary" style="width:100%" type="submit">End Contract</button>
            </form>
        </aside>
    </div>
</div>
@endsection
