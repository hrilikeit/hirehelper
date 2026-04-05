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
                    <a class="cta-link" href="{{ route('workspace.project-active') }}#terms-settings">Edit</a>
                </div>
            </div>
        </section>
    </div>

    <div class="spacer"></div>

    <section class="project-card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
            <div>
                <h3 style="font-size:30px;letter-spacing:-.04em;margin:0">Invoices & Payments</h3>
                <p class="muted" style="margin:4px 0 0">Total paid: ${{ number_format((float) $totalPaid, 2) }}</p>
            </div>
        </div>

        @if ($invoices->isEmpty())
            <div class="muted" style="text-align:center;padding:32px 0">No invoices yet. Invoices are generated automatically after each payment.</div>
        @else
            <div style="overflow-x:auto">
                <table class="data-table" style="width:100%;border-collapse:collapse">
                    <thead>
                        <tr>
                            <th style="text-align:left;padding:10px 12px;border-bottom:1px solid var(--border);font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:var(--muted)">Invoice</th>
                            <th style="text-align:left;padding:10px 12px;border-bottom:1px solid var(--border);font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:var(--muted)">Type</th>
                            <th style="text-align:left;padding:10px 12px;border-bottom:1px solid var(--border);font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:var(--muted)">Description</th>
                            <th style="text-align:left;padding:10px 12px;border-bottom:1px solid var(--border);font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:var(--muted)">Period</th>
                            <th style="text-align:right;padding:10px 12px;border-bottom:1px solid var(--border);font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:var(--muted)">Amount</th>
                            <th style="text-align:center;padding:10px 12px;border-bottom:1px solid var(--border);font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:var(--muted)">Status</th>
                            <th style="text-align:left;padding:10px 12px;border-bottom:1px solid var(--border);font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:var(--muted)">Date</th>
                            <th style="text-align:center;padding:10px 12px;border-bottom:1px solid var(--border);font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:var(--muted)"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $invoice)
                            <tr>
                                <td style="padding:10px 12px;border-bottom:1px solid var(--border);font-size:13px;font-family:monospace">{{ $invoice->invoice_number }}</td>
                                <td style="padding:10px 12px;border-bottom:1px solid var(--border);font-size:13px"><span style="display:inline-block;padding:2px 8px;border-radius:12px;font-size:11px;background:{{ $invoice->type === 'weekly' ? 'var(--jade-50, #e8f5e9)' : 'var(--blue-50, #e3f2fd)' }};color:{{ $invoice->type === 'weekly' ? 'var(--jade-700, #2e7d32)' : 'var(--blue-700, #1565c0)' }}">{{ ucfirst($invoice->type) }}</span></td>
                                <td style="padding:10px 12px;border-bottom:1px solid var(--border);font-size:13px">{{ \Illuminate\Support\Str::limit($invoice->description, 40) }}</td>
                                <td style="padding:10px 12px;border-bottom:1px solid var(--border);font-size:13px;white-space:nowrap">{{ $invoice->period_start ? $invoice->period_start->format('M j') . ' – ' . $invoice->period_end?->format('M j') : '—' }}</td>
                                <td style="padding:10px 12px;border-bottom:1px solid var(--border);font-size:13px;text-align:right;font-weight:600">${{ number_format((float) $invoice->amount, 2) }}</td>
                                <td style="padding:10px 12px;border-bottom:1px solid var(--border);font-size:13px;text-align:center"><span style="display:inline-block;padding:2px 8px;border-radius:12px;font-size:11px;background:{{ $invoice->status === 'paid' ? 'var(--jade-50, #e8f5e9)' : '#fff3e0' }};color:{{ $invoice->status === 'paid' ? 'var(--jade-700, #2e7d32)' : '#e65100' }}">{{ ucfirst($invoice->status) }}</span></td>
                                <td style="padding:10px 12px;border-bottom:1px solid var(--border);font-size:13px;white-space:nowrap">{{ $invoice->created_at->format('M j, Y') }}</td>
                                <td style="padding:10px 12px;border-bottom:1px solid var(--border);font-size:13px;text-align:center"><a href="{{ route('workspace.invoice.view', $invoice->id) }}" target="_blank" style="color:#6d6af8;font-weight:600;text-decoration:none;font-size:12px">View</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection
