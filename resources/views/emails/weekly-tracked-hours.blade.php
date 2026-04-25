@extends('emails.layout')

@section('title', 'Weekly Hours Report — HireHelper')

@section('content')
    <h2 style="margin:0 0 16px;font-size:20px;color:#1e293b">Weekly hours report</h2>

    <p style="margin:0 0 16px;color:#374151">
        Hi {{ $userName }}, here is the tracked hours summary for <strong>{{ $weekLabel }}</strong>.
    </p>

    <div style="background-color:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:16px;margin:16px 0">
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
                <td style="color:#6b7280;font-size:14px;padding-bottom:6px">Hours tracked</td>
                @php
                    $h = (int) floor($hoursTracked);
                    $m = (int) round(($hoursTracked - $h) * 60);
                    $hoursFormatted = sprintf('%02d:%02d', $h, $m);
                @endphp
                <td align="right" style="font-weight:700;color:#1e293b;font-size:18px;padding-bottom:6px">{{ $hoursFormatted }} hrs</td>
            </tr>
            <tr>
                <td style="color:#6b7280;font-size:14px;padding-bottom:6px">Hourly rate</td>
                <td align="right" style="font-weight:600;color:#1e293b;padding-bottom:6px">${{ $offer->hourly_rate }}/hr</td>
            </tr>
            <tr>
                <td style="color:#6b7280;font-size:14px;border-top:1px solid #d1fae5;padding-top:8px">Total</td>
                <td align="right" style="font-weight:700;color:#16a34a;font-size:18px;border-top:1px solid #d1fae5;padding-top:8px">${{ number_format($hoursTracked * (float) $offer->hourly_rate, 2) }}</td>
            </tr>
        </table>
    </div>

    <table cellpadding="0" cellspacing="0" style="margin:24px 0">
        <tr>
            <td style="background-color:#2563eb;border-radius:8px;padding:12px 28px">
                <a href="{{ $reportsUrl }}" style="color:#ffffff;text-decoration:none;font-weight:600;font-size:15px">View Full Report</a>
            </td>
        </tr>
    </table>
@endsection
