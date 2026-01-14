<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body { margin:0; font-family:'Inter',system-ui,-apple-system,sans-serif; background: linear-gradient(135deg,#dfe9f3,#f5f7fa); min-height:100vh; display:flex; align-items:center; justify-content:center; }
        .card { width:360px; padding:24px; border-radius:16px; background: rgba(255,255,255,0.25); border:1px solid rgba(255,255,255,0.6); backdrop-filter: blur(16px); box-shadow:0 10px 30px rgba(0,0,0,0.15); }
        .btn { width:100%; padding:12px; border-radius:12px; border:1px solid rgba(255,255,255,0.6); background: rgba(255,255,255,0.4); cursor:pointer; font-weight:600; }
        input { width:100%; padding:12px; border-radius:12px; border:1px solid rgba(0,0,0,0.1); margin-bottom:12px; background: rgba(255,255,255,0.8); }
        .error { color:#842029; background:#f8d7da; padding:8px; border-radius:8px; margin-bottom:8px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Sign in</h2>
        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <input type="email" name="email" placeholder="Email" value="{{ old('email', 'admin@example.com') }}" required />
            <input type="password" name="password" placeholder="Password" value="{{ old('password', 'password') }}" required />
            <label style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
                <input type="checkbox" name="remember" /> Remember me
            </label>
            <button class="btn" type="submit">Login</button>
        </form>
    </div>
</body>
</html>
