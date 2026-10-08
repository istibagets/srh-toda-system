<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Account - SRH LINK-TODA</title>
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

        #reg-form {
            width: 100%;
            height: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
        .form-area > .step-motivate,
        .form-area > .step-panel,
        .form-area > .signin-row {
            max-width: 620px;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
        }

        /* ── Mobile Phone Fullscreen Responsive ── */
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
                box-sizing: border-box;
                overflow-y: auto;
            }
            .shell {
                min-height: auto;
                height: auto;
                width: 100%;
                max-width: 100%;
                border-radius: 0;
                box-shadow: none;
                border: none;
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
            .form-area > .step-motivate,
            .form-area > .step-panel,
            .form-area > .signin-row {
                max-width: 100%;
            }
            .topbar { margin-bottom: 1rem; }
            .progress-wrap { margin-bottom: 1.25rem; }
            .step-title { font-size: 1.45rem; margin-bottom: 0.4rem; font-weight: 900; }
            .step-sub { font-size: 0.9rem; line-height: 1.5; margin-bottom: 1.5rem; }
            .role-grid { margin-bottom: 1.5rem; gap: 0.85rem; }
            .role-card { padding: 1.35rem 1rem; gap: 0.5rem; min-height: 140px; border-radius: 16px; }
            .role-icon-wrap { width: 44px; height: 44px; }
            .btn-continue { height: 52px; font-size: 1rem; border-radius: 14px; margin-top: 1.25rem; }
            .signin-row { margin-top: 1.5rem; }
            .field-row { gap: 0.75rem; }
            .f-group { margin-bottom: 0.85rem; }
            .f-input { height: 50px; font-size: 0.95rem; border-radius: 12px; }
        }

        /* ── Top bar ── */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            flex-shrink: 0;
        }
        .back-btn {
            display: inline-flex; align-items: center; gap: 0.3rem;
            background: none; border: none; cursor: pointer;
            font-family: inherit; font-size: 0.82rem; font-weight: 600; color: #999;
            padding: 0; transition: color 0.15s; text-decoration: none;
        }
        .back-btn:hover { color: #2563eb; }
        .step-counter { font-size: 0.75rem; font-weight: 600; color: #bbb; }

        /* ── Progress ── */
        .progress-wrap { margin-bottom: 1.75rem; flex-shrink: 0; }
        .progress-row  { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.5rem; }
        .progress-lbl  { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #bbb; }
        .progress-pct  { font-size: 0.78rem; font-weight: 800; color: #1a1a1a; }
        .progress-track { width: 100%; height: 5px; background: #f0f0f0; border-radius: 99px; overflow: hidden; margin-bottom: 0.9rem; }
        .progress-fill  { height: 100%; border-radius: 99px; background: #2563eb; transition: width 0.5s cubic-bezier(0.16,1,0.3,1); }
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
        .step-dot.active .dot-circle { border-color: #2563eb; color: #2563eb; }
        .step-dot.active .dot-lbl   { color: #2563eb; font-weight: 700; }

        /* ── Step panels ── */
        .step-panel { display: none; }
        .step-panel.active { display: block; animation: slideIn 0.22s ease; }
        @keyframes slideIn  { from { opacity:0; transform:translateX(16px); } to { opacity:1; transform:translateX(0); } }
        @keyframes slideBack { from { opacity:0; transform:translateX(-16px); } to { opacity:1; transform:translateX(0); } }
        .step-panel.back { animation: slideBack 0.22s ease; }

        .step-title { font-size: 1.3rem; font-weight: 800; color: #1a1a1a; letter-spacing: -0.025em; margin-bottom: 0.3rem; }
        .step-sub   { font-size: 0.85rem; color: #888; margin-bottom: 1.4rem; line-height: 1.5; }

        /* ── Role cards ── */
        .role-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.25rem; }
        .role-card {
            position: relative;
            border: 2px solid #e8e8e8; border-radius: 14px;
            padding: 1.1rem 0.85rem 0.9rem;
            cursor: pointer; background: #fafafa;
            text-align: center;
            transition: border-color 0.15s, background 0.15s, box-shadow 0.15s;
            display: flex; flex-direction: column; align-items: center; gap: 0.45rem;
            -webkit-user-select: none; user-select: none;
        }
        .role-card:hover { border-color: #93c5fd; background: #eff6ff; }
        .role-card input[type="radio"] { position: absolute; opacity: 0; pointer-events: none; }
        .role-card.selected {
            border-color: #2563eb; background: #fff;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }
        .role-icon-wrap {
            width: 44px; height: 44px; border-radius: 12px;
            background: #f0f0f0; display: flex; align-items: center; justify-content: center;
            transition: background 0.15s;
        }
        .role-card.selected .role-icon-wrap { background: #2563eb; }
        .role-card.selected .role-icon-wrap svg { stroke: #fff !important; }
        .role-name { font-size: 0.875rem; font-weight: 700; color: #1a1a1a; }
        .role-desc { font-size: 0.7rem; color: #999; font-weight: 500; }
        .role-check {
            position: absolute; top: 0.5rem; right: 0.55rem;
            width: 17px; height: 17px; border-radius: 50%;
            background: #2563eb; display: none;
            align-items: center; justify-content: center;
        }
        .role-card.selected .role-check { display: flex; }

        /* ── Form fields ── */
        .field { margin-bottom: 0.9rem; }
        .field:last-of-type { margin-bottom: 0; }
        .field label { display: block; font-size: 0.78rem; font-weight: 600; color: #444; margin-bottom: 0.35rem; }
        .field-wrap { position: relative; }
        .field-icon { position:absolute; top:0; bottom:0; left:0.8rem; display:flex; align-items:center; pointer-events:none; color:#c0c0c0; }
        .f-input {
            width: 100%; height: 46px;
            padding: 0 1rem 0 2.5rem;
            border-radius: 10px; border: 1.5px solid #e8e8e8;
            background: #fafafa; font-family: inherit;
            font-size: 0.875rem; font-weight: 500; color: #1a1a1a;
            outline: none; transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
        }
        .f-input:focus { border-color: #2563eb; background: #fff; box-shadow: 0 0 0 3px rgba(37,99,235,0.15); }
        .f-input.err   { border-color: #e33; background: #fff8f8; }
        .field-forgot  { display: flex; justify-content: space-between; align-items: baseline; }

        .err-text { font-size: 0.7rem; color: #e33; font-weight: 600; margin-top: 0.2rem; }

        /* ── Driver notice ── */
        .notice {
            background: #f5f5f7; border-radius: 10px;
            padding: 0.75rem 0.9rem; font-size: 0.78rem;
            color: #888; line-height: 1.55; margin-bottom: 1.1rem;
        }
        .notice strong { color: #1a1a1a; }

        /* ── Upload zones ── */
        .upload-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.65rem; }
        .upload-zone {
            border: 2px dashed #e0e0e0; border-radius: 12px;
            padding: 1.1rem 0.75rem; text-align: center;
            cursor: pointer; background: #fafafa;
            transition: border-color 0.15s, background 0.15s;
            display: block;
        }
        .upload-zone:hover  { border-color: #2563eb; background: #eff6ff; }
        .upload-zone.ready  { border-style: solid; border-color: #2563eb; background: #eff6ff; }
        .upload-zone.bad    { border-style: solid; border-color: #e33; background: #fff8f8; }
        .uz-name { display: block; font-size: 0.72rem; font-weight: 700; color: #444; margin-top: 0.35rem; }
        .uz-hint { display: block; font-size: 0.62rem; color: #bbb; font-weight: 500; margin-top: 0.1rem; }

        /* ── Password hints ── */
        .pwd-hint {
            display: flex; align-items: center; gap: 0.35rem;
            font-size: 0.72rem; font-weight: 600;
            color: #bbb; margin-top: 0.35rem;
            transition: color 0.2s;
        }
        .pwd-hint svg { flex-shrink: 0; transition: stroke 0.2s; }
        .pwd-hint.ok  { color: #16a34a; }
        .pwd-hint.bad { color: #e33;    }

        /* ── Continue button ── */
        .btn-continue {
            width: 100%; height: 50px; border-radius: 12px; border: none;
            background: #2563eb; color: #fff;
            font-family: inherit; font-size: 0.9rem; font-weight: 700;
            cursor: pointer; margin-top: 1.5rem;
            display: flex; align-items: center; justify-content: center; gap: 0.4rem;
            transition: background 0.15s, transform 0.12s, opacity 0.15s;
        }
        .btn-continue:hover { background: #1d4ed8; }
        .btn-continue:active { transform: scale(0.98); }
        .btn-continue:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

        /* ── Step header label ── */
        .step-motivate {
            font-size: 0.72rem;
            font-weight: 700;
            color: #2563eb;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 0.25rem;
            min-height: 1.2em;
            transition: opacity 0.2s;
        }

        /* ── Verify dot (trailing, always grayed) ── */
        .step-dot.verify-dot .dot-circle {
            border-color: #d0d0d0;
            color: #ccc;
            background: #fafafa;
        }
        .step-dot.verify-dot .dot-lbl { color: #ccc; }

        /* ── Bottom link ── */
        .signin-row { text-align: center; font-size: 0.83rem; color: #888; margin-top: 1.25rem; }
        .signin-row a { color: #2563eb; font-weight: 700; text-decoration: none; }
        .signin-row a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="reg-form">
@csrf
<input type="hidden" name="role" id="role-value" value="{{ old('role', 'passenger') }}">

<div class="page">
<div class="shell">

    {{-- ── LEFT INFO PANEL (desktop) ─────────────────────── --}}
    <div class="info-panel">
        <img src="{{ srh_logo_url() }}" alt="{{ \App\Support\SystemSettings::brandName() }}" class="info-logo">
        <div id="info-content">
            <p class="info-step-label" id="info-step-label">Step 1</p>
            <p class="info-step-title" id="info-step-title">Choose your role</p>
            <p class="info-step-desc" id="info-step-desc">Tell us who you are in the SRH TODA community.</p>
        </div>
        <div class="info-steps-list" id="info-steps-list"></div>
    </div>

    {{-- ── RIGHT FORM AREA ────────────────────────────────── --}}
    <div class="form-area">

        {{-- Top bar --}}
        <div class="topbar">
            <a href="{{ route('login') }}" class="back-btn" id="back-el">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
            <span class="step-counter" id="step-counter">Step 1 of 3</span>
        </div>

        {{-- Progress --}}
        <div class="progress-wrap">
            <div class="progress-row">
                <span class="progress-lbl">Your Progress</span>
                <span class="progress-pct" id="pct-lbl">20%</span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" id="prog-fill" style="width:20%;"></div>
            </div>
            <div class="step-dots" id="step-dots-el"></div>
        </div>

        {{-- ── Motivational message (updates per step) ── --}}
        <p class="step-motivate" id="step-motivate">Let's get started!</p>

        {{-- ── STEP 1: Role ──────────────────────────────── --}}
        <div class="step-panel active" id="sp-1">
            <p class="step-title">Who are you?</p>
            <p class="step-sub">Select your role in the SRH LINK-TODA community.</p>

            <div class="role-grid">
                <label class="role-card {{ old('role', 'passenger') !== 'driver' ? 'selected' : '' }}"
                       id="card-passenger" for="rp">
                    <input type="radio" id="rp" name="_role_ui" value="passenger"
                           {{ old('role', 'passenger') !== 'driver' ? 'checked' : '' }}
                           onchange="selectRole('passenger')">
                    <span class="role-check">
                        <svg width="9" height="9" viewBox="0 0 10 10" fill="none">
                            <path d="M2 5l2.5 2.5L8 2.5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <div class="role-icon-wrap">
                        <svg width="20" height="20" fill="none" stroke="#888" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span class="role-name">Passenger</span>
                    <span class="role-desc">Book TODA rides</span>
                </label>

                <label class="role-card {{ old('role') === 'driver' ? 'selected' : '' }}"
                       id="card-driver" for="rd">
                    <input type="radio" id="rd" name="_role_ui" value="driver"
                           {{ old('role') === 'driver' ? 'checked' : '' }}
                           onchange="selectRole('driver')">
                    <span class="role-check">
                        <svg width="9" height="9" viewBox="0 0 10 10" fill="none">
                            <path d="M2 5l2.5 2.5L8 2.5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <div class="role-icon-wrap">
                        <svg width="20" height="20" fill="none" stroke="#888" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <span class="role-name">Driver</span>
                    <span class="role-desc">Operate &amp; earn</span>
                </label>
            </div>

            <button type="button" class="btn-continue" onclick="goNext()">
                Continue
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            <p class="signin-row">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
        </div>

        {{-- ── STEP 2: Basic Info ─────────────────────────── --}}
        <div class="step-panel" id="sp-2">
            <p class="step-title">Your details</p>
            <p class="step-sub">We need a few details to create your account.</p>

            <div class="field">
                <label for="name">Full Name</label>
                <div class="field-wrap">
                    <span class="field-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </span>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" autocomplete="name"
                           class="f-input {{ $errors->has('name') ? 'err' : '' }}" placeholder="Juan Dela Cruz">
                </div>
                <x-input-error :messages="$errors->get('name')" class="err-text" />
            </div>

            <div class="field">
                <label for="email">Email Address</label>
                <div class="field-wrap">
                    <span class="field-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                        </svg>
                    </span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username"
                           class="f-input {{ $errors->has('email') ? 'err' : '' }}" placeholder="name@example.com"
                           oninput="onEmailInput()"
                           onblur="checkField('email', this.value)">
                </div>
                <div class="pwd-hint" id="hint-email-at" style="display:none;"></div>
                <div class="pwd-hint" id="hint-email" style="display:none;"></div>
                <x-input-error :messages="$errors->get('email')" class="err-text" />
            </div>

            <div class="field">
                <label for="phone_number">Mobile Number</label>
                <div class="field-wrap">
                    <span class="field-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </span>
                    <input id="phone_number" type="tel" name="phone_number" value="{{ old('phone_number') }}"
                           class="f-input {{ $errors->has('phone_number') ? 'err' : '' }}" placeholder="09171234567"
                           maxlength="11" inputmode="numeric" pattern="[0-9]*"
                           oninput="this.value = this.value.replace(/[^0-9]/g, ''); onPhoneInput()"
                           onblur="checkField('phone_number', this.value)">
                </div>
                <div class="pwd-hint" id="hint-phone-len" style="display:none;"></div>
                <div class="pwd-hint" id="hint-phone" style="display:none;"></div>
                <x-input-error :messages="$errors->get('phone_number')" class="err-text" />
            </div>

            <button type="button" class="btn-continue" onclick="goNext()">
                Continue
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        {{-- ── STEP 3: Password ───────────────────────────── --}}
        <div class="step-panel" id="sp-3">
            <p class="step-title">Set a password</p>
            <p class="step-sub">Choose a strong password to keep your account secure.</p>

            <div class="field">
                <label for="password">Password</label>
                <div class="field-wrap">
                    <span class="field-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </span>
                    <input id="password" type="password" name="password" autocomplete="new-password"
                           class="f-input {{ $errors->has('password') ? 'err' : '' }}"
                           placeholder="Minimum 8 characters"
                           oninput="onPwdInput()">
                </div>
                {{-- Live hint: shown as user types --}}
                <div class="pwd-hint" id="hint-length">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/>
                    </svg>
                    At least 8 characters
                </div>
                <x-input-error :messages="$errors->get('password')" class="err-text" />
            </div>

            <div class="field">
                <label for="password_confirmation">Confirm Password</label>
                <div class="field-wrap">
                    <span class="field-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </span>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           autocomplete="new-password" class="f-input" placeholder="Re-enter password"
                           oninput="onConfirmInput()">
                </div>
                {{-- Live match hint --}}
                <div class="pwd-hint" id="hint-match" style="display:none;">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" id="hint-match-icon">
                        <circle cx="12" cy="12" r="10"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/>
                    </svg>
                    <span id="hint-match-txt">Passwords must match</span>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="err-text" />
            </div>

            <button type="button" class="btn-continue" id="step3-btn" onclick="step3Action()">
                <span id="step3-txt">Create Account</span>
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        {{-- ── STEP 4: Driver Documents ─────────────────────── --}}
        <div class="step-panel" id="sp-4">
            <p class="step-title">Upload Documents</p>
            <p class="step-sub">Your application will be reviewed by the Admin before activation.</p>

            <div class="notice">
                Required: <strong>MTOP Certificate</strong> and <strong>Driver's License</strong>.<br>
                Accepted files: JPG, PNG, WEBP, HEIC, PDF, DOC, DOCX — max 20MB each.
            </div>

            <div class="field">
                <label for="full_name">Full Name (as on License)</label>
                <div class="field-wrap">
                    <span class="field-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                        </svg>
                    </span>
                    <input id="full_name" type="text" name="full_name" value="{{ old('full_name') }}"
                           class="f-input {{ $errors->has('full_name') ? 'err' : '' }}" placeholder="Juan Dela Cruz">
                </div>
                <x-input-error :messages="$errors->get('full_name')" class="err-text" />
            </div>

            <div class="field">
                <label for="mtop_number">MTOP Body Number</label>
                <div class="field-wrap">
                    <span class="field-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                        </svg>
                    </span>
                    <input id="mtop_number" type="text" name="mtop_number" value="{{ old('mtop_number') }}"
                           class="f-input {{ $errors->has('mtop_number') ? 'err' : '' }}" placeholder="01234"
                           maxlength="6" inputmode="numeric" pattern="[0-9]*"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>
                <x-input-error :messages="$errors->get('mtop_number')" class="err-text" />
            </div>

            <div class="field">
                <label>Documents</label>
                <div class="upload-grid">
                    <label for="mtop_cert" class="upload-zone" id="zone-mtop">
                        <div id="icon-mtop">
                            <svg width="24" height="24" fill="none" stroke="#c0c0c0" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="uz-name" id="uz-mtop">MTOP Certificate</span>
                        <span class="uz-hint">Tap to upload</span>
                        <input id="mtop_cert" type="file" name="mtop_certificate" class="hidden"
                               accept=".jpg,.jpeg,.png,.webp,.heic,.heif,.pdf,.doc,.docx"
                               onchange="handleFile(this,'mtop')">
                    </label>
                    <label for="drv_license" class="upload-zone" id="zone-lic">
                        <div id="icon-lic">
                            <svg width="24" height="24" fill="none" stroke="#c0c0c0" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2"/>
                            </svg>
                        </div>
                        <span class="uz-name" id="uz-lic">Driver's License</span>
                        <span class="uz-hint">Tap to upload</span>
                        <input id="drv_license" type="file" name="drivers_license" class="hidden"
                               accept=".jpg,.jpeg,.png,.webp,.heic,.heif,.pdf,.doc,.docx"
                               onchange="handleFile(this,'lic')">
                    </label>
                </div>
                <x-input-error :messages="$errors->get('mtop_certificate')" class="err-text" />
                <x-input-error :messages="$errors->get('drivers_license')" class="err-text" />
            </div>

            <button type="button" class="btn-continue" id="submit-btn" onclick="submitForm()">
                <span id="submit-txt">Create Account</span>
            </button>
        </div>

    </div>{{-- /form-area --}}
</div>{{-- /shell --}}
</div>{{-- /page --}}
</form>

<script>
(function () {
    // ── State ────────────────────────────────────────────
    let role    = '{{ old('role', 'passenger') }}';
    let current = 1;

    // ── Progress persistence (localStorage) ──────────────
    const STATE_KEY = 'srh_register_progress';
    function saveState() {
        try {
            localStorage.setItem(STATE_KEY, JSON.stringify({
                role: role,
                current: current,
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                phone_number: document.getElementById('phone_number').value,
                full_name: document.getElementById('full_name').value,
                mtop_number: document.getElementById('mtop_number').value,
            }));
        } catch (e) {}
    }
    function clearState() {
        try { localStorage.removeItem(STATE_KEY); } catch (e) {}
    }
    function loadState() {
        try {
            const s = localStorage.getItem(STATE_KEY);
            return s ? JSON.parse(s) : null;
        } catch (e) { return null; }
    }

    // Progress percentages starting at 20% — Verify is always the last step (happens on separate OTP page)
    const PCTS_P = [20, 50, 75, 100]; // passenger: Role, Details, Password, (Verify on next page)
    const PCTS_D = [20, 45, 65, 85, 100]; // driver: Role, Details, Password, Docs, (Verify on next page)
    function getPct(step) {
        const arr = role === 'driver' ? PCTS_D : PCTS_P;
        return arr[step - 1] || 100;
    }

    // Each step has professional header info + sidebar copy
    const STEP_INFO = {
        passenger: [
            { label:'Role',     motivate:'Select Account Type',   title:'Select your role',     desc:'Choose how you will participate in the SRH TODA community.' },
            { label:'Details',  motivate:'Account Details',       title:'Personal Details',     desc:'Provide your basic account information.' },
            { label:'Password', motivate:'Security Setup',        title:'Set a password',       desc:'Choose a strong password to protect your account.' },
        ],
        driver: [
            { label:'Role',     motivate:'Select Account Type',   title:'Select your role',     desc:'Choose how you will participate in the SRH TODA community.' },
            { label:'Details',  motivate:'Account Details',       title:'Personal Details',     desc:'Provide your basic account information.' },
            { label:'Password', motivate:'Security Setup',        title:'Set a password',       desc:'Choose a strong password to protect your account.' },
            { label:'Docs',     motivate:'Driver Credentials',   title:'Upload Documents',     desc:'Submit your required TODA driver credentials.' },
        ],
    };
    // Verify is always appended as a trailing dot (it happens on the OTP page after submit)
    const VERIFY_STEP = { label: 'Verify', motivate: '', title: '', desc: '' };

    // ── Role selection ────────────────────────────────────
    window.selectRole = function (r) {
        role = r;
        document.getElementById('role-value').value = r;
        document.getElementById('card-passenger').classList.toggle('selected', r === 'passenger');
        document.getElementById('card-driver').classList.toggle('selected', r === 'driver');
        document.getElementById('step3-txt').textContent = r === 'driver' ? 'Continue' : 'Create Account';
        updateUI();
        saveState();
    };

    // ── Progress & sidebar ────────────────────────────────
    function updateUI() {
        const steps    = STEP_INFO[role];
        const allDots  = [...steps, VERIFY_STEP]; // Verify dot always appended
        const total    = steps.length;
        const pct      = getPct(current);

        document.getElementById('prog-fill').style.width = pct + '%';
        document.getElementById('pct-lbl').textContent   = pct + '%';
        document.getElementById('step-counter').textContent = `Step ${current} of ${total}`;

        // Motivational message
        const info = steps[current - 1];
        const motivEl = document.getElementById('step-motivate');
        if (motivEl && info) motivEl.textContent = info.motivate;

        // Dots — include the trailing Verify dot
        const dotsEl = document.getElementById('step-dots-el');
        dotsEl.innerHTML = '';
        allDots.forEach((s, i) => {
            const n   = i + 1;
            const isVerify = i === allDots.length - 1; // last dot
            let cls, inner;
            if (isVerify) {
                cls   = 'verify-dot';
                inner = `<svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>`;
            } else {
                cls   = n < current ? 'done' : n === current ? 'active' : '';
                inner = n < current
                    ? `<svg width="11" height="11" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>`
                    : n;
            }
            dotsEl.innerHTML += `<div class="step-dot ${cls}"><div class="dot-circle">${inner}</div><span class="dot-lbl">${s.label}</span></div>`;
        });

        // Left sidebar info
        if (info) {
            const lbl  = document.getElementById('info-step-label');
            const ttl  = document.getElementById('info-step-title');
            const dsc  = document.getElementById('info-step-desc');
            const lst  = document.getElementById('info-steps-list');
            if (lbl)  lbl.textContent = `Step ${current} of ${total}`;
            if (ttl)  ttl.textContent = info.title;
            if (dsc)  dsc.textContent = info.desc;
            if (lst) {
                lst.innerHTML = '';
                [...steps, VERIFY_STEP].forEach((s, i) => {
                    const n   = i + 1;
                    const isV = i === steps.length;
                    const cls = isV ? '' : n < current ? 'done' : n === current ? 'active' : '';
                    const num = isV ? '?' : n < current ? '✓' : n;
                    lst.innerHTML += `<div class="info-step-item ${cls}"><div class="info-step-num">${num}</div>${s.label}</div>`;
                });
            }
        }

        // Back btn
        const backEl = document.getElementById('back-el');
        if (current === 1) {
            backEl.href = '{{ route('login') }}';
            backEl.onclick = null;
        } else {
            backEl.href = '#';
            backEl.onclick = (e) => { e.preventDefault(); goPrev(); };
        }
    }

    // ── Step navigation ───────────────────────────────────
    function showPanel(n, dir) {
        const old = document.getElementById('sp-' + current);
        const nxt = document.getElementById('sp-' + n);
        if (!nxt) return;
        if (old) old.classList.remove('active', 'back');
        current = n;
        nxt.classList.remove('active', 'back');
        void nxt.offsetWidth;
        nxt.classList.add('active');
        if (dir === 'back') nxt.classList.add('back');
        updateUI();
        saveState();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // ── AJAX field uniqueness check ───────────────────────────
    const _fieldErrors = { email: false, phone: false };
    let _timerEmail = null;
    let _timerPhone = null;

    window.onEmailInput = function () {
        const val    = document.getElementById('email').value.trim();
        const atHint = document.getElementById('hint-email-at');
        const input  = document.getElementById('email');
        clearTimeout(_timerEmail);

        if (!val) {
            atHint.style.display = 'none';
            document.getElementById('hint-email').style.display = 'none';
            input.classList.remove('err');
            return;
        }
        if (val.includes('@')) {
            atHint.style.display = 'none';
            input.classList.remove('err');
            _timerEmail = setTimeout(() => {
                checkField('email', val);
            }, 300);
        } else {
            atHint.style.display = 'flex';
            atHint.className = 'pwd-hint bad';
            atHint.innerHTML = `<svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg> Email must include @`;
            input.classList.add('err');
            document.getElementById('hint-email').style.display = 'none';
        }
    };

    window.onPhoneInput = function () {
        const val    = document.getElementById('phone_number').value.trim();
        const hintEl = document.getElementById('hint-phone-len');
        const input  = document.getElementById('phone_number');
        clearTimeout(_timerPhone);

        if (!val) {
            hintEl.style.display = 'none';
            document.getElementById('hint-phone').style.display = 'none';
            input.classList.remove('err');
            return;
        }
        if (val.length === 11) {
            hintEl.style.display = 'none';
            input.classList.remove('err');
            _timerPhone = setTimeout(() => {
                checkField('phone_number', val);
            }, 300);
        } else {
            hintEl.style.display = 'flex';
            hintEl.className = 'pwd-hint bad';
            hintEl.innerHTML = `<svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg> Must be exactly 11 digits`;
            input.classList.add('err');
            document.getElementById('hint-phone').style.display = 'none';
        }
    };

    window.checkField = async function (field, value) {
        if (!value) return;
        const hintId  = field === 'email' ? 'hint-email' : 'hint-phone';
        const inputId = field === 'email' ? 'email'      : 'phone_number';
        const label   = field === 'email' ? 'Email'      : 'Phone number';
        const hintEl  = document.getElementById(hintId);
        const inputEl = document.getElementById(inputId);
        const key     = field === 'email' ? 'email' : 'phone';

        if (field === 'email' && !value.includes('@')) {
            hintEl.style.display = 'none';
            return;
        }
        if (field === 'phone_number' && value.length !== 11) {
            hintEl.style.display = 'none';
            return;
        }

        try {
            const res  = await fetch('{{ route('check.field') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ field, value }),
            });
            const data = await res.json();

            if (!data.available) {
                hintEl.style.display = 'flex';
                hintEl.className = 'pwd-hint bad';
                hintEl.innerHTML = `<svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg> ${label} is already taken`;
                inputEl.classList.add('err');
                _fieldErrors[key] = true;
            } else {
                hintEl.style.display = 'none';
                inputEl.classList.remove('err');
                _fieldErrors[key] = false;
            }
        } catch (e) {
            hintEl.style.display = 'none';
            _fieldErrors[key] = false;
        }
    };

    window.goNext = async function () {
        if (current === 2) {
            const nameVal  = document.getElementById('name').value.trim();
            const emailVal = document.getElementById('email').value.trim();
            const phoneVal = document.getElementById('phone_number').value.trim();

            if (!nameVal)  { document.getElementById('name').focus(); return; }
            if (!emailVal) { document.getElementById('email').focus(); return; }
            onEmailInput();
            if (!emailVal.includes('@')) {
                document.getElementById('email').focus();
                return;
            }
            onPhoneInput();
            if (phoneVal.length !== 11) {
                document.getElementById('phone_number').focus();
                return;
            }
            if (!phoneVal) { document.getElementById('phone_number').focus(); return; }

            // Run AJAX checks if not already validated
            await checkField('email', emailVal);
            await checkField('phone_number', phoneVal);

            // Block if either is taken
            if (_fieldErrors.email || _fieldErrors.phone) return;
        }
        const max = role === 'driver' ? 4 : 3;
        if (current < max) showPanel(current + 1, 'forward');
    };

    window.goPrev = function () {
        if (current > 1) showPanel(current - 1, 'back');
    };


    // ── Live password hint handlers ─────────────────────────
    window.onPwdInput = function () {
        const val     = document.getElementById('password').value;
        const hintEl  = document.getElementById('hint-length');
        const input   = document.getElementById('password');
        if (val.length === 0) {
            hintEl.style.display = 'flex';
            hintEl.className = 'pwd-hint';
            hintEl.querySelector('svg').innerHTML = '<circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/>';
            input.classList.remove('err');
        } else if (val.length < 8) {
            hintEl.style.display = 'flex';
            hintEl.className = 'pwd-hint bad';
            hintEl.querySelector('svg').innerHTML = '<circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/>';
            input.classList.add('err');
        } else {
            hintEl.style.display = 'none';
            input.classList.remove('err');
        }
        // Also re-check confirm if it has a value
        const conf = document.getElementById('password_confirmation').value;
        if (conf.length > 0) onConfirmInput();
    };

    window.onConfirmInput = function () {
        const pwd     = document.getElementById('password').value;
        const conf    = document.getElementById('password_confirmation').value;
        const hintEl  = document.getElementById('hint-match');
        const txtEl   = document.getElementById('hint-match-txt');
        const iconEl  = document.getElementById('hint-match-icon');
        const input   = document.getElementById('password_confirmation');
        if (conf.length === 0) { hintEl.style.display = 'none'; input.classList.remove('err'); return; }
        if (conf === pwd) {
            hintEl.style.display = 'none';
            input.classList.remove('err');
        } else {
            hintEl.style.display = 'flex';
            hintEl.className = 'pwd-hint bad';
            txtEl.textContent = "Passwords don't match";
            iconEl.innerHTML  = '<circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/>';
            input.classList.add('err');
        }
    };

    window.step3Action = function () {
        const pwd   = document.getElementById('password').value;
        const conf  = document.getElementById('password_confirmation').value;
        // Trigger live hints to show errors
        onPwdInput();
        onConfirmInput();
        if (!pwd || pwd.length < 8) {
            document.getElementById('password').focus();
            return;
        }
        if (pwd !== conf) {
            document.getElementById('password_confirmation').focus();
            return;
        }
        if (role === 'driver') {
            showPanel(4, 'forward');
        } else {
            const btn = document.getElementById('step3-btn');
            const txt = document.getElementById('step3-txt');
            btn.disabled = true; txt.textContent = 'Creating Account...';
            clearState();
            document.getElementById('reg-form').submit();
        }
    };

    window.submitForm = function () {
        const fname = document.getElementById('full_name').value.trim();
        const mnum  = document.getElementById('mtop_number').value.trim();
        const mf    = document.getElementById('mtop_cert').files.length;
        const lf    = document.getElementById('drv_license').files.length;
        if (!fname) { document.getElementById('full_name').focus(); return; }
        if (!mnum)  { document.getElementById('mtop_number').focus(); return; }
        if (!mf || !lf) return;
        const btn = document.getElementById('submit-btn');
        const txt = document.getElementById('submit-txt');
        btn.disabled = true; txt.textContent = 'Creating Account...';
        clearState();
        document.getElementById('reg-form').submit();
    };

    // ── File uploads ──────────────────────────────────────
    window.handleFile = function (input, key) {
        const zoneId = key === 'mtop' ? 'zone-mtop' : 'zone-lic';
        const nameId = key === 'mtop' ? 'uz-mtop'   : 'uz-lic';
        const iconId = key === 'mtop' ? 'icon-mtop'  : 'icon-lic';
        const zone   = document.getElementById(zoneId);
        const nameEl = document.getElementById(nameId);
        const iconEl = document.getElementById(iconId);
        const ok     = /\.(jpg|jpeg|png|webp|heic|heif|pdf|doc|docx)$/i;
        const file   = input.files[0];
        zone.classList.remove('ready', 'bad');
        if (!file) return;
        if (!ok.test(file.name) || file.size > 20 * 1024 * 1024) {
            zone.classList.add('bad');
            nameEl.textContent = file.size > 20971520 ? 'Too large (max 20MB)' : 'Invalid format';
            nameEl.style.color = '#e33';
            return;
        }
        zone.classList.add('ready');
        nameEl.textContent = file.name.length > 18 ? file.name.slice(0,16)+'…' : file.name;
        nameEl.style.color = '#2563eb';
        iconEl.innerHTML = `<svg width="24" height="24" fill="none" stroke="#2563eb" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
    };

    // ── Init ──────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        const saved = loadState();

        if (saved && saved.role) {
            // Restore role + field values from localStorage
            role = (saved.role === 'driver') ? 'driver' : 'passenger';
            ['name', 'email', 'phone_number', 'full_name', 'mtop_number'].forEach(function (id) {
                const el = document.getElementById(id);
                if (el && saved[id] != null) el.value = saved[id];
            });
            selectRole(role);
            // Clamp saved step to a valid panel for the role
            const max    = role === 'driver' ? 4 : 3;
            const target = Math.min(Math.max(parseInt(saved.current, 10) || 1, 1), max);
            showPanel(target, 'forward');
        } else {
            selectRole('{{ old('role', 'passenger') }}');
        }

        // Save progress as the user types
        ['name', 'email', 'phone_number', 'full_name', 'mtop_number'].forEach(function (id) {
            const el = document.getElementById(id);
            if (el) el.addEventListener('input', saveState);
        });

        // Jump to right step if server returned errors
        const eDoc  = {{ $errors->has('full_name') || $errors->has('mtop_number') || $errors->has('mtop_certificate') || $errors->has('drivers_license') ? 'true' : 'false' }};
        const ePwd  = {{ $errors->has('password') ? 'true' : 'false' }};
        const eInfo = {{ $errors->has('name') || $errors->has('email') || $errors->has('phone_number') ? 'true' : 'false' }};
        if (eDoc)  showPanel(4, 'forward');
        else if (ePwd)  showPanel(3, 'forward');
        else if (eInfo) showPanel(2, 'forward');
        else if (!saved) updateUI();
    });
})();
</script>
</body>
</html>
