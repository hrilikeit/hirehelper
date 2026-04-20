@extends('emails.layout')

@section('title', 'New Message — HireHelper')

@section('content')
    <h2 style="margin:0 0 16px;font-size:20px;color:#1e293b">You have a new message</h2>

    <p style="margin:0 0 12px;color:#374151">
        <strong>{{ $projectMessage->sender_name }}</strong> sent a message on project <strong>{{ $projectTitle }}</strong>:
    </p>

    <div style="background-color:#f1f5f9;border-left:4px solid #2563eb;border-radius:8px;padding:16px;margin:16px 0">
        <p style="margin:0;color:#334155;white-space:pre-line">{{ \Illuminate\Support\Str::limit($projectMessage->message, 300) }}</p>
    </div>

    <table cellpadding="0" cellspacing="0" style="margin:24px 0">
        <tr>
            <td style="background-color:#2563eb;border-radius:8px;padding:12px 28px">
                <a href="{{ $messagesUrl }}" style="color:#ffffff;text-decoration:none;font-weight:600;font-size:15px">View Messages</a>
            </td>
        </tr>
    </table>

    <p style="margin:0;font-size:13px;color:#9ca3af">This email was sent because you have message notifications enabled.</p>
@endsection
