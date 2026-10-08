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
                    {{ $dateKey === $todayKey ? 'Today' : ($dateKey === $yesterdayKey ? 'Yesterday' : \Carbon\Carbon::parse($dateKey)->format('D, M j')) }}
                </span>
                <span class="text-[11px] font-bold text-gray-400 shrink-0"><span class="group-count">{{ $dayRides->count() }}</span> {{ $dayRides->count() === 1 ? 'trip' : 'trips' }}</span>
            </span>
            <svg class="history-group-chevron w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
        </button>

        <div class="history-group-body {{ $loop->first && !request()->has('page') ? '' : 'hidden' }}">
            @foreach ($dayRides as $ride)
                @php
                    $paxName = $ride->passenger_id ? (\App\Models\User::find($ride->passenger_id)->name ?? 'App Passenger') : 'Terminal walk-in';
                    $normStatus = ($ride->status === 'completed' || $ride->status === 'cancelled') ? $ride->status : 'in_progress';
                    $searchText = strtolower(($ride->pickup_location ?? '') . ' ' . ($ride->destination ?? '') . ' ' . $paxName . ' ' . $normStatus);
                @endphp
                <div class="history-row px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 hover:bg-gray-50/60 transition-colors border-t border-gray-50"
                     data-status="{{ $normStatus }}"
                     data-search="{{ $searchText }}"
                     data-ride-id="{{ $ride->id }}">

                    <!-- Date -->
                    <div class="sm:w-44 shrink-0">
                        <p class="text-xs font-extrabold text-gray-900">{{ $ride->created_at->format('M d, Y') }}</p>
                        <p class="text-[11px] text-gray-400 font-semibold mt-0.5">{{ $ride->created_at->format('h:i A') }}</p>
                    </div>

                    <!-- Route -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800 flex items-center gap-2 min-w-0">
                            <span class="truncate">{{ $ride->pickup_location }}</span>
                            <span class="text-gray-300 shrink-0">→</span>
                            <span class="truncate">{{ $ride->destination }}</span>
                        </p>
                        <p class="text-[11px] text-gray-400 font-semibold mt-1">
                            @if($ride->passenger_id)
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    <span>{{ $paxName }}</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                                    <span>Terminal walk-in</span>
                                </span>
                            @endif
                        </p>
                    </div>

                    <!-- Status + Fare -->
                    <div class="flex items-center justify-between sm:justify-end gap-4 sm:gap-5 shrink-0">
                        @if($ride->status === 'completed')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[11px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Completed
                            </span>
                        @elseif($ride->status === 'cancelled')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[11px] font-black uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200/80">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                Cancelled
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[11px] font-black uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200/80">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                In Progress
                            </span>
                        @endif
                        <span class="text-sm font-black text-gray-900 tabular-nums">₱{{ number_format($ride->fare ?? 30.00, 2) }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endforeach
