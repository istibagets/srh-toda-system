<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Email Address - SRH LINK-TODA</title>
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

        /* ── Left info panel (desktop) ── */
        .info-panel {
            display: none;
            flex-direction: column;
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
            .info-panel { display: flex; }
        }

        .info-logo {
            width: 72px; height: 72px; border-radius: 20px;
            object-fit: contain; background: #ffffff;
            padding: 6px; margin-bottom: 2rem;
            box-shadow: 0 4px 14px rgba(0,0,0,0.12);
        }
        .info-step-label {
            font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.1em; color: rgba(255,255,255,0.5);
            margin-bottom: 0.4rem;
        }
        .info-step-title {
            font-size: 1.45rem; font-weight: 800; color: #fff;
            letter-spacing: -0.02em; line-height: 1.3; margin-bottom: 0.6rem;
        }
        .info-step-desc {
            font-size: 0.92rem; color: rgba(255,255,255,0.75);
            line-height: 1.6; font-weight: 400;
        }
        .info-steps-list {
            margin-top: auto;
            display: flex; flex-direction: column; gap: 0.85rem;
        }
        .info-step-item {
            display: flex; align-items: center; gap: 0.75rem;
            font-size: 0.9rem; font-weight: 600;
            color: rgba(255,255,255,0.5);
            transition: color 0.3s;
        }
        .info-step-item.active { color: #fff; font-weight: 700; }
        .info-step-item.done   { color: rgba(255,255,255,0.7); }
        .info-step-num {
            width: 26px; height: 26px; border-radius: 50%;
            border: 1.5px solid rgba(255,255,255,0.3);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem; font-weight: 800;
            color: rgba(255,255,255,0.5);
            flex-shrink: 0;
            transition: all 0.3s;
        }
        .info-step-item.active .info-step-num { border-color: #fff; background: rgba(255,255,255,0.25); color: #fff; }
        .info-step-item.done   .info-step-num { background: rgba(255,255,255,0.2); border-color: transparent; color: rgba(255,255,255,0.9); }

        /* ── Right form area ── */
        .form-area {
            flex: 1 1 auto;
            width: auto;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem 4rem;
            height: 100%;
            overflow-y: auto;
            background: #ffffff;
            box-sizing: border-box;
        }
        .form-area > .topbar,
        .form-area > .progress-wrap,
        .form-area > .otp-panel,
        .form-area > .signin-row {
            max-width: 560px;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
        }

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
            .form-area {
                padding: 0;
                height: auto;
                min-height: 100%;
                width: 100%;
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
                align-items: stretch;
            }
            .form-area > .topbar,
            .form-area > .progress-wrap,
            .form-area > .otp-panel,
            .form-area > .signin-row {
                max-width: 100%;
            }
            .topbar { margin-bottom: 1rem; }
            .progress-wrap { margin-bottom: 1.25rem; }
            .otp-title { font-size: 1.45rem; margin-bottom: 0.4rem; font-weight: 900; }
            .otp-sub { font-size: 0.9rem; line-height: 1.5; margin-bottom: 1.5rem; }
            .btn-verify { height: 52px; font-size: 1rem; border-radius: 14px; margin-top: 1.25rem; }
            .signin-row { margin-top: 1.5rem; }
            .otp-input { width: 44px; height: 54px; font-size: 1.3rem; border-radius: 12px; }
        }

        /* ── Top bar ── */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            flex-shrink: 0;
        }
        .step-counter { font-size: 0.75rem; font-weight: 600; color: #bbb; margin-left: auto; }

        /* ── Progress ── */
        .progress-wrap { margin-bottom: 1.5rem; flex-shrink: 0; }
        .progress-row  { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.5rem; }
        .progress-lbl  { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #bbb; }
        .progress-pct  { font-size: 0.78rem; font-weight: 800; color: #1a1a1a; }
        .progress-track { width: 100%; height: 5px; background: #f0f0f0; border-radius: 99px; overflow: hidden; margin-bottom: 0.9rem; }
        .progress-fill  { height: 100%; border-radius: 99px; background: #2563eb; width: 100%; transition: width 0.5s ease; }
        
        .step-dots { display: flex; gap: 0.5rem; }
        .step-dot  { display: flex; flex-direction: column; align-items: center; gap: 0.25rem; flex: 1; }
        .dot-circle {
            width: 26px; height: 26px; border-radius: 50%;
            border: 1.5px solid #ddd; background: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.62rem; font-weight: 800; color: #ccc;
            transition: all 0.25s;
        }
        .dot-lbl { font-size: 0.58rem; font-weight: 600; color: #ccc; transition: color 0.25s; }
        .step-dot.done   .dot-circle { background: #2563eb; border-color: #2563eb; color: #fff; }
        .step-dot.done   .dot-lbl   { color: #888; }
        .step-dot.active .dot-circle { border-color: #2563eb; color: #2563eb; background: #fff; }
        .step-dot.active .dot-lbl   { color: #2563eb; font-weight: 700; }

        /* ── Step content ── */
        .step-motivate {
            font-size: 0.9rem; font-weight: 800; color: #1a1a1a;
            letter-spacing: -0.01em; margin-bottom: 0.1rem;
        }
        .step-title { font-size: 1.3rem; font-weight: 800; color: #1a1a1a; letter-spacing: -0.025em; margin-bottom: 0.35rem; }
        .step-sub   { font-size: 0.85rem; color: #888; margin-bottom: 1.5rem; line-height: 1.5; }
        .user-email-badge {
            display: inline-block;
            font-weight: 700;
            color: #1a1a1a;
            background: #f5f5f7;
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
            word-break: break-all;
        }

        /* ── 6-Digit OTP Inputs ── */
        .otp-container {
            display: flex;
            gap: 0.5rem;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        .otp-input {
            width: 100%;
            max-width: 48px;
            height: 52px;
            text-align: center;
            font-size: 1.25rem;
            font-weight: 800;
            border-radius: 12px;
            border: 1.5px solid #e8e8e8;
            background: #fafafa;
            color: #1a1a1a;
            font-family: inherit;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s, background 0.15s, transform 0.1s;
        }
        .otp-input:focus {
            border-color: #2563eb;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
            transform: translateY(-1px);
        }

        /* ── Submit button ── */
        .btn-submit {
            width: 100%; height: 50px; border-radius: 12px; border: none;
            background: #2563eb; color: #fff;
            font-family: inherit; font-size: 0.9rem; font-weight: 700;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 0.4rem;
            transition: background 0.15s, transform 0.12s, opacity 0.15s;
        }
        .btn-submit:hover { background: #1d4ed8; }
        .btn-submit:active { transform: scale(0.98); }
        .btn-submit:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

        /* ── Notifications ── */
        .alert-status {
            padding: 0.75rem 0.9rem; border-radius: 10px;
            background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534;
            font-size: 0.78rem; font-weight: 600; margin-bottom: 1.25rem;
        }
        .alert-error {
            padding: 0.75rem 0.9rem; border-radius: 10px;
            background: #fef2f2; border: 1px solid #fecaca; color: #991b1b;
            font-size: 0.78rem; font-weight: 600; margin-bottom: 1.25rem;
        }

        /* ── Footer links ── */
        .footer-links {
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid #f0f0f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.83rem;
            color: #888;
        }
        .resend-btn {
            background: none; border: none; padding: 0;
            font-family: inherit; font-size: 0.83rem; font-weight: 700;
            color: #2563eb; cursor: pointer; text-decoration: underline;
        }
        .resend-btn:hover { color: #1d4ed8; }
        .logout-btn {
            background: none; border: none; padding: 0;
            font-family: inherit; font-size: 0.78rem; font-weight: 500;
            color: #999; cursor: pointer; text-decoration: underline;
        }
        .logout-btn:hover { color: #e33; }
    </style>
</head>
<body>

@php
    $isDriver = ($user->role === 'driver');
    $stepCount = $isDriver ? 5 : 4;
@endphp

<div class="page">
    <div class="shell">

        {{-- ── LEFT INFO PANEL (desktop) ─────────────────────── --}}
        <div class="info-panel">
            <img src="{{ srh_logo_url() }}" alt="{{ \App\Support\SystemSettings::brandName() }}" class="info-logo">
            <div id="info-content">
                <p class="info-step-label">Final Step</p>
                <p class="info-step-title">Verify Email Address</p>
                <p class="info-step-desc">Enter the 6-digit verification code sent to your email to complete registration.</p>
            </div>
            <div class="info-steps-list">
                <div class="info-step-item done"><div class="info-step-num">✓</div>Role</div>
                <div class="info-step-item done"><div class="info-step-num">✓</div>Details</div>
                <div class="info-step-item done"><div class="info-step-num">✓</div>Password</div>
                @if($isDriver)
                    <div class="info-step-item done"><div class="info-step-num">✓</div>Docs</div>
                @endif
                <div class="info-step-item active"><div class="info-step-num">{{ $stepCount }}</div>Verify</div>
            </div>
        </div>

        {{-- ── RIGHT FORM AREA ────────────────────────────────── --}}
        <div class="form-area">

            {{-- Top bar --}}
            <div class="topbar">
                <span class="step-counter">Step {{ $stepCount }} of {{ $stepCount }}</span>
            </div>

            {{-- Progress bar --}}
            <div class="progress-wrap">
                <div class="progress-row">
                    <span class="progress-lbl">Your Progress</span>
                    <span class="progress-pct">100%</span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill" style="width: 100%;"></div>
                </div>
                <div class="step-dots">
                    <div class="step-dot done">
                        <div class="dot-circle">
                            <svg width="11" height="11" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <span class="dot-lbl">Role</span>
                    </div>
                    <div class="step-dot done">
                        <div class="dot-circle">
                            <svg width="11" height="11" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <span class="dot-lbl">Details</span>
                    </div>
                    <div class="step-dot done">
                        <div class="dot-circle">
                            <svg width="11" height="11" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <span class="dot-lbl">Password</span>
                    </div>
                    @if($isDriver)
                    <div class="step-dot done">
                        <div class="dot-circle">
                            <svg width="11" height="11" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <span class="dot-lbl">Docs</span>
                    </div>
                    @endif
                    <div class="step-dot active">
                        <div class="dot-circle">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <span class="dot-lbl">Verify</span>
                    </div>
                </div>
            </div>

            {{-- Headline --}}
            <p class="step-motivate">Final Step!</p>
            <h1 class="step-title">Verify Email Address</h1>
            <p class="step-sub">
                Enter the 6-digit verification code sent to <span class="user-email-badge">{{ $user->email }}</span>
            </p>

            {{-- Session alerts --}}
            @if (session('status'))
                <div class="alert-status">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert-error">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('verification.otp.verify') }}" id="otp-form">
                @csrf

                <div class="otp-container">
                    @for ($i = 0; $i < 6; $i++)
                        <input type="text" 
                               name="otp[]" 
                               maxlength="1" 
                               inputmode="numeric" 
                               pattern="[0-9]*" 
                               required 
                               aria-label="OTP Digit {{ $i + 1 }}"
                               class="otp-input" 
                               id="otp-{{ $i }}"
                               oninput="onOtpInput(this, {{ $i }})"
                               onkeydown="onOtpKeyDown(event, {{ $i }})"
                               onpaste="onOtpPaste(event)">
                    @endfor
                </div>

                <button type="submit" id="verify-btn" class="btn-submit">
                    <span>Verify Email Address</span>
                </button>
            </form>

            {{-- Footer actions --}}
            <div class="footer-links">
                <div>
                    Didn't receive the email code?
                    <form method="POST" action="{{ route('verification.otp.resend') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="resend-btn">Resend Code</button>
                    </form>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">Log out</button>
                </form>
            </div>

        </div>{{-- /form-area --}}
    </div>{{-- /shell --}}
</div>{{-- /page --}}

<script>
    function onOtpInput(input, index) {
        input.value = input.value.replace(/[^0-9]/g, '');
        if (input.value.length === 1 && index < 5) {
            const next = document.getElementById('otp-' + (index + 1));
            if (next) next.focus();
        }
    }

    function onOtpKeyDown(e, index) {
        if (e.key === 'Backspace' && !e.target.value && index > 0) {
            const prev = document.getElementById('otp-' + (index - 1));
            if (prev) {
                prev.focus();
                prev.value = '';
            }
        }
    }

    function onOtpPaste(e) {
        e.preventDefault();
        const data = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').trim();
        if (data.length >= 6) {
            for (let i = 0; i < 6; i++) {
                const input = document.getElementById('otp-' + i);
                if (input) input.value = data[i] || '';
            }
            const last = document.getElementById('otp-5');
            if (last) last.focus();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const firstInput = document.getElementById('otp-0');
        if (firstInput) firstInput.focus();
    });
</script>
</body>
</html>
