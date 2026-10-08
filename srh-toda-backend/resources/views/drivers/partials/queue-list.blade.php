@php
    $isAdmin = auth()->check() && auth()->user()->role === 'admin';
@endphp

<div id="live-queue-wrapper" class="space-y-6 pb-28">
    <!-- 1. Active Terminal Queue Section -->
    <div class="space-y-3">
        <div class="flex items-center justify-between px-1 mb-1 min-w-0 gap-2">
            <h3 class="text-xs font-black uppercase tracking-wider text-gray-500 flex items-center gap-1.5 min-w-0 truncate">
                <span class="truncate">Active Terminal Queue</span>
                <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-black shrink-0">{{ count($activeQueue) }}</span>
            </h3>
            @if($isAdmin)
                <span class="text-[10px] font-bold text-gray-400 shrink-0 hidden sm:inline">Drag handle (⋮⋮) to reorder</span>
            @endif
        </div>

        <div id="queue-items-container" class="space-y-3">
            @forelse($activeQueue as $index => $queueDriver)
                <div class="draggable-queue-item bg-white/95 backdrop-blur-md border border-gray-200/80 p-3 sm:p-4 pr-3.5 sm:pr-5 rounded-2xl flex justify-between items-center gap-2 min-w-0 shadow-sm hover:shadow-md transition-all duration-200"
                     data-driver-id="{{ $queueDriver->id }}">
                    
                    <!-- Left Side: Drag Handle + Queue Badge + Driver Name -->
                    <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                        @if($isAdmin)
                            <!-- Drag Handle Icon (⋮⋮) for Touch & Mouse Dragging -->
                            <div class="drag-handle shrink-0 p-1 text-gray-400 hover:text-blue-600 rounded-xl hover:bg-blue-50 transition cursor-grab active:cursor-grabbing select-none" data-no-sheet-drag title="Drag to reorder queue position">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <circle cx="9" cy="6" r="1.75"/>
                                    <circle cx="15" cy="6" r="1.75"/>
                                    <circle cx="9" cy="12" r="1.75"/>
                                    <circle cx="15" cy="12" r="1.75"/>
                                    <circle cx="9" cy="18" r="1.75"/>
                                    <circle cx="15" cy="18" r="1.75"/>
                                </svg>
                            </div>
                        @endif

                        <span class="queue-number-badge font-black text-sm sm:text-base px-2.5 py-1 rounded-xl shadow-xs transition-all {{ ($queueDriver->queue_position === 1 || $loop->first) ? 'bg-blue-600 text-white shadow-sm' : 'bg-blue-50 text-blue-700 border border-blue-200' }} shrink-0" data-no-sheet-drag>
                            #{{ $queueDriver->queue_position ?? ($loop->iteration) }}
                        </span>
                        
                        <div class="flex flex-col min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 min-w-0">
                                <span class="text-gray-900 font-black text-xs sm:text-sm tracking-tight truncate flex-1 min-w-0">{{ $queueDriver->full_name }}</span>
                                @if($queueDriver->user_id === auth()->id())
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-[9px] font-black bg-green-100 text-green-800 border border-green-300 uppercase tracking-wider shrink-0 leading-none">
                                        YOU
                                    </span>
                                @endif
                            </div>
                            <span class="text-[10px] font-bold text-gray-400 mt-0.5">MTOP {{ $queueDriver->mtop_number ?? 'N/A' }}</span>
                        </div>
                    </div>

                </div>
            @empty
                <div class="text-center text-gray-500 p-8 bg-white/60 backdrop-blur-md rounded-3xl border-2 border-dashed border-gray-300 shadow-inner">
                    <p class="font-black text-gray-700 text-base">No drivers currently waiting in the terminal queue.</p>
                    <p class="text-xs text-gray-400 mt-1">Drivers will appear here when they go online.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- 2. Drivers Currently On Trip / In Transit Section -->
    <div class="mt-8 space-y-3 pt-6 border-t border-gray-200/80">
        <div class="flex items-center justify-between px-1 min-w-0 gap-2">
            <h3 class="text-xs font-black uppercase tracking-wider text-emerald-700 flex items-center gap-1.5 min-w-0 truncate">
                <span class="truncate">Drivers Currently On Trip</span>
                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black shrink-0">{{ count($driversInTransit ?? []) }}</span>
            </h3>
            <span class="text-[10px] font-extrabold text-emerald-600 shrink-0 hidden xs:inline">Active Trips</span>
        </div>

        <div id="on-trip-items-container" class="space-y-3">
                @forelse($driversInTransit ?? [] as $ride)
                @php
                    $statusBadge = match($ride->status) {
                        'fare_proposed' => ['label' => 'Fare Proposed', 'bg' => 'bg-amber-100 text-amber-800 border-amber-200'],
                        'fare_accepted' => ['label' => 'Fare Confirmed', 'bg' => 'bg-emerald-100 text-emerald-800 border-emerald-200'],
                        'accepted' => ['label' => 'En Route', 'bg' => 'bg-blue-100 text-blue-800 border-blue-200'],
                        'arrived' => ['label' => 'At Pickup', 'bg' => 'bg-amber-100 text-amber-800 border-amber-200'],
                        'in_transit' => ['label' => 'In Transit', 'bg' => 'bg-emerald-100 text-emerald-800 border-emerald-200'],
                        'returning' => ['label' => 'Returning To Terminal', 'bg' => 'bg-violet-100 text-violet-800 border-violet-200'],
                        default => ['label' => 'On Trip', 'bg' => 'bg-gray-100 text-gray-800 border-gray-200']
                    };
                    $driverName = $ride->driverProfile->full_name ?? ($ride->driver->name ?? 'TODA Driver');
                    $mtopNum = $ride->driverProfile->mtop_number ?? 'N/A';
                @endphp
                
                <div class="bg-gradient-to-r from-emerald-50/60 via-white to-white border border-emerald-200/80 p-3 sm:p-4 rounded-2xl shadow-xs space-y-2 min-w-0" data-ride-id="{{ $ride->id }}">
                    <div class="flex items-center justify-between gap-2 min-w-0">
                        <div class="flex items-center gap-2 min-w-0 flex-1">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="font-black text-gray-900 text-xs sm:text-base truncate flex-1 min-w-0">{{ $driverName }}</span>
                                    @if($ride->driver_id === auth()->id())
                                        <span class="px-1.5 py-0.5 rounded-full text-[9px] font-black bg-emerald-600 text-white uppercase tracking-wider shrink-0">YOU</span>
                                    @endif
                                </div>
                                <p class="text-[10px] font-bold text-gray-400">MTOP: {{ $mtopNum }}</p>
                            </div>
                        </div>

                        <span class="queue-trip-badge px-2 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider border shadow-xs shrink-0 {{ $statusBadge['bg'] }}">
                            {{ $statusBadge['label'] }}
                        </span>
                    </div>

                    <div class="bg-white/80 rounded-xl p-2.5 border border-emerald-100/90 text-xs font-semibold text-gray-700 flex items-center justify-between gap-2 min-w-0">
                        <div class="truncate min-w-0 flex-1">
                            <span class="text-gray-400 font-bold uppercase text-[9px] block">Route</span>
                            <span class="font-bold text-gray-900 truncate block">{{ $ride->pickup_location }} ➔ {{ $ride->destination }}</span>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-emerald-700 font-black text-xs">₱{{ number_format($ride->fare, 2) }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 p-6 bg-slate-50/80 backdrop-blur-md rounded-2xl border border-dashed border-slate-200 shadow-xs">
                    <p class="font-extrabold text-slate-600 text-xs">No drivers currently on a trip.</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">Drivers on active passenger trips will appear here.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>