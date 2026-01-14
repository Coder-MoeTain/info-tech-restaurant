<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'RMS')</title>
    <style>
        body { margin:0; font-family: 'Inter', system-ui, -apple-system, sans-serif; background: linear-gradient(135deg,#dfe9f3,#f5f7fa); min-height:100vh; }
        .glass { background: rgba(255,255,255,0.25); border:1px solid rgba(255,255,255,0.5); backdrop-filter: blur(16px); box-shadow: 0 10px 30px rgba(0,0,0,0.15); border-radius: 16px; }
        .layout { display:flex; min-height:100vh; }
        .sidebar { width:240px; padding:18px; display:flex; flex-direction:column; gap:12px; position:sticky; top:0; height:100vh; }
        .sidebar a { text-decoration:none; color:#0f172a; font-weight:600; padding:10px 12px; border-radius:12px; display:block; }
        .sidebar a:hover { background: rgba(255,255,255,0.4); }
        .sidebar .logout { margin-top:auto; }
        .content { flex:1; padding:24px; max-width:1200px; margin:0 auto; width:100%; }
        .btn { padding:10px 14px; border-radius:12px; border:1px solid rgba(255,255,255,0.6); background: rgba(255,255,255,0.3); cursor:pointer; font-weight:600; }
        table { width:100%; border-collapse: collapse; margin-top:12px; }
        th, td { padding:10px; text-align:left; }
        th { background: rgba(0,0,0,0.05); }
        tr:nth-child(even) { background: rgba(0,0,0,0.02); }
        form.inline { display:inline; }
        input, select { padding:10px; border-radius:12px; border:1px solid rgba(0,0,0,0.1); width:100%; box-sizing:border-box; background: rgba(255,255,255,0.7); }
        label { font-weight:600; display:block; margin-top:10px; }
        .grid { display:grid; gap:12px; }
    </style>
</head>
<body>
    <div class="layout">
        @auth
        <aside class="glass sidebar">
            <div style="font-weight:700; font-size:18px;">GlassRMS</div>
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('menus.index') }}">Menus</a>
            <a href="{{ route('floors.index') }}">Floors</a>
            <a href="{{ route('tables.index') }}">Tables</a>
            <form method="POST" action="{{ route('logout') }}" class="logout">
                @csrf
                <button class="btn" type="submit" style="width:100%;">Logout</button>
            </form>
        </aside>
        @endauth

        <main class="content">
            @if (session('status'))
                <div class="glass" style="padding:12px; margin-bottom:12px;">
                    {{ session('status') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
