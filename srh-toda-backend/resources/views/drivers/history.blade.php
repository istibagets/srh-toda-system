<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Driving History') }}"></x-page-header>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4 space-y-6 pb-24 sm:pb-8">

            <!-- Summary Strip -->
            <div class="bg-white rounded-3xl p-3.5 sm:p-6 shadow-sm border border-gray-100">
                <div class="grid grid-cols-3 gap-2 sm:gap-4">
                    <!-- Completed trips -->
                    <div class="bg-slate-50/90 rounded-2xl p-2.5 sm:p-4 border border-slate-100 flex flex-col justify-between min-w-0">
                        <span class="text-[9px] sm:text-xs font-black uppercase tracking-wider text-slate-400 truncate">Completed</span>
                        <span class="text-base sm:text-2xl font-black text-slate-900 mt-1 truncate">{{ $totalCompleted ?? $rides->where('status', 'completed')->count() }}</span>
                    </div>
                    <!-- Total revenue -->
                    <div class="bg-emerald-50/80 rounded-2xl p-2.5 sm:p-4 border border-emerald-100/90 flex flex-col justify-between min-w-0">
                        <span class="text-[9px] sm:text-xs font-black uppercase tracking-wider text-emerald-700 truncate">Revenue</span>
                        <span class="text-xs sm:text-xl md:text-2xl font-black text-emerald-600 mt-1 truncate" title="₱{{ number_format($totalRevenue ?? $rides->where('status', 'completed')->sum('fare'), 2) }}">₱{{ number_format($totalRevenue ?? $rides->where('status', 'completed')->sum('fare'), 2) }}</span>
                    </div>
                    <!-- All trips -->
                    <div class="bg-slate-50/90 rounded-2xl p-2.5 sm:p-4 border border-slate-100 flex flex-col justify-between min-w-0">
                        <span class="text-[9px] sm:text-xs font-black uppercase tracking-wider text-slate-400 truncate">All Trips</span>
                        <span class="text-base sm:text-2xl font-black text-slate-900 mt-1 truncate">{{ $totalAll ?? $rides->total() }}</span>
                    </div>
                </div>

                @if($rides->isNotEmpty())
                    <div class="mt-3 pt-2.5 sm:mt-4 sm:pt-3 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider hidden xs:inline">Driver Activity</span>
                        <form action="{{ route('history.driver.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear your driving history view?');" class="ml-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-extrabold text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer border-none">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>Clear history view</span>
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            @if($rides->isEmpty())
                <!-- Empty State -->
                <div class="bg-white rounded-3xl p-10 sm:p-12 text-center shadow-sm border border-gray-100">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-blue-100">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                    </div>
                    <h4 class="text-lg font-black text-gray-900">No Fares Recorded Yet</h4>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mt-2 max-w-sm mx-auto">Start taking terminal rides or accepting online dispatches to build up your fare earnings!</p>
                    <a href="{{ route('dashboard') }}" class="mt-6 inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-blue-500/25 transition">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        <span>Return to Driver Hub</span>
                    </a>
                </div>
            @else
                <!-- Search & Filter Toolbar -->
                @php
                    $allTripsCount = $totalAll ?? $rides->total();
                    $completedCount = $totalCompleted ?? $rides->where('status', 'completed')->count();
                    $cancelledCount = $totalCancelled ?? $rides->where('status', 'cancelled')->count();
                    $inProgressCount = max(0, $allTripsCount - $completedCount - $cancelledCount);
                @endphp
                <div class="bg-white rounded-3xl p-5 shadow-sm border border-gray-100">
                    <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4">
                        <div class="relative w-full sm:w-96">
                            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" id="history-search" autocomplete="off" placeholder="Search by place, passenger, or status…"
                                   aria-label="Search trip history"
                                   class="block w-full pl-9 pr-4 py-2.5 text-xs font-bold text-gray-900 border border-gray-200 rounded-2xl bg-gray-50 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <span id="history-count" class="text-xs text-gray-400 font-bold whitespace-nowrap tabular-nums">Showing {{ $rides->count() }} of {{ $allTripsCount }} trips</span>
                    </div>

                    <!-- Status Filter Pills -->
                    <div class="flex flex-wrap items-center gap-1.5 mt-4 pt-4 border-t border-gray-100">
                        <button type="button" data-status="" data-active-class="bg-slate-900 text-white shadow-md" data-inactive-class="bg-gray-100 text-gray-600 hover:bg-gray-200"
                                class="history-status-pill px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition whitespace-nowrap bg-slate-900 text-white shadow-md">
                            All ({{ $allTripsCount }})
                        </button>
                        <button type="button" data-status="completed" data-active-class="bg-emerald-600 text-white shadow-md" data-inactive-class="bg-gray-100 text-gray-600 hover:bg-gray-200"
                                class="history-status-pill px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition whitespace-nowrap bg-gray-100 text-gray-600 hover:bg-gray-200">
                            Completed ({{ $completedCount }})
                        </button>
                        <button type="button" data-status="cancelled" data-active-class="bg-rose-500 text-white shadow-md" data-inactive-class="bg-gray-100 text-gray-600 hover:bg-gray-200"
                                class="history-status-pill px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition whitespace-nowrap bg-gray-100 text-gray-600 hover:bg-gray-200">
                            Cancelled ({{ $cancelledCount }})
                        </button>
                        <button type="button" data-status="in_progress" data-active-class="bg-amber-500 text-white shadow-md" data-inactive-class="bg-gray-100 text-gray-600 hover:bg-gray-200"
                                class="history-status-pill px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition whitespace-nowrap bg-gray-100 text-gray-600 hover:bg-gray-200">
                            In Progress ({{ $inProgressCount }})
                        </button>
                    </div>
                </div>

                <!-- Day-Grouped Ledger -->
                <div id="history-ledger" class="bg-white rounded-3xl shadow-sm border border-gray-100 divide-y divide-gray-100 overflow-hidden">
                    @include('drivers.partials.history-groups', ['rides' => $rides])
                </div>

                <!-- Load More Trips -->
                <div id="history-load-more-container" class="mt-6 text-center {{ $rides->hasMorePages() ? '' : 'hidden' }}">
                    <button type="button" id="history-load-more-btn" data-next-page="{{ $rides->currentPage() + 1 }}" data-total-all="{{ $allTripsCount }}"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-white hover:bg-slate-50 text-slate-800 font-extrabold text-xs uppercase tracking-wider border border-slate-200 shadow-sm hover:shadow active:scale-95 transition-all cursor-pointer">
                        <svg id="history-load-spinner" class="w-4 h-4 text-blue-600 animate-spin hidden" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="history-load-text">Load Older Trips</span>
                    </button>
                </div>

                <!-- No filter matches -->
                <div id="history-no-results" class="hidden bg-white rounded-3xl p-10 text-center shadow-sm border border-gray-100">
                    <h4 class="text-sm font-black text-gray-700">No Matching Trips</h4>
                    <p class="mt-1 text-xs text-gray-400 font-semibold">Try a different place, passenger, or status.</p>
                </div>
            @endif

        </div>
    </div>

    @if($rides->isNotEmpty())
    <script>
        (function () {
            var ledger = document.getElementById('history-ledger');
            var search = document.getElementById('history-search');
            var pills = [].slice.call(document.querySelectorAll('.history-status-pill'));
            var activeStatus = '';
            var count = document.getElementById('history-count');
            var noResults = document.getElementById('history-no-results');
            var loadBtn = document.getElementById('history-load-more-btn');
            var loadContainer = document.getElementById('history-load-more-container');
            var loadSpinner = document.getElementById('history-load-spinner');
            var loadText = document.getElementById('history-load-text');
            var totalAllServer = parseInt(loadBtn ? loadBtn.getAttribute('data-total-all') : '{{ $totalAll ?? $rides->total() }}', 10);
            var groups = [].slice.call(document.querySelectorAll('.history-group'));

            function groupBody(g) { return g.querySelector('.history-group-body'); }
            function chevron(g) { return g.querySelector('.history-group-chevron'); }

            function bindGroupToggle(g) {
                var btn = g.querySelector('.history-group-toggle');
                var body = groupBody(g);
                if (!btn || !body) return;
                btn.onclick = function () {
                    var collapsed = body.classList.toggle('hidden');
                    chevron(g).style.transform = collapsed ? 'rotate(-90deg)' : 'rotate(0deg)';
                    btn.setAttribute('aria-expanded', String(!collapsed));
                };
                chevron(g).style.transform = body.classList.contains('hidden') ? 'rotate(-90deg)' : 'rotate(0deg)';
            }

            groups.forEach(bindGroupToggle);

            function rowMatches(row) {
                var q = search.value.trim().toLowerCase();
                var s = activeStatus;
                if (q && row.getAttribute('data-search').indexOf(q) === -1) return false;
                if (s && row.getAttribute('data-status') !== s) return false;
                return true;
            }

            function isFiltering() {
                return search.value.trim() !== '' || activeStatus !== '';
            }

            pills.forEach(function (pill) {
                pill.addEventListener('click', function () {
                    pills.forEach(function (p) {
                        p.classList.remove.apply(p.classList, p.getAttribute('data-active-class').split(' '));
                        p.classList.add.apply(p.classList, p.getAttribute('data-inactive-class').split(' '));
                        p.setAttribute('aria-pressed', 'false');
                    });
                    pill.classList.remove.apply(pill.classList, pill.getAttribute('data-inactive-class').split(' '));
                    pill.classList.add.apply(pill.classList, pill.getAttribute('data-active-class').split(' '));
                    pill.setAttribute('aria-pressed', 'true');
                    activeStatus = pill.getAttribute('data-status');
                    applyFilters();
                    triggerServerSearch();
                });
            });

            function applyFilters() {
                var visible = 0;
                groups.forEach(function (g) {
                    var body = groupBody(g);
                    var rows = [].slice.call(g.querySelectorAll('.history-row'));
                    var matching = 0;
                    rows.forEach(function (r) {
                        var show = rowMatches(r);
                        r.classList.toggle('hidden', !show);
                        if (show) matching++;
                    });

                    if (matching === 0) {
                        g.classList.add('hidden');
                    } else {
                        g.classList.remove('hidden');
                        visible += matching;
                        if (isFiltering()) {
                            body.classList.remove('hidden');
                            chevron(g).style.transform = 'rotate(0deg)';
                        } else {
                            var defaultCollapsed = g.getAttribute('data-default-collapsed') === 'true';
                            body.classList.toggle('hidden', defaultCollapsed);
                            chevron(g).style.transform = defaultCollapsed ? 'rotate(-90deg)' : 'rotate(0deg)';
                        }
                    }
                });

                count.textContent = 'Showing ' + visible + ' of ' + totalAllServer + ' trips';
                noResults.classList.toggle('hidden', visible !== 0);
                ledger.classList.toggle('hidden', visible === 0);
                if (loadContainer) {
                    loadContainer.classList.toggle('hidden', !loadBtn || loadBtn.getAttribute('data-has-more') === '0');
                }
            }

            // Server-side live search for older/unloaded database records
            var searchDebounceTimer = null;
            var currentSearchAbort = null;

            function triggerServerSearch() {
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(async function () {
                    var q = search ? search.value.trim() : '';
                    if (currentSearchAbort) {
                        try { currentSearchAbort.abort(); } catch (e) {}
                    }
                    currentSearchAbort = new AbortController();

                    try {
                        var url = new URL(window.location.origin + '/history');
                        if (q) url.searchParams.set('search', q);
                        if (activeStatus) url.searchParams.set('status', activeStatus);
                        url.searchParams.set('page', '1');

                        var res = await fetch(url.toString(), {
                            signal: currentSearchAbort.signal,
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!res.ok) return;
                        var data = await res.json();
                        if (data.html) {
                            ledger.innerHTML = data.html;
                            groups = [].slice.call(ledger.querySelectorAll('.history-group'));
                            groups.forEach(bindGroupToggle);
                            applyFilters();

                            if (loadBtn && loadContainer) {
                                if (data.hasMore) {
                                    loadBtn.setAttribute('data-next-page', data.nextPage);
                                    loadBtn.setAttribute('data-has-more', '1');
                                    loadContainer.classList.remove('hidden');
                                } else {
                                    loadBtn.setAttribute('data-has-more', '0');
                                    loadContainer.classList.add('hidden');
                                }
                            }
                        }
                    } catch (e) {
                        if (e.name !== 'AbortError') console.error(e);
                    }
                }, 300);
            }

            search.addEventListener('input', function () {
                applyFilters();
                triggerServerSearch();
            });

            // Progressive Load More
            var isLoading = false;
            async function handleLoadMore() {
                if (isLoading || !loadBtn) return;
                var nextPage = parseInt(loadBtn.getAttribute('data-next-page') || '2', 10);
                isLoading = true;
                loadSpinner.classList.remove('hidden');
                loadText.textContent = 'Loading older trips...';
                loadBtn.disabled = true;

                try {
                    var url = new URL(window.location.origin + '/history');
                    url.searchParams.set('page', nextPage);
                    var q = search ? search.value.trim() : '';
                    if (q) url.searchParams.set('search', q);
                    if (activeStatus) url.searchParams.set('status', activeStatus);

                    var res = await fetch(url.toString(), {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!res.ok) throw new Error('HTTP error ' + res.status);
                    var data = await res.json();

                    if (data.html) {
                        var parser = new DOMParser();
                        var doc = parser.parseFromString(data.html, 'text/html');
                        var newGroups = [].slice.call(doc.querySelectorAll('.history-group'));

                        newGroups.forEach(function (ng) {
                            var groupKey = ng.getAttribute('data-group-key');
                            var existingGroup = ledger.querySelector('.history-group[data-group-key="' + groupKey + '"]');
                            if (existingGroup) {
                                var existingBody = existingGroup.querySelector('.history-group-body');
                                var newRows = [].slice.call(ng.querySelectorAll('.history-row'));
                                newRows.forEach(function (nr) {
                                    existingBody.appendChild(nr);
                                });
                                var countSpan = existingGroup.querySelector('.group-count');
                                if (countSpan) {
                                    countSpan.textContent = existingGroup.querySelectorAll('.history-row').length;
                                }
                            } else {
                                ledger.appendChild(ng);
                                bindGroupToggle(ng);
                            }
                        });

                        groups = [].slice.call(ledger.querySelectorAll('.history-group'));
                        applyFilters();
                    }

                    if (data.hasMore) {
                        loadBtn.setAttribute('data-next-page', data.nextPage);
                        loadBtn.setAttribute('data-has-more', '1');
                        loadText.textContent = 'Load Older Trips';
                        loadBtn.disabled = false;
                    } else {
                        loadBtn.setAttribute('data-has-more', '0');
                        loadContainer.classList.add('hidden');
                    }
                } catch (err) {
                    console.error('Failed to load history page', err);
                    loadText.textContent = 'Retry Loading Trips';
                    loadBtn.disabled = false;
                } finally {
                    loadSpinner.classList.add('hidden');
                    isLoading = false;
                }
            }

            if (loadBtn) {
                loadBtn.addEventListener('click', handleLoadMore);
            }
        })();
    </script>
    @endif
</x-app-layout>