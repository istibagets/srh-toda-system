<x-app-layout>
    <x-slot name="header">
        <!-- 1. CONFIG HEADER -->
        <div id="reportHubHeaderConfig" class="flex items-center gap-2.5 min-w-0">
            <a href="{{ route('drivers.index') }}" class="w-9 h-9 rounded-2xl bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 flex items-center justify-center shadow-xs transition active:scale-95 text-decoration-none shrink-0" title="Back to Admin">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div class="min-w-0 flex-1">
                <h1 class="text-base sm:text-xl font-black text-slate-900 tracking-tight truncate">Official Report Generator</h1>
            </div>
        </div>

        <!-- 2. PREVIEW HEADER (CONSISTENT DESIGN, NO RELOAD) -->
        <div id="reportHubHeaderPreview" class="hidden items-center justify-between gap-2 min-w-0 w-full">
            <div class="flex items-center gap-2 min-w-0">
                <button type="button" onclick="handleBackFromPreview()" class="px-3 py-2 rounded-2xl bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 font-extrabold text-xs uppercase tracking-wider shadow-xs transition active:scale-95 flex items-center gap-1.5 cursor-pointer shrink-0">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span class="hidden sm:inline">Back to Settings</span>
                    <span class="sm:hidden">Back</span>
                </button>
                <div class="hidden sm:inline-flex min-w-0">
                    <div id="previewBadgeText" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] sm:text-[11px] font-black uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200 truncate">Certified Incident Report</div>
                </div>
            </div>

            <button type="button" onclick="window.print()" class="px-3.5 sm:px-4 py-2 rounded-2xl bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-black text-xs uppercase tracking-wider shadow-md shadow-blue-600/20 transition flex items-center justify-center gap-1.5 cursor-pointer border-none shrink-0 whitespace-nowrap">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print / Save PDF</span>
            </button>
        </div>
    </x-slot>

    <div>

        <!-- ─── VIEW 1: REPORT CONFIGURATION FORM ─── -->
        <div id="reportHubConfigView" class="py-4 sm:py-8 pb-32 sm:pb-12">
            <div class="max-w-4xl mx-auto px-3 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">

                <div class="bg-white rounded-3xl p-4 sm:p-7 shadow-sm border border-gray-200/80">
                    <form id="officialReportForm" onsubmit="handleReportGenerate(event)" class="no-spa no-spa-submit space-y-5 sm:space-y-6">
                        
                        <!-- 1. REPORT LAYOUT SELECTION -->
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between gap-2">
                                <label class="text-[11px] sm:text-xs font-black text-slate-800 uppercase tracking-wider leading-tight">
                                    1. Select Report Layout Format<span class="text-red-500 ml-0.5">*</span>
                                </label>
                                <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 shrink-0 whitespace-nowrap bg-slate-100 px-2 py-0.5 rounded-full">4 Formats</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5" id="reportOptionGrid">
                                
                                <!-- FORMAT 1 -->
                                <label class="relative flex flex-col p-3.5 sm:p-4 rounded-2xl border-2 border-blue-600 bg-blue-50/70 shadow-xs cursor-pointer transition report-hub-card">
                                    <input type="radio" name="report_type" value="reports_all" checked class="sr-only" onchange="updateHubCardSelection(this)">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-[9px] sm:text-[10px] font-black uppercase text-blue-700 bg-blue-100/90 px-2 py-0.5 rounded-md font-bold">Format 1 &bull; Certified Table</span>
                                        <span class="w-3.5 h-3.5 rounded-full bg-blue-600 hub-check-dot flex items-center justify-center text-white text-[9px] font-black">&#10003;</span>
                                    </div>
                                    <div class="text-xs sm:text-sm font-black text-slate-900 leading-tight">Certified Incident Report</div>
                                    <p class="text-[11px] sm:text-xs text-slate-600 font-medium mt-0.5 leading-snug">Full masterlist of passenger complaints, disputed fares, and driver conduct incidents with resolution status.</p>
                                </label>

                                <!-- FORMAT 2 -->
                                <label class="relative flex flex-col p-3.5 sm:p-4 rounded-2xl border-2 border-slate-200 bg-slate-50/60 hover:border-blue-400 hover:bg-blue-50/40 cursor-pointer transition report-hub-card">
                                    <input type="radio" name="report_type" value="reports_date" class="sr-only" onchange="updateHubCardSelection(this)">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-[9px] sm:text-[10px] font-black uppercase text-emerald-700 bg-emerald-100/90 px-2 py-0.5 rounded-md font-bold">Format 2 &bull; Chronological</span>
                                        <span class="w-3.5 h-3.5 rounded-full bg-emerald-600 hub-check-dot hidden items-center justify-center text-white text-[9px] font-black">&#10003;</span>
                                    </div>
                                    <div class="text-xs sm:text-sm font-black text-slate-900 leading-tight">Incidents by Date Range</div>
                                    <p class="text-[11px] sm:text-xs text-slate-600 font-medium mt-0.5 leading-snug">Chronologically grouped passenger reports with monthly period subtotals, ideal for monthly meetings.</p>
                                </label>

                                <!-- FORMAT 3 -->
                                <label class="relative flex flex-col p-3.5 sm:p-4 rounded-2xl border-2 border-slate-200 bg-slate-50/60 hover:border-blue-400 hover:bg-blue-50/40 cursor-pointer transition report-hub-card">
                                    <input type="radio" name="report_type" value="drivers_roster" class="sr-only" onchange="updateHubCardSelection(this)">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-[9px] sm:text-[10px] font-black uppercase text-amber-800 bg-amber-100/90 px-2 py-0.5 rounded-md font-bold">Format 3 &bull; Grouped Roster</span>
                                        <span class="w-3.5 h-3.5 rounded-full bg-amber-600 hub-check-dot hidden items-center justify-center text-white text-[9px] font-black">&#10003;</span>
                                    </div>
                                    <div class="text-xs sm:text-sm font-black text-slate-900 leading-tight">TODA Fleet Drivers Masterlist</div>
                                    <p class="text-[11px] sm:text-xs text-slate-600 font-medium mt-0.5 leading-snug">Official certified list of all TODA drivers, MTOP numbers, and registration dates grouped by compliance status.</p>
                                </label>

                                <!-- FORMAT 4 -->
                                <label class="relative flex flex-col p-3.5 sm:p-4 rounded-2xl border-2 border-slate-200 bg-slate-50/60 hover:border-blue-400 hover:bg-blue-50/40 cursor-pointer transition report-hub-card">
                                    <input type="radio" name="report_type" value="statistics" class="sr-only" onchange="updateHubCardSelection(this)">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-[9px] sm:text-[10px] font-black uppercase text-purple-700 bg-purple-100/90 px-2 py-0.5 rounded-md font-bold">Format 4 &bull; Dashboard</span>
                                        <span class="w-3.5 h-3.5 rounded-full bg-purple-600 hub-check-dot hidden items-center justify-center text-white text-[9px] font-black">&#10003;</span>
                                    </div>
                                    <div class="text-xs sm:text-sm font-black text-slate-900 leading-tight">Operations &amp; Safety Dashboard</div>
                                    <p class="text-[11px] sm:text-xs text-slate-600 font-medium mt-0.5 leading-snug">Visual statistics report with dispute volume chart, compliance donut visual, and official audit insights.</p>
                                </label>

                            </div>
                        </div>

                        <!-- 2. TIMEFRAME SELECTION (NON-SCROLLABLE CLEAN GRID) -->
                        <div class="space-y-2.5 pt-4 border-t border-gray-100">
                            <label class="block text-xs font-black text-slate-800 uppercase tracking-wider">
                                2. Timeframe &amp; Date Period
                            </label>
                            
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2" id="hubDatePresetPills">
                                <label class="cursor-pointer">
                                    <input type="radio" name="date_preset" value="all" aria-label="Date preset: All Records" onchange="handleHubDatePresetChange('all', this)" class="sr-only">
                                    <span class="flex items-center justify-center text-center px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-[11px] sm:text-xs font-bold text-slate-700 transition hub-date-pill w-full">All Records</span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="date_preset" value="today" aria-label="Date preset: Today" onchange="handleHubDatePresetChange('today', this)" class="sr-only">
                                    <span class="flex items-center justify-center text-center px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-[11px] sm:text-xs font-bold text-slate-700 transition hub-date-pill w-full">Today</span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="date_preset" value="this_week" aria-label="Date preset: This Week" onchange="handleHubDatePresetChange('this_week', this)" class="sr-only">
                                    <span class="flex items-center justify-center text-center px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-[11px] sm:text-xs font-bold text-slate-700 transition hub-date-pill w-full">This Week</span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="date_preset" value="this_month" aria-label="Date preset: This Month" onchange="handleHubDatePresetChange('this_month', this)" class="sr-only" checked>
                                    <span class="flex items-center justify-center text-center px-3 py-2 rounded-xl border border-slate-900 bg-slate-900 text-[11px] sm:text-xs font-bold text-white transition hub-date-pill w-full">This Month</span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="date_preset" value="last_month" aria-label="Date preset: Last Month" onchange="handleHubDatePresetChange('last_month', this)" class="sr-only">
                                    <span class="flex items-center justify-center text-center px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-[11px] sm:text-xs font-bold text-slate-700 transition hub-date-pill w-full">Last Month</span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="date_preset" value="this_quarter" aria-label="Date preset: This Quarter" onchange="handleHubDatePresetChange('this_quarter', this)" class="sr-only">
                                    <span class="flex items-center justify-center text-center px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-[11px] sm:text-xs font-bold text-slate-700 transition hub-date-pill w-full">This Quarter</span>
                                </label>
                                <label class="cursor-pointer col-span-2 sm:col-span-1">
                                    <input type="radio" name="date_preset" value="custom" aria-label="Date preset: Custom Range" onchange="handleHubDatePresetChange('custom', this)" class="sr-only">
                                    <span class="flex items-center justify-center text-center px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-[11px] sm:text-xs font-bold text-slate-700 transition hub-date-pill w-full">Custom Range...</span>
                                </label>
                            </div>

                            <!-- Custom Date Pickers -->
                            <div id="hubCustomDateWrap" class="hidden grid-cols-1 sm:grid-cols-2 gap-2.5 p-3 sm:p-4 bg-slate-50 border border-slate-200 rounded-2xl mt-2">
                                <div class="space-y-1">
                                    <label for="hub_date_from" class="block text-[10px] font-black uppercase tracking-wider text-slate-500">Date From</label>
                                    <input type="date" id="hub_date_from" name="date_from" class="w-full rounded-xl border border-gray-300 p-2.5 text-xs font-bold text-slate-900 bg-white focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div class="space-y-1">
                                    <label for="hub_date_to" class="block text-[10px] font-black uppercase tracking-wider text-slate-500">Date To</label>
                                    <input type="date" id="hub_date_to" name="date_to" class="w-full rounded-xl border border-gray-300 p-2.5 text-xs font-bold text-slate-900 bg-white focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- 3. SIGNATORY BLOCK -->
                        <div class="space-y-2.5 pt-4 border-t border-gray-100">
                            <label class="block text-xs font-black text-slate-800 uppercase tracking-wider">
                                3. Signatory Certification Details
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 p-3.5 sm:p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                <div class="space-y-1">
                                    <label for="hub_signatory_name" class="block text-[11px] font-bold text-slate-500">Signatory Full Name</label>
                                    <input type="text" id="hub_signatory_name" name="signatory_name" value="{{ auth()->user()->name ?? 'TODA Administrator' }}" placeholder="e.g. Juan Dela Cruz" class="w-full rounded-xl border border-gray-300 p-2.5 text-xs font-bold text-slate-900 bg-white focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div class="space-y-1">
                                    <label for="hub_signatory_title" class="block text-[11px] font-bold text-slate-500">Official Designation / Title</label>
                                    <input type="text" id="hub_signatory_title" name="signatory_title" value="TODA Operations &amp; Safety Administrator" placeholder="e.g. TODA Safety Officer" class="w-full rounded-xl border border-gray-300 p-2.5 text-xs text-slate-700 bg-white font-medium focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- 4. ACTION BUTTON -->
                        <div class="pt-3 space-y-2.5">
                            <button type="submit" id="generateBtn" class="w-full py-3.5 sm:py-4 px-4 rounded-2xl bg-blue-600 hover:bg-blue-700 active:scale-98 text-white font-black text-xs sm:text-sm uppercase tracking-wider shadow-lg shadow-blue-600/25 transition flex items-center justify-center gap-2 cursor-pointer border-none text-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white shrink-0 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                <span>Generate Official Report</span>
                            </button>

                            <div class="text-center">
                                <a href="{{ route('drivers.index') }}" class="inline-block py-1 text-xs font-bold text-slate-500 hover:text-slate-800 transition text-decoration-none">
                                    &larr; Return to Admin Portal
                                </a>
                            </div>
                        </div>

                    </form>
                </div>

            </div>
        </div>

        <!-- ─── VIEW 2: INTERACTIVE PINCH & DRAG PRINT PREVIEW ─── -->
        <div id="reportHubPreviewView" class="hidden py-3 sm:py-6 pb-28 sm:pb-12">
            <div class="max-w-6xl mx-auto px-2 sm:px-6 lg:px-8 space-y-2.5">
                
                <!-- PREVIEW CONTROLS TOOLBAR -->
                <div class="no-print flex items-center justify-between px-3.5 py-2 bg-white rounded-2xl border border-gray-200/80 shadow-xs text-xs" id="previewControlsToolbar">
                    <div class="flex items-center gap-2 text-slate-800 font-bold">
                        <span class="text-xs sm:text-sm font-black tracking-tight text-slate-900">A4 Sheet Preview</span>
                    </div>
                    <div class="flex items-center gap-1 sm:gap-1.5">
                        <button type="button" onclick="zoomPreview(-0.12)" class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-800 font-black text-sm flex items-center justify-center transition border-none cursor-pointer" title="Zoom Out">&minus;</button>
                        <span id="zoomLevelDisplay" class="px-2 sm:px-2.5 py-1 min-w-[46px] text-center rounded-xl bg-slate-900 text-white font-extrabold text-[11px] sm:text-xs tracking-wider">100%</span>
                        <button type="button" onclick="zoomPreview(0.12)" class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-800 font-black text-sm flex items-center justify-center transition border-none cursor-pointer" title="Zoom In">&plus;</button>
                        
                        <!-- FIT TO PAGE BUTTON NEXT TO ZOOM -->
                        <button type="button" onclick="fitToPage(true)" class="px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 active:scale-95 text-blue-700 font-extrabold text-[11px] sm:text-xs uppercase tracking-wider flex items-center gap-1.5 transition border border-blue-200 cursor-pointer shadow-2xs" title="Fit Entire Page in View">
                            <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                            <span>Fit to Page</span>
                        </button>
                        
                        <button type="button" onclick="setExactScale(1.0)" class="hidden sm:inline-flex px-2.5 py-1 sm:py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] sm:text-xs items-center transition border-none cursor-pointer" title="100% Full Scale">100%</button>
                    </div>
                </div>

                <!-- PINCHABLE & DRAGGABLE DESK CANVAS CONTAINER -->
                <div class="w-full bg-slate-900/95 rounded-3xl p-2 sm:p-4 border border-slate-700/80 relative h-[70vh] min-h-[480px] max-h-[850px] select-none overflow-hidden shadow-inner" style="touch-action: none; cursor: grab;" id="previewViewportOuter">
                    
                    <!-- Transformable Paper Sheet Wrapper (Horizontally Centered, Starts from Top) -->
                    <div id="previewSheetWrapper" class="will-change-transform absolute" style="left: 50%; top: 16px; width: 794px; transform-origin: top center; transform: translate3d(-50%, 0, 0) scale(1);">
                        <div id="previewSheetContainer">
                            <!-- Authentic A4 Paper Sheet rendered dynamically here -->
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- PRINT STYLES FOR EXACT A4 PAPER CAPTURE (1:1 FIDELITY WITH PREVIEW) -->
    <style>
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
            /* Hide application shell, sidebars, navigation bars, and hub config form */
            nav, 
            aside, 
            header,
            footer,
            .no-print, 
            button, 
            a, 
            #reportHubHeaderConfig, 
            #reportHubHeaderPreview, 
            #reportHubConfigView, 
            #previewControlsToolbar, 
            .no-print-element, 
            .report-hub-card {
                display: none !important;
                visibility: hidden !important;
            }
            #reportHubConfigView {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                overflow: hidden !important;
            }
            main, 
            #reportHubPreviewView, 
            .max-w-4xl, 
            .max-w-5xl, 
            .max-w-6xl, 
            .mx-auto,
            .px-2, .px-3, .px-4, .px-6, .px-8, 
            .py-3, .py-4, .py-6, .py-8, 
            .pb-28, .pb-32, .pb-12, 
            .space-y-2\.5, .space-y-4, .space-y-6 {
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                border-radius: 0 !important;
                background: #ffffff !important;
                background-color: #ffffff !important;
                box-shadow: none !important;
                width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
            }
            #previewViewportOuter, 
            #previewSheetWrapper, 
            #previewSheetContainer {
                background: #ffffff !important;
                background-color: #ffffff !important;
                border: none !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 210mm !important;
                height: 297mm !important;
                min-height: 297mm !important;
                max-height: 297mm !important;
                overflow: hidden !important;
                transform: none !important;
                position: static !important;
                top: auto !important;
                left: auto !important;
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
            .report-header {
                display: flex !important;
                visibility: visible !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .report-logo-box, .report-logo-box img {
                display: flex !important;
                visibility: visible !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
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

    <script>
        // ─── INTERACTIVE PINCH & DRAG ENGINE ───
        let currentScale = 1.0;
        let panX = 0;
        let panY = 0;
        let fitScale = 1.0;

        let isDragging = false;
        let startDragX = 0;
        let startDragY = 0;

        let initialTouchDistance = null;
        let initialTouchScale = 1.0;
        let lastTouchTap = 0;

        function updateTransform(animate = false) {
            const wrapper = document.getElementById('previewSheetWrapper');
            const zoomText = document.getElementById('zoomLevelDisplay');
            if (!wrapper) return;
            
            if (animate) {
                wrapper.style.transition = 'transform 0.22s cubic-bezier(0.2, 0.8, 0.2, 1)';
                setTimeout(() => { 
                    if (wrapper) wrapper.style.transition = 'none'; 
                }, 240);
            } else {
                wrapper.style.transition = 'none';
            }
            
            wrapper.style.transform = `translate3d(calc(-50% + ${panX}px), ${panY}px, 0px) scale(${currentScale})`;
            
            if (zoomText) {
                zoomText.innerText = Math.round(currentScale * 100) + '%';
            }
        }

        function calcFitScale(fitFullPage = false) {
            const viewport = document.getElementById('previewViewportOuter');
            if (!viewport) return 0.45;
            
            const sheetWidth = 794;
            const sheetHeight = 1123;
            const vpWidth = viewport.clientWidth || (window.innerWidth - 32);
            const vpHeight = viewport.clientHeight || 600;
            
            const availWidth = Math.max(220, vpWidth - 24);
            const availHeight = Math.max(300, vpHeight - 24);

            const scaleW = availWidth / sheetWidth;
            const scaleH = availHeight / sheetHeight;
            
            if (fitFullPage) {
                return Math.min(Math.max(Math.min(scaleW, scaleH), 0.25), 1.0);
            }
            
            return Math.min(Math.max(scaleW, 0.35), 1.0);
        }

        function fitToPage(animate = true) {
            fitScale = calcFitScale(true);
            currentScale = fitScale;
            panX = 0;
            panY = 0;
            updateTransform(animate);
        }

        function resetPreviewTransform(animate = true) {
            fitScale = calcFitScale(false);
            currentScale = fitScale;
            panX = 0;
            panY = 0;
            updateTransform(animate);
        }

        function setExactScale(scaleVal, animate = true) {
            currentScale = Math.min(Math.max(scaleVal, 0.2), 3.5);
            panX = 0;
            panY = 0;
            updateTransform(animate);
        }

        function zoomPreview(delta, animate = true) {
            let newScale = currentScale + delta;
            newScale = Math.min(Math.max(newScale, 0.2), 3.5);
            currentScale = newScale;
            updateTransform(animate);
        }

        function initInteractiveViewer() {
            const viewport = document.getElementById('previewViewportOuter');
            if (!viewport || viewport.dataset.panzoomInit) return;
            viewport.dataset.panzoomInit = 'true';

            let pinchFocalClient = null;
            let pinchStartPanX = 0;
            let pinchStartPanY = 0;
            let pinchStartScale = 1.0;
            let initialTouchDistance = null;

            function getTouchCenter(t1, t2) {
                return {
                    x: (t1.clientX + t2.clientX) / 2,
                    y: (t1.clientY + t2.clientY) / 2
                };
            }

            // Touch Gestures (1-finger Pan, 2-finger Pinpoint Pinch-to-Zoom, Double-Tap)
            viewport.addEventListener('touchstart', (e) => {
                e.stopPropagation();
                if (e.touches.length === 1) {
                    isDragging = true;
                    startDragX = e.touches[0].clientX - panX;
                    startDragY = e.touches[0].clientY - panY;

                    const now = Date.now();
                    if (now - lastTouchTap < 320) {
                        const vpRect = viewport.getBoundingClientRect();
                        const tapX = e.touches[0].clientX - (vpRect.left + vpRect.width / 2);
                        const tapY = e.touches[0].clientY - (vpRect.top + 16);

                        if (Math.abs(currentScale - fitScale) < 0.08) {
                            const targetScale = Math.min(1.35, Math.max(fitScale * 2.2, 0.9));
                            const scaleRatio = targetScale / currentScale;
                            panX = tapX - (tapX - panX) * scaleRatio;
                            panY = tapY - (tapY - panY) * scaleRatio;
                            currentScale = targetScale;
                        } else {
                            currentScale = fitScale;
                            panX = 0;
                            panY = 0;
                        }
                        updateTransform(true);
                    }
                    lastTouchTap = now;
                } else if (e.touches.length === 2) {
                    isDragging = false;
                    initialTouchDistance = Math.hypot(
                        e.touches[0].clientX - e.touches[1].clientX,
                        e.touches[0].clientY - e.touches[1].clientY
                    );
                    pinchStartScale = currentScale;
                    pinchStartPanX = panX;
                    pinchStartPanY = panY;
                    pinchFocalClient = getTouchCenter(e.touches[0], e.touches[1]);
                }
            }, { passive: true });

            viewport.addEventListener('touchmove', (e) => {
                e.stopPropagation();
                if (e.touches.length === 1 && isDragging) {
                    if (e.cancelable) e.preventDefault();
                    panX = e.touches[0].clientX - startDragX;
                    panY = e.touches[0].clientY - startDragY;
                    updateTransform(false);
                } else if (e.touches.length === 2 && initialTouchDistance && pinchFocalClient) {
                    if (e.cancelable) e.preventDefault();
                    const currentDist = Math.hypot(
                        e.touches[0].clientX - e.touches[1].clientX,
                        e.touches[0].clientY - e.touches[1].clientY
                    );
                    if (initialTouchDistance > 0 && currentDist > 0) {
                        const currentFocalClient = getTouchCenter(e.touches[0], e.touches[1]);
                        const pinchFactor = currentDist / initialTouchDistance;
                        const newScale = Math.min(Math.max(pinchStartScale * pinchFactor, 0.2), 3.5);

                        const vpRect = viewport.getBoundingClientRect();
                        const focalX = pinchFocalClient.x - (vpRect.left + vpRect.width / 2);
                        const focalY = pinchFocalClient.y - (vpRect.top + 16);

                        const scaleRatio = newScale / pinchStartScale;
                        // Exactly anchor the pinch center point under the fingers
                        panX = focalX - (focalX - pinchStartPanX) * scaleRatio + (currentFocalClient.x - pinchFocalClient.x);
                        panY = focalY - (focalY - pinchStartPanY) * scaleRatio + (currentFocalClient.y - pinchFocalClient.y);
                        currentScale = newScale;

                        updateTransform(false);
                    }
                }
            }, { passive: false });

            viewport.addEventListener('touchend', (e) => {
                if (e.touches.length === 0) {
                    isDragging = false;
                    initialTouchDistance = null;
                    pinchFocalClient = null;
                } else if (e.touches.length === 1) {
                    isDragging = true;
                    startDragX = e.touches[0].clientX - panX;
                    startDragY = e.touches[0].clientY - panY;
                    initialTouchDistance = null;
                    pinchFocalClient = null;
                }
            });

            // Desktop Mouse Drag & Wheel Zoom with cursor focal-point anchor
            viewport.addEventListener('mousedown', (e) => {
                if (e.button !== 0) return;
                isDragging = true;
                startDragX = e.clientX - panX;
                startDragY = e.clientY - panY;
                viewport.style.cursor = 'grabbing';
            });

            window.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                panX = e.clientX - startDragX;
                panY = e.clientY - startDragY;
                updateTransform(false);
            });

            window.addEventListener('mouseup', () => {
                if (isDragging) {
                    isDragging = false;
                    if (viewport) viewport.style.cursor = 'grab';
                }
            });

            viewport.addEventListener('wheel', (e) => {
                e.preventDefault();
                const zoomFactor = e.deltaY < 0 ? 1.15 : 0.87;
                const newScale = Math.min(Math.max(currentScale * zoomFactor, 0.2), 3.5);
                const scaleRatio = newScale / currentScale;

                const vpRect = viewport.getBoundingClientRect();
                const mouseX = e.clientX - (vpRect.left + vpRect.width / 2);
                const mouseY = e.clientY - (vpRect.top + 16);

                panX = mouseX - (mouseX - panX) * scaleRatio;
                panY = mouseY - (mouseY - panY) * scaleRatio;
                currentScale = newScale;
                updateTransform(false);
            }, { passive: false });
        }

        window.addEventListener('resize', () => {
            if (document.getElementById('reportHubPreviewView') && !document.getElementById('reportHubPreviewView').classList.contains('hidden')) {
                resetPreviewTransform(false);
            }
        });

        function updateHubCardSelection(radioEl) {
            const cards = document.querySelectorAll('.report-hub-card');
            cards.forEach(c => {
                c.classList.remove('border-blue-600', 'bg-blue-50/70', 'shadow-xs');
                c.classList.add('border-slate-200', 'bg-slate-50/60');
                const dot = c.querySelector('.hub-check-dot');
                if (dot) {
                    dot.classList.add('hidden');
                    dot.classList.remove('flex');
                }
            });
            const parent = radioEl.closest('.report-hub-card');
            if (parent) {
                parent.classList.remove('border-slate-200', 'bg-slate-50/60');
                parent.classList.add('border-blue-600', 'bg-blue-50/70', 'shadow-xs');
                const dot = parent.querySelector('.hub-check-dot');
                if (dot) {
                    dot.classList.remove('hidden');
                    dot.classList.add('flex');
                }
            }
        }

        function handleHubDatePresetChange(val, radioEl) {
            const pills = document.querySelectorAll('.hub-date-pill');
            pills.forEach(p => {
                p.classList.remove('bg-slate-900', 'text-white', 'border-slate-900');
                p.classList.add('bg-slate-50', 'text-slate-700', 'border-slate-200');
            });
            const activeSpan = radioEl.nextElementSibling;
            if (activeSpan) {
                activeSpan.classList.remove('bg-slate-50', 'text-slate-700', 'border-slate-200');
                activeSpan.classList.add('bg-slate-900', 'text-white', 'border-slate-900');
            }
            const wrap = document.getElementById('hubCustomDateWrap');
            if (wrap) {
                wrap.classList.toggle('hidden', val !== 'custom');
                wrap.classList.toggle('grid', val === 'custom');
            }
        }

        function showReportPreview() {
            document.getElementById('reportHubConfigView').classList.add('hidden');
            document.getElementById('reportHubPreviewView').classList.remove('hidden');
            document.getElementById('reportHubHeaderConfig').classList.add('hidden');
            document.getElementById('reportHubHeaderPreview').classList.remove('hidden');
            document.getElementById('reportHubHeaderPreview').classList.add('flex');
            window.scrollTo({ top: 0, behavior: 'smooth' });
            initInteractiveViewer();

            // Push history state so Android swipe-back & browser back button return to layout selection
            try {
                if (!window.history.state || !window.history.state.srh_report_preview) {
                    window.history.pushState({ srh_report_preview: true }, '', window.location.href);
                }
            } catch (e) {}

            requestAnimationFrame(() => {
                resetPreviewTransform(false);
                setTimeout(() => {
                    resetPreviewTransform(false);
                }, 70);
            });
        }

        function showReportConfig(doHistoryBack = false) {
            if (doHistoryBack && window.history.state && window.history.state.srh_report_preview) {
                window.history.back();
                return;
            }
            document.getElementById('reportHubPreviewView').classList.add('hidden');
            document.getElementById('reportHubConfigView').classList.remove('hidden');
            document.getElementById('reportHubHeaderPreview').classList.add('hidden');
            document.getElementById('reportHubHeaderPreview').classList.remove('flex');
            document.getElementById('reportHubHeaderConfig').classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function handleBackFromPreview() {
            if (window.history.state && window.history.state.srh_report_preview) {
                window.history.back();
            } else {
                showReportConfig(false);
            }
        }

        window.addEventListener('popstate', function(e) {
            const previewView = document.getElementById('reportHubPreviewView');
            if (previewView && !previewView.classList.contains('hidden')) {
                showReportConfig(false);
            }
        });

        function handleReportGenerate(e) {
            if (e && e.preventDefault) e.preventDefault();

            const btn = document.getElementById('generateBtn');
            const origHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<svg class="w-4 h-4 sm:w-5 sm:h-5 text-white animate-spin shrink-0 inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> <span>Generating Preview...</span>';
            }

            const form = document.getElementById('officialReportForm');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            params.append('snippet', '1');

            fetch('{{ route('admin.reports.official') }}?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                if (!res.ok) throw new Error('Server returned ' + res.status);
                return res.text();
            })
            .then(html => {
                document.getElementById('previewSheetContainer').innerHTML = html;
                
                const selectedRadio = form.querySelector('input[name="report_type"]:checked');
                let badgeText = 'Certified Incident Report';
                if (selectedRadio) {
                    if (selectedRadio.value === 'reports_all') badgeText = 'Certified Incident Report';
                    else if (selectedRadio.value === 'reports_date') badgeText = 'Incidents by Date Range';
                    else if (selectedRadio.value === 'drivers_roster') badgeText = 'Fleet Drivers Roster';
                    else if (selectedRadio.value === 'statistics') badgeText = 'Safety & Operations Dashboard';
                }
                const badgeEl = document.getElementById('previewBadgeText');
                if (badgeEl) badgeEl.innerText = badgeText;

                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                }

                showReportPreview();
            })
            .catch(err => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                }
                if (typeof window.createSlidingToast === 'function') {
                    window.createSlidingToast('Could not load report preview. Please check your entries.', 'error');
                } else {
                    alert('Could not load report preview.');
                }
            });
        }
    </script>
</x-app-layout>
