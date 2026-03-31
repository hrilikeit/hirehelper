<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'HireHelper')</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:Arial,Helvetica,sans-serif;color:#111827;line-height:1.6">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6;padding:32px 16px">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.1)">
                    {{-- Header --}}
                    <tr>
                        <td style="background-color:#1e293b;padding:24px 32px;text-align:center">
                            <a href="{{ config('app.url') }}" style="color:#ffffff;font-size:22px;font-weight:700;text-decoration:none;letter-spacing:0.5px">HireHelper</a>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:32px">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color:#f9fafb;padding:20px 32px;text-align:center;border-top:1px solid #e5e7eb">
                            <p style="margin:0;font-size:13px;color:#6b7280">
                                &copy; {{ date('Y') }} HireHelper.ai &middot; All rights reserved.
                            </p>
                            @hasSection('footer-extra')
                                <p style="margin:8px 0 0;font-size:13px;color:#6b7280">
                                    @yield('footer-extra')
                                </p>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
