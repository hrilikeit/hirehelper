@extends('workspace.layouts.base', ['activeNav' => 'reports'])

@section('content')
<div class="container">
    @include('workspace.partials.flash')

    <div class="page-heading">
        <div>
            <span class="badge"><span class="dot"></span> Signed in</span>
            <h1>Reports</h1>
            <p>The reporting area summarizes spend, hours, pending offers, and workspace health after registration and login.</p>
        </div>
    </div>

    <div class="report-grid">
        <div class="report-card"><small>This week spend</small><strong>${{ number_format((float) $estimatedWeeklySpend, 2) }}</strong><div class="muted small">{{ $activeCount ? 'Estimated based on active contract' : 'No billed hours yet' }}</div></div>
        <div class="report-card"><small>Active contracts</small><strong>{{ $activeCount }}</strong><div class="muted small">{{ $activeCount ? 'Ready for time tracking' : 'Nothing active yet' }}</div></div>
        <div class="report-card"><small>Pending offers</small><strong>{{ $pendingCount }}</strong><div class="muted small">{{ $pendingCount ? 'Awaiting next action' : 'No pending offers' }}</div></div>
        <div class="report-card"><small>Billing status</small><strong>{{ $billingMethod ?: 'Action' }}</strong><div class="muted small">{{ $billingMethod ? 'Default billing method saved' : 'Verify billing to remove warnings' }}</div></div>
    </div>

    <div class="spacer"></div>

    <div class="grid-2">
        <section class="project-card chart-card">
            <h3 style="font-size:30px;letter-spacing:-.04em;margin:0">Hours trend</h3>
            <p class="muted">The chart presents workspace hours and invoice totals in a calm, minimal layout.</p>
            <div class="bar-chart">
                @foreach ([24, 24, 24, 24, 24, 24] as $height)
                    <div class="bar" style="height:{{ $height }}px"></div>
                @endforeach
            </div>
        </section>

        <section class="project-card">
            <h3 style="font-size:30px;letter-spacing:-.04em;margin:0 0 14px">Billing reminders</h3>
            <div class="setting-list">
                <div class="setting-row">
                    <div>
                        <strong>Verify billing method</strong>
                        <span>Remove pause warnings and keep contracts uninterrupted.</span>
                    </div>
                    <a class="cta-link" href="{{ route('workspace.billing-method') }}">Open</a>
                </div>
                <div class="setting-row">
                    <div>
                        <strong>Review weekly caps</strong>
                        <span>Make sure each freelancer has the right weekly hour limit before activation.</span>
                    </div>
                    <a class="cta-link" href="{{ route('workspace.invite-offer') }}">Edit</a>
                </div>
                <div class="setting-row">
                    <div>
                        <strong>Client dashboard check</strong>
                        <span>Keep the project list, offers, and activity feed aligned with the current UI.</span>
                    </div>
                    <a class="cta-link" href="{{ route('workspace.dashboard-live') }}">Review</a>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
