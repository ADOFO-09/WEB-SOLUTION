<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Verification Code</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:40px 20px;">
    <tr>
        <td align="center">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">

                {{-- Header --}}
                <tr>
                    <td style="background:linear-gradient(135deg,#0f2744 0%,#1e3a5f 50%,#2d5a8a 100%);padding:36px 40px;border-radius:12px 12px 0 0;text-align:center;">
                        <div style="width:64px;height:64px;background:rgba(212,175,55,0.2);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;">
                            <span style="font-size:28px;">🔐</span>
                        </div>
                        <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:700;letter-spacing:-0.3px;">Login Verification</h1>
                        <p style="margin:6px 0 0;color:rgba(255,255,255,0.6);font-size:13px;">
                            {{ \App\Helpers\SettingHelper::churchName() }}
                        </p>
                    </td>
                </tr>

                {{-- Body --}}
                <tr>
                    <td style="background:#ffffff;padding:40px;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;">
                        <p style="margin:0 0 8px;color:#334155;font-size:15px;line-height:1.6;">
                            Someone is signing in to your account. Use the code below to complete your login.
                        </p>
                        <p style="margin:0 0 28px;color:#64748b;font-size:13px;">
                            If you did not attempt to log in, please change your password immediately.
                        </p>

                        {{-- Code box --}}
                        <div style="background:#f8fafc;border:2px dashed #cbd5e1;border-radius:12px;padding:28px;text-align:center;margin-bottom:28px;">
                            <p style="margin:0 0 6px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:0.1em;font-weight:600;">
                                Your Verification Code
                            </p>
                            <div style="font-size:42px;font-weight:800;letter-spacing:14px;color:#1e3a5f;font-family:'Courier New',monospace;padding:8px 0;">
                                {{ $code }}
                            </div>
                            <p style="margin:10px 0 0;color:#94a3b8;font-size:12px;">
                                Expires in {{ $expiryMinutes }} minutes
                            </p>
                        </div>

                        <div style="background:#fef3c7;border-left:4px solid #d97706;border-radius:6px;padding:14px 16px;margin-bottom:0;">
                            <p style="margin:0;color:#92400e;font-size:13px;line-height:1.5;">
                                <strong>Never share this code</strong> with anyone, including church staff or administrators.
                            </p>
                        </div>
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="background:#f8fafc;padding:20px 40px;border:1px solid #e2e8f0;border-top:none;border-radius:0 0 12px 12px;text-align:center;">
                        <p style="margin:0;color:#94a3b8;font-size:12px;line-height:1.6;">
                            This is an automated message from
                            {{ \App\Helpers\SettingHelper::churchName() }}.<br>
                            Please do not reply to this email.
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>
