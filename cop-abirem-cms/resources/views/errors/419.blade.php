<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired</title>
    <meta http-equiv="refresh" content="0;url={{ route('login') }}">
    <style>
        body { font-family: sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; background: #f3f4f6; }
        .box { text-align: center; color: #374151; }
        .box p { color: #6b7280; font-size: 0.9rem; margin-top: 0.5rem; }
        a { color: #1e3a5f; font-weight: 600; }
    </style>
</head>
<body>
    <div class="box">
        <p>Your session has expired. Redirecting to sign in…</p>
        <p>Not redirected? <a href="{{ route('login') }}">Click here</a></p>
    </div>
    <script>window.location.replace('{{ route('login') }}');</script>
</body>
</html>
