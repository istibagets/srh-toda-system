<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Ride History') }}"></x-page-header>
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
                    <!-- Total spent -->
                    <div class="bg-emerald-50/80 rounded-2xl p-2.5 sm:p-4 border border-emerald-100/90 flex flex-col justify-between min-w-0">
                        <span class="text-[9px] sm:text-xs font-black uppercase tracking-wider text-emerald-700 truncate">Fares Paid</span>
                        <span class="text-xs sm:text-xl md:text-2xl font-black text-emerald-600 mt-1 truncate" title="₱{{ number_format($totalSpent ?? $rides->where('status', 'completed')->sum('fare'), 2) }}">₱{{ number_format($totalSpent ?? $rides->where('status', 'completed')->sum('fare'), 2) }}</span>
                    </div>
                    <!-- All trips -->
                    <div class="bg-slate-50/90 rounded-2xl p-2.5 sm:p-4 border border-slate-100 flex flex-col justify-between min-w-0">
                        <span class="text-[9px] sm:text-xs font-black uppercase tracking-wider text-slate-400 truncate">All Trips</span>
                        <span class="text-base sm:text-2xl font-black text-slate-900 mt-1 truncate">{{ $totalAll ?? $rides->total() }}</span>
                    </div>
                </div>

                @if($rides->isNotEmpty())
                    <div class="mt-3 pt-2.5 sm:mt-4 sm:pt-3 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider hidden xs:inline">Passenger Activity</span>
                        <form action="{{ route('history.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear your ride history view?');" class="ml-auto">
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
                    <h4 class="text-lg font-black text-gray-900">No Past Trips Recorded</h4>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mt-2 max-w-sm mx-auto">Book a TODA ride to reach your destination quickly, safely, and conveniently!</p>
                    <a href="{{ route('dashboard') }}" class="mt-6 inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-blue-500/25 transition">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        <span>Book a Ride Now</span>
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
                            <input type="text" id="history-search" autocomplete="off" placeholder="Search by place, driver, or status…"
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

                <!-- Day-Grouped Collapsible Ledger -->
                <div id="history-ledger" class="bg-white rounded-3xl shadow-sm border border-gray-100 divide-y divide-gray-100 overflow-hidden">
                    @include('passenger.partials.history-groups', ['rides' => $rides])
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
                    <p class="mt-1 text-xs text-gray-400 font-semibold">Try a different place, driver, or status.</p>
                </div>
            @endif

        </div>
    </div>

    <!-- Rating Modal -->
    <div id="passenger-rate-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
        <div class="bg-white w-full max-w-sm rounded-3xl p-6 shadow-2xl text-center relative border border-slate-100 animate-in fade-in zoom-in-95 duration-200">
            <button type="button" onclick="closeRateModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center font-bold text-sm cursor-pointer">&times;</button>
            <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-3 text-amber-500 border border-amber-100">
                <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
            </div>
            <h3 class="text-lg font-black text-slate-900 mb-0.5">Rate Your Trip</h3>
            <p class="text-xs text-slate-500 font-semibold mb-4" id="rate-modal-driver-subtitle">Rate your experience</p>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3 text-left">
                <div class="min-w-0">
                    <p id="rate-modal-driver-name" class="text-xs font-black text-slate-900 truncate">TODA Driver</p>
                    <p id="rate-modal-route" class="text-[11px] font-bold text-slate-500 truncate">Pickup to Dropoff</p>
                </div>
                <div class="text-right shrink-0">
                    <span class="text-[10px] font-black uppercase text-emerald-600 block">Paid</span>
                    <span id="rate-modal-fare" class="text-sm font-black text-emerald-600">₱30.00</span>
                </div>
            </div>

            <form id="history-rating-form" action="" method="POST" onsubmit="handleHistoryRatingSubmit(event, this)" class="space-y-4 mt-6">
                @csrf
                <input type="hidden" name="rating" id="history-rating-val" value="">
                <input type="hidden" name="feedback_tags" id="history-tags-val" value="">

                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-2">Tap Stars to Rate</span>
                    <div class="flex items-center justify-center gap-2" id="history-star-container">
                        @for($i=1;$i<=5;$i++)
                            <button type="button" onclick="setHistoryStarRating({{ $i }})" id="hist-star-btn-{{ $i }}" class="text-4xl hover:scale-110 transition-all focus:outline-none cursor-pointer" style="color: #cbd5e1 !important; background: transparent !important; border: none !important;">☆</button>
                        @endfor
                    </div>
                    <p id="history-star-label" class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-2">Tap stars to rate</p>
                </div>

                <button type="submit" id="history-rating-submit-btn" class="w-full py-3.5 px-4 rounded-2xl font-black text-sm uppercase tracking-wider text-white shadow-lg flex items-center justify-center gap-2 active:scale-95 transition-transform cursor-pointer" style="background-color: #2563eb !important; color: #ffffff !important; border: none;">
                    Submit Rating
                </button>
            </form>
        </div>
    </div>

    <script>
        (function() {
            var currentHistoryTags = [];

            // ── Accordion Collapsible Logic ──
            var ledger = document.getElementById('history-ledger');
            var search = document.getElementById('history-search');
            var pills = [].slice.call(document.querySelectorAll('.history-status-pill'));
            var countEl = document.getElementById('history-count');
            var noResultsEl = document.getElementById('history-no-results');
            var loadBtn = document.getElementById('history-load-more-btn');
            var loadContainer = document.getElementById('history-load-more-container');
            var loadSpinner = document.getElementById('history-load-spinner');
            var loadText = document.getElementById('history-load-text');
            var totalAllServer = parseInt(loadBtn ? loadBtn.getAttribute('data-total-all') : '{{ $totalAll ?? $rides->total() }}', 10);
            var groups = [].slice.call(document.querySelectorAll('.history-group'));
            var activeStatus = '';

            function bindGroupToggle(group) {
                var toggle = group.querySelector('.history-group-toggle');
                var body = group.querySelector('.history-group-body');
                var chevron = group.querySelector('.history-group-chevron');
                if (!toggle || !body) return;

                toggle.onclick = function() {
                    var isCollapsed = body.classList.contains('hidden');
                    if (isCollapsed) {
                        body.classList.remove('hidden');
                        if (chevron) chevron.style.transform = 'rotate(0deg)';
                    } else {
                        body.classList.add('hidden');
                        if (chevron) chevron.style.transform = 'rotate(-90deg)';
                    }
                };
                if (chevron) chevron.style.transform = body.classList.contains('hidden') ? 'rotate(-90deg)' : 'rotate(0deg)';
            }

            groups.forEach(bindGroupToggle);

            // ── Search & Status Filtering ──
            function applyFilters() {
                var query = search ? (search.value || '').trim().toLowerCase() : '';
                var visibleCount = 0;

                groups.forEach(function(group) {
                    var rows = [].slice.call(group.querySelectorAll('.history-row'));
                    var groupVisible = 0;

                    rows.forEach(function(row) {
                        var status = row.getAttribute('data-status') || '';
                        var searchData = row.getAttribute('data-search') || '';

                        var matchStatus = !activeStatus || status === activeStatus;
                        var matchQuery = !query || searchData.indexOf(query) !== -1;

                        if (matchStatus && matchQuery) {
                            row.classList.remove('hidden');
                            groupVisible++;
                            visibleCount++;
                        } else {
                            row.classList.add('hidden');
                        }
                    });

                    var body = group.querySelector('.history-group-body');
                    var chevron = group.querySelector('.history-group-chevron');

                    if (groupVisible > 0) {
                        group.classList.remove('hidden');
                        if (query || activeStatus) {
                            if (body) body.classList.remove('hidden');
                            if (chevron) chevron.classList.add('rotate-180');
                        }
                    } else {
                        group.classList.add('hidden');
                    }
                });

                if (countEl) countEl.textContent = 'Showing ' + visibleCount + ' of ' + totalAllServer + ' trips';
                if (noResultsEl && ledger) {
                    if (visibleCount === 0 && totalAllServer > 0) {
                        noResultsEl.classList.remove('hidden');
                        ledger.classList.add('hidden');
                    } else {
                        noResultsEl.classList.add('hidden');
                        ledger.classList.remove('hidden');
                    }
                }
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

            if (search) {
                search.addEventListener('input', function() {
                    applyFilters();
                    triggerServerSearch();
                });
            }

            pills.forEach(function(pill) {
                pill.addEventListener('click', function() {
                    activeStatus = pill.getAttribute('data-status') || '';
                    pills.forEach(function(p) {
                        var aCls = (p.getAttribute('data-active-class') || '').split(' ').filter(Boolean);
                        var iCls = (p.getAttribute('data-inactive-class') || '').split(' ').filter(Boolean);
                        if (p === pill) {
                            iCls.forEach(c => p.classList.remove(c));
                            aCls.forEach(c => p.classList.add(c));
                        } else {
                            aCls.forEach(c => p.classList.remove(c));
                            iCls.forEach(c => p.classList.add(c));
                        }
                    });
                    applyFilters();
                    triggerServerSearch();
                });
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

            // ── Rate Modal Logic ──
            window.openRateModalForHistory = function(rideId, driverName, pickup, dest, fare) {
                const modal = document.getElementById('passenger-rate-modal');
                const form = document.getElementById('history-rating-form');
                if (!modal || !form) return;

                form.action = '/rides/' + rideId + '/rate';
                document.getElementById('rate-modal-driver-subtitle').textContent = 'Rate your experience with ' + driverName;
                document.getElementById('rate-modal-driver-name').textContent = driverName;
                document.getElementById('rate-modal-route').textContent = pickup + ' to ' + dest;
                document.getElementById('rate-modal-fare').textContent = '₱' + fare;

                document.getElementById('history-rating-val').value = '';
                document.getElementById('history-tags-val').value = '';
                currentHistoryTags = [];

                for (let i = 1; i <= 5; i++) {
                    const b = document.getElementById('hist-star-btn-' + i);
                    if (b) {
                        b.textContent = '☆';
                        b.style.color = '#cbd5e1';
                    }
                }
                const lEl = document.getElementById('history-star-label');
                if (lEl) {
                    lEl.textContent = 'Tap stars to rate';
                    lEl.className = 'text-xs font-bold text-slate-400 uppercase tracking-wider mt-2';
                }

                modal.classList.remove('hidden');
            };

            window.closeRateModal = function() {
                const modal = document.getElementById('passenger-rate-modal');
                if (modal) modal.classList.add('hidden');
            };

            window.setHistoryStarRating = function(rating) {
                document.getElementById('history-rating-val').value = rating;
                const labels = {1:'Terrible (1/5)', 2:'Poor (2/5)', 3:'Okay (3/5)', 4:'Good! (4/5)', 5:'Excellent! (5/5)'};
                const lEl = document.getElementById('history-star-label');
                if (lEl) {
                    lEl.textContent = labels[rating] || '';
                    lEl.className = 'text-xs font-black text-amber-600 uppercase tracking-wider mt-2';
                }

                for (let i = 1; i <= 5; i++) {
                    const b = document.getElementById('hist-star-btn-' + i);
                    if (b) {
                        if (i <= rating) {
                            b.textContent = '★';
                            b.style.color = '#fbbf24';
                        } else {
                            b.textContent = '☆';
                            b.style.color = '#cbd5e1';
                        }
                    }
                }
            };

            window.toggleHistoryTag = function(btn, tag) {
                const idx = currentHistoryTags.indexOf(tag);
                if (idx > -1) {
                    currentHistoryTags.splice(idx, 1);
                    btn.classList.remove('bg-emerald-600', 'text-white', 'border-emerald-600');
                    btn.classList.add('bg-slate-100', 'text-slate-700', 'border-slate-200');
                } else {
                    currentHistoryTags.push(tag);
                    btn.classList.remove('bg-slate-100', 'text-slate-700', 'border-slate-200');
                    btn.classList.add('bg-emerald-600', 'text-white', 'border-emerald-600');
                }
                document.getElementById('history-tags-val').value = currentHistoryTags.join(', ');
            };

            window.handleHistoryRatingSubmit = function(event, form) {
                event.preventDefault();
                const val = document.getElementById('history-rating-val').value;
                if (!val) {
                    if (window.createSlidingToast) window.createSlidingToast('Please tap a star to rate your trip.', 'warning');
                    return;
                }

                closeRateModal();
                if (window.clearPageCache) window.clearPageCache();

                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    if (window.createSlidingToast) window.createSlidingToast(data.message || 'Trip rated! Thank you.', 'success');
                    if (window.navigateTo) window.navigateTo(window.location.href, false, false, true);
                })
                .catch(() => {
                    if (window.navigateTo) window.navigateTo(window.location.href, false, false, true);
                });
            };
        })();
    </script>

    @include('passenger.partials.report-modal')
</x-app-layout>