@extends('workspace.layouts.base', ['activeNav' => 'projects'])

@section('content')
<div class="container">
    @include('workspace.partials.flash')

    @if (! $billingVerified)
        <div class="notice-banner" data-dismissible-notice>
            <div style="display:flex;align-items:center;gap:12px"><span class="notice-icon">⚠</span><span><strong>Critical notice:</strong> all contracts are paused until the billing method is verified.</span></div>
            <div style="display:flex;align-items:center;gap:18px"><a href="{{ route('workspace.billing-method', ['offer' => $offer->id]) }}">Verify billing method</a><button class="link-button" type="button" data-dismiss-notice>Dismiss</button></div>
        </div>
    @endif

    <div class="breadcrumbs">
        <a href="{{ route('workspace.dashboard-live') }}">Dashboard</a><span>›</span><span>Project</span>
    </div>

    <div class="dual-col">
        <section class="project-card">
            <h1 style="font-size:48px;letter-spacing:-.05em;margin:0 0 20px">{{ $project->title }}</h1>
            <div class="inline-actions" style="margin-bottom:18px">
                <span class="badge">Terms & settings</span>
            </div>

            <div class="separator"></div>

            <h2 style="font-size:30px;letter-spacing:-.04em;margin:0 0 18px">Rate and limits</h2>
            <div class="data-list">
                <div class="data-item"><small>Hourly rate</small><strong>${{ number_format((float) $offer->hourly_rate, 2) }} / hr</strong></div>
                <div class="data-item"><small>Weekly limit</small><strong>{{ $offer->weekly_limit }} hrs / week</strong></div>
                <div class="data-item"><small>Manual time</small><strong>{{ $offer->manual_time ? 'Allowed' : 'Disabled' }}</strong></div>
                <div class="data-item"><small>Started by</small><strong>Client workspace</strong></div>
                <div class="data-item"><small>Status</small><strong>Pending</strong></div>
                <div class="data-item"><small>Offer created</small><strong>{{ optional($offer->sent_at)->format('M j, Y') ?: now()->format('M j, Y') }}</strong></div>
            </div>

            <div class="separator"></div>
            <h2 style="font-size:30px;letter-spacing:-.04em;margin:0 0 12px">Work description</h2>
            <p class="muted">{{ $project->description }}</p>

            <div class="separator"></div>
            <div class="inline-actions">
                <a class="button button-secondary" href="{{ route('workspace.invite-offer', ['project' => $project->id]) }}">Modify offer</a>
                <form method="post" action="{{ route('workspace.project.activate') }}">
                    @csrf
                    <input type="hidden" name="offer_id" value="{{ $offer->id }}">
                    <button class="button button-primary" type="submit">Open active contract</button>
                </form>
            </div>
        </section>

        <aside class="sidebar-card side-profile">
            <img src="{{ $offer->freelancer_display_avatar_url }}" alt="{{ $offer->freelancer_display_name }}">
            <h3>{{ $offer->freelancer_display_name }}</h3>
            <div class="place">{{ $offer->freelancer_display_location }}</div>
            <a class="cta-link" href="{{ route('workspace.messages') }}">Send a message</a>
            <div class="status-block">
                <div>Offer date: <strong>{{ optional($offer->sent_at)->format('M j, Y') ?: now()->format('M j, Y') }}</strong></div>
                <div>Billing: <strong>{{ $billingMethod ?: 'Not added yet' }}</strong></div>
                <div>Status: <span class="status-pill status-pending">Pending</span></div>
            </div>
        </aside>
    </div>
</div>
@endsection
