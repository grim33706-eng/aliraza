<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark light">
    <title>Reset your admin password</title>
</head>
<body style="margin:0;padding:0;background:#07080d;font-family:Inter,Segoe UI,Helvetica,Arial,sans-serif;color:#eef0f7;">
    <div style="display:none;max-height:0;overflow:hidden;">Use this link to choose a new password for the {{ $site }} admin panel.</div>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#07080d;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;">
                    <tr>
                        <td style="padding:0 4px 20px;">
                            <table role="presentation" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="width:40px;height:40px;border-radius:12px;background:#c6f45e;background-image:linear-gradient(135deg,#c6f45e,#8f80ff);color:#07080d;font-weight:700;font-size:14px;text-align:center;vertical-align:middle;">RS</td>
                                    <td style="padding-left:12px;font-size:15px;font-weight:600;color:#eef0f7;">{{ $site }}<br><span style="font-size:11px;letter-spacing:2px;color:#8c92a8;font-weight:500;">SITE ADMIN</span></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#11131f;border:1px solid #23263a;border-radius:20px;padding:36px 32px;">
                            <h1 style="margin:0 0 12px;font-size:24px;line-height:1.25;color:#eef0f7;">Reset your password</h1>
                            <p style="margin:0 0 24px;font-size:15px;line-height:1.6;color:#b7bccd;">Hi {{ $name }}, someone (hopefully you) asked to reset the password for the admin panel. Click the button below to choose a new one.</p>
                            <table role="presentation" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="border-radius:12px;background:#c6f45e;">
                                        <a href="{{ $url }}" style="display:inline-block;padding:14px 28px;font-size:15px;font-weight:700;color:#07080d;text-decoration:none;border-radius:12px;">Choose a new password &rarr;</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:24px 0 0;font-size:13px;line-height:1.6;color:#8c92a8;">This link works once and expires in {{ $expires }} minutes. If you didn't ask for this, you can ignore this email — your password won't change.</p>
                            <hr style="border:0;border-top:1px solid #23263a;margin:24px 0;">
                            <p style="margin:0;font-size:12px;line-height:1.6;color:#8c92a8;">Button not working? Paste this link into your browser:<br><a href="{{ $url }}" style="color:#8f80ff;word-break:break-all;">{{ $url }}</a></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 4px 0;font-size:12px;color:#5d6276;text-align:center;">Sent by the {{ $site }} website.</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
