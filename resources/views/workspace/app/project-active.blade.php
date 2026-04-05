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
                </div>
                <button class="cta-link" type="button" onclick="document.getElementById('bonusModal').style.display='grid'" style="border:none;background:none;cursor:pointer;font:inherit;color:var(--primary);font-weight:700;font-size:13px">Pay bonus</button>
            </div>

            {{-- Tabs --}}
            <div class="tab-nav" style="display:flex;gap:0;border-bottom:2px solid var(--line);margin:10px 0 24px">
                <button type="button" class="tab-btn active" data-tab="time-payments" style="padding:10px 20px;border:none;background:none;font:inherit;font-weight:600;cursor:pointer;border-bottom:2px solid var(--primary);margin-bottom:-2px;color:var(--primary)">Time & Payments</button>
                <button type="button" class="tab-btn" data-tab="terms-settings" style="padding:10px 20px;border:none;background:none;font:inherit;font-weight:600;cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px;color:var(--muted)">Terms & Settings</button>
            </div>

            {{-- Tab: Time & Payments --}}
            <div id="tab-time-payments" class="tab-panel">
                <div class="timesheet-grid">
                    <div class="timesheet-stat"><small>This week</small><strong>{{ number_format((float) ($currentWeekHours ?? 0), 2) }} hrs</strong><div class="muted small">of {{ $offer->weekly_limit }} hrs / week limit</div></div>
                    <div class="timesheet-stat"><small>Last week</small><strong>{{ number_format((float) ($lastWeekHours ?? 0), 2) }} hrs</strong><div class="muted small">${{ number_format((float) ($lastWeekAmount ?? 0), 2) }}</div></div>
                    <div class="timesheet-stat"><small>Since start</small><strong>{{ number_format((float) ($totalHours ?? 0), 2) }} hrs</strong><div class="muted small">${{ number_format((float) ($totalAmount ?? 0), 2) }}</div></div>
                </div>

                <div class="separator"></div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
                    <h2 style="font-size:30px;letter-spacing:-.04em;margin:0">Timesheet this week</h2>
                    <div class="muted small">Amount: ${{ number_format((float) ($currentWeekAmount ?? 0), 2) }}</div>
                </div>

                <div class="day-strip" style="margin-top:22px">
                    @php
                        $days = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
                        $dayLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                    @endphp
                    @foreach ($days as $idx => $day)
                        <div class="day-box"><div class="day">{{ $dayLabels[$idx] }}</div><div class="hours">{{ number_format((float) ($currentTimesheet[$day] ?? 0), 2) }}</div></div>
                    @endforeach
                </div>

                <div class="separator"></div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
                    <h2 style="font-size:30px;letter-spacing:-.04em;margin:0">All timesheets and earnings</h2>
                    <span class="badge">Last 30 days</span>
                </div>
                <table class="table" style="margin-top:14px">
                    <thead><tr><th>Week</th><th>Hours</th><th>Status</th><th>Amount</th></tr></thead>
                    <tbody>
                        @forelse ($timesheets ?? [] as $ts)
                            <tr>
                                <td>{{ $ts->week_start->format('M j, Y') }}</td>
                                <td>{{ number_format((float) $ts->total_hours, 2) }}</td>
                                <td><span class="badge badge-{{ $ts->status === 'paid' ? 'success' : ($ts->status === 'discarded' ? 'danger' : 'warning') }}">{{ ucfirst($ts->status) }}</span></td>
                                <td>${{ number_format((float) $ts->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="muted">No transaction meets your selected criteria yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tab: Terms & Settings --}}
            <div id="tab-terms-settings" class="tab-panel" style="display:none">
                <h3 style="font-size:24px;letter-spacing:-.03em;margin:0 0 20px">Rate and Limits</h3>

                <div class="setting-list">
                    <div class="setting-row">
                        <div><strong>Hourly Rate</strong></div>
                        <div>${{ number_format((float) $offer->hourly_rate, 2) }} / hr</div>
                    </div>
                    <div class="setting-row">
                        <div><strong>Weekly Limit</strong></div>
                        <div style="display:flex;align-items:center;gap:10px">
                            <span id="weeklyLimitDisplay">{{ (int) $offer->weekly_limit }} hrs / week</span>
                            <button type="button" class="link-button" onclick="document.getElementById('weeklyLimitEdit').style.display='flex';this.style.display='none'" style="font-size:13px">Edit</button>
                            <form id="weeklyLimitEdit" method="post" action="{{ route('workspace.project.update-weekly-limit') }}" style="display:none;align-items:center;gap:8px">
                                @csrf
                                <input type="hidden" name="offer_id" value="{{ $offer->id }}">
                                <input type="number" name="weekly_limit" value="{{ (int) $offer->weekly_limit }}" min="1" max="168" style="width:70px;padding:6px 10px;border:1px solid var(--line);border-radius:8px;font:inherit">
                                <span class="muted small">hrs / week</span>
                                <button type="submit" class="button button-primary button-compact">Save</button>
                            </form>
                        </div>
                    </div>
                    <div class="setting-row">
                        <div><strong>Manual time</strong></div>
                        <div>{{ $offer->manual_time ? 'Allowed' : 'Not allowed' }}</div>
                    </div>
                    <div class="setting-row">
                        <div><strong>Start Date</strong></div>
                        <div>{{ optional($offer->activated_at)->format('M j, Y') ?: optional($offer->created_at)->format('M j, Y') }}</div>
                    </div>
                    <div class="setting-row">
                        <div><strong>Hired By</strong></div>
                        <div>{{ $project->user?->name ?? '—' }}</div>
                    </div>
                    <div class="setting-row">
                        <div><strong>Contract ID</strong></div>
                        <div>{{ $offer->id }}</div>
                    </div>
                </div>

                <div class="separator"></div>

                <h3 style="font-size:24px;letter-spacing:-.03em;margin:0 0 14px">Description of Work</h3>
                <p style="color:var(--muted);line-height:1.7">{{ $project->description ?: 'No description provided.' }}</p>
            </div>
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
            @if ($unpaidAmount > 0)
                <form method="post" action="{{ route('workspace.project.pay-now') }}" style="width:100%">
                    @csrf
                    <input type="hidden" name="offer_id" value="{{ $offer->id }}">
                    <button class="button button-primary" type="submit" style="width:100%">Pay now — ${{ number_format((float) $unpaidAmount, 2) }}</button>
                </form>
            @else
                <button class="button button-primary" type="button" style="width:100%;opacity:.5;cursor:default" disabled>No outstanding balance</button>
            @endif
            <form method="post" action="{{ route('workspace.project.close') }}" style="width:100%">
                @csrf
                <input type="hidden" name="offer_id" value="{{ $offer->id }}">
                <button class="button button-secondary" style="width:100%" type="submit">End Contract</button>
            </form>
        </aside>
    </div>
</div>

{{-- Pay Bonus Modal --}}
<div id="bonusModal" class="modal-overlay" style="display:none">
    <div class="modal-card">
        <button class="modal-close" type="button" onclick="document.getElementById('bonusModal').style.display='none'">&times;</button>
        <div class="modal-copy">
            <h2>Pay a bonus</h2>
            <p>Send a one-time bonus payment to {{ $offer->freelancer_display_name }}.</p>
        </div>
        <form method="post" action="{{ route('workspace.project.pay-bonus') }}" style="margin-top:20px">
            @csrf
            <input type="hidden" name="offer_id" value="{{ $offer->id }}">
            <div style="margin-bottom:16px">
                <label style="display:block;font-weight:600;margin-bottom:6px">Amount ($)</label>
                <input type="number" name="amount" min="1" step="0.01" required placeholder="e.g. 100.00" style="width:100%;padding:12px 14px;border:1px solid var(--line);border-radius:10px;font:inherit;font-size:16px">
            </div>
            <div style="margin-bottom:20px">
                <label style="display:block;font-weight:600;margin-bottom:6px">Note (optional)</label>
                <textarea name="note" rows="3" placeholder="What is this bonus for?" style="width:100%;padding:12px 14px;border:1px solid var(--line);border-radius:10px;font:inherit;font-size:14px;resize:vertical"></textarea>
            </div>
            <button type="submit" class="button button-primary" style="width:100%">Send bonus payment</button>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('.tab-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn').forEach(function(b) {
            b.style.borderBottomColor = 'transparent';
            b.style.color = 'var(--muted)';
            b.classList.remove('active');
        });
        btn.style.borderBottomColor = 'var(--primary)';
        btn.style.color = 'var(--primary)';
        btn.classList.add('active');

        document.querySelectorAll('.tab-panel').forEach(function(p) { p.style.display = 'none'; });
        document.getElementById('tab-' + btn.dataset.tab).style.display = 'block';
    });
});
</script>
@endsection
