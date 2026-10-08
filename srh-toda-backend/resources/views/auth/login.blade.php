<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Support\SystemSettings::brandName() }}</title>
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#2563eb">
    <link rel="icon" type="image/png" href="{{ srh_logo_url() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ srh_logo_url() }}">
    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #ffffff;
            color: #1d1d1f;
            letter-spacing: normal;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
            overflow-x: hidden;
        }

        /* ── Fullscreen Desktop Layout ── */
        .page {
            min-height: 100vh;
            height: 100vh;
            width: 100%;
            max-width: 100%;
            display: flex;
            align-items: stretch;
            justify-content: stretch;
            padding: 0;
            margin: 0;
            background: #ffffff;
            overflow: hidden;
            box-sizing: border-box;
        }

        /* ── Two-column shell ── */
        .shell {
            display: flex;
            flex-direction: row;
            width: 100%;
            height: 100%;
            background: #ffffff;
            border-radius: 0;
            box-shadow: none;
            overflow: hidden;
            box-sizing: border-box;
        }

        /* ── Left branding panel (desktop only) ── */
        .brand-panel {
            display: none;
            flex-direction: column;
            align-items: flex-start;
            justify-content: space-between;
            flex: 0 0 38%;
            width: 38%;
            max-width: 500px;
            min-width: 350px;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #3b82f6 100%);
            padding: 4rem 3.5rem;
            color: #ffffff;
            box-sizing: border-box;
            height: 100%;
            overflow-y: auto;
        }
        @media (min-width: 768px) {
            .brand-panel { display: flex; }
        }
        .brand-logo {
            width: 72px; height: 72px;
            border-radius: 20px;
            object-fit: contain;
            background: #ffffff;
            padding: 6px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.12);
        }
        .brand-main { flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 2rem 0 1.5rem; }
        .brand-name { font-size: 1.5rem; font-weight: 900; letter-spacing: -0.02em; margin-bottom: 0.5rem; }
        .brand-tagline { font-size: 0.95rem; color: rgba(255,255,255,0.75); line-height: 1.6; }
        .brand-feats { display: flex; flex-direction: column; gap: 0.85rem; margin-top: 2rem; }
        .brand-feat {
            display: flex; align-items: center; gap: 0.75rem;
            font-size: 0.9rem; color: rgba(255,255,255,0.95); font-weight: 600;
        }
        .brand-feat-dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: #60a5fa; flex-shrink: 0;
        }
        .brand-footer { font-size: 0.8rem; color: rgba(255,255,255,0.45); }

        /* ── Right form panel ── */
        .form-panel {
            flex: 1 1 auto;
            width: auto;
            min-width: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 4rem;
            background: #ffffff;
            overflow-y: auto;
            height: 100%;
            box-sizing: border-box;
        }
        .form-inner { width: 100%; max-width: 500px; margin: 0 auto; box-sizing: border-box; }

        @media (max-width: 767px) {
            html, body {
                height: 100%;
                background: #ffffff;
                margin: 0;
                padding: 0;
                overflow-x: hidden;
            }
            .page {
                padding: 1.25rem 1.25rem 2rem;
                min-height: 100dvh;
                height: auto;
                width: 100%;
                align-items: stretch;
                justify-content: flex-start;
                background: #ffffff;
                overflow-y: auto;
                box-sizing: border-box;
            }
            .shell {
                width: 100%;
                max-width: 100%;
                min-height: auto;
                height: auto;
                border-radius: 0;
                box-shadow: none;
                background: #ffffff;
                display: block;
            }
            .form-panel {
                padding: 0;
                height: auto;
                min-height: 100%;
                width: 100%;
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
                align-items: stretch;
                background: #ffffff;
                box-sizing: border-box;
            }
            .form-inner {
                width: 100%;
                max-width: 100%;
            }
            #screen-welcome {
                min-height: calc(100dvh - 3.5rem);
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                text-align: center;
                padding: 1rem 0;
            }
            #screen-login {
                padding-top: 0.25rem;
                width: 100%;
            }
            .welcome-logo-wrap {
                width: 110px;
                height: 110px;
                border-radius: 28px;
                padding: 10px;
                margin-bottom: 1.5rem;
            }
            .welcome-logo-wrap img { width: 90px; height: 90px; }
            .welcome-title { font-size: 1.75rem; margin-bottom: 0.5rem; font-weight: 900; }
            .welcome-sub { font-size: 0.95rem; line-height: 1.55; margin-bottom: 2.25rem; }
            .btn-dark { height: 52px; font-size: 1rem; border-radius: 14px; margin-bottom: 0.85rem; }
            .btn-outline { height: 52px; font-size: 1rem; border-radius: 14px; }
            .form-title { font-size: 1.6rem; font-weight: 800; }
            .f-input { height: 52px; font-size: 0.95rem; border-radius: 13px; }
            .back-btn { margin-bottom: 1.25rem; }
        }

        /* ── Welcome screen ── */
        #screen-welcome {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
        }
        .welcome-logo-wrap {
            display: inline-flex;
            align-items: center; justify-content: center;
            width: 104px; height: 104px;
            border-radius: 26px;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            padding: 8px;
            margin-bottom: 1.25rem;
        }
        .welcome-logo-wrap img { width: 88px; height: 88px; object-fit: contain; }
        .welcome-title { font-size: 1.5rem; font-weight: 800; color: #1a1a1a; letter-spacing: -0.025em; margin-bottom: 0.4rem; }
        .welcome-sub   { font-size: 0.875rem; color: #6b6b6b; line-height: 1.5; margin-bottom: 2rem; }

        /* ── Buttons ── */
        .btn-dark {
            display: flex; align-items: center; justify-content: center;
            width: 100%; height: 50px;
            border-radius: 12px; border: none;
            background: #2563eb; color: #fff;
            font-family: inherit; font-size: 0.9rem; font-weight: 700;
            cursor: pointer; text-decoration: none;
            transition: background 0.15s, transform 0.12s;
            margin-bottom: 0.6rem;
        }
        .btn-dark:hover { background: #1d4ed8; }
        .btn-dark:active { transform: scale(0.98); }
        .btn-dark:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

        .btn-outline {
            display: flex; align-items: center; justify-content: center;
            width: 100%; height: 50px;
            border-radius: 12px; border: 1.5px solid #e0e0e0;
            background: transparent; color: #1a1a1a;
            font-family: inherit; font-size: 0.9rem; font-weight: 600;
            cursor: pointer; text-decoration: none;
            transition: background 0.15s, border-color 0.15s, transform 0.12s;
        }
        .btn-outline:hover { background: #f5f5f7; border-color: #bbb; }
        .btn-outline:active { transform: scale(0.98); }

        /* ── Login form screen ── */
        #screen-login { display: none; }
        .back-btn {
            display: inline-flex; align-items: center; gap: 0.35rem;
            background: none; border: none; cursor: pointer;
            font-family: inherit; font-size: 0.83rem; font-weight: 600; color: #888;
            padding: 0; margin-bottom: 1.5rem;
            transition: color 0.15s;
        }
        .back-btn:hover { color: #2563eb; }

        .form-title { font-size: 1.35rem; font-weight: 800; color: #1a1a1a; letter-spacing: -0.025em; margin-bottom: 0.35rem; }
        .form-sub   { font-size: 0.85rem; color: #888; margin-bottom: 1.5rem; }

        .field { display: flex; flex-direction: column; gap: 0.35rem; margin-bottom: 0.9rem; }
        .field label { font-size: 0.78rem; font-weight: 600; color: #444; }
        .field-wrap { position: relative; }
        .field-icon { position: absolute; top:0; bottom:0; left:0.8rem; display:flex; align-items:center; pointer-events:none; color:#bbb; }

        .f-input {
            width: 100%; height: 46px;
            padding: 0 1rem 0 2.5rem;
            border-radius: 10px; border: 1.5px solid #e8e8e8;
            background: #fafafa;
            font-family: inherit; font-size: 0.875rem; font-weight: 500; color: #1a1a1a;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
        }
        .f-input:focus { border-color: #2563eb; background: #fff; box-shadow: 0 0 0 3px rgba(37,99,235,0.15); }

        .forgot-row { display: flex; align-items: center; justify-content: space-between; }
        .forgot-link { font-size: 0.78rem; font-weight: 600; color: #2563eb; text-decoration: none; }
        .forgot-link:hover { text-decoration: underline; }

        .remember-row { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.4rem; }
        .remember-row label { font-size: 0.82rem; font-weight: 500; color: #888; cursor: pointer; }

        .bottom-link { text-align: center; font-size: 0.83rem; color: #888; margin-top: 1.25rem; }
        .bottom-link a { color: #2563eb; font-weight: 700; text-decoration: none; }
        .bottom-link a:hover { text-decoration: underline; }

        .err-msg { font-size: 0.72rem; color: #e33; font-weight: 600; margin-bottom: 0.75rem; }

        /* ── Transitions ── */
        .fade-in { animation: fadeIn 0.2s ease; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:translateY(0); } }
    </style>
</head>
<body>
<div class="page">
    <div class="shell">

        {{-- Left branding (desktop only) --}}
        <div class="brand-panel">
            <img src="{{ srh_logo_url() }}" alt="{{ \App\Support\SystemSettings::brandName() }}" class="brand-logo">
            <div class="brand-main">
                <p class="brand-name">{{ \App\Support\SystemSettings::brandName() }}</p>
                <p class="brand-tagline">{{ \App\Support\SystemSettings::brandTagline() }}</p>
                <div class="brand-feats">
                    <div class="brand-feat"><span class="brand-feat-dot"></span>Instant TODA Queueing</div>
                    <div class="brand-feat"><span class="brand-feat-dot"></span>Live Trip Tracking</div>
                    <div class="brand-feat"><span class="brand-feat-dot"></span>Verified Operators</div>
                    <div class="brand-feat"><span class="brand-feat-dot"></span>Fair Fare System</div>
                </div>
            </div>
            <p class="brand-footer">© {{ date('Y') }} {{ \App\Support\SystemSettings::brandName() }}</p>
        </div>

        {{-- Right form panel --}}
        <div class="form-panel">
            <div class="form-inner">

                {{-- Welcome screen --}}
                <div id="screen-welcome" class="fade-in">
                    <div class="welcome-logo-wrap">
                        <img src="{{ srh_logo_url() }}" alt="{{ \App\Support\SystemSettings::brandName() }}">
                    </div>
                    <h1 class="welcome-title">{{ \App\Support\SystemSettings::brandName() }}</h1>
                    <p class="welcome-sub">
                        {!! srh_setting('ui.welcome_sub', 'Connect with verified TODA drivers<br>in Santa Rosa Homes, Nueva Ecija') !!}
                    </p>
                    <a href="{{ route('register') }}" class="btn-dark">Get Started</a>
                    <button type="button" onclick="showLogin()" class="btn-outline">I Already Have an Account</button>
                </div>

                {{-- Login form --}}
                <div id="screen-login">
                    <button type="button" class="back-btn" onclick="showWelcome()">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back
                    </button>

                    <h2 class="form-title">Welcome back</h2>
                    <p class="form-sub">Sign in to your account to continue.</p>

                    @if ($errors->any())
                        <p class="err-msg">{{ $errors->first() }}</p>
                    @endif
                    <x-auth-session-status class="err-msg" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" onsubmit="handleLoginSubmit(this)">
                        @csrf

                        <div class="field">
                            <label for="email">Email Address</label>
                            <div class="field-wrap">
                                <span class="field-icon">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                    </svg>
                                </span>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                       autocomplete="username" class="f-input" placeholder="name@example.com">
                            </div>
                        </div>

                        <div class="field">
                            <div class="forgot-row">
                                <label for="password">Password</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot?</a>
                                @endif
                            </div>
                            <div class="field-wrap">
                                <span class="field-icon">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </span>
                                <input id="password" type="password" name="password" required
                                       autocomplete="current-password" class="f-input" placeholder="Password"
                                       style="padding-right:2.8rem;">
                                <button type="button" onclick="togglePwd()" aria-label="Toggle password"
                                        style="position:absolute;top:0;bottom:0;right:0.7rem;background:none;border:none;cursor:pointer;color:#bbb;display:flex;align-items:center;">
                                    <svg id="pw-eye-off" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg id="pw-eye-on" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a8.962 8.962 0 013.682-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M9 9l6 6"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="remember-row">
                            <input id="remember_me" type="checkbox" name="remember" style="width:15px;height:15px;cursor:pointer;accent-color:#2563eb;">
                            <label for="remember_me">Keep me signed in</label>
                        </div>

                        <button type="submit" id="login-btn" class="btn-dark" style="margin-top:0.75rem;">
                            <span id="login-btn-text">Sign In</span>
                        </button>
                    </form>

                    <p class="bottom-link">Don't have an account? <a href="{{ route('register') }}">Get started</a></p>
                </div>

            </div>
        </div>

    </div>
</div>

<script>
    const hasErrors = {{ ($errors->any() || session('status')) ? 'true' : 'false' }};

    function showLogin() {
        document.getElementById('screen-welcome').style.display = 'none';
        const l = document.getElementById('screen-login');
        l.style.display = 'block';
        l.classList.add('fade-in');
    }
    function showWelcome() {
        document.getElementById('screen-login').style.display = 'none';
        const w = document.getElementById('screen-welcome');
        w.style.display = 'flex';
        w.classList.add('fade-in');
    }
    function togglePwd() {
        const p = document.getElementById('password');
        const off = document.getElementById('pw-eye-off');
        const on  = document.getElementById('pw-eye-on');
        if (p.type === 'password') { p.type='text'; off.style.display='none'; on.style.display=''; }
        else { p.type='password'; on.style.display='none'; off.style.display=''; }
    }
    function handleLoginSubmit(form) {
        const btn=document.getElementById('login-btn');
        const txt=document.getElementById('login-btn-text');
        btn.disabled=true; txt.textContent='Signing in...';
    }
    document.addEventListener('DOMContentLoaded', () => {
        if (hasErrors) showLogin();
    });
    window.addEventListener('pageshow', e => { if (e.persisted) window.location.reload(); });
</script>
</body>
</html>