@php
    $sortedReports = $reports ? $reports->sortByDesc('created_at') : collect();
    $totalCount = $sortedReports->count();
    $pendingCount = $sortedReports->where('status', 'pending')->count();
    $investigatingCount = $sortedReports->where('status', 'investigating')->count();
    $resolvedCount = $sortedReports->where('status', 'resolved')->count();
    $dismissedCount = $sortedReports->where('status', 'dismissed')->count();
@endphp

<div class="space-y-5" x-data="{
    reportStatusFilter: 'all',
    reportDateFilter: 'all',
    setReportStatus(status) {
        this.reportStatusFilter = status;
        this.applyFilters();
    },
    setReportDate(dateRange) {
        this.reportDateFilter = dateRange;
        this.applyFilters();
    },
    applyFilters() {
        const status = this.reportStatusFilter.toLowerCase();
        const dateRange = this.reportDateFilter.toLowerCase();
        const search = (document.getElementById('reportSearchInput')?.value || '').toLowerCase().trim();
        const cards = document.querySelectorAll('.report-feed-card');
        
        const now = new Date();
        const startOfToday = new Date(now.getFullYear(), now.getMonth(), now.getDate()).getTime();
        const startOfWeek = new Date(now.setDate(now.getDate() - now.getDay())).getTime();
        const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1).getTime();

        let visibleCount = 0;

        cards.forEach(card => {
            const cardStatus = (card.getAttribute('data-status') || '').toLowerCase();
            const cardTimestamp = parseInt(card.getAttribute('data-timestamp') || '0', 10) * 1000;
            const cardSearchText = (card.getAttribute('data-search') || card.textContent).toLowerCase();

            // Status Match
            const matchesStatus = (status === 'all' || cardStatus === status);

            // Date Match
            let matchesDate = true;
            if (dateRange === 'today') {
                matchesDate = cardTimestamp >= startOfToday;
            } else if (dateRange === 'this_week') {
                matchesDate = cardTimestamp >= startOfWeek;
            } else if (dateRange === 'this_month') {
                matchesDate = cardTimestamp >= startOfMonth;
            }

            // Search Match
            const matchesSearch = (!search || cardSearchText.includes(search));

            if (matchesStatus && matchesDate && matchesSearch) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const emptyEl = document.getElementById('no-filtered-reports');
        if (emptyEl) {
            emptyEl.style.display = (visibleCount === 0 && cards.length > 0) ? 'block' : 'none';
        }
    }
}" x-init="$watch('reportStatusFilter', () => applyFilters()); $watch('reportDateFilter', () => applyFilters());">

    <!-- ─── 1. TOP CASE LOAD SUMMARY METRICS ─── -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="p-4 rounded-2xl bg-white border border-gray-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black text-gray-500 uppercase tracking-wider">Total Reports</span>
                <span class="w-7 h-7 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-black text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-gray-900 mt-1.5">{{ number_format($totalCount) }}</div>
            <span class="text-[10px] text-gray-400 font-medium">All logged cases</span>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-amber-200 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black text-amber-800 uppercase tracking-wider">Pending Review</span>
                <span class="w-7 h-7 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-black text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-amber-700 mt-1.5">{{ number_format($pendingCount) }}</div>
            <span class="text-[10px] text-amber-600 font-medium">Requires action</span>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-emerald-200 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black text-emerald-800 uppercase tracking-wider">Resolved</span>
                <span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-emerald-700 mt-1.5">{{ number_format($resolvedCount) }}</div>
            <span class="text-[10px] text-emerald-600 font-medium">Closed cases</span>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-gray-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-wider">Dismissed</span>
                <span class="w-7 h-7 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-black text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-700 mt-1.5">{{ number_format($dismissedCount) }}</div>
            <span class="text-[10px] text-slate-400 font-medium">Invalid or dismissed</span>
        </div>
    </div>

    <!-- ─── 2. FILTERS, DATE SWITCHER & SEARCH TOOLBAR ─── -->
    <div class="bg-white rounded-3xl p-4 sm:p-5 border border-gray-200 shadow-sm space-y-3.5">
        
        <!-- Search & Date Filter Row -->
        <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3">
            <!-- Search Bar -->
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none text-gray-400" style="left: 14px !important;">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" id="reportSearchInput" 
                       @input="applyFilters()"
                       aria-label="Search reports by passenger, driver, or category"
                       style="padding-left: 2.75rem !important; min-height: 42px;"
                       class="block w-full pr-4 py-2.5 text-xs font-bold text-gray-900 border border-gray-200 rounded-2xl bg-gray-50/80 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition placeholder:text-gray-400"
                       placeholder="Search by passenger, reported driver, category, or description...">
            </div>

            <!-- Date Range Filter Selector (Non-scrollable, responsive grid/flex) -->
            <div class="grid grid-cols-4 sm:flex items-center gap-1 bg-gray-100/90 p-1 rounded-2xl shrink-0 text-xs text-center">
                <button type="button" @click="setReportDate('all')"
                        :class="reportDateFilter === 'all' ? 'bg-white text-slate-900 font-black shadow-xs' : 'text-gray-600 hover:text-gray-900 font-bold'"
                        class="px-2.5 sm:px-3 py-1.5 rounded-xl transition border-none cursor-pointer text-center">
                    All Time
                </button>
                <button type="button" @click="setReportDate('today')"
                        :class="reportDateFilter === 'today' ? 'bg-white text-slate-900 font-black shadow-xs' : 'text-gray-600 hover:text-gray-900 font-bold'"
                        class="px-2.5 sm:px-3 py-1.5 rounded-xl transition border-none cursor-pointer text-center">
                    Today
                </button>
                <button type="button" @click="setReportDate('this_week')"
                        :class="reportDateFilter === 'this_week' ? 'bg-white text-slate-900 font-black shadow-xs' : 'text-gray-600 hover:text-gray-900 font-bold'"
                        class="px-2.5 sm:px-3 py-1.5 rounded-xl transition border-none cursor-pointer text-center">
                    This Week
                </button>
                <button type="button" @click="setReportDate('this_month')"
                        :class="reportDateFilter === 'this_month' ? 'bg-white text-slate-900 font-black shadow-xs' : 'text-gray-600 hover:text-gray-900 font-bold'"
                        class="px-2.5 sm:px-3 py-1.5 rounded-xl transition border-none cursor-pointer text-center">
                    This Month
                </button>
            </div>
        </div>

        <!-- Status Filter Pills (Non-scrollable, Natural Wrap) -->
        <div class="flex flex-wrap items-center gap-1.5 text-xs border-t border-gray-100 pt-3">
            <button type="button" @click="setReportStatus('all')"
                    :style="reportStatusFilter === 'all' ? 'background-color: #0f172a !important; color: #ffffff !important;' : 'background-color: #f1f5f9 !important; color: #334155 !important;'"
                    class="px-3 py-1.5 rounded-xl font-bold transition flex items-center gap-1.5 shrink-0 border-none cursor-pointer">
                <span>All Statuses</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black shrink-0 leading-none" 
                      :style="reportStatusFilter === 'all' ? 'background-color: rgba(255,255,255,0.25) !important; color: #ffffff !important;' : 'background-color: #e2e8f0 !important; color: #334155 !important;'">
                    {{ $totalCount }}
                </span>
            </button>

            <button type="button" @click="setReportStatus('pending')"
                    :style="reportStatusFilter === 'pending' ? 'background-color: #f59e0b !important; color: #ffffff !important;' : 'background-color: #f1f5f9 !important; color: #334155 !important;'"
                    class="px-3 py-1.5 rounded-xl font-bold transition flex items-center gap-1.5 shrink-0 border-none cursor-pointer">
                <span>Pending Review</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black shrink-0 leading-none" 
                      :style="reportStatusFilter === 'pending' ? 'background-color: rgba(255,255,255,0.25) !important; color: #ffffff !important;' : 'background-color: #e2e8f0 !important; color: #334155 !important;'">
                    {{ $pendingCount }}
                </span>
            </button>

            <button type="button" @click="setReportStatus('investigating')"
                    :style="reportStatusFilter === 'investigating' ? 'background-color: #2563eb !important; color: #ffffff !important;' : 'background-color: #f1f5f9 !important; color: #334155 !important;'"
                    class="px-3 py-1.5 rounded-xl font-bold transition flex items-center gap-1.5 shrink-0 border-none cursor-pointer">
                <span>Investigating</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black shrink-0 leading-none" 
                      :style="reportStatusFilter === 'investigating' ? 'background-color: rgba(255,255,255,0.25) !important; color: #ffffff !important;' : 'background-color: #e2e8f0 !important; color: #334155 !important;'">
                    {{ $investigatingCount }}
                </span>
            </button>

            <button type="button" @click="setReportStatus('resolved')"
                    :style="reportStatusFilter === 'resolved' ? 'background-color: #059669 !important; color: #ffffff !important;' : 'background-color: #f1f5f9 !important; color: #334155 !important;'"
                    class="px-3 py-1.5 rounded-xl font-bold transition flex items-center gap-1.5 shrink-0 border-none cursor-pointer">
                <span>Resolved</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black shrink-0 leading-none" 
                      :style="reportStatusFilter === 'resolved' ? 'background-color: rgba(255,255,255,0.25) !important; color: #ffffff !important;' : 'background-color: #e2e8f0 !important; color: #334155 !important;'">
                    {{ $resolvedCount }}
                </span>
            </button>

            <button type="button" @click="setReportStatus('dismissed')"
                    :style="reportStatusFilter === 'dismissed' ? 'background-color: #475569 !important; color: #ffffff !important;' : 'background-color: #f1f5f9 !important; color: #334155 !important;'"
                    class="px-3 py-1.5 rounded-xl font-bold transition flex items-center gap-1.5 shrink-0 border-none cursor-pointer">
                <span>Dismissed</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black shrink-0 leading-none" 
                      :style="reportStatusFilter === 'dismissed' ? 'background-color: rgba(255,255,255,0.25) !important; color: #ffffff !important;' : 'background-color: #e2e8f0 !important; color: #334155 !important;'">
                    {{ $dismissedCount }}
                </span>
            </button>
        </div>

    </div>

    <!-- ─── 3. OFFICIAL INCIDENTS FEED (NEWEST ON TOP) ─── -->
    <div class="space-y-3.5" id="reportsFeedContainer">
        @forelse($sortedReports as $report)
            @php
                $repStatus = strtolower($report->status ?? 'pending');
                $driverUser = $report->driver;
                $dProf = optional($report->driver)->driverProfile;
                $driverName = $report->driver->name ?? 'TODA Fleet Driver';
                $driverAvatar = ($driverUser && $driverUser->profile_photo_url) ? route('user.avatar', [$driverUser->id, 'v' => optional($driverUser->updated_at)->timestamp]) : null;
                $mtopNumber = $dProf->mtop_number ?? null;

                $passengerUser = $report->reporter;
                $passengerName = $report->reporter->name ?? 'Anonymous Passenger';
                $passengerEmail = $report->reporter->email ?? null;
                $passengerAvatar = ($passengerUser && $passengerUser->profile_photo_url) ? route('user.avatar', [$passengerUser->id, 'v' => optional($passengerUser->updated_at)->timestamp]) : null;
                $timestamp = $report->created_at ? $report->created_at->timestamp : 0;
            @endphp

            <div class="report-feed-card bg-white rounded-3xl p-5 border border-gray-200/90 shadow-sm transition hover:border-blue-300 hover:shadow-md space-y-4"
                 id="report-card-{{ $report->id }}"
                 data-status="{{ $repStatus }}"
                 data-timestamp="{{ $timestamp }}"
                 data-search="{{ strtolower($report->id . ' ' . $report->category . ' ' . $report->description . ' ' . $driverName . ' ' . ($mtopNumber ?? '') . ' ' . $passengerName . ' ' . $repStatus . ' ' . ($report->admin_notes ?? '')) }}">
                
                <!-- 1. Card Header: Category & Case # on Left, Status Pill & Date on Right -->
                <div class="flex items-center justify-between gap-3 sm:gap-4 pb-3 border-b border-gray-100 flex-wrap sm:flex-nowrap">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <!-- Category Badge: always nowrap, bold, clean padding & clear spacing -->
                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-black uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200/90 whitespace-nowrap shrink-0">
                            {{ $report->category }}
                        </span>
                        <!-- Report ID -->
                        <span class="text-xs font-bold text-slate-400 shrink-0">Case #{{ $report->id }}</span>
                    </div>

                    <div class="flex items-center gap-2.5 shrink-0 ml-auto flex-wrap sm:flex-nowrap">
                        <!-- Status Pill -->
                        @if($repStatus === 'resolved')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200 whitespace-nowrap shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                Resolved
                            </span>
                        @elseif($repStatus === 'investigating')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-black uppercase tracking-wider bg-blue-100 text-blue-800 border border-blue-200 whitespace-nowrap shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                                Investigating
                            </span>
                        @elseif($repStatus === 'dismissed')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-black uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200 whitespace-nowrap shrink-0">
                                Dismissed
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200 whitespace-nowrap shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                Pending Review
                            </span>
                        @endif

                        <!-- Date & Time (Clean formatted string with non-breaking space between time and AM/PM) -->
                        <span class="text-xs font-bold text-slate-500 whitespace-nowrap shrink-0 ml-1.5">
                            {{ $report->created_at ? $report->created_at->format('M d, Y • h:i A') : 'Recent' }}
                            @if($report->created_at)
                                <span class="text-[10px] text-slate-400 ml-0.5">({{ $report->created_at->diffForHumans() }})</span>
                            @endif
                        </span>
                    </div>
                </div>

                <!-- 2. Report Description Content -->
                <div class="space-y-2">
                    <p class="text-sm text-gray-800 font-medium whitespace-pre-line leading-relaxed">
                        {{ $report->description ?: 'No detailed explanation provided by the complainant.' }}
                    </p>

                    <!-- Attached Proof / Photo if available -->
                    @if($report->proof_photo_url)
                        <div class="pt-1">
                            <button type="button" 
                                    onclick="openAttachmentPreviewModal('{{ asset('storage/' . $report->proof_photo_url) }}', 'Passenger Photo Evidence - Case #{{ $report->id }}', event)"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl text-xs font-black text-slate-700 transition cursor-pointer">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>View Photo Evidence</span>
                            </button>
                        </div>
                    @endif
                </div>

                <!-- 3. Reported Parties Strip (With Real Profile Avatars) -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
                    <!-- Reported Driver Info -->
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs shadow-2xs shrink-0 overflow-hidden border border-slate-200 bg-amber-500 text-white">
                            @if($driverAvatar)
                                <img src="{{ $driverAvatar }}" 
                                     alt="{{ $driverName }}" 
                                     class="w-full h-full object-cover rounded-xl" 
                                     onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                <span class="font-black text-white text-xs leading-none uppercase hidden">
                                    {{ strtoupper(substr($driverName, 0, 1)) }}
                                </span>
                            @else
                                <span class="font-black text-white text-xs leading-none uppercase">
                                    {{ strtoupper(substr($driverName, 0, 1)) }}
                                </span>
                            @endif
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">Reported Driver</span>
                            <div class="font-extrabold text-slate-900">
                                {{ $driverName }}
                                @if($mtopNumber)
                                    <span class="text-blue-600 font-bold text-[11px]">(MTOP: {{ $mtopNumber }})</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Complainant Passenger Info -->
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs shadow-2xs shrink-0 overflow-hidden border border-slate-200 bg-blue-600 text-white">
                            @if($passengerAvatar)
                                <img src="{{ $passengerAvatar }}" 
                                     alt="{{ $passengerName }}" 
                                     class="w-full h-full object-cover rounded-xl" 
                                     onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                <span class="font-black text-white text-xs leading-none uppercase hidden">
                                    {{ strtoupper(substr($passengerName, 0, 1)) }}
                                </span>
                            @else
                                <span class="font-black text-white text-xs leading-none uppercase">
                                    {{ strtoupper(substr($passengerName, 0, 1)) }}
                                </span>
                            @endif
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">Complainant Passenger</span>
                            <div class="font-extrabold text-slate-900">
                                {{ $passengerName }}
                                @if($passengerEmail)
                                    <span class="text-gray-400 font-normal text-[11px]">({{ $passengerEmail }})</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Official Admin Resolution Notes (If present) -->
                @if($report->admin_notes)
                    <div class="p-3 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 text-xs space-y-1">
                        <span class="text-[10px] font-black text-emerald-800 uppercase tracking-wider block">Official Resolution Notes:</span>
                        <p class="text-emerald-950 font-medium leading-relaxed">{{ $report->admin_notes }}</p>
                    </div>
                @endif

                <!-- 5. Card Actions Footer -->
                <div class="flex items-center justify-between gap-2 pt-2 border-t border-gray-100">
                    <div class="text-[11px] text-gray-400 font-medium">
                        Last updated {{ $report->updated_at ? $report->updated_at->diffForHumans() : 'recently' }}
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Update Status / Action Button -->
                        <button type="button" 
                                onclick="openUpdateReportModal({{ json_encode([
                                    'id' => $report->id,
                                    'category' => $report->category,
                                    'created_at' => $report->created_at ? $report->created_at->format('M d, Y • g:i A') : '',
                                    'description' => $report->description,
                                    'reporter' => ['name' => $passengerName, 'email' => $passengerEmail],
                                    'driver' => ['name' => $driverName . ($mtopNumber ? ' (MTOP ' . $mtopNumber . ')' : '')],
                                    'status' => $report->status,
                                    'admin_notes' => $report->admin_notes
                                ]) }})"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-xs transition cursor-pointer border-none flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Review &amp; Action</span>
                        </button>

                        <!-- Delete Button -->
                        <button type="button" 
                                onclick="deleteReport({{ $report->id }})" 
                                class="px-2.5 py-2 bg-slate-100 hover:bg-red-50 hover:text-red-700 text-slate-500 rounded-xl text-xs font-bold transition cursor-pointer border-none"
                                title="Delete report">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>

            </div>
        @empty
            <div class="bg-white rounded-3xl p-12 text-center border border-gray-200 shadow-sm">
                <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h4 class="font-extrabold text-gray-800 text-base">No Passenger Reports Logged</h4>
                <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">All fleet operations are running smoothly with zero active safety complaints.</p>
            </div>
        @endforelse

        <!-- Empty state when search/filter returns zero results -->
        <div id="no-filtered-reports" style="display: none;" class="bg-white rounded-3xl p-10 text-center border border-gray-200 shadow-sm">
            <p class="text-sm font-bold text-gray-600">No reports match your selected status or search query.</p>
            <button type="button" @click="setReportStatus('all'); setReportDate('all'); document.getElementById('reportSearchInput').value = ''; applyFilters();" class="mt-2 text-xs font-black text-blue-600 hover:underline border-none bg-transparent cursor-pointer">
                Clear Filters
            </button>
        </div>
    </div>

</div>
