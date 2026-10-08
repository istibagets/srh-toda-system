<!-- ─── AUTHENTIC 1-TO-1 A4 PHYSICAL PAPER REPORT CANVAS ─── -->
<article class="report-sheet bg-white text-slate-900 mx-auto select-text flex flex-col justify-between" id="printableReportSheet" style="width: 794px; min-width: 794px; max-width: 794px; height: 1123px; min-height: 1123px; max-height: 1123px; padding: 14px 26px 14px 26px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15), 0 2px 8px rgba(15, 23, 42, 0.08); border: 1px solid #cbd5e1; border-radius: 2px; position: relative; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.35; box-sizing: border-box; background-color: #ffffff; overflow: hidden;">

    <!-- TOP SECTION: HEADER, METADATA, TITLE, TABLES, AND SIGNATORY -->
    <div class="report-content-main flex-1 flex flex-col">
        
        <!-- 1. OFFICIAL GOVERNMENT & TODA LETTERHEAD -->
        <div class="report-header flex items-center justify-between text-center pb-1.5 mb-1.5 gap-4 shrink-0" style="border-bottom: 2.5px solid #0f172a;">
            <div class="report-logo-box shrink-0 flex items-center justify-center" style="width: 58px; height: 58px;">
                <img src="{{ srh_logo_url() }}" alt="SRH Logo" style="width: 54px; height: 54px; object-fit: contain; background: transparent; border: none;">
            </div>
            <div class="report-header-text flex-1 px-1 min-w-0">
                <div class="gov-sub text-[10px] font-bold text-slate-600 uppercase tracking-widest leading-tight">Republic of the Philippines</div>
                <div class="gov-sub text-[10px] font-bold text-slate-600 uppercase tracking-wider leading-tight">Province of Nueva Ecija &bull; Municipality of Santa Rosa</div>
                <div class="gov-main text-[15px] font-black text-slate-950 uppercase tracking-tight my-0.5">Barangay Lourdes</div>
                <div class="gov-sub-org text-[11px] font-extrabold text-blue-900 uppercase tracking-wide">Santa Rosa Homes Tricycle Operators and Drivers' Association (SRH-LINK-TODA)</div>
                <div class="gov-address text-[8.5px] text-slate-500 font-medium mt-0.5 leading-tight">TODA Terminal Complex, Barangay Lourdes, Santa Rosa, Nueva Ecija, 3101, Philippines<br>Contact: 0917 000 1234 &bull; Email: srhlink.toda@gmail.com</div>
            </div>
            <div class="report-logo-box shrink-0 flex items-center justify-center" style="width: 58px; height: 58px;">
                <img src="{{ srh_logo_url() }}" alt="SRH Logo" style="width: 54px; height: 54px; object-fit: contain; background: transparent; border: none;">
            </div>
        </div>

        <!-- 2. REPORT METADATA -->
        <div class="report-meta-grid flex items-start justify-between text-[9.5px] text-slate-600 mb-1.5 pb-1 border-b border-slate-200 leading-normal shrink-0">
            <div class="space-y-0.5 text-left">
                <div><strong class="text-slate-900 font-bold">Coverage Period:</strong> {{ $dateRangeLabel }}</div>
                <div><strong class="text-slate-900 font-bold">Grouping / Scope:</strong> 
                    @if ($reportType === 'drivers_roster')
                        Fleet Compliance Status (Approved, Pending, Removed)
                    @elseif ($reportType === 'reports_date')
                        Incident Log Month / Period
                    @elseif ($reportType === 'statistics')
                        Safety &amp; Operations Metrics
                    @else
                        All Incident &amp; Dispute Records
                    @endif
                </div>
            </div>
            <div class="space-y-0.5 text-right shrink-0">
                <div><strong class="text-slate-900 font-bold">Date Generated:</strong> {{ $generatedAt->format('F j, Y \a\t g:i A') }}</div>
                <div><strong class="text-slate-900 font-bold">Document Status:</strong> <span class="text-emerald-700 font-extrabold uppercase">Certified Official</span></div>
            </div>
        </div>

        <!-- 3. REPORT TITLE -->
        <div class="report-title-box text-center my-1 mb-2 shrink-0">
            <h1 class="report-title text-[13.5px] font-black text-slate-950 uppercase tracking-wider">
                @if ($reportType === 'drivers_roster')
                    CERTIFIED MASTER ROSTER OF TODA FLEET DRIVERS
                @elseif ($reportType === 'reports_date')
                    PASSENGER INCIDENT &amp; SAFETY REPORTS ({{ strtoupper($dateRangeLabel) }})
                @elseif ($reportType === 'statistics')
                    TODA SAFETY &amp; FLEET OPERATIONS DASHBOARD
                @else
                    CERTIFIED LIST OF PASSENGER INCIDENT &amp; SAFETY REPORTS
                @endif
            </h1>
            @if ($reportType === 'statistics')
                <p class="report-subtitle text-[9.5px] text-slate-500 font-semibold mt-0.5">TODA Operations, Dispute Resolution &amp; Compliance Summary</p>
            @endif
        </div>

        <!-- ─── FORMAT 1: ALL PASSENGER INCIDENTS ─── -->
        @if ($reportType === 'reports_all')
            <div class="table-responsive w-full mb-1">
                <table class="report-table w-full border-collapse" style="font-size: 9px; table-layout: fixed; width: 100%;">
                    <thead>
                        <tr style="background-color: #0d3b66; color: #ffffff;">
                            <th class="p-1 border border-slate-700 text-center uppercase font-black" style="width: 26px;">#</th>
                            <th class="p-1 border border-slate-700 text-left uppercase font-black" style="width: 90px;">Date &amp; Time</th>
                            <th class="p-1 border border-slate-700 text-left uppercase font-black" style="width: 105px;">Complainant</th>
                            <th class="p-1 border border-slate-700 text-left uppercase font-black" style="width: 115px;">Driver Involved</th>
                            <th class="p-1 border border-slate-700 text-left uppercase font-black" style="width: 100px;">Category</th>
                            <th class="p-1 border border-slate-700 text-center uppercase font-black" style="width: 85px;">Status</th>
                            <th class="p-1 border border-slate-700 text-left uppercase font-black">Description &amp; Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reports as $index => $r)
                            @php 
                                $dProf = optional($r->driver)->driverProfile;
                                $st = strtolower($r->status);
                            @endphp
                            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-slate-50' }} border-b border-slate-200" style="page-break-inside: avoid; break-inside: avoid;">
                                <td class="p-1 border border-slate-200 text-center font-bold" style="overflow: hidden;">{{ $index + 1 }}</td>
                                <td class="p-1 border border-slate-200 text-[8.5px] leading-tight" style="overflow: hidden;">
                                    <div class="font-semibold text-slate-900 whitespace-nowrap">{{ $r->created_at ? $r->created_at->format('M j, Y') : '—' }}</div>
                                    <div class="text-[8px] text-slate-500 font-medium whitespace-nowrap mt-0.5">{{ $r->created_at ? $r->created_at->format('g:i A') : '' }}</div>
                                </td>
                                <td class="p-1 border border-slate-200 font-bold text-blue-700 truncate" style="overflow: hidden; text-overflow: ellipsis;">{{ $r->reporter->name ?? 'Deleted User' }}</td>
                                <td class="p-1 border border-slate-200" style="overflow: hidden;">
                                    <strong class="text-slate-900 block truncate">{{ $r->driver->name ?? 'Deleted User' }}</strong>
                                    @if($dProf && $dProf->mtop_number)
                                        <span class="text-[8px] text-blue-600 font-bold block truncate">(MTOP {{ $dProf->mtop_number }})</span>
                                    @endif
                                </td>
                                <td class="p-1 border border-slate-200 font-bold text-[8.5px]" style="overflow: hidden; word-break: break-word;">{{ $r->category }}</td>
                                <td class="p-1 border border-slate-200 text-center font-black" style="overflow: hidden; font-size: 8.5px; white-space: nowrap;">
                                    @if($st === 'resolved')
                                        <span style="color: #047857; font-weight: 900; letter-spacing: 0.02em;">RESOLVED</span>
                                    @elseif($st === 'investigating')
                                        <span style="color: #1d4ed8; font-weight: 900; font-size: 8px; letter-spacing: -0.01em;">INVESTIGATING</span>
                                    @elseif($st === 'dismissed')
                                        <span style="color: #475569; font-weight: 900; letter-spacing: 0.02em;">DISMISSED</span>
                                    @else
                                        <span style="color: #b45309; font-weight: 900; letter-spacing: 0.02em;">PENDING</span>
                                    @endif
                                </td>
                                <td class="p-1 border border-slate-200 text-[8.5px] text-slate-600 leading-snug" style="word-break: break-word;">{{ $r->description }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-4 text-center text-slate-400 font-medium">No incident records found for this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="grand-total-row text-right text-[10px] font-black text-slate-900 py-0.5 border-t-2 border-slate-900 mt-0.5 mb-1" style="page-break-inside: avoid; break-inside: avoid;">
                Total incident reports: {{ number_format($totalReports) }}
            </div>

        <!-- ─── FORMAT 2: INCIDENTS GROUPED BY DATE / MONTH ─── -->
        @elseif ($reportType === 'reports_date')
            @forelse (($groupedReportsByDate ?? $groupedReportsByMonth ?? collect()) as $mLabel => $mReports)
                <div class="group-banner p-1 px-2.5 font-black text-[10px] text-slate-900 bg-slate-100 border-l-4 border-blue-600 uppercase tracking-wider mt-1.5 mb-1" style="page-break-inside: avoid; break-inside: avoid;">
                    {{ $mLabel }} ({{ $mReports->count() }} {{ \Illuminate\Support\Str::plural('Incident', $mReports->count()) }})
                </div>
                <div class="table-responsive w-full mb-1">
                    <table class="report-table w-full border-collapse" style="font-size: 9px; table-layout: fixed; width: 100%;">
                        <thead>
                            <tr style="background-color: #0d3b66; color: #ffffff;">
                                <th class="p-1 border border-slate-700 text-center uppercase font-black" style="width: 26px;">#</th>
                                <th class="p-1 border border-slate-700 text-left uppercase font-black" style="width: 90px;">Date &amp; Time</th>
                                <th class="p-1 border border-slate-700 text-left uppercase font-black" style="width: 105px;">Complainant</th>
                                <th class="p-1 border border-slate-700 text-left uppercase font-black" style="width: 115px;">Driver Involved</th>
                                <th class="p-1 border border-slate-700 text-left uppercase font-black" style="width: 100px;">Category</th>
                                <th class="p-1 border border-slate-700 text-center uppercase font-black" style="width: 85px;">Status</th>
                                <th class="p-1 border border-slate-700 text-left uppercase font-black">Description &amp; Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mReports as $index => $r)
                                @php 
                                    $dProf = optional($r->driver)->driverProfile;
                                    $st = strtolower($r->status);
                                @endphp
                                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-slate-50' }} border-b border-slate-200" style="page-break-inside: avoid; break-inside: avoid;">
                                    <td class="p-1 border border-slate-200 text-center font-bold" style="overflow: hidden;">{{ $index + 1 }}</td>
                                    <td class="p-1 border border-slate-200 text-[8.5px] leading-tight" style="overflow: hidden;">
                                        <div class="font-semibold text-slate-900 whitespace-nowrap">{{ $r->created_at ? $r->created_at->format('M j, Y') : '—' }}</div>
                                        <div class="text-[8px] text-slate-500 font-medium whitespace-nowrap mt-0.5">{{ $r->created_at ? $r->created_at->format('g:i A') : '' }}</div>
                                    </td>
                                    <td class="p-1 border border-slate-200 font-bold text-blue-700 truncate" style="overflow: hidden; text-overflow: ellipsis;">{{ $r->reporter->name ?? 'Deleted User' }}</td>
                                    <td class="p-1 border border-slate-200" style="overflow: hidden;">
                                        <strong class="text-slate-900 block truncate">{{ $r->driver->name ?? 'Deleted User' }}</strong>
                                        @if($dProf && $dProf->mtop_number)
                                            <span class="text-[8px] text-blue-600 font-bold block truncate">(MTOP {{ $dProf->mtop_number }})</span>
                                        @endif
                                    </td>
                                    <td class="p-1 border border-slate-200 font-bold text-[8.5px]" style="overflow: hidden; word-break: break-word;">{{ $r->category }}</td>
                                    <td class="p-1 border border-slate-200 text-center font-black" style="overflow: hidden; font-size: 8.5px; white-space: nowrap;">
                                        @if($st === 'resolved')
                                            <span style="color: #047857; font-weight: 900; letter-spacing: 0.02em;">RESOLVED</span>
                                        @elseif($st === 'investigating')
                                            <span style="color: #1d4ed8; font-weight: 900; font-size: 8px; letter-spacing: -0.01em;">INVESTIGATING</span>
                                        @elseif($st === 'dismissed')
                                            <span style="color: #475569; font-weight: 900; letter-spacing: 0.02em;">DISMISSED</span>
                                        @else
                                            <span style="color: #b45309; font-weight: 900; letter-spacing: 0.02em;">PENDING</span>
                                        @endif
                                    </td>
                                    <td class="p-1 border border-slate-200 text-[8.5px] text-slate-600 leading-snug" style="word-break: break-word;">{{ $r->description }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="subtotal-row text-right text-[10px] font-bold text-slate-700 py-0.5 mb-1" style="page-break-inside: avoid; break-inside: avoid;">
                    Subtotal ({{ strtoupper($mLabel) }}): {{ $mReports->count() }}
                </div>
            @empty
                <div class="p-4 text-center text-slate-400 font-medium">No incident records found.</div>
            @endforelse

            <div class="grand-total-row text-right text-[10px] font-black text-slate-900 py-0.5 border-t-2 border-slate-900 mt-0.5 mb-1" style="page-break-inside: avoid; break-inside: avoid;">
                Total incident reports: {{ number_format($totalReports) }}
            </div>

        <!-- ─── FORMAT 3: MASTER FLEET DRIVERS ROSTER ─── -->
        @elseif ($reportType === 'drivers_roster')
            @forelse ($groupedDriversByStatus as $statusLabel => $statusDrivers)
                <div class="group-banner p-1 px-2.5 font-black text-[10px] uppercase tracking-wider {{ strtolower($statusLabel) === 'approved' ? 'text-emerald-800 bg-emerald-100 border-l-4 border-emerald-600' : (strtolower($statusLabel) === 'pending' ? 'text-amber-800 bg-amber-100 border-l-4 border-amber-600' : 'text-red-800 bg-red-100 border-l-4 border-red-600') }} mt-1.5 mb-1" style="page-break-inside: avoid; break-inside: avoid;">
                    {{ $statusLabel }} DRIVERS (Status: {{ ucwords(strtolower($statusLabel)) }})
                </div>
                <div class="table-responsive w-full mb-1">
                    <table class="report-table w-full border-collapse" style="font-size: 9px; table-layout: fixed; width: 100%;">
                        <thead>
                            <tr style="background-color: #0d3b66; color: #ffffff;">
                                <th class="p-1 border border-slate-700 text-center uppercase font-black" style="width: 26px;">#</th>
                                <th class="p-1 border border-slate-700 text-left uppercase font-black" style="width: 140px;">Driver Name</th>
                                <th class="p-1 border border-slate-700 text-left uppercase font-black" style="width: 90px;">MTOP Number</th>
                                <th class="p-1 border border-slate-700 text-left uppercase font-black" style="width: 105px;">Registered Date</th>
                                <th class="p-1 border border-slate-700 text-left uppercase font-black" style="width: 130px;">Contact / Email</th>
                                <th class="p-1 border border-slate-700 text-left uppercase font-black">Notes / Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($statusDrivers as $index => $d)
                                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-slate-50' }} border-b border-slate-200" style="page-break-inside: avoid; break-inside: avoid;">
                                    <td class="p-1 border border-slate-200 text-center font-bold" style="overflow: hidden;">{{ $index + 1 }}</td>
                                    <td class="p-1 border border-slate-200 font-bold text-blue-700 truncate" style="overflow: hidden; text-overflow: ellipsis;">{{ $d->full_name }}</td>
                                    <td class="p-1 border border-slate-200 font-black" style="overflow: hidden;">{{ $d->mtop_number ?? 'N/A' }}</td>
                                    <td class="p-1 border border-slate-200 text-[9px]" style="overflow: hidden;">{{ $d->created_at ? $d->created_at->format('M j, Y') : '—' }}</td>
                                    <td class="p-1 border border-slate-200 text-[9px] text-slate-600 truncate" style="overflow: hidden; text-overflow: ellipsis;">{{ $d->user->email ?? '—' }}</td>
                                    <td class="p-1 border border-slate-200 text-[9px] text-slate-600" style="overflow: hidden;">
                                        @if($d->suspension_reason)
                                            <span class="text-red-600 font-bold">{{ $d->suspension_reason }}</span>
                                        @else
                                            <span>Active fleet member</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="subtotal-row text-right text-[10px] font-bold text-slate-700 py-0.5 mb-1" style="page-break-inside: avoid; break-inside: avoid;">
                    Subtotal ({{ $statusLabel }}): {{ $statusDrivers->count() }}
                </div>
            @empty
                <div class="p-4 text-center text-slate-400 font-medium">No fleet drivers found.</div>
            @endforelse

            <div class="grand-total-row text-right text-[10px] font-black text-slate-900 py-0.5 border-t-2 border-slate-900 mt-0.5 mb-1" style="page-break-inside: avoid; break-inside: avoid;">
                Total fleet roster: {{ number_format($totalDrivers) }} (Approved: {{ $approvedDrivers }} | Pending: {{ $pendingDrivers }} | Suspended: {{ $suspendedDrivers }})
            </div>

        <!-- ─── FORMAT 4: STATISTICS & COMPLIANCE DASHBOARD ─── -->
        @elseif ($reportType === 'statistics')
            <div class="stats-grid grid grid-cols-4 gap-2 mb-2" style="page-break-inside: avoid; break-inside: avoid;">
                <div class="stat-card p-2 rounded border border-slate-200 bg-white" style="border-top: 3px solid #10b981;">
                    <div class="text-[8.5px] font-bold text-slate-500 uppercase tracking-wide">Total Fleet Drivers</div>
                    <div class="text-base font-black text-slate-900 mt-0.5 leading-none">{{ number_format($totalDrivers) }}</div>
                </div>
                <div class="stat-card p-2 rounded border border-slate-200 bg-white" style="border-top: 3px solid #3b82f6;">
                    <div class="text-[8.5px] font-bold text-slate-500 uppercase tracking-wide">Total Reports</div>
                    <div class="text-base font-black text-slate-900 mt-0.5 leading-none">{{ number_format($totalReports) }}</div>
                </div>
                <div class="stat-card p-2 rounded border border-slate-200 bg-white" style="border-top: 3px solid #f59e0b;">
                    <div class="text-[8.5px] font-bold text-slate-500 uppercase tracking-wide">Pending Review</div>
                    <div class="text-base font-black text-slate-900 mt-0.5 leading-none">{{ number_format($pendingReports) }}</div>
                </div>
                <div class="stat-card p-2 rounded border border-slate-200 bg-white" style="border-top: 3px solid #ef4444;">
                    <div class="text-[8.5px] font-bold text-slate-500 uppercase tracking-wide">Resolution Rate</div>
                    <div class="text-base font-black text-slate-900 mt-0.5 leading-none">{{ $resolutionRate }}%</div>
                </div>
            </div>

            <div class="charts-row grid grid-cols-2 gap-2 mb-2" style="page-break-inside: avoid; break-inside: avoid;">
                <div class="chart-box border border-slate-200 rounded p-2 bg-white">
                    <div class="text-[10px] font-black text-slate-900 uppercase tracking-wide mb-1">Incident Reports Volume by Month</div>
                    <div class="bar-chart-visual flex items-end justify-around h-20 pt-2 border-b border-slate-300">
                        @php $maxM = max(1, $monthlyReportVolume->max() ?: 1); @endphp
                        @forelse ($monthlyReportVolume as $month => $count)
                            @php $pct = round(($count / $maxM) * 100); @endphp
                            <div class="bar-col flex flex-col items-center gap-0.5 h-full justify-end">
                                <div class="bar-bar bg-blue-600 rounded-t relative" style="width: 20px; height: {{ max(12, $pct) }}%; min-height: 6px;">
                                    <span class="bar-val absolute -top-3 left-1/2 -translate-x-1/2 text-[8px] font-black text-blue-900">{{ $count }}</span>
                                </div>
                                <span class="bar-lbl text-[8px] font-bold text-slate-500 mt-0.5">{{ $month }}</span>
                            </div>
                        @empty
                            <div class="text-xs text-slate-400 m-auto">No incident volume recorded</div>
                        @endforelse
                    </div>
                </div>

                <div class="chart-box border border-slate-200 rounded p-2 bg-white">
                    <div class="text-[10px] font-black text-slate-900 uppercase tracking-wide mb-1">Driver Fleet Compliance Status</div>
                    <div class="role-donut-visual flex items-center justify-center gap-3 h-20">
                        <div class="donut-circle rounded-full flex items-center justify-center shrink-0" style="width: 64px; height: 64px; background: conic-gradient(#10b981 0% 65%, #f59e0b 65% 85%, #ef4444 85% 95%, #94a3b8 95% 100%);">
                            <div class="donut-hole bg-white rounded-full flex flex-col items-center justify-center text-[10px] font-black text-slate-900 leading-none" style="width: 38px; height: 38px;">
                                {{ $totalDrivers }}
                                <span class="text-[6.5px] text-slate-500 font-bold">drivers</span>
                            </div>
                        </div>
                        <div class="role-legend text-[8.5px] space-y-0.5">
                            <div class="flex items-center gap-1">
                                <span class="w-2 h-2 rounded-xs bg-emerald-500 shrink-0"></span>
                                <strong>Approved</strong>: {{ $approvedDrivers }} ({{ $totalDrivers > 0 ? round(($approvedDrivers/$totalDrivers)*100) : 0 }}%)
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="w-2 h-2 rounded-xs bg-amber-500 shrink-0"></span>
                                <strong>Pending</strong>: {{ $pendingDrivers }} ({{ $totalDrivers > 0 ? round(($pendingDrivers/$totalDrivers)*100) : 0 }}%)
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="w-2 h-2 rounded-xs bg-red-500 shrink-0"></span>
                                <strong>Suspended</strong>: {{ $suspendedDrivers }} ({{ $totalDrivers > 0 ? round(($suspendedDrivers/$totalDrivers)*100) : 0 }}%)
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TOP REPORT CATEGORIES TABLE -->
            <div class="mb-1.5" style="page-break-inside: avoid; break-inside: avoid;">
                <div class="text-[10px] font-black text-slate-900 uppercase tracking-wide mb-0.5">Top Passenger Report Categories</div>
                <div class="table-responsive w-full">
                    <table class="report-table w-full border-collapse" style="font-size: 9px;">
                        <thead>
                            <tr style="background-color: #0d3b66; color: #ffffff;">
                                <th class="p-1 border border-slate-700 text-center uppercase font-black" style="width: 26px;">#</th>
                                <th class="p-1 border border-slate-700 text-left uppercase font-black">Category Name</th>
                                <th class="p-1 border border-slate-700 text-center uppercase font-black" style="width: 85px;">Incident Count</th>
                                <th class="p-1 border border-slate-700 text-left uppercase font-black" style="width: 130px;">Share (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $catIdx = 1; @endphp
                            @forelse ($topCategories as $catName => $catCount)
                                @php $catPct = $totalReports > 0 ? round(($catCount / $totalReports) * 100, 1) : 0; @endphp
                                <tr class="{{ $catIdx % 2 === 0 ? 'bg-white' : 'bg-slate-50' }} border-b border-slate-200">
                                    <td class="p-1 border border-slate-200 text-center font-bold">{{ $catIdx++ }}</td>
                                    <td class="p-1 border border-slate-200 font-bold">{{ $catName }}</td>
                                    <td class="p-1 border border-slate-200 text-center font-bold">{{ number_format($catCount) }}</td>
                                    <td class="p-1 border border-slate-200">
                                        <div class="flex items-center gap-1">
                                            <div class="flex-1 h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                                <div class="h-full bg-blue-600" style="width: {{ $catPct }}%;"></div>
                                            </div>
                                            <span class="text-[8.5px] font-bold text-slate-600 min-w-[24px]">{{ $catPct }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="p-2 text-center text-slate-400">No dispute categories logged.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- OFFICIAL INSIGHT SUMMARY -->
            <div class="insight-card border border-emerald-300 bg-emerald-50 rounded p-1.5 mb-1.5" style="page-break-inside: avoid; break-inside: avoid;">
                <div class="text-[10px] font-black text-emerald-900 uppercase tracking-wide mb-0.5">TODA Safety Operations &amp; Compliance Insight</div>
                <div class="text-[9px] text-emerald-950 leading-relaxed">
                    During the reporting period <strong>({{ $dateRangeLabel }})</strong>, a total of <strong>{{ number_format($totalReports) }}</strong> passenger complaints were recorded with an overall resolution rate of <strong>{{ $resolutionRate }}%</strong>.
                    Active fleet compliance stands at <strong>{{ $approvedDrivers }} verified drivers</strong> operating under Santa Rosa Homes TODA guidelines. All safety disputes, appeals, and suspension logs are documented in compliance with Barangay Ordinance and TODA safety protocols.
                </div>
            </div>

            <div class="grand-total-row text-right text-[10px] font-black text-slate-900 py-0.5 border-t-2 border-slate-900 mt-0.5 mb-1" style="page-break-inside: avoid; break-inside: avoid;">
                Total records analyzed: {{ number_format($totalReports) }} Incident Reports &bull; {{ number_format($totalDrivers) }} Fleet Drivers
            </div>
        @endif

        <!-- ─── SIGNATORY BLOCK (PROPORTIONATE TYPOGRAPHY IN NATURAL CONTENT FLOW) ─── -->
        <div class="signatory-section mt-3 mb-1 flex justify-start shrink-0" style="page-break-inside: avoid; break-inside: avoid;">
            <div class="signatory-box text-center" style="width: 250px;">
                <div class="sig-lead text-left font-semibold text-slate-600 mb-2.5" style="font-size: 10px;">Prepared by:</div>
                <div class="sig-name font-black text-slate-950 pb-0.5" style="font-size: 11px; border-bottom: 1.5px solid #0f172a;">{{ $signatoryName }}</div>
                <div class="sig-title font-semibold text-slate-700 mt-0.5" style="font-size: 9px;">{{ $signatoryTitle }}</div>
                <div class="sig-caption italic text-slate-400 mt-0.5" style="font-size: 8px;">Signature over printed name</div>
            </div>
        </div>

    </div>

    <!-- ─── RUNNING DOCUMENT FOOTER (PINNED AT THE BOTTOM OF THE A4 SHEET) ─── -->
    <div class="report-footer mt-auto pt-2 border-t border-slate-300 flex justify-between items-end text-slate-500 leading-tight shrink-0" style="font-size: 8.5px; page-break-inside: avoid; break-inside: avoid;">
        <div>
            <strong class="text-slate-700">Barangay Lourdes &bull; SRH-LINK-TODA</strong><br>
            Santa Rosa, Nueva Ecija, Philippines<br>
            Contact: 0917 000 1234 &bull; Email: srhlink.toda@gmail.com
        </div>
        <div class="text-right">
            Date Printed: {{ now()->format('F j, Y') }} &bull; Time: {{ now()->format('g:i A') }}<br>
            <em>Generated by: SRH-LINK-TODA Information Management System</em>
        </div>
    </div>

</article>
