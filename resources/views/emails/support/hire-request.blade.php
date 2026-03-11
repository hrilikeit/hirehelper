<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Website hiring request</title>
</head>
<body style="font-family:Arial,Helvetica,sans-serif;color:#111827;line-height:1.6">
    <h1 style="margin-bottom:12px">New website hiring request</h1>
    <p>A public hiring request was sent to {{ $supportInbox }}.</p>

    <table cellpadding="8" cellspacing="0" border="1" style="border-collapse:collapse;width:100%;max-width:860px">
        <tr><th align="left">Category</th><td>{{ $hireRequest->category }}</td></tr>
        <tr><th align="left">Project title</th><td>{{ $hireRequest->project_title }}</td></tr>
        <tr><th align="left">Outcome</th><td>{{ $hireRequest->outcome }}</td></tr>
        <tr><th align="left">Timeline</th><td>{{ $hireRequest->timeline }}</td></tr>
        <tr><th align="left">Budget</th><td>{{ $hireRequest->budget }}</td></tr>
        <tr><th align="left">Team</th><td>{{ $hireRequest->team }}</td></tr>
        <tr><th align="left">Name</th><td>{{ $hireRequest->name }}</td></tr>
        <tr><th align="left">Email</th><td>{{ $hireRequest->email }}</td></tr>
        <tr><th align="left">Company</th><td>{{ $hireRequest->company ?: '—' }}</td></tr>
        <tr><th align="left">Website</th><td>{{ $hireRequest->website ?: '—' }}</td></tr>
        <tr><th align="left">Source</th><td>{{ $hireRequest->source ?: '—' }}</td></tr>
        <tr><th align="left">Submitted at</th><td>{{ optional($hireRequest->created_at)->format('Y-m-d H:i:s') }}</td></tr>
        <tr><th align="left">IP address</th><td>{{ $hireRequest->ip_address ?: '—' }}</td></tr>
    </table>

    <h2 style="margin-top:24px;margin-bottom:8px">What needs to change</h2>
    <div style="white-space:pre-line;border:1px solid #e5e7eb;border-radius:12px;padding:16px;background:#f9fafb;max-width:860px">{{ $hireRequest->needs }}</div>

    <h2 style="margin-top:24px;margin-bottom:8px">Extra context</h2>
    <div style="white-space:pre-line;border:1px solid #e5e7eb;border-radius:12px;padding:16px;background:#f9fafb;max-width:860px">{{ $hireRequest->context ?: '—' }}</div>

    <p style="margin-top:20px;color:#4b5563">Any uploaded files are attached to this email when present.</p>
</body>
</html>
