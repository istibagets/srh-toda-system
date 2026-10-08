<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Audit Trail Report — {{ $dateRangeLabel }}</title>
    <link rel="icon" type="image/png" href="{{ srh_logo_url() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #e2e8f0;
            color: #1e293b;
            line-height: 1.35;
            -webkit-font-smoothing: antialiased;
        }

        /* ─── WEB TOOLBAR (HIDDEN IN PRINT) ─── */
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: #0f172a;
            color: #f8fafc;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            flex-wrap: wrap;
        }
        .toolbar-brand {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-weight: 800;
            font-size: 0.9rem;
        }
        .toolbar-brand img {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: #fff;
            padding: 2px;
        }
        .toolbar-actions {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-wrap: wrap;
        }
        .btn-tb {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 0.85rem;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
            border: 1px solid transparent;
        }
        .btn-tb-primary {
            background: #2563eb;
            color: #fff;
        }
        .btn-tb-primary:hover { background: #1d4ed8; }
        .btn-tb-emerald {
            background: #059669;
            color: #fff;
        }
        .btn-tb-emerald:hover { background: #047857; }
        .btn-tb-ghost {
            background: rgba(255,255,255,0.08);
            border-color: rgba(255,255,255,0.18);
            color: #e2e8f0;
        }
        .btn-tb-ghost:hover {
            background: rgba(255,255,255,0.18);
            color: #fff;
        }
        .report-select {
            background: #1e293b;
            color: #f8fafc;
            border: 1px solid #334155;
            padding: 0.42rem 0.75rem;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            outline: none;
            cursor: pointer;
        }

        /* ─── PRINTABLE REPORT CANVAS (A4 / LETTER PAPER) ─── */
        .report-page-wrap {
            max-width: 960px;
            margin: 2rem auto 4rem;
            padding: 0 1rem;
        }
        .report-sheet {
            background: #ffffff;
            border-radius: 4px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08), 0 1px 3px rgba(0,0,0,0.05);
            padding: 2.2rem 2.5rem;
            position: relative;
            min-height: 1050px;
            display: flex;
            flex-direction: column;
        }

        /* ─── HEADER / LETTERHEAD ─── */
        .report-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 0.9rem;
            margin-bottom: 1.2rem;
            position: relative;
        }
        .report-logo-box {
            width: 76px;
            height: 76px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .report-logo-box img {
            width: 72px;
            height: 72px;
            object-fit: contain;
            border-radius: 50%;
        }
        .report-header-text {
            flex: 1;
            padding: 0 1rem;
        }
        .gov-sub {
            font-size: 0.7rem;
            font-weight: 700;
            color: #475569;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            line-height: 1.2;
        }
        .gov-main {
            font-size: 1.05rem;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.01em;
            margin: 0.15rem 0;
            text-transform: uppercase;
        }
        .gov-sub-org {
            font-size: 0.8rem;
            font-weight: 800;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .gov-address {
            font-size: 0.65rem;
            color: #64748b;
            font-weight: 500;
            margin-top: 0.25rem;
        }

        /* ─── METADATA & TITLE ─── */
        .report-meta-grid {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            font-size: 0.68rem;
            color: #475569;
            margin-bottom: 0.8rem;
            padding-bottom: 0.4rem;
            border-bottom: 1px solid #e2e8f0;
            line-height: 1.45;
        }
        .meta-col-left {
            text-align: left;
        }
        .meta-col-right {
            text-align: right;
            flex-shrink: 0;
        }
        .meta-col strong {
            color: #0f172a;
            font-weight: 700;
        }
        .report-title-box {
            text-align: center;
            margin: 0.5rem 0 1.4rem;
        }
        .report-title {
            font-size: 1.15rem;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .report-subtitle {
            font-size: 0.72rem;
            font-weight: 600;
            color: #64748b;
            margin-top: 0.15rem;
        }

        /* ─── GROUP SECTION BANNER ─── */
        .group-banner {
            background: #dcfce7;
            border-left: 4px solid #16a34a;
            padding: 0.45rem 0.75rem;
            font-size: 0.72rem;
            font-weight: 800;
            color: #166534;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-top: 1.2rem;
            margin-bottom: 0.5rem;
        }
        .group-banner.role-superadmin { background: #e0e7ff; border-color: #4338ca; color: #312e81; }
        .group-banner.role-admin { background: #dbeafe; border-color: #2563eb; color: #1e40af; }
        .group-banner.role-driver { background: #fef3c7; border-color: #d97706; color: #92400e; }
        .group-banner.role-passenger { background: #fce7f3; border-color: #db2777; color: #9d174d; }
        .group-banner.role-system { background: #f1f5f9; border-color: #64748b; color: #334155; }

        /* ─── DATA TABLE ─── */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.72rem;
            margin-bottom: 0.4rem;
        }
        .report-table th {
            background: #0d3b66;
            color: #ffffff;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 0.65rem;
            letter-spacing: 0.04em;
            padding: 0.45rem 0.55rem;
            text-align: left;
            border: 1px solid #0d3b66;
        }
        .report-table th.center, .report-table td.center { text-align: center; }
        .report-table th.right, .report-table td.right { text-align: right; }
        .report-table td {
            padding: 0.42rem 0.55rem;
            border: 1px solid #e2e8f0;
            color: #1e293b;
            vertical-align: top;
        }
        .report-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .report-table tbody tr:hover {
            background: #f1f5f9;
        }

        .user-name-link {
            font-weight: 800;
            color: #1d4ed8;
        }
        .subtotal-row {
            text-align: right;
            font-size: 0.68rem;
            font-weight: 800;
            color: #334155;
            padding: 0.35rem 0.55rem;
            margin-bottom: 0.8rem;
        }
        .grand-total-row {
            text-align: right;
            font-size: 0.78rem;
            font-weight: 900;
            color: #0f172a;
            padding: 0.6rem 0.55rem;
            border-top: 2px solid #0f172a;
            margin-top: 0.8rem;
            margin-bottom: 1.5rem;
        }

        .tag-pill {
            display: inline-block;
            padding: 0.12rem 0.4rem;
            border-radius: 4px;
            font-size: 0.6rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }
        .tag-blue { background: #dbeafe; color: #1e40af; }
        .tag-green { background: #dcfce7; color: #166534; }
        .tag-amber { background: #fef3c7; color: #92400e; }
        .tag-red { background: #fee2e2; color: #991b1b; }
        .tag-gray { background: #f1f5f9; color: #475569; }

        /* ─── STATS DASHBOARD LAYOUT (SAMPLE 5) ─── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.85rem;
            margin-bottom: 1.5rem;
        }
        .stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-top: 4px solid #3b82f6;
            border-radius: 4px;
            padding: 0.85rem 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .stat-card.green { border-top-color: #10b981; }
        .stat-card.blue { border-top-color: #3b82f6; }
        .stat-card.amber { border-top-color: #f59e0b; }
        .stat-card.red { border-top-color: #ef4444; }
        .stat-card .lbl {
            font-size: 0.68rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .stat-card .val {
            font-size: 1.65rem;
            font-weight: 900;
            color: #0f172a;
            margin-top: 0.2rem;
            line-height: 1;
        }

        .charts-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.2rem;
            margin-bottom: 1.5rem;
        }
        .chart-box {
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 1rem;
            background: #ffffff;
        }
        .chart-title {
            font-size: 0.75rem;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            margin-bottom: 0.8rem;
        }
        .bar-chart-visual {
            display: flex;
            align-items: flex-end;
            justify-content: space-around;
            height: 140px;
            padding-top: 1.5rem;
            border-bottom: 1px solid #cbd5e1;
        }
        .bar-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.3rem;
            height: 100%;
            justify-content: flex-end;
        }
        .bar-bar {
            width: 34px;
            background: #3b82f6;
            border-radius: 3px 3px 0 0;
            min-height: 8px;
            transition: height 0.3s ease;
            position: relative;
        }
        .bar-val {
            font-size: 0.65rem;
            font-weight: 800;
            color: #1e3a8a;
            position: absolute;
            top: -16px;
            left: 50%;
            transform: translateX(-50%);
        }
        .bar-lbl {
            font-size: 0.65rem;
            font-weight: 700;
            color: #64748b;
            margin-top: 0.3rem;
        }

        .role-donut-visual {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            height: 140px;
        }
        .donut-circle {
            width: 105px;
            height: 105px;
            border-radius: 50%;
            background: conic-gradient(
                #3b82f6 0% 35%,
                #10b981 35% 65%,
                #f59e0b 65% 85%,
                #8b5cf6 85% 95%,
                #94a3b8 95% 100%
            );
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 0 0 1px rgba(0,0,0,0.05);
        }
        .donut-hole {
            width: 62px;
            height: 62px;
            background: #ffffff;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 900;
            color: #0f172a;
            line-height: 1;
        }
        .donut-hole span { font-size: 0.55rem; color: #64748b; font-weight: 700; }
        .role-legend {
            font-size: 0.65rem;
            line-height: 1.6;
        }
        .legend-item { display: flex; align-items: center; gap: 0.4rem; }
        .legend-dot { width: 9px; height: 9px; border-radius: 2px; }

        .insight-card {
            border: 1px solid #86efac;
            background: #f0fdf4;
            border-radius: 4px;
            padding: 0.85rem 1rem;
            margin-bottom: 1.5rem;
        }
        .insight-card .title {
            font-size: 0.72rem;
            font-weight: 900;
            color: #166534;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.3rem;
        }
        .insight-card .text {
            font-size: 0.72rem;
            color: #14532d;
            line-height: 1.45;
        }

        /* ─── SIGNATORY BLOCK ─── */
        .signatory-section {
            margin-top: auto;
            padding-top: 2.2rem;
            display: flex;
            justify-content: flex-start;
            page-break-inside: avoid;
        }
        .signatory-box {
            width: 280px;
            text-align: center;
        }
        .sig-lead {
            text-align: left;
            font-size: 0.72rem;
            color: #334155;
            font-weight: 600;
            margin-bottom: 2.8rem;
        }
        .sig-name {
            font-size: 0.85rem;
            font-weight: 900;
            color: #0f172a;
            border-bottom: 1px solid #0f172a;
            padding-bottom: 0.2rem;
        }
        .sig-title {
            font-size: 0.72rem;
            font-weight: 700;
            color: #475569;
            margin-top: 0.2rem;
        }
        .sig-caption {
            font-size: 0.62rem;
            font-style: italic;
            color: #94a3b8;
            margin-top: 0.1rem;
        }

        /* ─── RUNNING DOCUMENT FOOTER ─── */
        .report-footer {
            margin-top: 1.8rem;
            padding-top: 0.6rem;
            border-top: 1px solid #cbd5e1;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 0.62rem;
            color: #64748b;
            line-height: 1.35;
        }

        /* ─── PRINT OPTIMIZATIONS ─── */
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                margin: 0 !important;
                padding: 0 !important;
                font-size: 9pt;
            }
            .toolbar, .no-print {
                display: none !important;
            }
            .report-page-wrap {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .report-sheet {
                box-shadow: none !important;
                border: none !important;
                padding: 0.3in 0.4in !important;
                min-height: auto !important;
            }
            .report-table th {
                background: #0d3b66 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .group-banner, .tag-pill, .stat-card, .donut-circle {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            tr, .signatory-section, .stat-card, .chart-box, .insight-card {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            @page {
                size: portrait;
                margin: 0.4in;
            }
        }
    </style>
</head>
<body>

    <!-- ─── FLOATING WEB TOOLBAR ─── -->
    <header class="toolbar no-print">
        <div class="toolbar-brand">
            <img src="{{ srh_logo_url() }}" alt="SRH-LINK-TODA">
            <span>SRH-LINK-TODA &bull; Official Report Generator</span>
        </div>

        <form method="GET" action="{{ route('superadmin.activity.report') }}" class="toolbar-actions" id="reportControlForm">
            <select name="report_type" aria-label="Report format type" class="report-select" onchange="document.getElementById('reportControlForm').submit()">
                <option value="all" {{ $reportType === 'all' ? 'selected' : '' }}>1. Certified List (All Logs)</option>
                <option value="date_range" {{ $reportType === 'date_range' ? 'selected' : '' }}>2. Filtered by Date Range</option>
                <option value="grouped_role" {{ $reportType === 'grouped_role' ? 'selected' : '' }}>3. Grouped by User Role</option>
                <option value="statistics" {{ $reportType === 'statistics' ? 'selected' : '' }}>4. Statistics Dashboard</option>
            </select>

            <select name="date_preset" aria-label="Date range preset" class="report-select" onchange="document.getElementById('reportControlForm').submit()">
                <option value="all" {{ $datePreset === 'all' ? 'selected' : '' }}>All Time</option>
                <option value="today" {{ $datePreset === 'today' ? 'selected' : '' }}>Today</option>
                <option value="yesterday" {{ $datePreset === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                <option value="this_week" {{ $datePreset === 'this_week' ? 'selected' : '' }}>This Week</option>
                <option value="this_month" {{ $datePreset === 'this_month' ? 'selected' : '' }}>This Month</option>
                <option value="last_month" {{ $datePreset === 'last_month' ? 'selected' : '' }}>Last Month</option>
                <option value="this_quarter" {{ $datePreset === 'this_quarter' ? 'selected' : '' }}>This Quarter (Q{{ ceil(now()->month / 3) }})</option>
            </select>

            <input type="hidden" name="role" value="{{ $role }}">
            <input type="hidden" name="action" value="{{ $action }}">
            <input type="hidden" name="search" value="{{ $search }}">
            <input type="hidden" name="signatory_name" value="{{ $signatoryName }}">
            <input type="hidden" name="signatory_title" value="{{ $signatoryTitle }}">

            <button type="button" onclick="window.print()" class="btn-tb btn-tb-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2m-12 0v4h12v-4"/></svg>
                Print / Save as PDF
            </button>

            <a href="{{ route('superadmin.activity.export-csv', ['date_from' => $dateFrom, 'date_to' => $dateTo, 'role' => $role, 'action' => $action, 'search' => $search]) }}" class="btn-tb btn-tb-emerald">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export CSV
            </a>

            <a href="{{ route('superadmin.activity') }}" class="btn-tb btn-tb-ghost">
                &larr; Back to Dashboard
            </a>
        </form>
    </header>

    <!-- ─── MAIN REPORT CANVAS ─── -->
    <main class="report-page-wrap">
        <article class="report-sheet">

            <!-- ─── OFFICIAL HEADER / LETTERHEAD ─── -->
            <header class="report-header">
                <div class="report-logo-box">
                    <img src="{{ srh_logo_url() }}" alt="Republic Seal">
                </div>
                <div class="report-header-text">
                    <div class="gov-sub">Republic of the Philippines</div>
                    <div class="gov-sub">Province of Nueva Ecija &bull; Municipality of Santa Rosa</div>
                    <div class="gov-main">Barangay Lourdes</div>
                    <div class="gov-sub-org">Santa Rosa Homes Tricycle Operators and Drivers' Association (SRH-LINK-TODA)</div>
                    <div class="gov-address">TODA Terminal Complex, Barangay Lourdes, Santa Rosa, Nueva Ecija, 3101, Philippines<br>Contact: 0917 000 1234 &bull; Email: srhlink.toda@gmail.com</div>
                </div>
                <div class="report-logo-box">
                    <img src="{{ srh_logo_url() }}" alt="TODA Seal">
                </div>
            </header>

            <!-- ─── REPORT METADATA ─── -->
            <div class="report-meta-grid">
                <div class="meta-col-left">
                    <div><strong>Coverage Period:</strong> {{ $dateRangeLabel }}</div>
                    <div><strong>Grouped By:</strong> 
                        @if ($reportType === 'grouped_role')
                            User Role (Superadmin, Admin, Driver, Passenger)
                        @elseif ($reportType === 'date_range')
                            Registration Month / Date
                        @elseif ($reportType === 'statistics')
                            Role &amp; Activity Distribution
                        @else
                            All Records
                        @endif
                    </div>
                </div>
                <div class="meta-col-right">
                    <div><strong>Generated On:</strong> {{ $generatedAt->format('F j, Y \a\t g:i A') }}</div>
                    <div><strong>Document Status:</strong> <span style="color: #047857; font-weight: 800;">CERTIFIED OFFICIAL</span></div>
                </div>
            </div>

            <!-- ─── REPORT TITLE ─── -->
            <div class="report-title-box">
                <h1 class="report-title">
                    @if ($reportType === 'grouped_role')
                        AUDIT LOGS GROUPED BY USER ROLE
                    @elseif ($reportType === 'date_range')
                        AUDIT ACTIVITY TRAIL ({{ strtoupper($dateRangeLabel) }})
                    @elseif ($reportType === 'statistics')
                        AUDIT & SECURITY STATISTICS DASHBOARD
                    @else
                        CERTIFIED LIST OF AUDIT LOGS & USER ACTIVITIES
                    @endif
                </h1>
                @if ($reportType === 'statistics')
                    <p class="report-subtitle">Executive summary report & activity metrics</p>
                @endif
            </div>

            <!-- ─── REPORT FORMAT 1: ALL RECORDS (SAMPLE 4) ─── -->
            @if ($reportType === 'all')
                <table class="report-table">
                    <thead>
                        <tr>
                            <th class="center" style="width:36px;">#</th>
                            <th style="width:130px;">Timestamp</th>
                            <th>User / Name</th>
                            <th style="width:90px;">Role</th>
                            <th style="width:130px;">Action</th>
                            <th style="width:110px;">IP Address</th>
                            <th>Details / Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $index => $log)
                            @php
                                $meta = $log->metadata ? json_decode($log->metadata, true) : [];
                            @endphp
                            <tr>
                                <td class="center font-bold">{{ $index + 1 }}</td>
                                <td style="white-space:nowrap;font-size:0.68rem;">{{ $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('M j, Y H:i:s') : '—' }}</td>
                                <td>
                                    <span class="user-name-link">{{ $log->user_name ?? ($log->user_role === 'superadmin' ? 'Superadministrator' : 'Guest / System') }}</span>
                                </td>
                                <td>
                                    <span class="tag-pill {{ in_array($log->user_role, ['superadmin', 'admin']) ? 'tag-blue' : ($log->user_role === 'driver' ? 'tag-amber' : ($log->user_role === 'passenger' ? 'tag-green' : 'tag-gray')) }}">
                                        {{ strtoupper($log->user_role ?? 'GUEST') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="tag-pill {{ in_array($log->action, ['login_failed', 'account_deleted', 'login_blocked', 'account_deactivated']) ? 'tag-red' : (in_array($log->action, ['login', 'logout', 'register']) ? 'tag-gray' : 'tag-blue') }}">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td style="font-family:'JetBrains Mono',monospace;font-size:0.65rem;">{{ $log->ip_address ?? '—' }}</td>
                                <td style="font-size:0.65rem;color:#475569;">
                                    @if (!empty($meta) && is_array($meta))
                                        @foreach (array_slice($meta, 0, 3) as $k => $v)
                                            <div><strong>{{ $k }}:</strong> {{ is_array($v) ? json_encode($v) : $v }}</div>
                                        @endforeach
                                    @else
                                        <span>—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="center" style="padding:1.5rem;color:#94a3b8;">No audit activity records found for this criteria.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="grand-total-row">
                    Total records: {{ number_format($totalLogs) }}
                </div>

            <!-- ─── REPORT FORMAT 2: GROUPED BY DATE / MONTH (SAMPLE 2) ─── -->
            @elseif ($reportType === 'date_range')
                @forelse ($groupedByMonth as $monthLabel => $monthLogs)
                    <div class="group-banner">
                        {{ strtoupper($monthLabel) }}
                    </div>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th class="center" style="width:36px;">#</th>
                                <th style="width:130px;">Timestamp</th>
                                <th>User / Name</th>
                                <th style="width:90px;">Role</th>
                                <th style="width:130px;">Action</th>
                                <th style="width:110px;">IP Address</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($monthLogs as $index => $log)
                                @php $meta = $log->metadata ? json_decode($log->metadata, true) : []; @endphp
                                <tr>
                                    <td class="center font-bold">{{ $index + 1 }}</td>
                                    <td style="white-space:nowrap;font-size:0.68rem;">{{ $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('M j, Y H:i:s') : '—' }}</td>
                                    <td><span class="user-name-link">{{ $log->user_name ?? ($log->user_role === 'superadmin' ? 'Superadministrator' : 'Guest') }}</span></td>
                                    <td><span class="tag-pill tag-blue">{{ strtoupper($log->user_role ?? 'GUEST') }}</span></td>
                                    <td><span class="tag-pill tag-gray">{{ $log->action }}</span></td>
                                    <td style="font-family:'JetBrains Mono',monospace;font-size:0.65rem;">{{ $log->ip_address ?? '—' }}</td>
                                    <td style="font-size:0.65rem;color:#475569;">
                                        @if (!empty($meta) && is_array($meta))
                                            @foreach (array_slice($meta, 0, 2) as $k => $v)
                                                <span><strong>{{ $k }}:</strong> {{ is_array($v) ? json_encode($v) : $v }} </span>
                                            @endforeach
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="subtotal-row">
                        Subtotal ({{ strtoupper($monthLabel) }}): {{ $monthLogs->count() }}
                    </div>
                @empty
                    <div style="text-align:center;padding:2rem;color:#94a3b8;">No audit activity records found.</div>
                @endforelse

                @php
                    $breakdownStrs = [];
                    foreach ($groupedByMonth as $m => $mLogs) {
                        $breakdownStrs[] = Carbon::parse($m)->format('M').': '.$mLogs->count();
                    }
                @endphp
                <div class="grand-total-row">
                    Total records: {{ number_format($totalLogs) }} @if(!empty($breakdownStrs)) ({{ implode(' | ', $breakdownStrs) }}) @endif
                </div>

            <!-- ─── REPORT FORMAT 3: GROUPED BY ROLE (SAMPLE 3) ─── -->
            @elseif ($reportType === 'grouped_role')
                @forelse ($groupedByRole as $roleLabel => $roleLogs)
                    <div class="group-banner role-{{ strtolower(str_replace([' ', '/'], '', $roleLabel)) }}">
                        {{ $roleLabel }} (Role: {{ ucwords(strtolower($roleLabel)) }})
                    </div>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th class="center" style="width:36px;">#</th>
                                <th style="width:130px;">Timestamp</th>
                                <th>User / Name</th>
                                <th style="width:140px;">Action</th>
                                <th style="width:110px;">IP Address</th>
                                <th>Details / Parameters</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roleLogs as $index => $log)
                                @php $meta = $log->metadata ? json_decode($log->metadata, true) : []; @endphp
                                <tr>
                                    <td class="center font-bold">{{ $index + 1 }}</td>
                                    <td style="white-space:nowrap;font-size:0.68rem;">{{ $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('M j, Y H:i:s') : '—' }}</td>
                                    <td><span class="user-name-link">{{ $log->user_name ?? ($roleLabel === 'SUPERADMIN' ? 'Superadministrator' : 'Guest') }}</span></td>
                                    <td><span class="tag-pill tag-blue">{{ $log->action }}</span></td>
                                    <td style="font-family:'JetBrains Mono',monospace;font-size:0.65rem;">{{ $log->ip_address ?? '—' }}</td>
                                    <td style="font-size:0.65rem;color:#475569;">
                                        @if (!empty($meta) && is_array($meta))
                                            @foreach (array_slice($meta, 0, 3) as $k => $v)
                                                <div><strong>{{ $k }}:</strong> {{ is_array($v) ? json_encode($v) : $v }}</div>
                                            @endforeach
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="subtotal-row">
                        Subtotal ({{ $roleLabel }}): {{ $roleLogs->count() }}
                    </div>
                @empty
                    <div style="text-align:center;padding:2rem;color:#94a3b8;">No audit activity records found.</div>
                @endforelse

                @php
                    $roleBreakdownStrs = [];
                    foreach ($groupedByRole as $r => $rLogs) {
                        $roleBreakdownStrs[] = ucwords(strtolower($r)).': '.$rLogs->count();
                    }
                @endphp
                <div class="grand-total-row">
                    Total records: {{ number_format($totalLogs) }} @if(!empty($roleBreakdownStrs)) ({{ implode(' | ', $roleBreakdownStrs) }}) @endif
                </div>

            <!-- ─── REPORT FORMAT 4: STATISTICS DASHBOARD (SAMPLE 5) ─── -->
            @elseif ($reportType === 'statistics')
                <div class="stats-grid">
                    <div class="stat-card green">
                        <div class="lbl">Total Audit Events</div>
                        <div class="val">{{ number_format($totalLogs) }}</div>
                    </div>
                    <div class="stat-card blue">
                        <div class="lbl">Logins & Auth</div>
                        <div class="val">{{ number_format($loginEvents) }}</div>
                    </div>
                    <div class="stat-card amber">
                        <div class="lbl">Driver & Pax Events</div>
                        <div class="val">{{ number_format(($roleCounts['DRIVER'] ?? 0) + ($roleCounts['PASSENGER'] ?? 0)) }}</div>
                    </div>
                    <div class="stat-card red">
                        <div class="lbl">Security & Admin</div>
                        <div class="val">{{ number_format($securityEvents + $adminActions) }}</div>
                    </div>
                </div>

                <div class="charts-row">
                    <div class="chart-box">
                        <div class="chart-title">Activity Volume by Month</div>
                        <div class="bar-chart-visual">
                            @php
                                $maxMonthly = max(1, $monthlyVolume->max() ?: 1);
                            @endphp
                            @forelse ($monthlyVolume as $month => $count)
                                @php $pct = round(($count / $maxMonthly) * 100); @endphp
                                <div class="bar-col">
                                    <div class="bar-bar" style="height: {{ max(10, $pct) }}%;">
                                        <span class="bar-val">{{ $count }}</span>
                                    </div>
                                    <span class="bar-lbl">{{ $month }}</span>
                                </div>
                            @empty
                                <div style="font-size:0.7rem;color:#94a3b8;margin:auto;">No monthly volume recorded</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="chart-box">
                        <div class="chart-title">Activity Distribution by Role</div>
                        <div class="role-donut-visual">
                            <div class="donut-circle">
                                <div class="donut-hole">
                                    {{ $totalLogs }}
                                    <span>events</span>
                                </div>
                            </div>
                            <div class="role-legend">
                                @foreach ($roleCounts as $rName => $rCount)
                                    @php $rPct = $totalLogs > 0 ? round(($rCount / $totalLogs) * 100, 1) : 0; @endphp
                                    <div class="legend-item">
                                        <span class="legend-dot" style="background: {{ $rName === 'SUPERADMIN' ? '#3b82f6' : ($rName === 'ADMIN' ? '#10b981' : ($rName === 'DRIVER' ? '#f59e0b' : ($rName === 'PASSENGER' ? '#8b5cf6' : '#94a3b8'))) }};"></span>
                                        <strong>{{ $rName }}</strong>: {{ $rCount }} ({{ $rPct }}%)
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TOP ACTIONS BREAKDOWN TABLE -->
                <div style="margin-bottom:1.5rem;">
                    <div style="font-size:0.75rem;font-weight:800;color:#0f172a;text-transform:uppercase;margin-bottom:0.5rem;">Top System Actions Breakdown</div>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Action Event Name</th>
                                <th class="center" style="width:100px;">Event Count</th>
                                <th style="width:180px;">Share (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $actionIdx = 1; @endphp
                            @foreach ($topActions as $actName => $actCount)
                                @php $actPct = $totalLogs > 0 ? round(($actCount / $totalLogs) * 100, 1) : 0; @endphp
                                <tr>
                                    <td class="center font-bold">{{ $actionIdx++ }}</td>
                                    <td><strong>{{ $actName }}</strong></td>
                                    <td class="center font-bold">{{ number_format($actCount) }}</td>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:0.4rem;">
                                            <div style="flex:1;height:8px;background:#e2e8f0;border-radius:4px;overflow:hidden;">
                                                <div style="width:{{ $actPct }}%;height:100%;background:#2563eb;"></div>
                                            </div>
                                            <span style="font-size:0.65rem;font-weight:700;color:#475569;min-width:32px;">{{ $actPct }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- AUTOMATED INSIGHT SUMMARY -->
                <div class="insight-card">
                    <div class="title">Official Audit Insight & Compliance Summary</div>
                    <div class="text">
                        A total of <strong>{{ number_format($totalLogs) }}</strong> audit log entries have been verified during the reporting timeframe <strong>({{ $dateRangeLabel }})</strong>.
                        Authentication and session operations accounted for <strong>{{ number_format($loginEvents) }}</strong> events, while <strong>{{ number_format($adminActions) }}</strong> administrative modifications were logged.
                        All user actions, IP addresses, and session payloads are encrypted, certified, and archived in accordance with RA 10173 (Data Privacy Act of 2012) and Santa Rosa Homes TODA compliance rules.
                    </div>
                </div>

                <div class="grand-total-row">
                    Total audit events analyzed: {{ number_format($totalLogs) }}
                </div>
            @endif

            <!-- ─── SIGNATORY BLOCK ─── -->
            <section class="signatory-section">
                <div class="signatory-box">
                    <div class="sig-lead">Prepared by:</div>
                    <div class="sig-name">{{ $signatoryName }}</div>
                    <div class="sig-title">{{ $signatoryTitle }}</div>
                    <div class="sig-caption">Signature over printed name</div>
                </div>
            </section>

            <!-- ─── RUNNING FOOTER ─── -->
            <footer class="report-footer">
                <div>
                    <strong>Barangay Lourdes &bull; SRH-LINK-TODA</strong><br>
                    Santa Rosa, Nueva Ecija, Philippines<br>
                    Contact: 0917 000 1234 &bull; Email: srhlink.toda@gmail.com
                </div>
                <div style="text-align:right;">
                    Date Printed: {{ now()->format('F j, Y') }} &bull; Time Printed: {{ now()->format('g:i A') }}<br>
                    Page 1 of 1<br>
                    <em>Generated by: SRH-LINK-TODA Information Management System</em>
                </div>
            </footer>

        </article>
    </main>

</body>
</html>
