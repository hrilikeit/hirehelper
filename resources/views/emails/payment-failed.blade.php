@extends('emails.layout')

@section('title', 'Payment Failed — HireHelper')

@section('content')
    <h2 style="margin:0 0 16px;font-size:20px;color:#dc2626">Payment failed</h2>

    <p style="margin:0 0 16px;color:#374151">
        Hi {{ $userName }}, we were unable to process your scheduled payment. Please update your payment method to keep your project active.
    </p>

    <div style="background-color:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:16px;margin:16px 0">
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td style="color:#6b7280;font-size:14px;padding-bottom:6px">Project</td>
                <td align="right" style="font-weight:600;color:#1e293b;padding-bottom:6px">{{ $offer->project?->title ?? 'Your project' }}</td>
            </tr>
            <tr>
                <td style="color:#6b7280;font-size:14px;padding-bottom:6px">Freelancer</td>
                <td align="right" style="font-weight:600;color:#1e293b;padding-bottom:6px">{{ $offer->freelancer_display_name }}</td>
            </tr>
            <tr>
                <td style="color:#6b7280;font-size:14px">Amount</td>
                <td align="right" style="font-weight:700;color:#dc2626">{{ $amount }}</td>
            </tr>
        </table>
    </div>

    <p style="margin:16px 0;color:#374151">
        Please check your PayPal account or update your billing method to ensure uninterrupted service.
    </p>

    <table cellpadding="0" cellspacing="0" style="margin:24px 0">
        <tr>
            <td style="background-color:#dc2626;border-radius:8px;padding:12px 28px">
                <a href="{{ $billingUrl }}" style="color:#ffffff;text-decoration:none;font-weight:600;font-size:15px">Update Payment Method</a>
            </td>
        </tr>
    </table>

    <p style="margin:0;font-size:13px;color:#9ca3af">If you believe this is an error, please contact us at <a href="mailto:support@hirehelper.ai" style="color:#2563eb;text-decoration:none">support@hirehelper.ai</a>.</p>
@endsection
