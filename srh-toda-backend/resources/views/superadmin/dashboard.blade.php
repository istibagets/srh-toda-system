<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Superadmin — {{ $pageTitle }}</title>
    <link rel="icon" type="image/png" href="{{ srh_logo_url() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            min-height: 100%;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            -webkit-font-smoothing: antialiased;
        }
        #superadmin-progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            width: 0%;
            background: linear-gradient(90deg, #2563eb, #60a5fa);
            z-index: 99999;
            transition: width 0.2s ease, opacity 0.3s ease;
            pointer-events: none;
            opacity: 0;
        }
        .topbar {
            background: #0f172a;
            color: #f1f5f9;
            position: sticky;
            top: 0;
            z-index: 40;
            border-bottom: 1px solid #1e293b;
        }
        .topbar-inner {
            width: 100%;
            margin: 0;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.9rem;
        }
        .topbar .brand-wrap {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .topbar .brand-img {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #fff;
            padding: 4px;
            object-fit: contain;
            display: block;
        }
        .topbar .brand-text { font-weight: 900; font-size: 0.95rem; letter-spacing: -0.01em; line-height: 1.1; }
        .topbar .brand-sub { font-size: 0.65rem; color: #94a3b8; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; }
        .topbar .spacer { flex: 1; }
        .topbar .logout-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: rgba(148, 163, 184, 0.12);
            border: 1px solid rgba(148, 163, 184, 0.25);
            color: #e2e8f0;
            padding: 0.48rem 0.95rem;
            border-radius: 10px;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: background .12s ease, transform .12s ease, border-color .12s ease, color .12s ease;
        }
        .topbar .logout-btn:hover {
            background: rgba(239, 68, 68, 0.18);
            border-color: rgba(239, 68, 68, 0.35);
            color: #fca5a5;
        }
        .topbar .logout-btn:active { transform: scale(0.96); }

        .menu-toggle-btn {
            display: none;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 9px;
            background: rgba(148, 163, 184, 0.14);
            border: 1px solid rgba(148, 163, 184, 0.25);
            color: #e2e8f0;
            cursor: pointer;
            transition: background .12s ease, transform .12s ease;
            flex-shrink: 0;
        }
        .menu-toggle-btn:hover { background: rgba(148, 163, 184, 0.24); }
        .menu-toggle-btn:active { transform: scale(0.95); }

        .sidebar-backdrop {
            display: none;
        }
        .sidebar-mobile-header {
            display: none;
        }

        /* Desktop: dark sticky sidebar navigation */
        .layout { display: flex; align-items: flex-start; }
        .sidebar {
            width: 258px;
            flex-shrink: 0;
            position: sticky;
            top: 61px;
            height: calc(100vh - 61px);
            overflow-y: auto;
            background: #0f172a;
            border-right: 1px solid #1e293b;
            padding: 1.2rem 0.85rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }
        .sidebar::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-thumb { background: #334155; border-radius: 999px; }
        .side-group { display: flex; flex-direction: column; }
        .side-label {
            font-size: 0.6rem;
            font-weight: 800;
            letter-spacing: 0.13em;
            text-transform: uppercase;
            color: #64748b;
            padding: 0 0.65rem;
            margin-bottom: 0.4rem;
        }
        .side-link {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            color: #94a3b8;
            font-size: 0.82rem;
            font-weight: 600;
            padding: 0.52rem 0.65rem;
            border-radius: 10px;
            margin-bottom: 0.15rem;
            text-decoration: none;
            transition: color .12s ease, background .12s ease;
        }
        .side-link svg { flex-shrink: 0; }
        .side-link:hover { background: rgba(148, 163, 184, 0.1); color: #f1f5f9; }
        .side-link.active {
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: #fff;
            box-shadow: 0 8px 18px -8px rgba(37, 99, 235, 0.7);
        }
        .side-status {
            margin-top: auto;
            background: rgba(148, 163, 184, 0.08);
            border: 1px solid rgba(148, 163, 184, 0.15);
            border-radius: 11px;
            padding: 0.6rem 0.7rem;
            font-size: 0.68rem;
            font-weight: 700;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            line-height: 1.4;
        }
        .side-status .dot { width: 7px; height: 7px; border-radius: 50%; background: #10b981; flex-shrink: 0; }
        .side-status .dot.down { background: #f59e0b; }
        .side-content { flex: 1; min-width: 0; }
        main {
            max-width: 1100px;
            margin: 0 auto;
            padding: 1.5rem 1.25rem 3rem;
            transition: opacity 0.15s ease;
        }
        main.is-loading {
            opacity: 0.4;
            pointer-events: none;
        }
        .toast {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            font-size: 0.82rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
        }
        .toast-error {
            background: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }
        .card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.4rem;
            margin-bottom: 1.25rem;
            box-shadow: none;
        }
        .card h2 {
            font-size: 0.98rem;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 0.25rem;
            letter-spacing: -0.01em;
        }
        .card .card-sub { font-size: 0.76rem; color: #94a3b8; margin-bottom: 1.1rem; font-weight: 500; }
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 0.8rem;
            margin-bottom: 1.25rem;
        }
        .stat {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 1rem 1.1rem;
            box-shadow: none;
        }
        .stat .num { font-size: 1.6rem; font-weight: 900; color: #2563eb; letter-spacing: -0.02em; line-height: 1; }
        .stat .lbl { font-size: 0.68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.06em; margin-top: 0.4rem; }
        .stat .num.na { color: #f59e0b; font-size: 1.1rem; }
        .info-table { width: 100%; border-collapse: collapse; font-size: 0.8rem; }
        .info-table td {
            padding: 0.55rem 0.2rem;
            border-bottom: 1px solid #f1f5f9;
            font-weight: 600;
        }
        .info-table td:first-child { color: #94a3b8; font-weight: 700; width: 46%; }
        .info-table td:last-child { color: #334155; font-weight: 700; }
        .info-table tr:last-child td { border-bottom: none; }
        .tag {
            display: inline-block;
            padding: 2px 9px;
            border-radius: 999px;
            font-size: 0.66rem;
            font-weight: 800;
            letter-spacing: 0.04em;
        }
        .tag-gray { background: #f1f5f9; color: #475569; }
        .tag-blue { background: #dbeafe; color: #1d4ed8; }
        .tag-red { background: #fee2e2; color: #b91c1c; }
        .tag-amber { background: #fef3c7; color: #b45309; }
        .tag-green { background: #d1fae5; color: #047857; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            border: none;
            border-radius: 10px;
            padding: 0.55rem 1rem;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: transform .12s ease, filter .12s ease, box-shadow .12s ease;
        }
        .btn:active { transform: scale(0.96); }
        .btn-primary { background: linear-gradient(135deg, #2563eb, #3b82f6); color: #fff; box-shadow: 0 8px 18px -8px rgba(37, 99, 235, 0.6); }
        .btn-primary:hover { filter: brightness(1.06); }
        .btn-ghost { background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; }
        .btn-ghost:hover { background: #e2e8f0; }
        .btn-danger { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .btn-danger:hover { background: #fecaca; }
        .btn-sm { padding: 0.4rem 0.8rem; font-size: 0.72rem; }
        .field { margin-bottom: 1rem; }
        .field label {
            display: block;
            font-size: 0.72rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 0.4rem;
            letter-spacing: 0.03em;
        }
        .field input[type="text"], .field input[type="password"], .field textarea, .field select {
            width: 100%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 11px;
            padding: 0.68rem 0.85rem;
            font-size: 0.85rem;
            font-weight: 500;
            color: #0f172a;
            outline: none;
            transition: border-color .15s, box-shadow .15s, background .15s;
            font-family: inherit;
        }
        .field textarea { resize: vertical; min-height: 74px; line-height: 1.5; }
        .field input:focus, .field textarea:focus, .field select:focus {
            border-color: #3b82f6;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }
        .field .hint { font-size: 0.68rem; color: #94a3b8; margin-top: 0.3rem; font-weight: 500; }
        .error-text { color: #dc2626; font-size: 0.72rem; font-weight: 700; margin-top: 0.35rem; }
        .row { display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; }
        .logo-preview {
            width: 96px;
            height: 96px;
            border-radius: 18px;
            background: #fff;
            border: 1px solid #e2e8f0;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 16px -8px rgba(15, 23, 42, 0.18);
        }
        .logo-preview img { width: 100%; height: 100%; object-fit: contain; display: block; }
        .log-toolbar {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        .log-toolbar select {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.5rem 0.7rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: #334155;
            outline: none;
            font-family: inherit;
            cursor: pointer;
        }
        pre.log-body {
            background: #0f172a;
            color: #cbd5e1;
            font-family: 'JetBrains Mono', ui-monospace, monospace;
            font-size: 0.7rem;
            line-height: 1.55;
            padding: 1rem;
            border-radius: 12px;
            max-height: 560px;
            overflow: auto;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .log-meta { display: flex; align-items: center; gap: 0.9rem; flex-wrap: wrap; margin-bottom: 0.7rem; }
        .log-meta span { font-size: 0.72rem; font-weight: 700; color: #64748b; }
        .file-list { display: grid; gap: 0.6rem; }
        .file-row {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 11px;
            padding: 0.7rem 0.9rem;
            text-decoration: none;
            transition: border-color .12s ease, background .12s ease;
        }
        .file-row:hover { border-color: #93c5fd; background: #eff6ff; }
        .file-row .fname { font-weight: 800; font-size: 0.8rem; color: #0f172a; flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .file-row .fmeta { font-size: 0.68rem; color: #94a3b8; font-weight: 600; }
        .avatar-inline {
            width: 30px; height: 30px;
            border-radius: 8px;
            background: #dbeafe;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            color: #1d4ed8;
            font-size: 0.75rem;
        }
        .hint-box {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
            border-radius: 12px;
            padding: 0.85rem 1rem;
            font-size: 0.76rem;
            font-weight: 600;
            line-height: 1.55;
            margin-bottom: 1.25rem;
        }
        .empty { text-align: center; color: #94a3b8; font-size: 0.8rem; font-weight: 600; padding: 2.5rem 0; }
        .search-bar { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1.1rem; }
        .search-bar input[type="text"], .search-bar select {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.5rem 0.7rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: #334155;
            outline: none;
            font-family: inherit;
        }
        .search-bar input[type="text"] { flex: 1; min-width: 180px; }
        .search-bar input:focus, .search-bar select:focus { border-color: #3b82f6; background: #fff; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15); }
        .inline-form { display: inline; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 0.78rem; }
        .data-table th {
            text-align: left;
            padding: 0.6rem 0.6rem;
            border-bottom: 2px solid #e2e8f0;
            color: #64748b;
            font-size: 0.66rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 800;
            white-space: nowrap;
        }
        .data-table td { padding: 0.6rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-weight: 500; color: #334155; }
        .data-table tbody tr:hover td { background: #f8fafc; }
        .data-table td:first-child, .data-table th:first-child { padding-left: 0; }
        .pagination { display: flex; gap: 0.35rem; list-style: none; flex-wrap: wrap; margin-top: 1.1rem; }
        .pagination a, .pagination span {
            display: inline-flex;
            min-width: 30px;
            height: 30px;
            align-items: center;
            justify-content: center;
            padding: 0 0.5rem;
            border-radius: 8px;
            font-size: 0.72rem;
            font-weight: 700;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #334155;
            text-decoration: none;
        }
        .pagination .active span { background: #2563eb; color: #fff; border-color: #2563eb; }
        .pagination .disabled span { opacity: 0.4; }
        .report-row {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 13px;
            padding: 0.9rem 1rem;
            margin-bottom: 0.8rem;
        }
        .report-head { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.55rem; }
        .report-meta { display: flex; gap: 1.1rem; flex-wrap: wrap; font-size: 0.74rem; color: #64748b; margin-bottom: 0.4rem; }
        .report-subject { font-weight: 800; font-size: 0.8rem; color: #0f172a; margin-bottom: 0.25rem; }
        .report-desc { font-size: 0.75rem; color: #475569; line-height: 1.55; margin-bottom: 0.6rem; }
        .report-notes {
            font-size: 0.72rem; color: #92400e; font-weight: 600; background: #fffbeb;
            border: 1px solid #fde68a; border-radius: 9px; padding: 0.45rem 0.7rem; margin-bottom: 0.6rem;
        }
        .report-form { border-top: 1px dashed #e2e8f0; padding-top: 0.7rem; }
        .report-form select, .report-form input[type="text"] {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            padding: 0.45rem 0.6rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #334155;
            outline: none;
            font-family: inherit;
        }
        @media (max-width: 960px) {
            .menu-toggle-btn {
                display: inline-flex;
            }
            .sidebar-backdrop {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.65);
                backdrop-filter: blur(4px);
                -webkit-backdrop-filter: blur(4px);
                z-index: 90;
                opacity: 0;
                pointer-events: none;
                transition: opacity .25s ease;
            }
            .sidebar-backdrop.open {
                opacity: 1;
                pointer-events: auto;
            }
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: 280px;
                max-width: 85vw;
                height: 100vh;
                height: 100dvh;
                z-index: 100;
                transform: translateX(-100%);
                transition: transform .25s cubic-bezier(0.16, 1, 0.3, 1);
                border-right: 1px solid #1e293b;
                box-shadow: none;
                padding: 1.2rem 1rem;
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .sidebar-mobile-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding-bottom: 0.8rem;
                margin-bottom: 0.5rem;
                border-bottom: 1px solid #1e293b;
            }
            .sidebar-mobile-title {
                font-size: 0.85rem;
                font-weight: 800;
                color: #f1f5f9;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }
            .sidebar-close-btn {
                background: rgba(148, 163, 184, 0.12);
                border: 1px solid rgba(148, 163, 184, 0.22);
                color: #94a3b8;
                width: 32px;
                height: 32px;
                border-radius: 8px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: background .12s ease, color .12s ease;
            }
            .sidebar-close-btn:hover {
                background: rgba(148, 163, 184, 0.24);
                color: #fff;
            }
        }
        @media (max-width: 640px) {
            .topbar-inner { padding: 0.65rem 0.85rem; gap: 0.6rem; }
            .topbar .brand-text { font-size: 0.85rem; }
            .topbar .brand-img { width: 32px; height: 32px; border-radius: 9px; }
            .topbar .brand-sub { display: none; }
            .topbar .logout-btn { padding: 0.4rem 0.75rem; font-size: 0.72rem; }
            main { padding: 1.1rem 0.85rem 3rem; }
            .card { padding: 1rem; border-radius: 13px; overflow-x: auto; }
            .data-table, .info-table { min-width: 480px; }
            .stat-grid { grid-template-columns: repeat(2, 1fr); gap: 0.6rem; }
            .stat { padding: 0.85rem 0.95rem; border-radius: 12px; }
            .stat .num { font-size: 1.25rem; }
            pre.log-body { max-height: 380px; }
            .file-row .fmeta { display: none; }
            .log-toolbar select { flex: 1; min-width: 0; }
        }
    </style>
</head>
<body>

<div id="superadmin-progress-bar"></div>

<header class="topbar">
    <div class="topbar-inner">
        <button id="superadmin-menu-toggle" type="button" class="menu-toggle-btn" aria-label="Toggle navigation menu" title="Open Navigation Menu">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <div class="brand-wrap">
            <img class="brand-img" src="{{ srh_logo_url() }}" alt="Logo">
            <div>
                <div class="brand-text">{{ \App\Support\SystemSettings::brandName() }}</div>
                <div class="brand-sub">Superadmin Panel</div>
            </div>
        </div>
        <div class="spacer"></div>
        <form method="POST" action="{{ route('superadmin.logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span>Sign Out</span>
            </button>
        </form>
    </div>
</header>

<div id="sidebar-backdrop" class="sidebar-backdrop"></div>

<div class="layout">
    <aside class="sidebar">
        <div class="sidebar-mobile-header">
            <div class="sidebar-mobile-title">
                <img src="{{ srh_logo_url() }}" alt="Logo" style="width:24px;height:24px;border-radius:6px;background:#fff;padding:2px;object-fit:contain;">
                <span>Navigation</span>
            </div>
            <button id="sidebar-close-btn" type="button" class="sidebar-close-btn" aria-label="Close menu" title="Close Menu">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="side-group">
            <div class="side-label">Core</div>
            <a class="side-link {{ $activeTab === 'overview' ? 'active' : '' }}" href="{{ route('superadmin.dashboard', ['tab' => 'overview']) }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 13h6V4H4v9zm10 7h6v-9h-6v9zM4 21h6v-4H4v4zm10-17v4h6V4h-6z"/></svg>
                Overview
            </a>
        </div>

        <div class="side-group">
            <div class="side-label">Dispatch & Rides</div>
            <a class="side-link {{ $activeTab === 'rides' ? 'active' : '' }}" href="{{ route('superadmin.rides') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                Rides & Trips
            </a>
            <a class="side-link {{ $activeTab === 'reports' ? 'active' : '' }}" href="{{ route('superadmin.reports') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Passenger Reports
            </a>
        </div>

        <div class="side-group">
            <div class="side-label">User & Access</div>
            <a class="side-link {{ $activeTab === 'users' ? 'active' : '' }}" href="{{ route('superadmin.users') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                User Management
            </a>
            <a class="side-link {{ $activeTab === 'sessions' ? 'active' : '' }}" href="{{ route('superadmin.sessions') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Active Sessions
            </a>
            <a class="side-link {{ $activeTab === 'activity' ? 'active' : '' }}" href="{{ route('superadmin.activity') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Audit Activity Trail
            </a>
        </div>

        <div class="side-group">
            <div class="side-label">Communication</div>
            <a class="side-link {{ $activeTab === 'announcements' ? 'active' : '' }}" href="{{ route('superadmin.announcements') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                Announcements
            </a>
            <a class="side-link {{ $activeTab === 'notifications' ? 'active' : '' }}" href="{{ route('superadmin.notifications') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Push Notifications
            </a>
        </div>

        <div class="side-group">
            <div class="side-label">Portal & CMS</div>
            <a class="side-link {{ $activeTab === 'landing_cms' ? 'active' : '' }}" href="{{ route('superadmin.dashboard', ['tab' => 'landing_cms']) }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                Landing Page CMS
            </a>
            <a class="side-link {{ $activeTab === 'branding' ? 'active' : '' }}" href="{{ route('superadmin.dashboard', ['tab' => 'branding']) }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Identity & Logos
            </a>
        </div>

        <div class="side-group">
            <div class="side-label">System & Security</div>
            <a class="side-link {{ $activeTab === 'health' ? 'active' : '' }}" href="{{ route('superadmin.health') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                System Health
            </a>
            <a class="side-link {{ $activeTab === 'logs' ? 'active' : '' }}" href="{{ route('superadmin.dashboard', ['tab' => 'logs']) }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                System Logs
            </a>
            <a class="side-link {{ $activeTab === 'security' ? 'active' : '' }}" href="{{ route('superadmin.dashboard', ['tab' => 'security']) }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Superadmin Credentials
            </a>
        </div>
        @isset($appMaintenanceDown)
            <div class="side-status">
                <span class="dot {{ $appMaintenanceDown ? 'down' : '' }}"></span>
                {{ $appMaintenanceDown ? 'App in maintenance mode' : 'App is live' }}
            </div>
        @endisset
    </aside>
    <div class="side-content">

<main>

@if (session('status'))
    <div class="toast" id="flash-toast">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        {{ session('status') }}
    </div>
@endif
@if ($errors->any())
    <div class="toast toast-error">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        {{ $errors->first() }}
    </div>
@endif

@if ($activeTab === 'overview')

    <div class="stat-grid">
        @foreach ($stats as $label => $value)
            <div class="stat">
                <div class="num {{ $value === -1 ? 'na' : '' }}">{{ $value === -1 ? 'n/a' : number_format($value) }}</div>
                <div class="lbl">{{ $label }}</div>
            </div>
        @endforeach
    </div>

    @if ($appMaintenanceDown)
        <div class="hint-box">Maintenance mode is currently active. Users cannot use the app.</div>
    @endif

    <div class="card">
        <h2>Environment</h2>
        <p class="card-sub">Runtime & configuration</p>
        <table class="info-table">
            <tr><td>PHP Version</td><td>{{ $phpVersion }}</td></tr>
            <tr><td>Laravel Version</td><td>{{ $laravelVersion }}</td></tr>
            <tr><td>App Environment</td><td><span class="tag {{ $appEnv === 'production' ? 'tag-green' : 'tag-amber' }}">{{ $appEnv }}</span></td></tr>
            <tr><td>App URL</td><td>{{ $appUrl }}</td></tr>
            <tr><td>Database Driver</td><td><span class="tag tag-blue">{{ $dbDriver }}</span></td></tr>
            <tr><td>Session Driver</td><td><span class="tag tag-gray">{{ $sessionDriver }}</span></td></tr>
            <tr><td>Cache Store</td><td><span class="tag tag-gray">{{ $cacheStore }}</span></td></tr>
        </table>
    </div>

    <div class="card">
        <h2>Log Files</h2>
        <p class="card-sub">Laravel application & superadmin activity logs</p>
        <div class="file-list">
            @forelse ($logs as $log)
                <a class="file-row" href="{{ route('superadmin.dashboard', ['tab' => 'logs', 'file' => $log['key']]) }}">
                    <span class="avatar-inline">{{ strtoupper(substr($log['key'], 0, 1)) }}</span>
                    <span class="fname">{{ $log['name'] }}</span>
                    <span class="fmeta">{{ $log['size'] }} · {{ \Carbon\Carbon::createFromTimestamp($log['updated'])->diffForHumans() }}</span>
                </a>
            @empty
                <div class="empty">No log files found.</div>
            @endforelse
        </div>
    </div>

@elseif ($activeTab === 'logs')

    <div class="card">
        <h2>System Logs</h2>
        <p class="card-sub">Tail view of the application log files</p>

        <div class="log-toolbar">
            <select id="log-file-select" aria-label="Select log file" onchange="location.href='{{ route('superadmin.dashboard', ['tab' => 'logs']) }}&file=' + this.value + '&lines={{ $lineCount }}';">
                @foreach ($logs as $log)
                    <option value="{{ $log['key'] }}" {{ $selectedLog === $log['key'] ? 'selected' : '' }}>{{ $log['name'] }} ({{ $log['size'] }})</option>
                @endforeach
            </select>
            <select id="log-lines-select" aria-label="Select number of log lines" onchange="location.href='{{ route('superadmin.dashboard', ['tab' => 'logs', 'file' => $selectedLog]) }}&lines=' + this.value;">
                @foreach ([200, 500, 1000, 3000] as $n)
                    <option value="{{ $n }}" {{ $lineCount === $n ? 'selected' : '' }}>Last {{ $n }} lines</option>
                @endforeach
            </select>
            <div class="spacer" style="flex:1"></div>
            <a class="btn btn-ghost btn-sm" href="{{ route('superadmin.dashboard', ['tab' => 'logs', 'file' => $selectedLog, 'lines' => $lineCount]) }}">Refresh</a>
            <a class="btn btn-ghost btn-sm" href="{{ route('superadmin.logs.download', $selectedLog) }}">Download</a>
            <form method="POST" action="{{ route('superadmin.logs.clear') }}" onsubmit="return confirm('Clear this log file? This cannot be undone.');" style="display:inline;">
                @csrf
                <input type="hidden" name="file" value="{{ $selectedLog }}">
                <button type="submit" class="btn btn-danger btn-sm">Clear</button>
            </form>
        </div>

        <div class="log-meta">
            <span class="tag tag-blue">{{ $selectedLog }}.log</span>
            <span>Size: {{ $logSize }}</span>
            @if ($logUpdated)
                <span>Updated: {{ \Carbon\Carbon::createFromTimestamp($logUpdated)->format('Y-m-d H:i:s') }}</span>
            @endif
        </div>

        <pre class="log-body">{{ $logLines ?: '(Empty log file)' }}</pre>
    </div>

@elseif ($activeTab === 'branding')

    <div class="card">
        <h2>Logo</h2>
        <p class="card-sub">Uploaded logo appears in the app navigation, login page, favicon & notifications. Keep it square and at least 256×256px for best results.</p>
        <div class="row" style="margin-bottom:1.1rem;">
            <div class="logo-preview">
                <img src="{{ $logoUrl }}" alt="Current logo">
            </div>
            <div style="flex:1;min-width:200px;">
                <div class="field" style="margin-bottom:0;">
                    <label>Current logo</label>
                    @if ($hasCustomLogo)
                        <span class="tag tag-green">Custom logo active</span>
                    @else
                        <span class="tag tag-gray">Default logo (favicon.png)</span>
                    @endif
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('superadmin.branding.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="field">
                <label for="logo">Replace logo (PNG, JPG or WebP — max 5 MB)</label>
                <input id="logo" type="file" name="logo" accept="image/png,image/jpeg,image/webp">
                <div class="hint">Leaving this empty keeps the current logo.</div>
            </div>
            <div class="row">
                <button type="submit" class="btn btn-primary">Save Logo</button>
                @if ($hasCustomLogo)
                    <button type="submit" class="btn btn-ghost" form="remove-logo-form" onclick="return confirm('Revert to the default logo?');">Remove Custom Logo</button>
                @endif
            </div>
        </form>
        @if ($hasCustomLogo)
            <form id="remove-logo-form" method="POST" action="{{ route('superadmin.branding.remove-logo') }}" style="display:none;">
                @csrf
            </form>
        @endif
    </div>

    <div class="card">
        <h2>UI Texts</h2>
        <p class="card-sub">These replace the default brand texts across the app. Empty fields fall back to defaults.</p>
        <form method="POST" action="{{ route('superadmin.branding.update') }}">
            @csrf
            <div class="field">
                <label for="brand_name">Brand name (login page, titles)</label>
                <input id="brand_name" type="text" name="brand_name" value="{{ $brandName }}" maxlength="60">
            </div>
            <div class="field">
                <label for="nav_brand">Navigation bar brand (top bar)</label>
                <input id="nav_brand" type="text" name="nav_brand" value="{{ $navBrand }}" maxlength="60">
                <div class="hint">Shown next to the logo in the app's sticky navigation.</div>
            </div>
            <div class="field">
                <label for="brand_tagline">Login tagline (left branding panel)</label>
                <textarea id="brand_tagline" name="brand_tagline" maxlength="300">{{ $brandTagline }}</textarea>
            </div>
            <div class="field">
                <label for="welcome_sub">Welcome subtitle (login screen)</label>
                <textarea id="welcome_sub" name="welcome_sub" maxlength="200">{{ $welcomeSub }}</textarea>
                <div class="hint">HTML line breaks (&lt;br&gt;) are allowed.</div>
            </div>
            <button type="submit" class="btn btn-primary">Save Texts</button>
        </form>
    </div>

@elseif ($activeTab === 'security')

    <div class="hint-box">
        Current superadmin username: <strong>{{ $username }}</strong>.<br>
        Security events (logins, logouts, settings changes, log clears) are recorded in <strong>superadmin.log</strong>.
    </div>

    <div class="card">
        <h2>Change Superadmin Credentials</h2>
        <p class="card-sub">You must confirm your current password to make changes.</p>
        <form method="POST" action="{{ route('superadmin.security.update') }}">
            @csrf
            <div class="field">
                <label for="current_password">Current password</label>
                <input id="current_password" type="password" name="current_password" required autocomplete="current-password">
            </div>
            <div class="field">
                <label for="username">New username</label>
                <input id="username" type="text" name="username" value="{{ old('username', $username) }}" required minlength="3" maxlength="30">
                <div class="hint">Letters, numbers, dashes and underscores only.</div>
            </div>
            <div class="field">
                <label for="new_password">New password (leave blank to keep current)</label>
                <input id="new_password" type="password" name="new_password" autocomplete="new-password" minlength="8">
            </div>
            <div class="field">
                <label for="new_password_confirmation">Confirm new password</label>
                <input id="new_password_confirmation" type="password" name="new_password_confirmation" autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-primary">Save Credentials</button>
        </form>
    </div>

@elseif ($activeTab === 'landing_cms')

    @include('superadmin.partials.landing_cms')

@elseif ($activeTab === 'users')

    @include('superadmin.partials.users')

@elseif ($activeTab === 'sessions')

    @include('superadmin.partials.sessions')

@elseif ($activeTab === 'activity')

    @include('superadmin.partials.activity')

@elseif ($activeTab === 'reports')

    @include('superadmin.partials.reports')

@elseif ($activeTab === 'rides')

    @include('superadmin.partials.rides')

@elseif ($activeTab === 'announcements')

    @include('superadmin.partials.announcements')

@elseif ($activeTab === 'notifications')

    @include('superadmin.partials.notifications')

@elseif ($activeTab === 'health')

    @include('superadmin.partials.health')

@endif

</main>
    </div>
</div>

<script>
(function () {
    // Toast auto dismiss
    function setupToast() {
        var t = document.getElementById('flash-toast');
        if (t) {
            setTimeout(function () { t.style.display = 'none'; }, 6000);
        }
    }
    setupToast();

    // Mobile Navigation Drawer
    var menuBtn = document.getElementById('superadmin-menu-toggle');
    var sidebar = document.querySelector('.sidebar');
    var backdrop = document.getElementById('sidebar-backdrop');
    var closeBtn = document.getElementById('sidebar-close-btn');

    function openMobileNav() {
        if (sidebar) sidebar.classList.add('open');
        if (backdrop) backdrop.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileNav() {
        if (sidebar) sidebar.classList.remove('open');
        if (backdrop) backdrop.classList.remove('open');
        document.body.style.overflow = '';
    }

    if (menuBtn) menuBtn.addEventListener('click', openMobileNav);
    if (backdrop) backdrop.addEventListener('click', closeMobileNav);
    if (closeBtn) closeBtn.addEventListener('click', closeMobileNav);

    // ==========================================
    // SUPERADMIN SPA CLIENT-SIDE ROUTING ENGINE
    // ==========================================
    var progressBar = document.getElementById('superadmin-progress-bar');
    var mainContainer = document.querySelector('main');
    var currentUrl = window.location.href;

    function setProgress(percent) {
        if (!progressBar) return;
        if (percent > 0 && percent < 100) {
            progressBar.style.opacity = '1';
            progressBar.style.width = percent + '%';
        } else if (percent >= 100) {
            progressBar.style.width = '100%';
            setTimeout(function () {
                progressBar.style.opacity = '0';
                setTimeout(function () { progressBar.style.width = '0%'; }, 250);
            }, 180);
        }
    }

    function updateActiveSidebar(targetUrl) {
        try {
            var targetObj = new URL(targetUrl, window.location.origin);
            var targetTab = targetObj.searchParams.get('tab');
            var targetPath = targetObj.pathname;

            var links = document.querySelectorAll('.side-link');
            links.forEach(function (link) {
                var linkObj = new URL(link.href, window.location.origin);
                var linkTab = linkObj.searchParams.get('tab');
                var linkPath = linkObj.pathname;

                var isMatch = false;
                if (targetTab && linkTab) {
                    isMatch = (targetTab === linkTab);
                } else if (!targetTab && !linkTab) {
                    isMatch = (targetPath === linkPath);
                }

                if (isMatch) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        } catch (e) {}
    }

    async function navigateTo(url, pushState) {
        if (pushState === undefined) pushState = true;
        if (url === currentUrl && pushState) return;

        if (mainContainer) mainContainer.classList.add('is-loading');
        setProgress(30);

        try {
            var res = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            });

            if (!res.ok) {
                window.location.href = url;
                return;
            }

            var html = await res.text();
            setProgress(75);

            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');
            var newMain = doc.querySelector('main');

            if (newMain && mainContainer) {
                mainContainer.innerHTML = newMain.innerHTML;
                document.title = doc.title || document.title;

                if (pushState) {
                    window.history.pushState({ url: url }, '', url);
                }
                currentUrl = url;
                updateActiveSidebar(url);
                window.scrollTo({ top: 0, behavior: 'instant' });
                setupToast();
            } else {
                window.location.href = url;
            }
        } catch (err) {
            window.location.href = url;
        } finally {
            setProgress(100);
            if (mainContainer) mainContainer.classList.remove('is-loading');
            closeMobileNav();
        }
    }

    // Intercept clicks on links
    document.addEventListener('click', function (e) {
        var link = e.target.closest('a');
        if (!link) return;

        var href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
        if (link.target === '_blank' || link.hasAttribute('download')) return;

        try {
            var urlObj = new URL(link.href, window.location.origin);
            // Only intercept internal /superadmin routes
            if (urlObj.origin === window.location.origin && urlObj.pathname.startsWith('/superadmin')) {
                // Exclude logout and log download routes
                if (urlObj.pathname.includes('logout') || urlObj.pathname.includes('download')) return;

                e.preventDefault();
                navigateTo(link.href, true);
            }
        } catch (err) {}
    });

    // Intercept GET search/filter forms inside main (e.g. users search, rides search, reports filter)
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (form.method && form.method.toUpperCase() === 'GET' && form.closest('main')) {
            try {
                var action = form.getAttribute('action') || window.location.pathname;
                var urlObj = new URL(action, window.location.origin);
                var formData = new FormData(form);

                for (var pair of formData.entries()) {
                    if (pair[1] !== '') {
                        urlObj.searchParams.set(pair[0], pair[1]);
                    } else {
                        urlObj.searchParams.delete(pair[0]);
                    }
                }

                e.preventDefault();
                navigateTo(urlObj.toString(), true);
            } catch (err) {}
        }
    });

    // Handle browser Back and Forward history buttons
    window.addEventListener('popstate', function (e) {
        navigateTo(window.location.href, false);
    });
})();
</script>
</body>
</html>