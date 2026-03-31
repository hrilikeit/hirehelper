@extends('emails.layout')

@section('title', 'Payment Method Added — HireHelper')

@section('content')
    <h2 style="margin:0 0 16px;font-size:20px;color:#1e293b">Payment method added</h2>

    <p style="margin:0 0 16px;color:#374151">
        Hi {{ $userName }}, your payment method has been successfully added to your account.
    </p>

    <div style="background-color:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:16px;margin:16px 0">
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td style="color:#6b7280;font-size:14px;padding-bottom:6px">Method</td>
                <td align="right" style="font-weight:600;color:#1e293b;padding-bottom:6px">{{ $billingMethod->display_label }}</td>
            </tr>
            @if($billingMethod->provider_email)
            <tr>
                <td style="color:#6b7280;font-size:14px">Account</td>
                <td align="right" style="font-weight:600;color:#1e293b">{{ $billingMethod->provider_email }}</td>
            </tr>
            @endif
        </table>
    </div>

    <p style="margin:16px 0;color:#374151">
        You can now proceed with your project. Payments will be processed automatically based on your contract terms.
    </p>

    <table cellpadding="0" cellspacing="0" style="margin:24px 0">
        <tr>
            <td style="background-color:#2563eb;border-radius:8px;padding:12px 28px">
                <a href="{{ $dashboardUrl }}" style="color:#ffffff;text-decoration:none;font-weight:600;font-size:15px">Go to Dashboard</a>
            </td>
        </tr>
    </table>
@endsection
