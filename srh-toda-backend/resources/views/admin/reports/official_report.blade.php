<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TODA Official Report — {{ $dateRangeLabel }}</title>
    <link rel="icon" type="image/png" href="{{ srh_logo_url() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            line-height: 1.35;
            -webkit-font-smoothing: antialiased;
        }

        /* ─── PRINT OPTIMIZATIONS (1-TO-1 PHYSICAL EXACT PREVIEW FIDELITY) ─── */
        @media print {
            @page {
                size: A4 portrait;
                margin: 0 !important;
            }
            *, *::before, *::after {
                box-shadow: none !important;
                text-shadow: none !important;
            }
            html, body {
                background: #ffffff !important;
                background-color: #ffffff !important;
                color: #000000 !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 210mm !important;
                height: 297mm !important;
                min-height: 297mm !important;
                max-height: 297mm !important;
                overflow: hidden !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                border-radius: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .report-page-wrap {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 210mm !important;
                height: 297mm !important;
                overflow: hidden !important;
                border-radius: 0 !important;
                background: #ffffff !important;
            }
            #printableReportSheet {
                background: #ffffff !important;
                background-color: #ffffff !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                padding: 8mm 12mm 8mm 12mm !important;
                width: 210mm !important;
                min-width: 210mm !important;
                max-width: 210mm !important;
                height: 297mm !important;
                min-height: 297mm !important;
                max-height: 297mm !important;
                margin: 0 !important;
                box-sizing: border-box !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
                overflow: hidden !important;
                page-break-after: avoid !important;
                page-break-before: avoid !important;
                page-break-inside: avoid !important;
                break-after: avoid !important;
                break-before: avoid !important;
                break-inside: avoid !important;
            }
            .report-content-main {
                flex: 1 !important;
                display: flex !important;
                flex-direction: column !important;
            }
            .signatory-section {
                margin-top: 14px !important;
                margin-bottom: 8px !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .report-footer {
                margin-top: auto !important;
                padding-top: 8px !important;
                border-top: 1px solid #cbd5e1 !important;
                display: flex !important;
                justify-content: space-between !important;
                align-items: flex-end !important;
                visibility: visible !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .report-table th {
                background-color: #0d3b66 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .group-banner, .tag-pill, .stat-card, .donut-circle {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            tr, .stat-card, .chart-box, .insight-card {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <!-- ─── TOPBAR (CONSISTENT THEME WITH APPLICATION) ─── -->
    <header class="no-print sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-200 px-4 py-3 shadow-xs">
        <div class="max-w-5xl mx-auto flex items-center justify-between gap-3">
            <a href="{{ route('admin.reports.generator') }}" class="px-3.5 py-2 rounded-2xl bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 font-extrabold text-xs uppercase tracking-wider shadow-xs transition active:scale-95 flex items-center gap-1.5 text-decoration-none">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back to Generator</span>
            </a>

            <div class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                <span>{{ $dateRangeLabel }}</span>
            </div>

            <button type="button" onclick="window.print()" class="px-4 py-2 rounded-2xl bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-black text-xs uppercase tracking-wider shadow-md shadow-blue-600/20 transition flex items-center justify-center gap-1.5 cursor-pointer border-none">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print / Download as PDF</span>
            </button>
        </div>
    </header>

    <!-- ─── AUTHENTIC PAPER CANVAS ─── -->
    <main class="report-page-wrap flex-1 py-6 px-2 sm:px-6">
        @include('admin.reports.official_sheet')
    </main>

</body>
</html>
