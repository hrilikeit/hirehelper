@extends('emails.layout')

@section('title', 'Contract Active — HireHelper')

@section('content')
    <h2 style="margin:0 0 16px;font-size:20px;color:#1e293b">Your contract is now active!</h2>

    <p style="margin:0 0 16px;color:#374151">
        Hi {{ $userName }}, great news — your contract has been activated and work can begin.
    </p>

    <div style="background-color:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:16px;margin:16px 0">
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
                <td style="color:#6b7280;font-size:14px;padding-bottom:6px">Role</td>
                <td align="right" style="font-weight:600;color:#1e293b;padding-bottom:6px">{{ $offer->role ?? '—' }}</td>
            </tr>
            <tr>
                <td style="color:#6b7280;font-size:14px;padding-bottom:6px">Rate</td>
                <td align="right" style="font-weight:600;color:#1e293b;padding-bottom:6px">${{ $offer->hourly_rate }}/hr</td>
            </tr>
            <tr>
                <td style="color:#6b7280;font-size:14px">Weekly limit</td>
                <td align="right" style="font-weight:600;color:#1e293b">{{ $offer->weekly_limit ?? '—' }} hrs</td>
            </tr>
        </table>
    </div>

    <p style="margin:16px 0;color:#374151">
        You can now communicate with your freelancer through the messaging system and track progress on your dashboard.
    </p>

    <table cellpadding="0" cellspacing="0" style="margin:24px 0">
        <tr>
            <td style="background-color:#2563eb;border-radius:8px;padding:12px 28px">
                <a href="{{ $projectUrl }}" style="color:#ffffff;text-decoration:none;font-weight:600;font-size:15px">View Your Project</a>
            </td>
        </tr>
    </table>
@endsection
