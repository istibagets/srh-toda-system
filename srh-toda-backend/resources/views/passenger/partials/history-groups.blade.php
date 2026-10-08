@php
    $groups = $rides->groupBy(fn ($ride) => $ride->created_at->toDateString());
    $todayKey = \Carbon\Carbon::today()->toDateString();
    $yesterdayKey = \Carbon\Carbon::yesterday()->toDateString();
@endphp

@foreach ($groups as $dateKey => $dayRides)
    <section class="history-group" data-group-key="{{ $dateKey }}"
             data-default-collapsed="{{ $loop->first && !request()->has('page') ? 'false' : 'true' }}">

        <button type="button" class="history-group-toggle w-full flex items-center justify-between gap-3 px-4 sm:px-6 py-3 text-left hover:bg-gray-50 transition-colors cursor-pointer border-none bg-transparent">
            <span class="flex items-center gap-2.5 min-w-0">
                <span class="text-xs font-black text-gray-800 uppercase tracking-wider">
                    {{ $dateKey === $todayKey ? 'Today' : ($dateKey === $yesterdayKey ? 'Yesterday' : \Carbon\Carbon::parse($dateKey)->format('D, M j, Y')) }}
                </span>
                <span class="text-[11px] font-bold text-gray-400 shrink-0"><span class="group-count">{{ $dayRides->count() }}</span> {{ $dayRides->count() === 1 ? 'trip' : 'trips' }}</span>
            </span>
            <svg class="history-group-chevron w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
        </button>

        <div class="history-group-body {{ $loop->first && !request()->has('page') ? '' : 'hidden' }}">
            @foreach ($dayRides as $ride)
                @php
                    $driverUser = $ride->driver;
                    $driverProfile = optional($driverUser)->driverProfile;
                    $driverName = $driverUser ? $driverUser->name : 'TODA Driver';
                    $driverMtop = $driverProfile ? $driverProfile->mtop_number : null;
                    $normStatus = ($ride->status === 'completed' || $ride->status === 'cancelled') ? $ride->status : 'in_progress';
                    $searchText = strtolower(($ride->pickup_location ?? '') . ' ' . ($ride->destination ?? '') . ' ' . $driverName . ' ' . ($driverMtop ?? '') . ' ' . $normStatus);
                @endphp
                <div class="history-row content-auto px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 hover:bg-gray-50/60 transition-colors border-t border-gray-50"
                     data-status="{{ $normStatus }}"
                     data-search="{{ $searchText }}"
                     data-ride-id="{{ $ride->id }}">

                    <!-- Date & Time -->
                    <div class="sm:w-40 shrink-0">
                        <p class="text-xs font-extrabold text-gray-900">{{ $ride->created_at->format('M d, Y') }}</p>
                        <p class="text-[11px] text-gray-400 font-semibold mt-0.5">{{ $ride->created_at->format('h:i A') }}</p>
                    </div>

                    <!-- Route -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800 flex items-center gap-2 min-w-0">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                            <span class="truncate">{{ $ride->pickup_location }}</span>
                            <span class="text-gray-300 shrink-0">→</span>
                            <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>
                            <span class="truncate">{{ $ride->destination }}</span>
                        </p>
                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                            <span class="inline-flex items-center gap-1 text-[11px] text-blue-700 font-bold bg-blue-50 px-2 py-0.5 rounded-lg border border-blue-100">
                                <svg class="w-3 h-3 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                <span>{{ $driverName }}</span>
                            </span>
                            @if($driverMtop)
                                <span class="text-[10px] font-black text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded-md">MTOP #{{ $driverMtop }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Fare & Status & Rating -->
                    <div class="sm:w-56 shrink-0 flex flex-row sm:flex-col items-center sm:items-end justify-between gap-1.5 text-right">
                        <div class="flex items-center gap-2">
                            <span class="text-sm sm:text-base font-black text-slate-900">₱{{ number_format($ride->fare ?? 30.00, 2) }}</span>
                            @if($ride->status === 'completed')
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800">Completed</span>
                            @elseif($ride->status === 'cancelled')
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-800">Cancelled</span>
                            @else
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800">In Progress</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 mt-1">
                            @if($ride->status === 'completed')
                                @if($ride->rating)
                                    <div class="flex items-center gap-0.5 text-amber-500 text-xs font-black" title="Rated {{ $ride->rating }}/5">
                                        @for($s=1; $s<=5; $s++)
                                            <span>{{ $s <= $ride->rating ? '★' : '☆' }}</span>
                                        @endfor
                                    </div>
                                @else
                                    <button type="button" onclick="openRateModalForHistory({{ $ride->id }}, '{{ addslashes($driverName) }}', '{{ addslashes($ride->pickup_location) }}', '{{ addslashes($ride->destination) }}', '{{ number_format($ride->fare ?? 30.00, 2) }}')" class="px-2 py-0.5 bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-[10px] rounded-lg border border-amber-200/80 transition cursor-pointer">
                                        ★ Rate Driver
                                    </button>
                                @endif
                            @endif

                            <!-- Report Incident Button -->
                            <button type="button" 
                                    onclick="openPassengerReportModalFromRide({{ $ride->id }}, {{ $ride->driver_id ?? 'null' }}, '{{ addslashes($driverName) }}')" 
                                    class="text-[10px] font-bold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-100 px-1.5 py-0.5 rounded-md transition cursor-pointer flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Report</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endforeach
