<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pay {{ config('app.name', 'HireHelper') }}</title>
    <style>
        :root {
            --bg: #ffffff;
            --panel: #ffffff;
            --panel-2: #f8f9fb;
            --border: #e2e5ea;
            --text: #1a1a2e;
            --muted: #6b7280;
            --accent: #ffc439;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f3f4f6;
            color: var(--text);
        }
        .wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
        }
        .card {
            width: 100%;
            max-width: 760px;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.08);
        }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255,255,255,0.04);
            color: var(--muted);
            font-size: 13px;
            margin-bottom: 18px;
        }
        h1 {
            margin: 0 0 10px;
            font-size: 32px;
            line-height: 1.1;
        }
        p.lead {
            margin: 0 0 24px;
            color: var(--muted);
            font-size: 16px;
            line-height: 1.6;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin: 24px 0;
        }
        .box {
            background: var(--panel-2);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 18px;
        }
        .label {
            color: var(--muted);
            font-size: 13px;
            margin-bottom: 6px;
        }
        .value {
            font-size: 20px;
            line-height: 1.35;
            word-break: break-word;
        }
        .amount {
            font-size: 40px;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 10px 14px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 18px;
        }
        .status.open { background: rgba(59, 130, 246, 0.08); color: #2563eb; }
        .status.pending { background: rgba(245, 158, 11, 0.08); color: #d97706; }
        .status.paid { background: rgba(34, 197, 94, 0.08); color: #16a34a; }
        .message {
            border-radius: 16px;
            padding: 14px 16px;
            margin-bottom: 16px;
            border: 1px solid transparent;
            line-height: 1.5;
        }
        .message.success { background: rgba(34, 197, 94, 0.12); border-color: rgba(34, 197, 94, 0.28); }
        .message.warning { background: rgba(245, 158, 11, 0.12); border-color: rgba(245, 158, 11, 0.28); }
        .message.error { background: rgba(239, 68, 68, 0.12); border-color: rgba(239, 68, 68, 0.28); }
        .cta {
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        button, .ghost {
            appearance: none;
            border: 0;
            border-radius: 14px;
            padding: 14px 22px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }
        .paypal {
            background: var(--accent);
            color: #111827;
        }
        .ghost {
            background: transparent;
            color: var(--muted);
            border: 1px solid var(--border);
        }
        .foot {
            margin-top: 18px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }
        @media (max-width: 640px) {
            .card { padding: 22px; }
            .grid { grid-template-columns: 1fr; }
            .amount { font-size: 34px; }
            h1 { font-size: 28px; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <div class="eyebrow">One-time payment link</div>

        @if (session('payment_success'))
            <div class="message success">{{ session('payment_success') }}</div>
        @endif

        @if (session('payment_notice'))
            <div class="message warning">{{ session('payment_notice') }}</div>
        @endif

        @if (session('payment_error'))
            <div class="message error">{{ session('payment_error') }}</div>
        @endif

        <div class="status {{ $paymentLink->status }}">Status: {{ ucfirst($paymentLink->status) }}</div>

        <h1>{{ config('app.name', 'HireHelper') }} payment</h1>
        <p class="lead">Use this secure one-time link to pay the freelancer with PayPal.</p>

        <div class="grid">
            <div class="box">
                <div class="label">Amount</div>
                <div class="value amount">${{ number_format((float) $paymentLink->amount, 2) }}</div>
            </div>
            <div class="box">
                <div class="label">Description</div>
                <div class="value">{{ $paymentLink->description }}</div>
            </div>
        </div>

        <div class="cta">
            @if ($paymentLink->status === 'open')
                <form method="POST" action="{{ route('payment-links.paypal.start', $paymentLink) }}">
                    @csrf
                    <button class="paypal" type="submit">Pay with PayPal</button>
                </form>
            @elseif ($paymentLink->status === 'pending')
                <a class="ghost" href="{{ route('payment-links.show', $paymentLink) }}">Refresh page</a>
            @else
                <a class="ghost" href="{{ url('/') }}">Back to home</a>
            @endif
        </div>

        <div class="foot">
            This link can be used once. After the PayPal payment is completed, the link automatically shows as paid in the admin panel.
            @if ($paymentLink->paid_at)
                <br>Paid on {{ $paymentLink->paid_at->format('M j, Y g:i A') }}.
            @endif
        </div>
    </div>
</div>
</body>
</html>
