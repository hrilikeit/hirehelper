@extends('workspace.layouts.base', ['activeNav' => 'reports'])

@section('content')
<div class="container">
    @include('workspace.partials.flash')

    <div class="page-heading">
        <div>
            <h1>Reports</h1>
            <p>The reporting area summarizes spend, hours, pending offers, and workspace health after registration and login.</p>
        </div>
    </div>

    <div class="report-grid">
        <div class="report-card"><small>This week spend</small><strong>${{ number_format((float) $estimatedWeeklySpend, 2) }}</strong><div class="muted small">{{ $estimatedWeeklySpend > 0 ? 'Based on tracked hours' : 'No billed hours yet' }}</div></div>
        <div class="report-card"><small>Active contracts</small><strong>{{ $activeCount }}</strong><div class="muted small">{{ $activeCount ? 'Ready for time tracking' : 'Nothing active yet' }}</div></div>
        <div class="report-card"><small>Pending offers</small><strong>{{ $pendingCount }}</strong><div class="muted small">{{ $pendingCount ? 'Awaiting next action' : 'No pending offers' }}</div></div>
        <div class="report-card"><small>Billing status</small><strong>PayPal {{ $paypalStatus }}</strong><div class="muted small">{{ $paypalStatus === 'Active' ? 'Subscription active' : 'No active subscription' }}</div></div>
    </div>

    <div class="spacer"></div>

    <div class="grid-2">
        <section class="project-card chart-card">
            <h3 style="font-size:30px;letter-spacing:-.04em;margin:0">Hours trend</h3>
            <p class="muted">Daily tracked hours from the last 2 weeks.</p>
            <div class="bar-chart" style="overflow-x:auto">
                @php
                    $maxHours = max(1, collect($hoursTrend)->max('hours'));
                @endphp
                @foreach ($hoursTrend as $entry)
                    <div class="bar-wrapper" style="text-align:center;min-width:36px">
                        <div class="bar" style="height:{{ $maxHours > 0 ? round(($entry['hours'] / $maxHours) * 80, 0) : 4 }}px; min-height:4px"></div>
                        <div class="muted small" style="margin-top:4px;font-size:10px">{{ $entry['hours'] > 0 ? $entry['hours'] . 'h' : '' }}</div>
                        <div class="muted small" style="font-size:9px">{{ $entry['week'] }}</div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="project-card">
            <h3 style="font-size:30px;letter-spacing:-.04em;margin:0 0 14px">Billing reminders</h3>
            <div class="setting-list">
                @if (! $billingMethod)
                    <div class="setting-row">
                        <div>
                            <strong>Verify billing method</strong>
                            <span>Add, remove, or change the primary billing method for the workspace.</span>
                        </div>
                        <a class="cta-link" href="{{ route('workspace.billing-method') }}">Open</a>
                    </div>
                @endif
                <div class="setting-row">
                    <div>
                        <strong>Invoice details</strong>
                        <span>Keep company name, VAT, and billing contact details ready for invoicing.</span>
                    </div>
                    <a class="cta-link" href="{{ route('workspace.invoice-details') }}">Edit</a>
                </div>
                <div class="setting-row">
                    <div>
                        <strong>Review weekly caps</strong>
                        <span>Make sure each freelancer has the right weekly hour limit before activation.</span>
                    </div>
                    <a class="cta-link" href="{{ route('workspace.hire-flow', array_filter(['project' => $primaryProject?->id, 'freelancer' => $primaryOffer?->freelancer_id])) }}">Edit</a>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
