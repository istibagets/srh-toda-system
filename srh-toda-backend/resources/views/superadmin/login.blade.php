<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Superadmin Login — {{ \App\Support\SystemSettings::brandName() }}</title>
    <link rel="icon" type="image/png" href="{{ srh_logo_url() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            height: 100%;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            -webkit-font-smoothing: antialiased;
        }
        .page {
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            background:
                radial-gradient(900px 420px at 15% -10%, rgba(37, 99, 235, 0.35), transparent 60%),
                radial-gradient(800px 400px at 110% 110%, rgba(6, 182, 212, 0.18), transparent 60%),
                #0f172a;
        }
        .card {
            width: 100%;
            max-width: 410px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 20px;
            padding: 2.25rem 2rem 2rem;
            box-shadow: 0 24px 70px -20px rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(8px);
        }
        .logo-wrap {
            display: flex;
            justify-content: center;
            margin-bottom: 1.15rem;
        }
        .logo-frame {
            width: 76px;
            height: 76px;
            border-radius: 18px;
            background: #fff;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 12px 30px -8px rgba(37, 99, 235, 0.45);
        }
        .logo-frame img { width: 100%; height: 100%; object-fit: contain; display: block; }
        .badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin: 0 auto 1.6rem;
            width: fit-content;
            padding: 4px 12px;
            border-radius: 999px;
            background: rgba(37, 99, 235, 0.18);
            border: 1px solid rgba(59, 130, 246, 0.35);
            color: #93c5fd;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }
        h1 {
            font-size: 1.35rem;
            font-weight: 900;
            color: #f1f5f9;
            text-align: center;
            letter-spacing: -0.02em;
        }
        .sub {
            text-align: center;
            color: #94a3b8;
            font-size: 0.8rem;
            margin: 0.4rem 0 1.6rem;
            line-height: 1.5;
        }
        .field { margin-bottom: 1rem; }
        .field label {
            display: block;
            font-size: 0.72rem;
            font-weight: 700;
            color: #cbd5e1;
            margin-bottom: 0.4rem;
            letter-spacing: 0.04em;
        }
        .field input {
            width: 100%;
            background: rgba(15, 23, 42, 0.65);
            border: 1px solid rgba(148, 163, 184, 0.3);
            border-radius: 12px;
            padding: 0.72rem 0.9rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #f1f5f9;
            outline: none;
            transition: border-color .15s, box-shadow .15s, background .15s;
        }
        .field input::placeholder { color: #64748b; }
        .field input:focus {
            border-color: #3b82f6;
            background: rgba(15, 23, 42, 0.85);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.18);
        }
        .btn {
            width: 100%;
            margin-top: 0.5rem;
            padding: 0.78rem;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: #fff;
            font-weight: 800;
            font-size: 0.9rem;
            cursor: pointer;
            transition: transform .12s ease, box-shadow .12s ease, filter .12s ease;
            box-shadow: 0 12px 26px -10px rgba(37, 99, 235, 0.6);
        }
        .btn:hover { filter: brightness(1.07); }
        .btn:active { transform: scale(0.97); }
        .error {
            margin-top: 1rem;
            padding: 0.65rem 0.85rem;
            border-radius: 10px;
            background: rgba(220, 38, 38, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #fca5a5;
            font-size: 0.78rem;
            font-weight: 600;
            line-height: 1.45;
        }
        .footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.68rem;
            color: #64748b;
        }
        .footer a { color: #93c5fd; text-decoration: none; font-weight: 600; }
        .footer a:hover { text-decoration: underline; }
        .lock-note {
            margin-top: 1rem;
            padding: 0.65rem 0.85rem;
            border-radius: 10px;
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: #fcd34d;
            font-size: 0.78rem;
            font-weight: 600;
            line-height: 1.45;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="card">
        <div class="logo-wrap">
            <div class="logo-frame">
                <img src="{{ srh_logo_url() }}" alt="Logo">
            </div>
        </div>
        <span class="badge">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            Superadmin
        </span>
        <h1>{{ \App\Support\SystemSettings::brandName() }}</h1>
        <p class="sub">Restricted area. Sign in with your superadmin credentials.</p>

        @if (session('status'))
            <div class="lock-note">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('superadmin.authenticate') }}" autocomplete="off">
            @csrf
            <div class="field">
                <label for="username">Username</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus
                       autocomplete="off" placeholder="admin">
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required
                       autocomplete="current-password" placeholder="••••••••">
            </div>
            <button type="submit" class="btn">Sign In</button>
        </form>

        <p class="footer">
            <a href="{{ route('login') }}">← Back to main site</a>
        </p>
    </div>
</div>
</body>
</html>