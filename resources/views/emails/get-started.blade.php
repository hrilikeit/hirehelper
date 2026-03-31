@extends('emails.layout')

@section('title', 'Welcome to HireHelper')

@section('content')
    <h2 style="margin:0 0 16px;font-size:20px;color:#1e293b">Welcome to HireHelper, {{ $user->name }}!</h2>

    <p style="margin:0 0 16px;color:#374151">
        Your account has been created successfully. HireHelper makes it easy to find and hire top freelancers for your projects.
    </p>

    <h3 style="margin:24px 0 12px;font-size:16px;color:#1e293b">Here's how to get started:</h3>

    <table cellpadding="0" cellspacing="0" width="100%" style="margin:0 0 24px">
        <tr>
            <td style="padding:12px 0;border-bottom:1px solid #f1f5f9">
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="background-color:#eff6ff;border-radius:50%;width:32px;height:32px;text-align:center;font-weight:700;color:#2563eb;font-size:14px;vertical-align:middle">1</td>
                        <td style="padding-left:12px;color:#374151;font-size:15px"><strong>Browse freelancers</strong> — Explore our curated talent pool</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding:12px 0;border-bottom:1px solid #f1f5f9">
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="background-color:#eff6ff;border-radius:50%;width:32px;height:32px;text-align:center;font-weight:700;color:#2563eb;font-size:14px;vertical-align:middle">2</td>
                        <td style="padding-left:12px;color:#374151;font-size:15px"><strong>Create a project</strong> — Describe what you need built</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding:12px 0">
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="background-color:#eff6ff;border-radius:50%;width:32px;height:32px;text-align:center;font-weight:700;color:#2563eb;font-size:14px;vertical-align:middle">3</td>
                        <td style="padding-left:12px;color:#374151;font-size:15px"><strong>Add a payment method</strong> — Secure payments via PayPal</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table cellpadding="0" cellspacing="0" style="margin:24px 0">
        <tr>
            <td style="background-color:#2563eb;border-radius:8px;padding:12px 28px">
                <a href="{{ $dashboardUrl }}" style="color:#ffffff;text-decoration:none;font-weight:600;font-size:15px">Go to Your Dashboard</a>
            </td>
        </tr>
    </table>

    <p style="margin:16px 0 0;color:#374151">
        If you have any questions, just reply to this email or contact us at <a href="mailto:support@hirehelper.ai" style="color:#2563eb;text-decoration:none">support@hirehelper.ai</a>.
    </p>
@endsection
