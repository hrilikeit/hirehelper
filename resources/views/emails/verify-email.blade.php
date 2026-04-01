@extends('emails.layout')

@section('title', 'Verify Your Email — HireHelper')

@section('content')
    <h2 style="margin:0 0 16px;font-size:20px;color:#1e293b">Verify your email address</h2>

    <p style="margin:0 0 16px;color:#374151">
        Hi {{ $user->name }}, please verify your email address by clicking the button below.
    </p>

    <table cellpadding="0" cellspacing="0" style="margin:24px 0">
        <tr>
            <td style="background-color:#2563eb;border-radius:8px;padding:12px 28px">
                <a href="{{ $verificationUrl }}" style="color:#ffffff;text-decoration:none;font-weight:600;font-size:15px">Verify Email</a>
            </td>
        </tr>
    </table>

    <p style="margin:16px 0;color:#374151;font-size:14px">
        If you did not create an account, no further action is required.
    </p>

    <p style="margin:16px 0 0;font-size:13px;color:#9ca3af">
        If the button doesn't work, copy and paste this URL into your browser:<br />
        <a href="{{ $verificationUrl }}" style="color:#2563eb;word-break:break-all">{{ $verificationUrl }}</a>
    </p>
@endsection
