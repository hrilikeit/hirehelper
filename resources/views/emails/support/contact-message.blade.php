<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Website contact form</title>
</head>
<body style="font-family:Arial,Helvetica,sans-serif;color:#111827;line-height:1.6">
    <h1 style="margin-bottom:12px">New website contact form submission</h1>
    <p>A public contact form submission was sent to {{ $supportInbox }}.</p>

    <table cellpadding="8" cellspacing="0" border="1" style="border-collapse:collapse;width:100%;max-width:760px">
        <tr><th align="left">Name</th><td>{{ $contactMessage->name }}</td></tr>
        <tr><th align="left">Email</th><td>{{ $contactMessage->email }}</td></tr>
        <tr><th align="left">Company</th><td>{{ $contactMessage->company ?: '—' }}</td></tr>
        <tr><th align="left">Topic</th><td>{{ $contactMessage->topic }}</td></tr>
        <tr><th align="left">Submitted at</th><td>{{ optional($contactMessage->created_at)->format('Y-m-d H:i:s') }}</td></tr>
        <tr><th align="left">IP address</th><td>{{ $contactMessage->ip_address ?: '—' }}</td></tr>
    </table>

    <h2 style="margin-top:24px;margin-bottom:8px">Message</h2>
    <div style="white-space:pre-line;border:1px solid #e5e7eb;border-radius:12px;padding:16px;background:#f9fafb;max-width:760px">{{ $contactMessage->message }}</div>

    <p style="margin-top:20px;color:#4b5563">Any uploaded files are attached to this email when present.</p>
</body>
</html>
