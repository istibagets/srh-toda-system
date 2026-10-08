<x-app-layout>
    @php
        $activeRide = \App\Models\Ride::with('driver')->where('passenger_id', auth()->id())
            ->whereIn('status', ['searching','fare_proposed','fare_accepted','accepted','arrived','in_transit'])
            ->first();

        $latestCompletedRide = \App\Models\Ride::with('driver')->where('passenger_id', auth()->id())
            ->whereIn('status', ['completed', 'returning'])->latest()->first();

        $unratedRide = ($latestCompletedRide && is_null($latestCompletedRide->rating) && $latestCompletedRide->updated_at >= now()->subHours(24))
            ? $latestCompletedRide
            : null;

        if ($unratedRide && session('skipped_ride_' . $unratedRide->id)) { $unratedRide = null; }

        $bookingMode = !$activeRide;

        $lastRideStatus = null;
        if (!$activeRide) {
            $latestEndedRide = \App\Models\Ride::where('passenger_id', auth()->id())
                ->whereIn('status', ['completed', 'cancelled'])
                ->latest('updated_at')->first();
            if ($latestEndedRide) { $lastRideStatus = $latestEndedRide->status; }
        }
    @endphp

    <style>
        @@keyframes toastFade { 0%{opacity:0;transform:translate(-50%,-1rem)} 10%{opacity:1;transform:translate(-50%,0)} 80%{opacity:1} 100%{opacity:0;pointer-events:none} }
        .auto-fade-toast { animation:toastFade 4.5s ease-out forwards; }
        @@keyframes ping { 75%, 100% { transform: scale(2.2); opacity: 0; } }
        .pax-driver-pulse { animation: ping 1.8s cubic-bezier(0, 0, 0.2, 1) infinite; }
        .min-h-screen:has(#passenger-status-wrapper) header { display: none; }
        /* Ride states: hide the mobile bottom nav so it never covers the sheet
           buttons (Accept/Decline fare) sitting at the bottom of the screen. */
        .min-h-screen:has(#passenger-status-wrapper[data-status]:not([data-status="none"])) #mobile-bottom-nav { display: none !important; }

        /* ðŸš€ Grab-style glass panel */
        .srh-wait-glass {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.6);
            box-shadow: 0 20px 40px -15px rgba(15,23,42,0.18);
        }
        .srh-hud-glass {
            background: rgba(15,23,42,0.82);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(255,255,255,0.12);
            box-shadow: 0 8px 32px rgba(0,0,0,0.35);
        }

        /* Bottom sheet sliding — locked static margins so header never expands or shifts */
        /* Bottom sheet sliding — locked static margins so header never expands or shifts */
        #pax-booking-sheet {
            padding-top: 8px !important;
            padding-left: 16px !important;
            padding-right: 16px !important;
            will-change: auto !important;
            overflow: visible !important;
            overflow-x: visible !important;
            overflow-y: visible !important;
        }
        #pax-booking-sheet.srh-edge-bottom-sheet {
            overflow: visible !important;
            overflow-x: visible !important;
            overflow-y: visible !important;
        }
        #pax-sheet-drag-handle {
            height: auto !important;
            min-height: unset !important;
            max-height: unset !important;
            flex-shrink: 0 !important;
            padding-top: 2px !important;
            padding-bottom: 2px !important;
            margin-top: 0px !important;
            margin-bottom: 0px !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: flex-start !important;
        }
        #pax-sheet-drag-handle > div:first-child {
            margin-bottom: 4px !important;
            margin-top: 0px !important;
            flex-shrink: 0 !important;
            width: 48px !important;
            height: 4px !important;
        }
        #pax-sheet-drag-handle > div:last-child {
            flex-shrink: 0 !important;
            margin-top: 0px !important;
            margin-bottom: 0px !important;
        }
        #dest-autocomplete-dropdown {
            position: absolute !important;
            top: auto !important;
            bottom: calc(100% + 8px) !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            margin-top: 0px !important;
            margin-bottom: 0px !important;
            z-index: 999999 !important;
            background: #ffffff !important;
            border-radius: 20px !important;
            border: 1px solid rgba(226, 232, 240, 0.95) !important;
            box-shadow: 0 25px 60px -10px rgba(15, 23, 42, 0.5), 0 0 0 1px rgba(0, 0, 0, 0.1) !important;
            max-height: 280px !important;
            overflow-y: auto !important;
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }
        #dest-autocomplete-dropdown::-webkit-scrollbar {
            display: none !important;
            width: 0px !important;
            height: 0px !important;
        }

        #pax-sheet-details-content {
            max-height: none !important;
            overflow: visible !important;
            overflow-x: visible !important;
            overflow-y: visible !important;
            margin-top: 6px !important;
            transition: none !important;
        }
        html, body {
            overscroll-behavior-y: none !important;
            overscroll-behavior: none !important;
            -webkit-overscroll-behavior: none !important;
        }
        #pax-booking-sheet,
        #pax-booking-sheet * {
            touch-action: none !important;
            overscroll-behavior: none !important;
            overscroll-behavior-y: none !important;
            -webkit-overscroll-behavior: none !important;
            -webkit-user-drag: none;
        }

        /* Fare proposed pulse */
        @@keyframes fareGlow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(245,158,11,0.5); }
            50% { box-shadow: 0 0 0 12px rgba(245,158,11,0); }
        }
        .fare-glow { animation: fareGlow 1.8s ease-in-out infinite; }

        /* Driver found success ring */
        @@keyframes successRing {
            0%, 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0.5); }
            50% { box-shadow: 0 0 0 14px rgba(16,185,129,0); }
        }
        .success-ring { animation: successRing 1.6s ease-in-out infinite; }

        /* Card entrance: fade + slide-up + scale when the action block content swaps */
        @@keyframes paxCardIn {
            0% { opacity: 0; transform: translateY(10px) scale(0.98); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }
        .pax-card-in { animation: paxCardIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) both; }

        /* Status pill / hud tint changes slide instead of snapping */
        #pax-wait-hud-icon, #pax-hud-icon {
            transition: background-color 0.35s ease, transform 0.2s ease;
        }
        #pax-sheet-drag-handle .rounded-full {
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Cancel button subtle urgency pulse */
        @@keyframes paxCancelPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(225,29,72,0.35); }
            50% { box-shadow: 0 0 0 8px rgba(225,29,72,0); }
        }
        .pax-cancel-pulse { animation: paxCancelPulse 2s ease-in-out infinite; }

        /* âš¡ 1-Shot Click Ripple Feedback (0.6s duration) â€” Driver Hub parity */
        @@keyframes clickRipplePulse {
            0% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.6), 0 10px 25px rgba(15, 23, 42, 0.25); transform: scale(0.88); }
            50% { box-shadow: 0 0 0 20px rgba(37, 99, 235, 0), 0 15px 35px rgba(37, 99, 235, 0.35); transform: scale(1.05); }
            100% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0), 0 10px 25px rgba(15, 23, 42, 0.25); transform: scale(1); }
        }
        .click-ripple-pulse { animation: clickRipplePulse 0.6s ease-out 1 !important; }

        /* ðŸŒŸ Smooth Form & Sheet Content Transition Animation â€” Driver Hub parity */
        .sheet-content-animate-in {
            animation: paxCardIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) both !important;
            will-change: opacity, transform;
        }

        /* ðŸŒŠ Glassy state-swap choreography for the waiting sheet (Driver Hub parity) */
        @@keyframes paxSwapOut {
            0% { opacity: 1; transform: translateY(0) scale(1); }
            100% { opacity: 0; transform: translateY(-10px) scale(0.98); }
        }
        .pax-swap-out { animation: paxSwapOut 0.18s cubic-bezier(0.4, 0, 1, 1) both !important; }
        @@keyframes paxSwapIn {
            0% { opacity: 0; transform: translateY(16px) scale(0.97); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }
        .pax-swap-in { animation: paxSwapIn 0.45s cubic-bezier(0.16, 1, 0.3, 1) both !important; }
        @@keyframes paxIconPop {
            0% { transform: scale(0.6); opacity: 0; }
            70% { transform: scale(1.12); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        .pax-icon-pop { animation: paxIconPop 0.35s cubic-bezier(0.16, 1, 0.3, 1) both; }

        /* Soft fade for HUD text swaps (wait states + in-place track entry) */
        @@keyframes paxHudFade {
            0% { opacity: 0; transform: translateY(3px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .pax-hud-fade { animation: paxHudFade 0.35s cubic-bezier(0.16, 1, 0.3, 1) both; }

        /* 120Hz ProMotion GPU-Accelerated Sliding Sheets â€” Driver Hub parity */
        #pax-booking-sheet, #pax-sheet-details-content {
            will-change: transform, opacity;
            transform: translate3d(0, 0, 0);
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            transition-timing-function: cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        /* 120Hz ProMotion Floating Action Buttons Spring Motion — Driver Hub parity */
        .floating-action-btn {
            transition: transform 0.15s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: bottom, transform;
            transform: translate3d(0, 0, 0);
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
        }
        #pax-floating-map-controls-container,
        #pax-wait-map-controls-container {
            z-index: 10 !important;
        }
    </style>

    {{-- CONFIRM CANCEL / DECLINE REQUEST MODAL --}}
    <div id="pax-cancel-confirm-modal" class="hidden fixed inset-0 flex items-center justify-center p-4" style="position: fixed; inset: 0; top: 0; left: 0; right: 0; bottom: 0; z-index: 999999 !important; background-color: rgba(15, 23, 42, 0.8) !important; backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);" onclick="if(event.target===this) closePaxCancelModal()">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-center shadow-2xl border border-slate-100 space-y-4 relative z-[1000000] pax-card-in" style="animation-duration: 0.25s;">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center mx-auto shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h3 id="pax-cancel-modal-title" class="text-base font-black text-slate-900 tracking-tight">Cancel this ride request?</h3>
                <p id="pax-cancel-modal-desc" class="text-xs font-medium text-slate-500 mt-1 leading-relaxed">Your ride request will be cancelled. Any driver already reviewing your route will be released back to the queue.</p>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="closePaxCancelModal()" class="flex-1 py-3 px-4 rounded-2xl border border-slate-300 bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs uppercase tracking-wider transition-all active:scale-95 cursor-pointer shadow-xs" style="background-color: #f1f5f9; color: #1e293b; border: 1px solid #cbd5e1;">
                    Keep Waiting
                </button>
                <button type="button" id="pax-cancel-confirm-btn" onclick="confirmPaxCancel()" class="flex-1 py-3 px-4 rounded-2xl text-white font-black text-xs uppercase tracking-wider shadow-lg transition-all active:scale-95 cursor-pointer hover:brightness-110" style="background: linear-gradient(135deg, #e11d48, #be123c) !important; color: #ffffff !important; box-shadow: 0 6px 18px rgba(225, 29, 72, 0.45) !important; border: none !important;">
                    Yes, Cancel
                </button>
            </div>
        </div>
    </div>
    @php
        $_paxWrapLoc = null;
        if ($activeRide && in_array($activeRide->status, ['accepted','arrived','in_transit'])) {
            $_paxWrapLoc = \Illuminate\Support\Facades\Cache::get("ride_driver_location_{$activeRide->id}");
            if (!$_paxWrapLoc && $activeRide->driver_id) {
                $_paxWrapLoc = \Illuminate\Support\Facades\Cache::get("driver_location_{$activeRide->driver_id}");
            }
            if (!$_paxWrapLoc && $activeRide->driverProfile && $activeRide->driverProfile->current_lat && $activeRide->driverProfile->current_lng) {
                $_paxWrapLoc = ['lat' => (float)$activeRide->driverProfile->current_lat, 'lng' => (float)$activeRide->driverProfile->current_lng];
            }
            if (!$_paxWrapLoc && $activeRide->driver && $activeRide->driver->current_lat && $activeRide->driver->current_lng) {
                $_paxWrapLoc = ['lat' => (float)$activeRide->driver->current_lat, 'lng' => (float)$activeRide->driver->current_lng];
            }
            if (!$_paxWrapLoc && $activeRide->pickup_lat && $activeRide->pickup_lng) {
                $_paxWrapLoc = ['lat' => (float)$activeRide->pickup_lat, 'lng' => (float)$activeRide->pickup_lng];
            }
            if (!$_paxWrapLoc) {
                $termLat = 15.429550175641715;
                $termLng = 120.92240292427664;
                $_paxWrapLoc = ['lat' => $termLat, 'lng' => $termLng];
            }
        }
    @endphp
    <div id="passenger-status-wrapper"
         data-status="{{ $activeRide ? $activeRide->status : 'none' }}"
         data-last-status="{{ $lastRideStatus ?? '' }}"
         data-ride-id="{{ $activeRide ? $activeRide->id : '' }}"
         data-driver-id="{{ $activeRide ? ($activeRide->driver_id ?? '') : '' }}"
         data-driver-lat="{{ $activeRide && $_paxWrapLoc ? $_paxWrapLoc['lat'] : '' }}"
         data-driver-lng="{{ $activeRide && $_paxWrapLoc ? $_paxWrapLoc['lng'] : '' }}"
         data-pickup-lat="{{ $activeRide ? ($activeRide->pickup_lat ?? '') : '' }}"
         data-pickup-lng="{{ $activeRide ? ($activeRide->pickup_lng ?? '') : '' }}"
         data-dest-lat="{{ $activeRide ? ($activeRide->destination_lat ?? '') : '' }}"
         data-dest-lng="{{ $activeRide ? ($activeRide->destination_lng ?? '') : '' }}"
         class="relative w-full h-dvh bg-[#f4f3f0] sm:h-[calc(100dvh-65px)] overflow-hidden">

    @if($activeRide && in_array($activeRide->status, ['accepted','arrived','in_transit']))
        {{-- ===== ACTIVE TRACKING MODE ===== --}}
        @php
            $pSavedLoc = \Illuminate\Support\Facades\Cache::get("ride_driver_location_{$activeRide->id}");
            if (!$pSavedLoc && $activeRide->driver_id) {
                $pSavedLoc = \Illuminate\Support\Facades\Cache::get("driver_location_{$activeRide->driver_id}");
            }
            if (!$pSavedLoc && $activeRide->driverProfile && $activeRide->driverProfile->current_lat && $activeRide->driverProfile->current_lng) {
                $pSavedLoc = ['lat' => (float)$activeRide->driverProfile->current_lat, 'lng' => (float)$activeRide->driverProfile->current_lng];
            }
            if (!$pSavedLoc && $activeRide->driver && $activeRide->driver->current_lat && $activeRide->driver->current_lng) {
                $pSavedLoc = ['lat' => (float)$activeRide->driver->current_lat, 'lng' => (float)$activeRide->driver->current_lng];
            }
            if (!$pSavedLoc && $activeRide->pickup_lat && $activeRide->pickup_lng) {
                $pSavedLoc = ['lat' => (float)$activeRide->pickup_lat, 'lng' => (float)$activeRide->pickup_lng];
            }
            if (!$pSavedLoc) {
                $termLat = 15.429550175641715;
                $termLng = 120.92240292427664;
                $pSavedLoc = ['lat' => $termLat, 'lng' => $termLng];
            }
        @endphp

        {{-- Fullscreen Map Background --}}
        <div class="absolute inset-0 z-0 bg-[#f4f3f0]">
            <div id="pax-home-map" class="w-full h-full relative z-0"
                data-pickup-lat="{{ $activeRide->pickup_lat ?? 15.4265 }}" data-pickup-lng="{{ $activeRide->pickup_lng ?? 120.9405 }}"
                data-dest-lat="{{ $activeRide->destination_lat ?? 15.4215 }}" data-dest-lng="{{ $activeRide->destination_lng ?? 120.9350 }}"
                data-status="{{ $activeRide->status }}" data-ride-id="{{ $activeRide->id }}" data-driver-id="{{ $activeRide->driver_id ?? 0 }}"
                data-driver-lat="{{ $pSavedLoc['lat'] }}" data-driver-lng="{{ $pSavedLoc['lng'] }}"></div>

            {{-- Active Trip Overlay Elements --}}
            <div id="pax-active-trip-view" class="transition-all duration-400">
                {{-- Floating Glass HUD --}}
                <div class="absolute top-3 left-3 right-3 z-20 pointer-events-none">
                    <div class="srh-hud-glass rounded-2xl px-4 py-3 flex items-center justify-between gap-3 pointer-events-auto shadow-xl">
                        <div class="flex items-center gap-3 min-w-0">
                            <div id="pax-hud-icon" class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-blue-600 text-white shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p id="pax-hud-dist" class="text-sm font-black text-white truncate leading-tight">
                                    @if($activeRide->status==='accepted') Driver on the way
                                    @elseif($activeRide->status==='arrived') Driver has arrived!
                                    @else In transit to destination @endif
                                </p>
                                <p id="pax-hud-dest" class="text-[11px] font-semibold text-slate-300 truncate">
                                    {{ $activeRide->status==='in_transit' ? $activeRide->destination : $activeRide->pickup_location }}
                                </p>
                            </div>
                        </div>
                        <span id="pax-hud-eta" class="px-2.5 py-1 rounded-full text-xs font-black shrink-0 bg-emerald-950/80 text-emerald-300 border border-emerald-500/50 shadow-sm">
                            @if($activeRide->status === 'arrived')
                                ARRIVED
                            @elseif($activeRide->status === 'in_transit')
                                In Transit
                            @else
                                On the way
                            @endif
                        </span>
                    </div>
                </div>

                {{-- FLOATING MAP CONTROLS (Bottom Right - Fixed above Driver Card) --}}
                <div id="pax-active-trip-map-controls-container" class="absolute z-35 flex flex-col gap-2.5 pointer-events-auto" style="bottom: calc(13.5rem + env(safe-area-inset-bottom, 0px)); right: 16px;">
                    {{-- Custom Re-center Button (Centers map on driver) --}}
                    <button type="button" id="pax-active-recenter-btn" onclick="if(window.recenterPassengerMap) window.recenterPassengerMap();" class="w-11 h-11 rounded-2xl bg-white/95 text-slate-800 shadow-xl border border-slate-200/90 flex items-center justify-center transition-all active:scale-90 cursor-pointer hover:bg-slate-50" title="Re-center Driver Location">
                        <svg class="w-5 h-5 text-slate-700 leading-none select-none" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3c-.46-4.17-3.77-7.48-7.94-7.94V1h-2v2.06C6.83 3.52 3.52 6.83 3.06 11H1v2h2.06c.46 4.17 3.77 7.48 7.94 7.94V23h2v-2.06c4.17-.46 7.48-3.77 7.94-7.94H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/></svg>
                    </button>

                    {{-- Reset Compass / North Button --}}
                    <button type="button" id="pax-active-compass-btn" onclick="const m = window.paxHomeMapInstance || window.bookingMapInstance || window.paxMapInstance; if(m) m.easeTo({ bearing: 0, pitch: 0, duration: 400 });" class="w-11 h-11 rounded-2xl bg-white/95 text-slate-700 shadow-xl border border-slate-200/90 flex items-center justify-center transition-all active:scale-90 cursor-pointer hover:bg-slate-50" title="Reset North">
                        <svg class="w-5 h-5 text-rose-600 leading-none select-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76" fill="currentColor"></polygon>
                        </svg>
                    </button>
                </div>

                {{-- Floating Driver Card --}}
                <div id="pax-active-driver-card" class="absolute z-20 left-3 right-3 bottom-[calc(4.5rem+env(safe-area-inset-bottom,0px))] sm:bottom-6 lg:left-auto lg:right-6 lg:bottom-6 lg:w-[400px] bg-white rounded-3xl p-4 sm:p-5 border border-slate-200 shadow-2xl space-y-3 sm:space-y-4">
                    <div class="flex items-center gap-3.5 pb-4 border-b border-slate-100">
                        <div class="w-14 h-14 rounded-2xl overflow-hidden shrink-0 border-2 border-white shadow-md bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-black text-xl">
                            @if($activeRide->driver && $activeRide->driver->profile_photo_url)
                                <img src="{{ route('user.avatar', [$activeRide->driver, 'v'=>optional($activeRide->driver->updated_at)->timestamp]) }}" alt="{{ $activeRide->driver->name }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($activeRide->driver->name ?? 'D', 0, 1)) }}
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="text-base font-black text-slate-900 truncate">{{ $activeRide->driver->name ?? 'TODA Driver' }}</h4>
                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 text-xs font-black border border-amber-200 shrink-0">
                                    &#9733; {{ number_format($activeRide->driver->average_rating ?? 5.0, 1) }}
                                </span>
                            </div>
                            <p class="text-xs font-bold text-slate-500 truncate">MTOP #{{ optional($activeRide->driverProfile)->mtop_number ?? '001' }} &bull; Santa Rosa TODA</p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-[10px] font-black uppercase text-slate-400 block">Fare</span>
                            <span class="text-xl font-black text-emerald-600">&#8369;{{ number_format($activeRide->fare ?? 30.00, 2) }}</span>
                        </div>
                    </div>

                    {{-- Quick Action Buttons --}}
                    <div class="grid grid-cols-2 gap-2.5">
                        <button type="button" onclick="openRideChatModal({{ $activeRide->id }}, '{{ addslashes($activeRide->driver->name ?? 'TODA Driver') }}', '{{ $activeRide->driver && $activeRide->driver->profile_photo_url ? route('user.avatar', [$activeRide->driver, 'v'=>optional($activeRide->driver->updated_at)->timestamp]) : '' }}', 'Driver')"
                                class="relative py-3 px-4 rounded-2xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center gap-2 transition-colors cursor-pointer border border-blue-200 shadow-xs active:scale-[0.98]">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <span>Chat Driver</span>
                            <span class="srh-chat-unread-badge hidden absolute -top-1.5 -right-1.5 px-2 py-0.5 rounded-full bg-rose-600 text-white font-black text-[10px] shadow-sm animate-bounce">1</span>
                        </button>
                        <button type="button" onclick="openPassengerReportModalFromRide({{ $activeRide->id }}, {{ $activeRide->driver_id ?? 'null' }}, '{{ addslashes($activeRide->driver->name ?? '') }}')"
                                class="py-3 px-4 rounded-2xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs flex items-center justify-center gap-2 transition-colors cursor-pointer border border-rose-200 shadow-xs active:scale-[0.98]">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.5 3h3a1 1 0 01.87.5l5 8.66a1 1 0 010 1l-5 8.66a1 1 0 01-.87.5h-3a1 1 0 01-.87-.5l-5-8.66a1 1 0 010-1l5-8.66a1 1 0 01.87-.5z"/></svg>
                            <span>Report</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Seamless In-Place Booking View (Preserved for Instant Restoration) --}}
            <div id="pax-booking-view" class="absolute inset-0 pointer-events-none z-20 transition-all duration-400" style="display: none;">
                {{-- Top Left Standalone Profile Circle --}}
                <a href="{{ route('profile.edit') }}" id="pax-booking-profile-btn" class="absolute top-3 left-3 z-20 pointer-events-auto block w-10 h-10 rounded-full bg-white/95 backdrop-blur-md p-0.5 border border-slate-200 shadow-xl hover:scale-105 active:scale-95 transition-all text-decoration-none group" title="Profile Settings">
                    <div class="w-full h-full rounded-full bg-gradient-to-tr from-blue-600 to-blue-400 flex items-center justify-center overflow-hidden">
                        @if(Auth::user()->profile_photo_url)
                            <img src="{{ route('user.avatar', [Auth::user(), 'v' => optional(Auth::user()->updated_at)->timestamp]) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover rounded-full" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                            <span class="w-full h-full text-white font-black text-xs flex items-center justify-center bg-blue-600 hidden">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                        @else
                            <span class="text-white font-black text-xs">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                        @endif
                    </div>
                </a>

                <div class="absolute top-3 left-1/2 -translate-x-1/2 z-20 pointer-events-none">
                    <span id="booking-map-hint" class="px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-full text-[11px] sm:text-xs font-black bg-slate-950/90 text-white shadow-xl border border-slate-700/50 backdrop-blur-md whitespace-nowrap">
                        Tap map to pin drop-off destination
                    </span>
                </div>

                {{-- Floating Top Right Buttons (Notification Bell + Recenter) --}}
                <div id="pax-booking-top-right" class="absolute top-3 right-3 z-30 flex items-center gap-2">
                    {{-- Notifications Floating Button & Dropdown --}}
                    <div x-data="notificationSystem()" @srh-close-notifications.window="open = false" class="relative">
                        <button @click.stop="toggleDropdown()" type="button" class="flex items-center justify-center w-10 h-10 rounded-full bg-white/95 backdrop-blur-md border border-slate-200 shadow-xl text-slate-700 hover:text-blue-600 active:scale-90 transition-transform cursor-pointer relative" title="Notifications">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <template x-if="unreadCount > 0">
                                <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-600 text-white font-black text-[9px] rounded-full flex items-center justify-center border-2 border-white animate-pulse" x-text="unreadCount"></span>
                            </template>
                        </button>

                        <!-- Notifications Dropdown Menu -->
                        <div x-show="open" @click.outside="if (!confirmDeleteModalOpen) open = false" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                             class="fixed left-3 right-3 sm:absolute sm:left-auto sm:right-0 top-14 sm:w-96 max-h-[60vh] sm:max-h-[70vh] bg-white border border-slate-200/90 rounded-3xl shadow-2xl overflow-hidden z-[9999] flex flex-col"
                             x-cloak>
                            
                            <div class="p-3.5 bg-slate-50/90 border-b border-slate-100 flex items-center justify-between shrink-0">
                                <span class="font-black text-xs text-slate-800 uppercase tracking-wider">ANNOUNCEMENTS</span>
                                <button @click="markAllAsRead()" type="button" class="text-xs font-extrabold text-blue-600 hover:text-blue-800 cursor-pointer">Mark as read</button>
                            </div>

                            <div class="overflow-y-auto max-h-[48vh] sm:max-h-72 divide-y divide-slate-100">
                                <template x-if="!announcements || announcements.length === 0">
                                    <div class="p-6 text-center text-slate-400 text-xs italic font-medium">No announcements found.</div>
                                </template>
                                <template x-for="item in announcements" :key="item.id">
                                    <div @click="handleNotificationClick(item)" 
                                         class="p-3.5 transition cursor-pointer relative border-b border-slate-100 flex items-start justify-between gap-3" 
                                         :style="!item.is_read ? 'background-color: #ebf5ff !important;' : 'background-color: #ffffff !important;'">
                                        
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h4 class="text-xs font-black text-slate-900 leading-snug truncate" x-text="item.title"></h4>
                                                <span x-show="!item.is_read" class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: #1877f2;"></span>
                                            </div>
                                            <p class="text-[11px] font-medium text-slate-600 leading-relaxed whitespace-pre-line" x-text="item.message"></p>
                                            <span class="text-[10px] font-semibold text-slate-400 mt-1 block" x-text="item.created_at"></span>
                                        </div>

                                        @if(Auth::check() && Auth::user()->role === 'admin')
                                            <div class="shrink-0 pt-0.5" @click.stop>
                                                <button @click.stop="promptDeleteAnnouncement(item.id)" type="button" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition cursor-pointer" title="Delete Announcement">
                                                    <svg class="w-4 h-4 text-slate-400 hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Custom Delete Confirmation Modal -->
                        <div x-show="confirmDeleteModalOpen" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="fixed inset-0 z-[100000] flex items-center justify-center p-4"
                             style="position: fixed; inset: 0; z-index: 100000; background-color: transparent; display: none !important;"
                             x-cloak
                             @click.stop>
                            
                            <div class="bg-white rounded-3xl p-6 max-w-xs w-full shadow-2xl border border-slate-200 text-center space-y-4" @click.stop>
                                <div class="w-12 h-12 rounded-2xl bg-red-50 border border-red-100 text-red-600 flex items-center justify-center mx-auto shadow-sm">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </div>

                                <div>
                                    <h3 class="text-base font-black text-slate-900 tracking-tight">Delete Announcement?</h3>
                                    <p class="text-xs font-medium text-slate-500 mt-1 leading-relaxed">This announcement will be permanently removed. This action cannot be undone.</p>
                                </div>

                                <div class="flex items-center gap-3 pt-2">
                                    <button type="button" @click.stop="cancelDeleteAnnouncement()" class="flex-1 py-2.5 px-3 rounded-xl border border-slate-200 text-slate-700 font-extrabold text-xs uppercase hover:bg-slate-100 transition cursor-pointer">
                                        Cancel
                                    </button>
                                    <button type="button" @click.stop="confirmDeleteAnnouncement()" class="flex-1 py-2.5 px-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider shadow-md transition cursor-pointer">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FLOATING MAP CONTROLS STACK --}}
                <div id="pax-floating-map-controls-container" class="absolute flex flex-col gap-2.5 floating-action-btn" style="bottom: 336px; right: 16px; z-index: 10 !important;">
                    <button type="button" id="pax-recenter-location-btn" onclick="recenterOnRealLocation(paxActiveMapForButtons(), this)" class="w-10 h-10 rounded-2xl bg-white/95 text-slate-800 shadow-md border border-slate-200 flex items-center justify-center transition-all transform active:scale-90 cursor-pointer hover:bg-slate-50" title="Re-center on Live Hardware GPS Location">
                        <svg class="w-5 h-5 text-slate-700 leading-none select-none" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3c-.46-4.17-3.77-7.48-7.94-7.94V1h-2v2.06C6.83 3.52 3.52 6.83 3.06 11H1v2h2.06c.46 4.17 3.77 7.48 7.94 7.94V23h2v-2.06c4.17-.46 7.48-3.77 7.94-7.94H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/></svg>
                    </button>

                    <button type="button" id="pax-compass-mode-btn" onclick="toggleSrhCompassMode(paxActiveMapForButtons())" class="w-10 h-10 rounded-2xl bg-white/95 text-slate-700 shadow-md border border-slate-200 flex items-center justify-center transition-all transform active:scale-90 cursor-pointer" title="2D Flat Top View (Tap for Live 3D Compass Tracking)">
                        <svg class="w-5 h-5 text-slate-700 leading-none select-none" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm2.19 12.19L6 18l3.81-8.19L18 6l-3.81 8.19z"/></svg>
                    </button>
                </div>

                {{-- Pullable Grab-style Bottom Sheet (Booking Form) --}}
                <div class="absolute bottom-0 left-0 right-0 pointer-events-none" style="z-index: 99999 !important;">
                    <div id="pax-booking-sheet" class="bg-white/95 backdrop-blur-md srh-edge-bottom-sheet border-t border-white/60 shadow-2xl px-4 pt-2 pb-24 sm:p-5 sm:pb-8 transition-transform duration-300 pointer-events-auto border-x-0 border-b-0" style="transform: translate3d(0, 0px, 0);">
                        {{-- Drag Handle + Collapsed Status Header --}}
                        <div id="pax-sheet-drag-handle" class="w-full pt-1 pb-1 flex flex-col items-center cursor-pointer touch-none select-none group shrink-0">
                            <div class="w-12 h-1 bg-slate-300 rounded-full mb-1 shrink-0"></div>
                            <div class="w-full flex items-center justify-between px-1 shrink-0">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0 shadow-md">
                                        <img src="{{ srh_logo_url() }}" alt="{{ \App\Support\SystemSettings::brandName() }}" class="w-5 h-5 sm:w-6 sm:h-6 object-contain">
                                    </div>
                                    <div class="min-w-0 text-left">
                                        <span class="text-xs font-black uppercase tracking-wider text-slate-800">Book a TODA Ride</span>
                                        <span class="text-[10px] font-bold text-slate-500 block truncate">Pull up to open the booking form</span>
                                    </div>
                                </div>
                                <span id="pax-sheet-badge" class="px-2.5 py-1 bg-blue-600 text-white rounded-full text-[10px] font-black uppercase tracking-wider shadow-sm shrink-0">Ready</span>
                            </div>
                        </div>

                        {{-- Booking Form Content --}}
                        <div id="pax-sheet-details-content" style="opacity: 1; margin-top: 6px; pointer-events: auto; overflow: visible;">
                            <form action="{{ route('rides.store') }}" method="POST" class="space-y-2 pt-0" onsubmit="handleRideActionSubmit(event, this)">
                                @csrf
                                <input type="hidden" id="pickup_lat_input" name="pickup_lat" value="15.429550175641715">
                                <input type="hidden" id="pickup_lng_input" name="pickup_lng" value="120.92240292427664">
                                <input type="hidden" id="destination_lat_input" name="destination_lat" value="15.427800">
                                <input type="hidden" id="destination_lng_input" name="destination_lng" value="120.924500">
                                {{-- Pickup Row --}}
                                <div id="pickup-row" class="p-2.5 sm:p-3 rounded-2xl bg-slate-50 border-2 border-slate-200 flex items-center gap-2.5 sm:gap-3 transition-all">
                                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-blue-50 border border-blue-200 text-blue-600 flex items-center justify-center shrink-0 shadow-md">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 fill-current" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-600 block">Pickup Address (Required)</span>
                                        <input type="text" id="passenger-pickup-text-input" name="pickup_location" required aria-label="Pickup Address" placeholder="Required: Enter Block & Lot" class="w-full text-xs font-extrabold text-slate-900 focus:ring-0 focus:outline-none" style="border: none !important; outline: none !important; box-shadow: none !important; background: transparent !important; padding: 0 !important;">
                                    </div>
                                    <button type="button" id="gps-auto-btn" onclick="requestLocationPermissionAndPin(true)" class="px-2.5 py-1 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-[10px] font-black border border-blue-200 shrink-0 flex items-center gap-1.5 transition-all cursor-pointer shadow-xs active:scale-95" title="Recenter to current GPS location">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" id="gps-dot"></span>
                                        <span id="gps-btn-text">GPS Auto</span>
                                    </button>
                                </div>

                                {{-- Destination Row (Anchored container with z-50 so dropdown overlaps in front) --}}
                                <div class="relative z-50">
                                    <div id="dest-row" class="p-2.5 sm:p-3 rounded-2xl bg-slate-50 border-2 border-emerald-300 flex items-center gap-2 sm:gap-2.5 transition-all focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-200">
                                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-600 flex items-center justify-center text-white shrink-0 shadow-md shadow-emerald-500/20">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 fill-current text-white" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.683 2.282c.405.319.788.567 1.144.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" /></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <span class="text-[9px] font-black uppercase tracking-wider text-emerald-600 block">Drop-off Destination Name (Required)</span>
                                            <input type="text" id="passenger-dest-text-input" name="destination" required autocomplete="off" aria-label="Drop-off Destination Name" placeholder="Search real places or enter landmark..." class="w-full text-xs font-extrabold text-slate-900 focus:ring-0 focus:outline-none" style="border: none !important; outline: none !important; box-shadow: none !important; background: transparent !important; padding: 0 !important;">
                                        </div>
                                        <div class="flex items-center gap-1 shrink-0">
                                            <span class="px-2 py-1 rounded-xl bg-emerald-50 text-emerald-700 text-[10px] font-black border border-emerald-200 shrink-0 flex items-center gap-1">
                                                <svg class="w-3 h-3 text-emerald-600 fill-current" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                                Pin
                                            </span>
                                        </div>
                                    </div>
                                    {{-- Results float directly on top of the drop-off input box, overlapping form in front --}}
                                    <div id="dest-autocomplete-dropdown" class="hidden absolute z-[99999] bg-white rounded-2xl shadow-2xl border border-slate-200/90 max-h-64 overflow-y-auto divide-y divide-slate-100" style="position: absolute !important; top: auto !important; bottom: calc(100% + 6px) !important; left: 0 !important; right: 0 !important; width: 100% !important; z-index: 99999 !important; background: #ffffff !important; box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.45), 0 0 0 1px rgba(0, 0, 0, 0.1) !important;"></div>
                                </div>

                                <button type="submit" class="w-full py-3.5 px-4 rounded-2xl font-black text-xs sm:text-sm uppercase tracking-wider text-white shadow-lg flex items-center justify-center gap-2 active:scale-95 transition-transform cursor-pointer" style="background-color: #2563eb !important; color: #ffffff !important; border: none;">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    Find Available Driver
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @elseif(!$activeRide)
        {{-- ===== BOOKING FORM MODE ===== --}}
        {{-- Fullscreen Interactive Map Background --}}
        <div class="absolute inset-0 z-0 bg-[#f4f3f0]">
            <div id="pax-home-map" class="w-full h-full relative z-0"></div>
            
            {{-- Top Left Standalone Profile Circle --}}
            <a href="{{ route('profile.edit') }}" id="pax-booking-profile-btn" class="absolute top-3 left-3 z-20 pointer-events-auto block w-10 h-10 rounded-full bg-white/95 backdrop-blur-md p-0.5 border border-slate-200 shadow-xl hover:scale-105 active:scale-95 transition-all text-decoration-none group" title="Profile Settings">
                <div class="w-full h-full rounded-full bg-gradient-to-tr from-blue-600 to-blue-400 flex items-center justify-center overflow-hidden">
                    @if(Auth::user()->profile_photo_url)
                        <img src="{{ route('user.avatar', [Auth::user(), 'v' => optional(Auth::user()->updated_at)->timestamp]) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover rounded-full" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                        <span class="w-full h-full text-white font-black text-xs flex items-center justify-center bg-blue-600 hidden">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                    @else
                        <span class="text-white font-black text-xs">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                    @endif
                </div>
            </a>

            <div class="absolute top-3 left-1/2 -translate-x-1/2 z-20 pointer-events-none">
                <span id="booking-map-hint" class="px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-full text-[11px] sm:text-xs font-black bg-slate-950/90 text-white shadow-xl border border-slate-700/50 backdrop-blur-md whitespace-nowrap">
                    Tap map to pin drop-off destination
                </span>
            </div>
            {{-- Floating Top Right Buttons (Notification Bell + Recenter) --}}
            <div id="pax-booking-top-right" class="absolute top-3 right-3 z-30 flex items-center gap-2">
                {{-- Notifications Floating Button & Dropdown --}}
                <div x-data="notificationSystem()" @srh-close-notifications.window="open = false" class="relative">
                    <button @click.stop="toggleDropdown()" type="button" class="flex items-center justify-center w-10 h-10 rounded-full bg-white/95 backdrop-blur-md border border-slate-200 shadow-xl text-slate-700 hover:text-blue-600 active:scale-90 transition-transform cursor-pointer relative" title="Notifications">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <template x-if="unreadCount > 0">
                            <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-600 text-white font-black text-[9px] rounded-full flex items-center justify-center border-2 border-white animate-pulse" x-text="unreadCount"></span>
                        </template>
                    </button>

                    <!-- Notifications Dropdown Menu -->
                    <div x-show="open" @click.outside="if (!confirmDeleteModalOpen) open = false" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                         class="fixed left-3 right-3 sm:absolute sm:left-auto sm:right-0 top-14 sm:w-96 max-h-[60vh] sm:max-h-[70vh] bg-white border border-slate-200/90 rounded-3xl shadow-2xl overflow-hidden z-[9999] flex flex-col"
                         x-cloak>
                        
                        <div class="p-3.5 bg-slate-50/90 border-b border-slate-100 flex items-center justify-between shrink-0">
                            <span class="font-black text-xs text-slate-800 uppercase tracking-wider">ANNOUNCEMENTS</span>
                            <button @click="markAllAsRead()" type="button" class="text-xs font-extrabold text-blue-600 hover:text-blue-800 cursor-pointer">Mark as read</button>
                        </div>

                        <div class="overflow-y-auto max-h-[48vh] sm:max-h-72 divide-y divide-slate-100">
                            <template x-if="!announcements || announcements.length === 0">
                                <div class="p-6 text-center text-slate-400 text-xs italic font-medium">No announcements found.</div>
                            </template>
                            <template x-for="item in announcements" :key="item.id">
                                <div @click="handleNotificationClick(item)" 
                                     class="p-3.5 transition cursor-pointer relative border-b border-slate-100 flex items-start justify-between gap-3" 
                                     :style="!item.is_read ? 'background-color: #ebf5ff !important;' : 'background-color: #ffffff !important;'">
                                    
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="text-xs font-black text-slate-900 leading-snug truncate" x-text="item.title"></h4>
                                            <span x-show="!item.is_read" class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: #1877f2;"></span>
                                        </div>
                                        <p class="text-[11px] font-medium text-slate-600 leading-relaxed whitespace-pre-line" x-text="item.message"></p>
                                        <span class="text-[10px] font-semibold text-slate-400 mt-1 block" x-text="item.created_at"></span>
                                    </div>

                                    @if(Auth::check() && Auth::user()->role === 'admin')
                                        <div class="shrink-0 pt-0.5" @click.stop>
                                            <button @click.stop="promptDeleteAnnouncement(item.id)" type="button" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition cursor-pointer" title="Delete Announcement">
                                                <svg class="w-4 h-4 text-slate-400 hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Custom Delete Confirmation Modal (Clean overlay, no blur/dark opacity) -->
                    <div x-show="confirmDeleteModalOpen" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="fixed inset-0 z-[100000] flex items-center justify-center p-4"
                         style="position: fixed; inset: 0; z-index: 100000; background-color: transparent; display: none !important;"
                         x-cloak
                         @click.stop>
                        
                        <div class="bg-white rounded-3xl p-6 max-w-xs w-full shadow-2xl border border-slate-200 text-center space-y-4" @click.stop>
                            <div class="w-12 h-12 rounded-2xl bg-red-50 border border-red-100 text-red-600 flex items-center justify-center mx-auto shadow-sm">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>

                            <div>
                                <h3 class="text-base font-black text-slate-900 tracking-tight">Delete Announcement?</h3>
                                <p class="text-xs font-medium text-slate-500 mt-1 leading-relaxed">This announcement will be permanently removed. This action cannot be undone.</p>
                            </div>

                            <div class="flex items-center gap-3 pt-2">
                                <button type="button" @click.stop="cancelDeleteAnnouncement()" class="flex-1 py-2.5 px-3 rounded-xl border border-slate-200 text-slate-700 font-extrabold text-xs uppercase hover:bg-slate-100 transition cursor-pointer">
                                    Cancel
                                </button>
                                <button type="button" @click.stop="confirmDeleteAnnouncement()" class="flex-1 py-2.5 px-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider shadow-md transition cursor-pointer">
                                    Delete
                                </button>
                            </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FLOATING MAP CONTROLS STACK (Bottom Right - Same as Driver View) --}}
        <div id="pax-floating-map-controls-container" class="absolute flex flex-col gap-2.5 floating-action-btn" style="bottom: 336px; right: 16px; z-index: 10 !important;">
            {{-- Custom Re-center Live Hardware GPS Location Button (Driver Hub parity) --}}
            <button type="button" id="pax-recenter-location-btn" onclick="recenterOnRealLocation(paxActiveMapForButtons(), this)" class="w-10 h-10 rounded-2xl bg-white/95 text-slate-800 shadow-md border border-slate-200 flex items-center justify-center transition-all transform active:scale-90 cursor-pointer hover:bg-slate-50" title="Re-center on Live Hardware GPS Location">
                <svg class="w-5 h-5 text-slate-700 leading-none select-none" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3c-.46-4.17-3.77-7.48-7.94-7.94V1h-2v2.06C6.83 3.52 3.52 6.83 3.06 11H1v2h2.06c.46 4.17 3.77 7.48 7.94 7.94V23h2v-2.06c4.17-.46 7.48-3.77 7.94-7.94H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/></svg>
            </button>

            {{-- Google Maps 3D Live Compass Mode Toggle Button (Driver Hub parity) --}}
            <button type="button" id="pax-compass-mode-btn" onclick="toggleSrhCompassMode(paxActiveMapForButtons())" class="w-10 h-10 rounded-2xl bg-white/95 text-slate-700 shadow-md border border-slate-200 flex items-center justify-center transition-all transform active:scale-90 cursor-pointer" title="2D Flat Top View (Tap for Live 3D Compass Tracking)">
                <svg class="w-5 h-5 text-slate-700 leading-none select-none" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm2.19 12.19L6 18l3.81-8.19L18 6l-3.81 8.19z"/></svg>
            </button>
        </div>

        {{-- Pullable Grab-style Bottom Sheet (Booking Form) --}}
        <div class="absolute bottom-0 left-0 right-0 pointer-events-none" style="z-index: 99999 !important;">
            <div id="pax-booking-sheet" class="bg-white/95 backdrop-blur-md srh-edge-bottom-sheet border-t border-white/60 shadow-2xl px-4 pt-2 pb-24 sm:p-5 sm:pb-8 transition-transform duration-300 pointer-events-auto border-x-0 border-b-0" style="transform: translate3d(0, 0px, 0);">
                {{-- Drag Handle + Collapsed Status Header --}}
                <div id="pax-sheet-drag-handle" class="w-full pt-1 pb-1 flex flex-col items-center cursor-pointer touch-none select-none group shrink-0">
                    <div class="w-12 h-1 bg-slate-300 rounded-full mb-1 shrink-0"></div>
                    <div class="w-full flex items-center justify-between px-1 shrink-0">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0 shadow-md">
                                <img src="{{ srh_logo_url() }}" alt="{{ \App\Support\SystemSettings::brandName() }}" class="w-5 h-5 sm:w-6 sm:h-6 object-contain">
                            </div>
                            <div class="min-w-0 text-left">
                                <span class="text-xs font-black uppercase tracking-wider text-slate-800">Book a TODA Ride</span>
                                <span class="text-[10px] font-bold text-slate-500 block truncate">Pull up to open the booking form</span>
                            </div>
                        </div>
                        <span id="pax-sheet-badge" class="px-2.5 py-1 bg-blue-600 text-white rounded-full text-[10px] font-black uppercase tracking-wider shadow-sm shrink-0">Ready</span>
                    </div>
                </div>

                {{-- Booking Form Content --}}
                <div id="pax-sheet-details-content" style="opacity: 1; margin-top: 6px; pointer-events: auto; overflow: visible;">
                    <form action="{{ route('rides.store') }}" method="POST" class="space-y-2 pt-0" onsubmit="handleRideActionSubmit(event, this)">
                @csrf
                <input type="hidden" id="pickup_lat_input" name="pickup_lat" value="15.429550175641715">
                <input type="hidden" id="pickup_lng_input" name="pickup_lng" value="120.92240292427664">
                <input type="hidden" id="destination_lat_input" name="destination_lat" value="15.427800">
                <input type="hidden" id="destination_lng_input" name="destination_lng" value="120.924500">
                {{-- Pickup Row (Required Block/Lot Text Input + Instant Automatic GPS) --}}
                <div id="pickup-row" class="p-2.5 sm:p-3 rounded-2xl bg-slate-50 border-2 border-slate-200 flex items-center gap-2.5 sm:gap-3 transition-all">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-blue-50 border border-blue-200 text-blue-600 flex items-center justify-center shrink-0 shadow-md">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 fill-current" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-600 block">Pickup Address (Required)</span>
                        <input type="text" id="passenger-pickup-text-input" name="pickup_location" required aria-label="Pickup Address" placeholder="Required: Enter Block & Lot" class="w-full text-xs font-extrabold text-slate-900 focus:ring-0 focus:outline-none" style="border: none !important; outline: none !important; box-shadow: none !important; background: transparent !important; padding: 0 !important;">
                    </div>
                    <button type="button" id="gps-auto-btn" onclick="requestLocationPermissionAndPin(true)" class="px-2.5 py-1 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-[10px] font-black border border-blue-200 shrink-0 flex items-center gap-1.5 transition-all cursor-pointer shadow-xs active:scale-95" title="Recenter to current GPS location">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" id="gps-dot"></span>
                        <span id="gps-btn-text">GPS Auto</span>
                    </button>
                </div>

                {{-- Destination Row (Anchored container with z-50 so dropdown overlaps in front) --}}
                <div class="relative z-50">
                    <div id="dest-row" class="p-2.5 sm:p-3 rounded-2xl bg-slate-50 border-2 border-emerald-500 flex items-center gap-2 sm:gap-2.5 transition-all">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-600 flex items-center justify-center text-white shrink-0 shadow-md shadow-emerald-500/20">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 fill-current text-white" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.683 2.282c.405.319.788.567 1.144.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" /></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-[9px] font-black uppercase tracking-wider text-emerald-600 block">Drop-off Destination Name (Required)</span>
                            <input type="text" id="passenger-dest-text-input" name="destination" required autocomplete="off" aria-label="Drop-off Destination Name" placeholder="Search real places or enter landmark..." class="w-full text-xs font-extrabold text-slate-900 focus:ring-0 focus:outline-none" style="border: none !important; outline: none !important; box-shadow: none !important; background: transparent !important; padding: 0 !important;">
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <span class="px-2 py-1 rounded-xl bg-emerald-50 text-emerald-700 text-[10px] font-black border border-emerald-200 shrink-0 flex items-center gap-1">
                                <svg class="w-3 h-3 text-emerald-600 fill-current" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                Pin
                            </span>
                        </div>
                    </div>
                    {{-- Results float directly on top of the drop-off input box, overlapping form in front --}}
                    <div id="dest-autocomplete-dropdown" class="hidden absolute z-[99999] bg-white rounded-2xl shadow-2xl border border-slate-200/90 max-h-64 overflow-y-auto divide-y divide-slate-100" style="position: absolute !important; top: auto !important; bottom: calc(100% + 6px) !important; left: 0 !important; right: 0 !important; width: 100% !important; z-index: 99999 !important; background: #ffffff !important; box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.45), 0 0 0 1px rgba(0, 0, 0, 0.1) !important;"></div>
                </div>

                <button type="submit" class="w-full py-3.5 px-4 rounded-2xl font-black text-xs sm:text-sm uppercase tracking-wider text-white shadow-lg flex items-center justify-center gap-2 active:scale-95 transition-transform cursor-pointer" style="background-color: #2563eb !important; color: #ffffff !important; border: none;">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Find Available Driver
                </button>
            </form>
            </div>
            </div>
        </div>

    @else
        {{-- ===== SEARCHING / FARE PROPOSED / FARE ACCEPTED - IMMERSIVE MAP STATES ===== --}}
        @php
            $waitPickupLat = $activeRide->pickup_lat ? (float)$activeRide->pickup_lat : 15.429550175641715;
            $waitPickupLng = $activeRide->pickup_lng ? (float)$activeRide->pickup_lng : 120.92240292427664;
            $waitDestLat   = $activeRide->destination_lat ? (float)$activeRide->destination_lat : ($waitPickupLat - 0.003);
            $waitDestLng   = $activeRide->destination_lng ? (float)$activeRide->destination_lng : ($waitPickupLng + 0.004);
        @endphp

        {{-- Fullscreen Map Background --}}
        <div class="absolute inset-0 z-0 bg-[#f4f3f0]">
            <div id="pax-home-map" class="w-full h-full relative z-0"
                data-pickup-lat="{{ $waitPickupLat }}" data-pickup-lng="{{ $waitPickupLng }}"
                data-dest-lat="{{ $waitDestLat }}" data-dest-lng="{{ $waitDestLng }}"
                data-status="{{ $activeRide->status }}"></div>
        </div>

        {{-- TOP FLOATING HUD: removed â€” the status toast hid the map strip above the sheet --}}

        {{-- FLOATING MAP CONTROLS STACK (Bottom Right - Waiting View) --}}
        <div id="pax-wait-map-controls-container" class="absolute z-30 flex flex-col gap-2.5 floating-action-btn" style="bottom: 90px; right: 16px;">
            {{-- Custom Re-center Live Hardware GPS Location Button (Driver Hub parity) --}}
            <button type="button" id="pax-wait-recenter-btn" onclick="recenterOnRealLocation(paxActiveMapForButtons(), this)" class="w-10 h-10 rounded-2xl bg-white/95 text-slate-800 shadow-md border border-slate-200 flex items-center justify-center transition-all transform active:scale-90 cursor-pointer hover:bg-slate-50" title="Re-center on Live Hardware GPS Location">
                <svg class="w-5 h-5 text-slate-700 leading-none select-none" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3c-.46-4.17-3.77-7.48-7.94-7.94V1h-2v2.06C6.83 3.52 3.52 6.83 3.06 11H1v2h2.06c.46 4.17 3.77 7.48 7.94 7.94V23h2v-2.06c4.17-.46 7.48-3.77 7.94-7.94H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/></svg>
            </button>

            {{-- Google Maps 3D Live Compass Mode Toggle Button (Driver Hub parity) --}}
            <button type="button" id="pax-wait-compass-btn" onclick="toggleSrhCompassMode(paxActiveMapForButtons())" class="w-10 h-10 rounded-2xl bg-white/95 text-slate-700 shadow-md border border-slate-200 flex items-center justify-center transition-all transform active:scale-90 cursor-pointer" title="2D Flat Top View (Tap for Live 3D Compass Tracking)">
                <svg class="w-5 h-5 text-slate-700 leading-none select-none" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm2.19 12.19L6 18l3.81-8.19L18 6l-3.81 8.19z"/></svg>
            </button>
        </div>

        {{-- BOTTOM SHEET â€” reuses the exact booking-form shell (same ids, same gesture,
             same two-state snapping). Only the content inside swaps per state. --}}
        <div class="absolute bottom-0 left-0 right-0 z-20 pointer-events-none">
            <div id="pax-booking-sheet" class="bg-white/95 backdrop-blur-md srh-edge-bottom-sheet border-t border-white/60 shadow-2xl px-4 pt-2 pb-20 sm:p-5 sm:pb-6 transition-transform duration-300 pointer-events-auto border-x-0 border-b-0" style="transform: translate3d(0, 0px, 0);">

                {{-- Drag Handle + Status Header --}}
                <div id="pax-sheet-drag-handle" class="w-full pt-1 pb-2 flex flex-col items-center justify-center cursor-pointer touch-none select-none group">
                    <div class="w-12 h-1.5 bg-slate-300 rounded-full mb-2"></div>
                    <div class="w-full flex items-center justify-between px-1">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div id="pax-wait-handle-icon" class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 shadow-md
                                @if($activeRide->status === 'searching') bg-blue-50 border border-blue-100
                                @elseif($activeRide->status === 'fare_proposed') bg-amber-50 border border-amber-200
                                @else bg-emerald-50 border border-emerald-200 @endif">
                                @if($activeRide->status === 'searching')
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                @elseif($activeRide->status === 'fare_proposed')
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                                @else
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                @endif
                            </div>
                            <div class="min-w-0 text-left">
                                <span id="pax-wait-handle-title" class="text-xs font-black uppercase tracking-wider text-slate-800">
                                    @if($activeRide->status === 'searching') Searching for TODA Driver
                                    @elseif($activeRide->status === 'fare_proposed') Driver Proposed a Fare
                                    @else Fare Accepted &mdash; Pending Driver @endif
                                </span>
                                <span class="text-[10px] font-semibold text-slate-400 block truncate">{{ $activeRide->pickup_location }} &rarr; {{ $activeRide->destination }}</span>
                            </div>
                        </div>
                        <span id="pax-wait-handle-badge" class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider shadow-sm shrink-0
                            @if($activeRide->status === 'searching') bg-blue-600 text-white
                            @elseif($activeRide->status === 'fare_proposed') bg-amber-500 text-white
                            @else bg-emerald-600 text-white @endif">
                            @if($activeRide->status === 'searching') Searching
                            @elseif($activeRide->status === 'fare_proposed') Respond
                            @else Confirmed @endif
                        </span>
                    </div>
                </div>

                {{-- Collapsible Content (open by default in the wait states) --}}
                <div id="pax-sheet-details-content" style="max-height: min(75dvh, 620px); opacity: 1; margin-top: 12px; pointer-events: auto; overflow-y: auto; overflow-x: hidden; transition: max-height 0.35s cubic-bezier(0.16, 1, 0.3, 1), margin-top 0.35s ease;">
                    <div class="pt-1 space-y-3">

                        {{-- Ride Route Info --}}
                        <div class="space-y-2 text-left bg-slate-50/90 rounded-2xl p-3 border border-slate-200/80">
                            <div class="flex items-start gap-2.5">
                                <div class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">P</div>
                                <div>
                                    <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest">Pickup Location</p>
                                    <p class="text-sm font-black text-slate-900 leading-snug">{{ $activeRide->pickup_location }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">D</div>
                                <div>
                                    <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">Destination</p>
                                    <p class="text-sm font-black text-slate-900 leading-snug">{{ $activeRide->destination }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Status-specific actions (choreographed swap) --}}
                        <div id="pax-wait-action-block">
                        @if($activeRide->status === 'searching')
                            <div class="p-3.5 bg-gradient-to-r from-blue-50 via-indigo-50 to-blue-100 border border-blue-200/90 rounded-2xl space-y-1.5 text-left pax-card-in">
                                <div class="flex items-center gap-2 font-black text-blue-900 text-xs">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-ping"></span>
                                    <span class="uppercase tracking-wider">Broadcasting your request to drivers</span>
                                </div>
                                <p class="text-xs text-slate-600 font-medium leading-snug">A TODA driver in the queue will review your route and propose a fare. You'll be notified immediately.</p>
                                <button type="button" onclick="openPaxCancelModal({{ $activeRide->id }})" class="mt-1 w-full py-3 px-4 rounded-2xl bg-white/80 hover:bg-rose-50 text-rose-600 font-bold text-xs uppercase tracking-wider border border-rose-200 flex items-center justify-center gap-2 transition-all active:scale-95 cursor-pointer pax-cancel-pulse">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Cancel Request
                                </button>
                            </div>

                        @elseif($activeRide->status === 'fare_proposed')
                            <div class="p-4 bg-gradient-to-br from-amber-50 to-yellow-50 border-2 border-amber-300 rounded-2xl text-center shadow-md pax-card-in space-y-3 pax-fare-proposal-card">
                                @if($activeRide->driver)
                                <div class="flex items-center gap-3 text-left bg-white/70 border border-amber-200/80 rounded-2xl p-3">
                                    <div class="w-11 h-11 rounded-full overflow-hidden shrink-0 border-2 border-white shadow-md bg-gradient-to-tr from-amber-500 to-orange-500 flex items-center justify-center text-white font-black text-base">
                                        @if($activeRide->driver->profile_photo_url)
                                            <img src="{{ route('user.avatar', [$activeRide->driver, 'v'=>optional($activeRide->driver->updated_at)->timestamp]) }}" alt="{{ $activeRide->driver->name }}" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr($activeRide->driver->name ?? 'D', 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-black text-slate-900 truncate">{{ $activeRide->driver->name ?? 'TODA Driver' }}</p>
                                        <p class="text-[10px] font-bold text-slate-500 flex items-center gap-1">
                                            <span class="text-amber-600">&#9733;</span> {{ number_format($activeRide->driver->average_rating ?? 5.0, 1) }} &bull; MTOP #{{ optional($activeRide->driverProfile)->mtop_number ?? '001' }}
                                        </p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-[9px] font-black uppercase text-amber-600 block">Fare</span>
                                        <span class="text-lg font-black text-emerald-600">&#8369;{{ number_format($activeRide->fare, 2) }}</span>
                                    </div>
                                </div>
                                @else
                                <p class="text-[10px] font-black uppercase tracking-widest text-amber-600">Proposed Fare</p>
                                <p class="text-4xl font-black text-slate-900 my-1">&#8369;{{ number_format($activeRide->fare, 2) }}</p>
                                @endif
                                <p class="text-xs font-semibold text-slate-500">Review the fare and choose an option below.</p>
                                <div class="flex gap-2.5">
                                    <button type="button" onclick="acceptPaxFare('{{ route('rides.confirm-fare', $activeRide) }}')"
                                        style="background-color: #059669 !important; color: #ffffff !important; border: none;"
                                        class="flex-1 py-3.5 px-4 rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-600/30 hover:bg-emerald-700 active:scale-95 transition-all cursor-pointer flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Accept Fare
                                    </button>
                                    <button type="button" onclick="openPaxCancelModal({{ $activeRide->id }}, 'decline')"
                                        style="background: #fff1f2 !important; color: #e11d48 !important; border: 1px solid #fecdd3 !important;"
                                        class="flex-1 py-3.5 px-4 rounded-2xl font-bold text-xs tracking-wider shadow-xs active:scale-95 transition-all cursor-pointer flex items-center justify-center gap-2">
                                        Decline
                                    </button>
                                </div>
                            </div>
                        @elseif($activeRide->status === 'fare_accepted')
                            <div class="p-3.5 bg-gradient-to-r from-emerald-50 via-teal-50 to-emerald-100 border border-emerald-200/90 rounded-2xl space-y-1.5 text-left">
                                <div class="flex items-center gap-2 font-black text-emerald-900 text-xs">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span class="uppercase tracking-wider">Fare Confirmed &mdash; Awaiting Driver</span>
                                </div>
                                <p class="text-xs text-slate-600 font-medium leading-snug">Your &#8369;{{ number_format($activeRide->fare, 2) }} fare is locked in. The driver is preparing to head to your pickup point.</p>
                            </div>
                        @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endif
    </div>

    {{-- RATING MODAL FOR RECENT COMPLETED RIDES --}}
    @if($unratedRide)
    <div id="trip-completed-modal" data-ride-id="{{ $unratedRide->id }}" onclick="if(event.target===this) skipTripRating({{ $unratedRide->id }})" class="fixed inset-0 flex items-center justify-center p-4" style="position: fixed; inset: 0; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999999 !important; background-color: rgba(15, 23, 42, 0.85) !important; backdrop-filter: blur(12px);">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 text-center shadow-2xl border border-slate-100 space-y-4 relative z-[10000000]">
            <div class="flex items-center justify-between">
                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Trip Completed
                </span>
                <button type="button" onclick="skipTripRating({{ $unratedRide->id }})" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 flex items-center justify-center font-black text-sm transition-colors cursor-pointer" title="Close">&#10005;</button>
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-900">How was your ride?</h3>
                <p class="text-xs font-semibold text-slate-500 mt-1">Rate your experience with {{ $unratedRide->driver->name ?? 'your TODA driver' }}</p>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3 text-left">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-full overflow-hidden shrink-0 bg-blue-600 text-white font-black text-base flex items-center justify-center">
                        @if($unratedRide->driver && $unratedRide->driver->profile_photo_url)
                            <img src="{{ route('user.avatar', [$unratedRide->driver, 'v'=>optional($unratedRide->driver->updated_at)->timestamp]) }}" alt="Driver" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($unratedRide->driver->name ?? 'D', 0, 1)) }}
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-black text-slate-900 truncate">{{ $unratedRide->driver->name ?? 'TODA Driver' }}</p>
                        <p class="text-[11px] font-bold text-slate-500 truncate">{{ $unratedRide->pickup_location }} to {{ $unratedRide->destination }}</p>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <span class="text-[10px] font-black uppercase text-emerald-600 block">Paid</span>
                    <span class="text-sm font-black text-emerald-600">&#8369;{{ number_format($unratedRide->fare, 2) }}</span>
                </div>
            </div>

            <form action="{{ route('rides.rate', $unratedRide) }}" method="POST" id="rating-form-{{ $unratedRide->id }}" onsubmit="handleRatingSubmit(event,this)" class="no-spa space-y-4">
                @csrf
                <input type="hidden" name="rating" id="selected-rating-val-{{ $unratedRide->id }}" value="">
                <input type="hidden" name="feedback_tags" id="selected-tags-val-{{ $unratedRide->id }}" value="">

                <div>
                    <div class="flex items-center justify-center gap-2" id="star-rating-container-{{ $unratedRide->id }}">
                        @for($i=1;$i<=5;$i++)
                            <button type="button" onclick="setTripStarRating({{ $unratedRide->id }},{{ $i }})" id="star-btn-{{ $unratedRide->id }}-{{ $i }}" class="text-4xl hover:scale-110 transition-all focus:outline-none cursor-pointer" style="color: #cbd5e1 !important; background: transparent !important; border: none !important;">&#9734;</button>
                        @endfor
                    </div>
                    <p id="star-rating-label-{{ $unratedRide->id }}" class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-2">Tap stars to rate</p>
                </div>

                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-2">Quick Compliments</span>
                    <div class="flex flex-wrap gap-1.5 justify-center">
                        <button type="button" onclick="toggleTripFeedbackTag(this,{{ $unratedRide->id }},'Safe Driving')" class="py-1.5 px-3 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 transition-colors">Safe Driving</button>
                        <button type="button" onclick="toggleTripFeedbackTag(this,{{ $unratedRide->id }},'Polite Driver')" class="py-1.5 px-3 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 transition-colors">Polite Driver</button>
                        <button type="button" onclick="toggleTripFeedbackTag(this,{{ $unratedRide->id }},'Clean Vehicle')" class="py-1.5 px-3 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 transition-colors">Clean Vehicle</button>
                        <button type="button" onclick="toggleTripFeedbackTag(this,{{ $unratedRide->id }},'Fast & Punctual')" class="py-1.5 px-3 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 transition-colors">Fast &amp; Punctual</button>
                    </div>
                </div>

                <textarea name="review_comment" aria-label="Write a review comment (optional)" rows="2" placeholder="Write a brief comment (optional)..." class="w-full rounded-2xl border-slate-200 p-3 text-xs font-semibold text-slate-800 focus:ring-blue-500 focus:border-blue-500"></textarea>

                <button type="submit" class="w-full py-3.5 px-4 rounded-2xl font-black text-sm uppercase tracking-wider text-white shadow-lg flex items-center justify-center gap-2 active:scale-95 transition-transform cursor-pointer" style="background-color: #2563eb !important; color: #ffffff !important; border: none;">
                    Submit Rating
                </button>

                <div class="flex items-center justify-between pt-1">
                    <button type="button" onclick="skipTripRating({{ $unratedRide->id }})" class="text-xs font-bold text-slate-400 hover:text-slate-600">Skip for now</button>
                    <button type="button" onclick="openPassengerReportModalFromRide({{ $unratedRide->id }}, {{ $unratedRide->driver_id ?? 'null' }}, '{{ addslashes($unratedRide->driver->name ?? '') }}')" class="text-xs font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1">
                        Report Issue
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
    (function() {
        const rId = {{ $unratedRide->id }};
        window.dismissedRatingRides = window.dismissedRatingRides || {};
        if (localStorage.getItem('dismissed_rating_modal_' + rId) === 'true' || 
            sessionStorage.getItem('dismissed_rating_modal_' + rId) === 'true' ||
            window.dismissedRatingRides[rId]) {
            const m = document.getElementById('trip-completed-modal');
            if (m) m.remove();
        }
    })();
    </script>
    @endif

<script>
    window.isRealCoordinate = function(lat, lng) {
        return typeof lat === 'number' && typeof lng === 'number' &&
               !isNaN(lat) && !isNaN(lng) &&
               lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180 &&
               (lat !== 0 || lng !== 0);
    };
    function isRealCoordinate(lat, lng) {
        return window.isRealCoordinate(lat, lng);
    }

    window.dismissedRatingRides = window.dismissedRatingRides || {};
    window.tripSelectedTags = window.tripSelectedTags || {};

    // Active ride hint for the per-ride PRIVATE chat subscription (server-rendered track card)
    window._srhActiveChatRideId = {{ isset($activeRide) && $activeRide ? $activeRide->id : 'null' }};

    window.setTripStarRating = function(rideId, rating) {
        const h = document.getElementById('selected-rating-val-' + rideId);
        if (h) h.value = rating;
        for (let i=1;i<=5;i++) {
            const b = document.getElementById('star-btn-'+rideId+'-'+i);
            if (b) {
                b.innerHTML = i<=rating ? '&#9733;' : '&#9734;';
                b.style.color = i<=rating ? '#fbbf24' : '#cbd5e1';
            }
        }
        const labels = {1:'Terrible (1/5)',2:'Poor (2/5)',3:'Okay (3/5)',4:'Good! (4/5)',5:'Excellent! (5/5)'};
        const lEl = document.getElementById('star-rating-label-'+rideId);
        if (lEl) {
            lEl.textContent = labels[rating] || '';
            lEl.className = 'text-xs font-black text-amber-600 uppercase tracking-wider mt-2';
        }
    };

    window.toggleTripFeedbackTag = function(btn, rideId, tag) {
        window.tripSelectedTags[rideId] = window.tripSelectedTags[rideId] || [];
        const tags = window.tripSelectedTags[rideId];
        const idx = tags.indexOf(tag);
        if (idx > -1) {
            tags.splice(idx,1);
            btn.classList.remove('bg-emerald-600','text-white','border-emerald-600');
            btn.classList.add('bg-slate-100','text-slate-700','border-slate-200');
        } else {
            tags.push(tag);
            btn.classList.remove('bg-slate-100','text-slate-700','border-slate-200');
            btn.classList.add('bg-emerald-600','text-white','border-emerald-600');
        }
        const inp = document.getElementById('selected-tags-val-'+rideId);
        if (inp) inp.value = tags.join(', ');
    };

    window.restorePaxBookingChrome = function() {
        const bookingView = document.getElementById('pax-booking-view');
        if (bookingView) {
            bookingView.style.setProperty('display', 'block', 'important');
            bookingView.style.setProperty('opacity', '1', 'important');
            bookingView.style.setProperty('visibility', 'visible', 'important');
            bookingView.style.setProperty('transform', 'none', 'important');
            bookingView.style.setProperty('pointer-events', 'none', 'important');
        }

        const profileBtn = document.getElementById('pax-booking-profile-btn');
        if (profileBtn) {
            profileBtn.style.setProperty('display', 'block', 'important');
            profileBtn.style.setProperty('opacity', '1', 'important');
            profileBtn.style.setProperty('visibility', 'visible', 'important');
            profileBtn.style.setProperty('pointer-events', 'auto', 'important');
        }

        const hint = document.getElementById('booking-map-hint');
        if (hint) {
            hint.style.setProperty('display', '', 'important');
            hint.style.setProperty('opacity', '1', 'important');
            hint.style.setProperty('visibility', 'visible', 'important');
            if (hint.parentElement) {
                hint.parentElement.style.setProperty('display', 'block', 'important');
                hint.parentElement.style.setProperty('opacity', '1', 'important');
                hint.parentElement.style.setProperty('visibility', 'visible', 'important');
                hint.parentElement.style.setProperty('pointer-events', 'none', 'important');
            }
        }

        const topRight = document.getElementById('pax-booking-top-right');
        if (topRight) {
            topRight.style.setProperty('display', 'flex', 'important');
            topRight.style.setProperty('opacity', '1', 'important');
            topRight.style.setProperty('visibility', 'visible', 'important');
            topRight.style.setProperty('pointer-events', 'auto', 'important');
        }

        const bookControls = document.getElementById('pax-floating-map-controls-container');
        if (bookControls) {
            bookControls.style.setProperty('display', 'flex', 'important');
            bookControls.style.setProperty('opacity', '1', 'important');
            bookControls.style.setProperty('visibility', 'visible', 'important');
            bookControls.style.setProperty('pointer-events', 'auto', 'important');
        }
        if (window.syncPaxFloatingButtons) window.syncPaxFloatingButtons();
    };

    // Smooth, instant rating dismissal without double popups or full-screen reloads
    window.skipTripRating = function(rideId) {
        if (rideId) {
            window.dismissedRatingRides[rideId] = true;
            try {
                localStorage.setItem('dismissed_rating_modal_' + rideId, 'true');
                sessionStorage.setItem('dismissed_rating_modal_' + rideId, 'true');
            } catch(e) {}
        }
        const m = document.getElementById('trip-completed-modal');
        if (m) {
            m.style.transition = 'opacity 0.25s ease, transform 0.25s cubic-bezier(0.16, 1, 0.3, 1)';
            m.style.opacity = '0';
            m.style.transform = 'scale(0.96)';
            setTimeout(() => { try { m.remove(); } catch(e){} }, 260);
        }

        if (typeof window.restorePaxBookingChrome === 'function') {
            window.restorePaxBookingChrome();
        }
        if (typeof window.exitWaitBackToBooking === 'function') {
            window._paxExitedToBookingDone = false;
            window.exitWaitBackToBooking(true);
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (rideId) {
            fetch('/rides/' + rideId + '/rate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ skip: 1, rating: 0 })
            }).then(() => {
                // Dashboard behind the modal is already warm & current - stay on it
                // (no reload, no map reboot); just refresh the SPA cache quietly.
                if (window.srhPrefetchUrl) window.srhPrefetchUrl('/dashboard');
            }).catch(() => {
                if (window.srhPrefetchUrl) window.srhPrefetchUrl('/dashboard');
            });
        }
    };

    window.handleRatingSubmit = function(event, form) {
        if (event) {
            event.preventDefault();
            if (event.stopImmediatePropagation) event.stopImmediatePropagation();
        }
        if (!form || form._submitting) return;
        form._submitting = true;
        const modal = form.closest('#trip-completed-modal') || document.getElementById('trip-completed-modal');
        const val = form.querySelector('input[name="rating"]')?.value;
        if (!val) {
            form._submitting = false;
            if (window.createSlidingToast) window.createSlidingToast('Please tap a star to rate your trip.', 'warning');
            return;
        }
        const rideId = modal ? modal.dataset.rideId : null;
        if (rideId) {
            window.dismissedRatingRides[rideId] = true;
            try {
                localStorage.setItem('dismissed_rating_modal_' + rideId, 'true');
                sessionStorage.setItem('dismissed_rating_modal_' + rideId, 'true');
            } catch(e) {}
        }
        if (modal) {
            modal.style.transition = 'opacity 0.25s ease, transform 0.25s cubic-bezier(0.16, 1, 0.3, 1)';
            modal.style.opacity = '0';
            modal.style.transform = 'scale(0.96)';
            setTimeout(() => { try { modal.remove(); } catch(e){} }, 260);
        }
        if (window.createSlidingToast) window.createSlidingToast('Trip rated! Thank you.', 'success');

        if (typeof window.restorePaxBookingChrome === 'function') {
            window.restorePaxBookingChrome();
        }
        if (typeof window.exitWaitBackToBooking === 'function') {
            window._paxExitedToBookingDone = false;
            window.exitWaitBackToBooking(true);
        }

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).then(() => {
            // Dashboard behind the modal is already warm & current - stay on it
            // (no reload, no map reboot); just refresh the SPA cache quietly.
            if (window.srhPrefetchUrl) window.srhPrefetchUrl('/dashboard');
        }).catch(() => {
            if (window.srhPrefetchUrl) window.srhPrefetchUrl('/dashboard');
        });
    };

    // Map groups: statuses that share the same map canvas element
    // wait-group  â†’ pax-home-map  (searching / fare_proposed / fare_accepted)
    // track-group â†’ pax-home-map  (accepted / arrived / in_transit)
    // none-group  â†’ pax-home-map  (no active ride)
    function getMapGroup(status) {
        if (!status || status === 'none') return 'none';
        if (['searching','fare_proposed','fare_accepted'].includes(status)) return 'wait';
        if (['accepted','arrived','in_transit'].includes(status)) return 'track';
        return 'none';
    }

    // Reimagined wait-group choreography (Driver Hub parity): the booking-form sheet
    // shell is REUSED â€” only the content inside it swaps per state. The map instance
    // is never re-created; the camera just glides to the new focal point.
    function patchWaitGroupUI(status, ride) {
        const hudIcon   = document.getElementById('pax-wait-hud-icon');
        const hudGlass  = hudIcon ? hudIcon.closest('.srh-hud-glass') : null;
        const hudTitle  = hudGlass ? hudGlass.querySelector('p.text-sm') : null;
        const hudBadge  = hudGlass ? hudGlass.querySelector('span[class*="rounded-full"]') : null;
        const handleIcon  = document.getElementById('pax-wait-handle-icon');
        const handleTitle = document.getElementById('pax-wait-handle-title');
        const handleBadge = document.getElementById('pax-wait-handle-badge');
        const actionBlock = document.getElementById('pax-wait-action-block');

        const fare = ride && ride.fare ? parseFloat(ride.fare).toFixed(2) : '0.00';
        const rideId = ride && ride.id ? ride.id : 0;
        const fareConfirmUrl = '/rides/' + rideId + '/confirm-fare';

        const themes = {
            searching: {
                hudIcon:  'w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-md bg-blue-600 text-white',
                hudTitle: 'Searching for Drivers...',
                hudBadge: ['px-2.5 py-1 rounded-full text-[10px] font-black shrink-0 bg-blue-900/80 text-blue-300 border border-blue-500/50 shadow-sm animate-pulse', 'LIVE'],
                handleBadge: ['px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider shadow-sm shrink-0 bg-blue-600 text-white', 'Searching'],
                handleTitle: 'Searching for TODA Driver',
                handleIconClass: 'w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 shadow-md bg-blue-50 border border-blue-100',
                handleSvg: '<svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>'
            },
            fare_proposed: {
                hudIcon:  'w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-md bg-amber-500 text-white',
                hudTitle: 'Driver Proposed a Fare!',
                hudBadge: ['px-2.5 py-1 rounded-full text-[10px] font-black shrink-0 bg-amber-900/80 text-amber-300 border border-amber-500/50 shadow-sm fare-glow', 'OFFER'],
                handleBadge: ['px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider shadow-sm shrink-0 bg-amber-500 text-white', 'Respond'],
                handleTitle: 'Driver Proposed a Fare',
                handleIconClass: 'w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 shadow-md bg-amber-50 border border-amber-200',
                handleSvg: '<svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>'
            },
            fare_accepted: {
                hudIcon:  'w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-md bg-emerald-600 text-white',
                hudTitle: 'Fare Confirmed — Waiting for Driver',
                hudBadge: ['px-2.5 py-1 rounded-full text-[10px] font-black shrink-0 bg-emerald-900/80 text-emerald-300 border border-emerald-500/50 shadow-sm', 'CONFIRMED'],
                handleBadge: ['px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider shadow-sm shrink-0 bg-emerald-600 text-white', 'Confirmed'],
                handleTitle: 'Fare Accepted — Pending Driver',
                handleIconClass: 'w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 shadow-md bg-emerald-50 border border-emerald-200',
                handleSvg: '<svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>'
            }
        };
        const t = themes[status] || themes.searching;

        if (hudIcon)  hudIcon.className = t.hudIcon;
        if (hudTitle && hudTitle.textContent !== t.hudTitle) {
            hudTitle.textContent = t.hudTitle;
            fadeEl(hudTitle);
        }
        if (hudBadge) { hudBadge.className = t.hudBadge[0]; hudBadge.textContent = t.hudBadge[1]; }
        if (handleBadge) { handleBadge.className = t.handleBadge[0]; handleBadge.textContent = t.handleBadge[1]; fadeEl(handleBadge); }
        if (handleTitle && handleTitle.textContent !== t.handleTitle) { handleTitle.textContent = t.handleTitle; }
        if (handleIcon && handleIcon.dataset.iconState !== status) {
            handleIcon.dataset.iconState = status;
            handleIcon.className = t.handleIconClass;
            handleIcon.innerHTML = t.handleSvg;
            handleIcon.classList.remove('pax-icon-pop');
            void handleIcon.offsetWidth;
            handleIcon.classList.add('pax-icon-pop');
        }

        if (actionBlock) {
            let html = '';
            if (status === 'fare_proposed') {
                const dName = (ride && ride.driver_name) || 'TODA Driver';
                const dRating = ride && ride.driver_rating ? Number(ride.driver_rating).toFixed(1) : '5.0';
                html = `
                    <div class="p-4 bg-gradient-to-br from-amber-50 to-yellow-50 border-2 border-amber-300 rounded-2xl text-center shadow-md space-y-3">
                        <div class="flex items-center gap-3 text-left bg-white/70 border border-amber-200/80 rounded-2xl p-3">
                            <div class="w-11 h-11 rounded-full overflow-hidden shrink-0 border-2 border-white shadow-md bg-gradient-to-tr from-amber-500 to-orange-500 flex items-center justify-center text-white font-black text-base">${(dName[0] || 'D').toUpperCase()}</div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-black text-slate-900 truncate">${dName}</p>
                                <p class="text-[10px] font-bold text-slate-500 flex items-center gap-1"><span class="text-amber-600">&#9733;</span> ${dRating} &bull; Santa Rosa TODA</p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-[9px] font-black uppercase text-amber-600 block">Fare</span>
                                <span class="text-lg font-black text-emerald-600">&#8369;${fare}</span>
                            </div>
                        </div>
                        <p class="text-xs font-semibold text-slate-500">Review the fare and choose an option below.</p>
                        <div class="flex gap-2.5">
                            <button type="button" onclick="acceptPaxFare('${fareConfirmUrl}')"
                                style="background-color: #059669 !important; color: #ffffff !important; border: none;"
                                class="flex-1 py-3.5 px-4 rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-600/30 hover:bg-emerald-700 active:scale-95 transition-all cursor-pointer flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Accept Fare
                            </button>
                            <button type="button" onclick="openPaxCancelModal(${rideId}, 'decline')"
                                style="background: #fff1f2 !important; color: #e11d48 !important; border: 1px solid #fecdd3 !important;"
                                class="flex-1 py-3.5 px-4 rounded-2xl font-bold text-xs tracking-wider shadow-xs active:scale-95 transition-all cursor-pointer flex items-center justify-center gap-2">
                                Decline
                            </button>
                        </div>
                    </div>`;
            } else if (status === 'fare_accepted') {
                html = `
                    <div class="p-3.5 bg-gradient-to-r from-emerald-50 via-teal-50 to-emerald-100 border border-emerald-200/90 rounded-2xl space-y-1.5 text-left">
                        <div class="flex items-center gap-2 font-black text-emerald-900 text-xs">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="uppercase tracking-wider">Fare Confirmed &mdash; Awaiting Driver</span>
                        </div>
                        <p class="text-xs text-slate-600 font-medium leading-snug">Your &#8369;${fare} fare is locked in. The driver is preparing to head to your pickup point.</p>
                    </div>`;
            } else {
                html = `
                    <div class="p-3.5 bg-gradient-to-r from-blue-50 via-indigo-50 to-blue-100 border border-blue-200/90 rounded-2xl space-y-1.5 text-left">
                        <div class="flex items-center gap-2 font-black text-blue-900 text-xs">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-ping"></span>
                            <span class="uppercase tracking-wider">Broadcasting your request to drivers</span>
                        </div>
                        <p class="text-xs text-slate-600 font-medium leading-snug">A TODA driver in the queue will review your route and propose a fare. You'll be notified immediately.</p>
                        <button type="button" onclick="openPaxCancelModal(${rideId}, 'cancel')" class="mt-1 w-full py-3 px-4 rounded-2xl bg-white/80 hover:bg-rose-50 text-rose-600 font-bold text-xs uppercase tracking-wider border border-rose-200 flex items-center justify-center gap-2 transition-all active:scale-95 cursor-pointer pax-cancel-pulse">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            Cancel Request
                        </button>
                    </div>`;
            }
            swapPaxWaitAction(html);

            // A fare proposal needs a response: make sure the sheet is fully
            // expanded so the Accept/Decline buttons are on screen (the user
            // may have collapsed the sheet during searching).
            if (status === 'fare_proposed') {
                // No forced expansion: the sheet hugs whatever the proposal card
                // needs so Accept/Decline stay on screen. The post-swap content-fit
                // in swapPaxWaitAction already makes room by re-hugging.
                const details = document.getElementById('pax-sheet-details-content');
                if (details) {
                    details.style.overflowY = 'auto';
                    details.style.overflowX = 'hidden';
                }
            }
        }

        // Smooth camera glide to the state's focal point (same map instance, never re-created)
        choreographPaxWaitMap(status, ride);

        if (window.animatePaxFloatingButtonsSync) window.animatePaxFloatingButtonsSync(300);
    }

    // Glassy two-stage content swap: fade/slide the old card out, then spring the new one in
    function swapPaxWaitAction(html) {
        const block = document.getElementById('pax-wait-action-block');
        if (!block) return;
        if (block.innerHTML.trim() === html.trim()) return;
        block.classList.add('pax-swap-out');
        setTimeout(() => {
            block.innerHTML = html;
            // Any wait-state card swap (searching / fare proposal / fare accepted /
            // in-place track card) changes the content height â€” re-hug the sheet so
            // it always rests flush with the real content, never a stale height.
            if (window.paxHugSheetToContent) window.paxHugSheetToContent(420);
            block.classList.remove('pax-swap-out');
            void block.offsetWidth;
            block.classList.add('pax-swap-in');
            setTimeout(() => { block.classList.remove('pax-swap-in'); }, 520);
        }, 170);
    }

    // Keep the passenger sheet resting at 0 (contents fully visible above navbar)
    window.paxHugSheetToContent = function(duration) {
        const card = document.getElementById('pax-booking-sheet');
        if (card) {
            card.style.transform = 'translate3d(0, 0px, 0)';
        }
        if (window.syncPaxFloatingButtons) window.syncPaxFloatingButtons();
    };

    // Driver-Hub-parity camera glide between wait states
    // Frame both pins in the VISIBLE map strip above the bottom sheet. Zoom is computed
    // pixel-accurately from the map's own projection, so there is no fitBounds padding
    // option to stack with the persistent sheet padding (which cramps content to the top
    // edge). The camera midpoint sits at the pin midpoint; the sheet padding then renders
    // it dead-center in the strip.
    function paxFramePinsInStrip(m, pLat, pLng, dLat, dLng, duration) {
        if (!m || typeof m.easeTo !== 'function') return;

        if (window.syncPaxMapPadding) window.syncPaxMapPadding();

        const cLat = (pLat + dLat) / 2;
        const cLng = (pLng + dLng) / 2;
        let zoom = 14;

        try {
            const curZoom = m.getZoom ? m.getZoom() : 14;
            const w = m.getContainer().clientWidth;
            const h = m.getContainer().clientHeight;
            const sheetEl = document.getElementById('pax-booking-sheet') ||
                            document.getElementById('pax-wait-sheet') ||
                            document.querySelector('.srh-wait-glass') ||
                            document.querySelector('.bg-white.rounded-3xl.shadow-2xl');
            let bottomPad = 60;
            if (sheetEl && sheetEl.offsetParent !== null) {
                const r = sheetEl.getBoundingClientRect();
                const mapRect = m.getContainer().getBoundingClientRect();
                const sheetTopFromMapBottom = mapRect.bottom - r.top;
                if (sheetTopFromMapBottom > 40) {
                    bottomPad = Math.min(sheetTopFromMapBottom + 12, Math.round(window.innerHeight / 2));
                }
            }

            if (typeof window.srhSetMapPadding === 'function') {
                window.srhSetMapPadding(m, bottomPad);
            }

            const visibleH = Math.max(120, h - bottomPad);
            const tl = m.unproject([0, 0]);
            const bl = m.unproject([0, h]);
            const tr = m.unproject([w, 0]);
            const fullLatDeg = Math.abs(tl.lat - bl.lat) || 0.0001;
            const fullLngDeg = Math.abs(tl.lng - tr.lng) || 0.0001;
            const latSpan = Math.abs(dLat - pLat) || 0.0005;
            const lngSpan = Math.abs(dLng - pLng) || 0.0005;

            const availH = visibleH * 0.60;
            const availW = w * 0.65;
            const zLat = curZoom + Math.log2(Math.max(0.001, (fullLatDeg * availH) / (latSpan * h)));
            const zLng = curZoom + Math.log2(Math.max(0.001, (fullLngDeg * availW) / (lngSpan * w)));
            zoom = Math.max(10, Math.min(16.5, Math.min(zLat, zLng)));
        } catch(e) {}

        window._isMapCameraAnimating = true;
        try {
            m.easeTo({
                center: [cLng, cLat],
                zoom: zoom,
                duration: duration,
                easing: function(t) { return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2; }
            });
        } catch(e) {}
        setTimeout(() => { window._isMapCameraAnimating = false; }, duration + 150);
    }
    window.paxFramePinsInStrip = paxFramePinsInStrip;

    function choreographPaxWaitMap(status, ride) {
        let map = null;
        try {
            map = window.paxHomeMapInstance || window.bookingMapInstance || window.paxMapInstance || window.map;
        } catch(e) {}
        if (!map || typeof map.easeTo !== 'function') return;

        let pLat = parseFloat(window._paxWaitPickupLat), pLng = parseFloat(window._paxWaitPickupLng);
        let dLat = parseFloat(window._paxWaitDestLat), dLng = parseFloat(window._paxWaitDestLng);
        if (ride) {
            if (!isNaN(parseFloat(ride.pickup_lat))) { pLat = parseFloat(ride.pickup_lat); pLng = parseFloat(ride.pickup_lng); }
            if (!isNaN(parseFloat(ride.destination_lat))) { dLat = parseFloat(ride.destination_lat); dLng = parseFloat(ride.destination_lng); }
        }
        if (isNaN(pLat) || isNaN(pLng)) return;

        let lat = pLat, lng = pLng, zoom = 15.5;
        if (status === 'searching') {
            // Zoom out far enough that BOTH the pickup and drop-off pins are visible
            // in the map strip above the expanded sheet (sheet-aware bottom padding).
            if (!isNaN(dLat) && !isNaN(dLng)) {
                // Strip-aware framing: BOTH pins glide centered in the visible map above
                // the expanded sheet (no fitBounds padding stacking with the persistent
                // sheet padding, which cramped everything to the top edge).
                window._isMapCameraAnimating = true;
                try { window.paxFramePinsInStrip(map, pLat, pLng, dLat, dLng, 1000); } catch(e) {}
                setTimeout(() => { window._isMapCameraAnimating = false; }, 1150);
                return;
            }
            lat = pLat; lng = pLng; zoom = 15.5;
        } else if (status === 'fare_proposed') {
            lat = pLat; lng = pLng; zoom = 16.5;
        } else if (status === 'fare_accepted') {
            lat = pLat; lng = pLng; zoom = 16;
        }

        window._isMapCameraAnimating = true;
        try {
            map.easeTo({
                center: [lng, lat],
                zoom: zoom,
                duration: 800,
                easing: function(t) { return t * (2 - t); }
            });
        } catch(e) {}
        setTimeout(() => { window._isMapCameraAnimating = false; }, 900);
    }


    // Resolve the floating HUD elements regardless of which server branch rendered
    // (wait branch: #pax-wait-hud-icon glass; track branch: #pax-hud-icon glass).
    function getPaxHudEls() {
        const icon = document.getElementById('pax-wait-hud-icon') || document.getElementById('pax-hud-icon');
        const glass = icon && typeof icon.closest === 'function' ? icon.closest('.srh-hud-glass') : null;
        const title = glass ? glass.querySelector('p.text-sm') : null;
        const badge = glass ? glass.querySelector('span[class*="rounded-full"]') : null;
        return { icon: icon, title: title, badge: badge };
    }

    // Re-trigger the soft fade on any HUD element after its text/class changes
    function fadeEl(el) {
        if (!el) return;
        el.classList.remove('pax-hud-fade');
        void el.offsetWidth;
        el.classList.add('pax-hud-fade');
    }

    // Surgically update only the HUD text / labels in the track-group (accepted/arrived/in_transit)
    function patchTrackGroupUI(status) {
        const hud = getPaxHudEls();
        const etaBadge = document.getElementById('pax-hud-eta') || (hud ? hud.badge : null);
        const handleTitle = document.getElementById('pax-wait-handle-title');
        const handleBadge = document.getElementById('pax-wait-handle-badge');
        const cancelForm = document.querySelector('#pax-booking-sheet form, #pax-sheet-details-content form');

        if (status === 'accepted') {
            if (hud.title && hud.title.textContent !== 'Driver on the way') { hud.title.textContent = 'Driver on the way'; fadeEl(hud.title); }
            if (hud.icon) hud.icon.style.background = '#2563eb';
            if (etaBadge && (etaBadge.textContent.includes('Locating') || etaBadge.textContent === 'ARRIVED')) {
                etaBadge.textContent = 'On the way';
                etaBadge.className = 'px-2.5 py-1 rounded-full text-xs font-black shrink-0 bg-blue-950/80 text-blue-300 border border-blue-500/50 shadow-sm';
            }
        } else if (status === 'arrived') {
            if (hud.title && hud.title.textContent !== 'Driver has arrived!') { hud.title.textContent = 'Driver has arrived!'; fadeEl(hud.title); }
            if (hud.icon) hud.icon.style.background = '#10b981';
            if (etaBadge) {
                etaBadge.textContent = 'ARRIVED';
                etaBadge.className = 'px-2.5 py-1 rounded-full text-xs font-black shrink-0 bg-emerald-950/80 text-emerald-300 border border-emerald-500/50 shadow-sm';
            }
            if (handleTitle) handleTitle.textContent = 'Driver has arrived!';
            if (handleBadge) { handleBadge.className = 'px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider shadow-sm shrink-0 bg-emerald-600 text-white'; handleBadge.textContent = 'Arrived'; }
            if (cancelForm && cancelForm.action && cancelForm.action.includes('/cancel')) cancelForm.style.display = 'none';
        } else if (status === 'in_transit') {
            if (hud.title && hud.title.textContent !== 'In transit to destination') { hud.title.textContent = 'In transit to destination'; fadeEl(hud.title); }
            if (hud.icon) hud.icon.style.background = '#10b981';
            if (etaBadge && etaBadge.textContent === 'ARRIVED') {
                etaBadge.textContent = 'In Transit';
                etaBadge.className = 'px-2.5 py-1 rounded-full text-xs font-black shrink-0 bg-emerald-950/80 text-emerald-300 border border-emerald-500/50 shadow-sm';
            }
            if (handleTitle) handleTitle.textContent = 'In transit to destination';
            if (handleBadge) { handleBadge.className = 'px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider shadow-sm shrink-0 bg-emerald-600 text-white'; handleBadge.textContent = 'In Transit'; }
            if (cancelForm && cancelForm.action && cancelForm.action.includes('/cancel')) cancelForm.style.display = 'none';
        }
    }

    // Global instant state-change handler for active tracking mode (HUD text, pins, route, camera glide)
    window.handlePaxRideStatusChange = function(newStatus, rideData) {
        const wrapper = document.getElementById('passenger-status-wrapper');
        if (!wrapper) return;
        wrapper.dataset.status = newStatus;

        // 1. Update HUD UI labels & badges
        if (typeof patchTrackGroupUI === 'function') {
            patchTrackGroupUI(newStatus);
        }

        // 2. Resolve coordinates
        const pLat = parseFloat((rideData && rideData.pickup_lat) || wrapper.dataset.pickupLat || window._paxWaitPickupLat);
        const pLng = parseFloat((rideData && rideData.pickup_lng) || wrapper.dataset.pickupLng || window._paxWaitPickupLng);
        const dLat = parseFloat((rideData && (rideData.dest_lat || rideData.destination_lat)) || wrapper.dataset.destLat || window._paxWaitDestLat);
        const dLng = parseFloat((rideData && (rideData.dest_lng || rideData.destination_lng)) || wrapper.dataset.destLng || window._paxWaitDestLng);

        let drvLat = null, drvLng = null;
        if (rideData && isRealCoordinate(parseFloat(rideData.driver_lat), parseFloat(rideData.driver_lng))) {
            drvLat = parseFloat(rideData.driver_lat);
            drvLng = parseFloat(rideData.driver_lng);
        } else if (typeof _paxDriverMarker !== 'undefined' && _paxDriverMarker && typeof _paxDriverMarker.getLngLat === 'function') {
            const pos = _paxDriverMarker.getLngLat();
            drvLat = pos.lat;
            drvLng = pos.lng;
        } else if (window._paxDriverMarker && typeof window._paxDriverMarker.getLngLat === 'function') {
            const pos = window._paxDriverMarker.getLngLat();
            drvLat = pos.lat;
            drvLng = pos.lng;
        } else if (window._paxLastDriverLocation && isRealCoordinate(window._paxLastDriverLocation.lat, window._paxLastDriverLocation.lng)) {
            drvLat = window._paxLastDriverLocation.lat;
            drvLng = window._paxLastDriverLocation.lng;
        } else if (isRealCoordinate(parseFloat(wrapper.dataset.driverLat), parseFloat(wrapper.dataset.driverLng))) {
            drvLat = parseFloat(wrapper.dataset.driverLat);
            drvLng = parseFloat(wrapper.dataset.driverLng);
        }

        if (isRealCoordinate(drvLat, drvLng)) {
            window._paxLastDriverLocation = { lat: drvLat, lng: drvLng };
        }

        // 3. Update Pins: In transit -> remove pickup pin smoothly; Accepted/Arrived -> show both pins
        if (newStatus === 'in_transit') {
            if (window._paxRidePinMarkers && Array.isArray(window._paxRidePinMarkers)) {
                window._paxRidePinMarkers = window._paxRidePinMarkers.filter(m => {
                    if (m && m._isPickupPin) {
                        try { m.remove(); } catch(e){}
                        return false;
                    }
                    return true;
                });
            }
            if (typeof pickupMarker !== 'undefined' && pickupMarker) {
                try { pickupMarker.remove(); } catch(e){}
                pickupMarker = null;
            }
            if (window.pickupMarker) {
                try { window.pickupMarker.remove(); } catch(e){}
                window.pickupMarker = null;
            }
        } else if (newStatus === 'accepted' || newStatus === 'arrived') {
            if (typeof frameRidePinsWithEase === 'function') {
                frameRidePinsWithEase(pLat, pLng, dLat, dLng, false);
            }
        }

        // 4. Update Pathway / Route line immediately
        if (newStatus === 'arrived') {
            // Driver is waiting at the pickup point - no pathway to drop-off yet
            if (typeof window.paxClearDriverRoute === 'function') window.paxClearDriverRoute();
        } else if (isRealCoordinate(drvLat, drvLng) && typeof window.paxDrawDriverRoute === 'function') {
            if (newStatus === 'accepted' && isRealCoordinate(pLat, pLng)) {
                window.paxDrawDriverRoute(drvLat, drvLng, pLat, pLng, '#2563eb');
            } else if (newStatus === 'in_transit' && isRealCoordinate(dLat, dLng)) {
                window.paxDrawDriverRoute(drvLat, drvLng, dLat, dLng, '#10b981');
            }
        }

        // 5. Update driver marker & HUD ETA
        const drvHeading = (rideData && typeof rideData.driver_heading === 'number') ? rideData.driver_heading : null;
        if (isRealCoordinate(drvLat, drvLng) && typeof window.updatePaxDriverMarker === 'function') {
            window.updatePaxDriverMarker(drvLat, drvLng, newStatus, pLat, pLng, dLat, dLng, drvHeading);
        }

        // 6. Smoothly pan camera directly to driver's location
        if (isRealCoordinate(drvLat, drvLng)) {
            const map = (typeof bookingMap !== 'undefined' ? bookingMap : null) || window.paxHomeMapInstance || window.bookingMapInstance || window.paxMapInstance || window.map;
            if (map && typeof map.easeTo === 'function') {
                if (typeof syncPaxMapPadding === 'function') syncPaxMapPadding();
                window._isMapCameraAnimating = true;
                map.easeTo({
                    center: [drvLng, drvLat],
                    zoom: 17.8,
                    duration: 800,
                    easing: function(t) { return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2; }
                });
                setTimeout(() => { window._isMapCameraAnimating = false; }, 850);
            }
        }
    };

    // Zoom-out frame for entering track mode: the identical pickup + drop-off pin view.
    // Uses the SAME strip-aware framing math as the searching state, so wait â†’ track
    // glides at a consistent zoom that fits the pin pair instead of jumping to a fixed 15.
    function frameTrackMap(ride) {
        let map = null;
        try { map = window.paxHomeMapInstance || window.bookingMapInstance || window.paxMapInstance || window.map; } catch(e) {}
        if (!map || typeof map.easeTo !== 'function') return;
        let pLat = window._paxWaitPickupLat, pLng = window._paxWaitPickupLng;
        let dLat = window._paxWaitDestLat, dLng = window._paxWaitDestLng;
        if (ride) {
            if (!isNaN(parseFloat(ride.pickup_lat))) { pLat = parseFloat(ride.pickup_lat); pLng = parseFloat(ride.pickup_lng); }
            if (!isNaN(parseFloat(ride.destination_lat))) { dLat = parseFloat(ride.destination_lat); dLng = parseFloat(ride.destination_lng); }
        }
        if (isNaN(pLat) || isNaN(pLng)) return;
        window._isMapCameraAnimating = true;
        try {
            if (!isNaN(dLat) && !isNaN(dLng)) {
                window.paxFramePinsInStrip(map, pLat, pLng, dLat, dLng, 900);
            } else {
                map.easeTo({ center: [pLng, pLat], zoom: 16.5, duration: 900, easing: function(t) { return t * (2 - t); } });
            }
        } catch(e) {}
        setTimeout(function() { window._isMapCameraAnimating = false; }, 1050);
    }

    // Inline HTML-escaping for strings interpolated into injected templates
    function paxEsc(str) {
        return String(str == null ? '' : str)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    // ---- In-place booking â†’ wait transition (Find Available Driver) ----
    // The booking map instance and its gradient pins are KEPT as-is: the sheet content
    // becomes the waiting status card, the waiting HUD + controls mount, and the camera
    // just zooms out to frame the existing pickup + drop-off pins. No page reload, no
    // map re-creation, no pin replacement.
    function enterWaitInPlace(bookingData) {
        const wrapper = document.getElementById('passenger-status-wrapper');
        const mapEl = document.getElementById('pax-home-map');
        if (!wrapper || !mapEl) return;
        if (document.getElementById('pax-wait-action-block')) return;

        wrapper.dataset.status = 'searching';
        window._paxSavedWaitView = null;

        const rideId = bookingData ? (bookingData.ride_id || (bookingData.ride && bookingData.ride.id) || 0) : 0;
        window._currentPaxRideId = rideId;

        // Authoritative coords come from the booking form's hidden fields
        const num = function(id, fb) {
            const el = document.getElementById(id);
            const v = el ? parseFloat(el.value) : NaN;
            return isNaN(v) ? fb : v;
        };
        const pLat = num('pickup_lat_input', 15.429550175641715);
        const pLng = num('pickup_lng_input', 120.92240292427664);
        const dLat = num('destination_lat_input', pLat - 0.003);
        const dLng = num('destination_lng_input', pLng + 0.004);
        window._paxWaitPickupLat = pLat; window._paxWaitPickupLng = pLng;
        window._paxWaitDestLat = dLat;   window._paxWaitDestLng = dLng;

        const pickupStr = (document.getElementById('passenger-pickup-text-input') || {}).value || 'Your pickup point';
        const destStr = (document.getElementById('passenger-dest-text-input') || {}).value || 'Destination';
        window._paxWaitPickupStr = pickupStr;
        window._paxWaitDestStr = destStr;

        // Hide the booking-only chrome
        const profile = document.getElementById('pax-booking-profile-btn'); if (profile) profile.style.display = 'none';
        const hint = document.getElementById('booking-map-hint'); if (hint) hint.style.display = 'none';
        const topRight = document.getElementById('pax-booking-top-right'); if (topRight) topRight.style.display = 'none';
        const bookControls = document.getElementById('pax-floating-map-controls-container'); if (bookControls) bookControls.style.display = 'none';

        // Waiting view has no top HUD toast â€” the sheet handle already shows the status,
        // and the toast was hiding the map strip where the ride pins sit.

        // Mount the waiting-view floating controls (recenter + compass) above the sheet
        const sheetWrap = document.getElementById('pax-booking-sheet') ? document.getElementById('pax-booking-sheet').parentElement : null;
        if (sheetWrap && !document.getElementById('pax-wait-map-controls-container')) {
            const ctlWrap = document.createElement('div');
            ctlWrap.innerHTML = `
                <div id="pax-wait-map-controls-container" class="absolute z-30 flex flex-col gap-2.5 floating-action-btn" style="bottom: 90px; right: 16px;">
                    <button type="button" id="pax-wait-recenter-btn" onclick="recenterOnRealLocation(paxActiveMapForButtons(), this)" class="w-10 h-10 rounded-2xl bg-white/95 text-slate-800 shadow-md border border-slate-200 flex items-center justify-center transition-all transform active:scale-90 cursor-pointer hover:bg-slate-50" title="Re-center on Live Hardware GPS Location">
                        <svg class="w-5 h-5 text-slate-700 leading-none select-none" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3c-.46-4.17-3.77-7.48-7.94-7.94V1h-2v2.06C6.83 3.52 3.52 6.83 3.06 11H1v2h2.06c.46 4.17 3.77 7.48 7.94 7.94V23h2v-2.06c4.17-.46 7.48-3.77 7.94-7.94H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/></svg>
                    </button>
                    <button type="button" id="pax-wait-compass-btn" onclick="toggleSrhCompassMode(paxActiveMapForButtons())" class="w-10 h-10 rounded-2xl bg-white/95 text-slate-700 shadow-md border border-slate-200 flex items-center justify-center transition-all transform active:scale-90 cursor-pointer" title="2D Flat Top View (Tap for Live 3D Compass Tracking)">
                        <svg class="w-5 h-5 text-slate-700 leading-none select-none" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm2.19 12.19L6 18l3.81-8.19L18 6l-3.81 8.19z"/></svg>
                    </button>
                </div>`;
            sheetWrap.parentElement.insertBefore(ctlWrap.firstElementChild, sheetWrap);
            if (window.syncPaxFloatingButtons) window.syncPaxFloatingButtons();
        }

        // Swap the shared sheet shell into the waiting-view status card (same ids)
        const sheet = document.getElementById('pax-booking-sheet');
        if (sheet) {
            sheet.innerHTML = `
                <div id="pax-sheet-drag-handle" class="w-full pt-1 pb-2 flex flex-col items-center justify-center cursor-pointer touch-none select-none group">
                    <div class="w-12 h-1.5 bg-slate-300 rounded-full mb-2"></div>
                    <div class="w-full flex items-center justify-between px-1">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div id="pax-wait-handle-icon" class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 shadow-md bg-blue-50 border border-blue-100">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <div class="min-w-0 text-left">
                                <span id="pax-wait-handle-title" class="text-xs font-black uppercase tracking-wider text-slate-800">Searching for TODA Driver</span>
                                <span class="text-[10px] font-semibold text-slate-400 block truncate">${paxEsc(pickupStr)} \u2192 ${paxEsc(destStr)}</span>
                            </div>
                        </div>
                        <span id="pax-wait-handle-badge" class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider shadow-sm shrink-0 bg-blue-600 text-white">Searching</span>
                    </div>
                </div>
                <div id="pax-sheet-details-content" style="max-height: 400px; opacity: 1; margin-top: 12px; pointer-events: auto; overflow-y: auto; overflow-x: hidden; transition: max-height 0.35s cubic-bezier(0.16,1,0.3,1), margin-top 0.35s ease;">
                    <div class="pt-1 space-y-3">
                        <div class="space-y-2 text-left bg-slate-50/90 rounded-2xl p-3 border border-slate-200/80">
                            <div class="flex items-start gap-2.5">
                                <div class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">P</div>
                                <div>
                                    <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest">Pickup Location</p>
                                    <p class="text-sm font-black text-slate-900 leading-snug">${paxEsc(pickupStr)}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">D</div>
                                <div>
                                    <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">Destination</p>
                                    <p class="text-sm font-black text-slate-900 leading-snug">${paxEsc(destStr)}</p>
                                </div>
                            </div>
                        </div>
                        <div id="pax-wait-action-block">
                            <div class="p-3.5 bg-gradient-to-r from-blue-50 via-indigo-50 to-blue-100 border border-blue-200/90 rounded-2xl space-y-1.5 text-left">
                                <div class="flex items-center gap-2 font-black text-blue-900 text-xs">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-ping"></span>
                                    <span class="uppercase tracking-wider">Broadcasting your request to drivers</span>
                                </div>
                                <p class="text-xs text-slate-600 font-medium leading-snug">A TODA driver in the queue will review your route and propose a fare. You'll be notified immediately.</p>
                                <button type="button" onclick="openPaxCancelModal(${rideId}, 'cancel')" class="mt-1 w-full py-3 px-4 rounded-2xl bg-white/80 hover:bg-rose-50 text-rose-600 font-bold text-xs uppercase tracking-wider border border-rose-200 flex items-center justify-center gap-2 transition-all active:scale-95 cursor-pointer pax-cancel-pulse">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Cancel Request
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
            // Rest the sheet at the content-hugging peek (same resting anchor the
            // sheet uses across the wait states) — no forced full expansion.
            if (window.getPaxDefaultPeekY) {
                sheet.style.transform = 'translate3d(0, ' + window.getPaxDefaultPeekY() + 'px, 0)';
            }
        }

        // Force browser layout flush so sheet.getBoundingClientRect() reflects the new searching card immediately
        if (sheet) void sheet.offsetHeight;

        // Immediately update map padding and floating buttons to the searching card's resting height
        if (window.syncPaxMapPadding) window.syncPaxMapPadding();
        if (window.syncPaxFloatingButtons) window.syncPaxFloatingButtons();

        // Re-attach the shared sheet gesture in waiting mode (boots expanded)
        mapEl.dataset.status = 'searching';
        try { window.initPaxSheetSwipeGesture(); } catch(e) {}

        // The map NEVER reloads and the pins NEVER change — the camera just zooms out
        // to frame the existing pickup + drop-off pins using the new searching padding!
        choreographPaxWaitMap('searching', null);

        if (window.animatePaxFloatingButtonsSync) window.animatePaxFloatingButtonsSync(300);

        // Inject real ride id directly from booking response
        if (rideId) {
            const block = document.getElementById('pax-wait-action-block');
            if (block) {
                const fixed = block.innerHTML.replace(/openPaxCancelModal\(\s*0\s*,/g, 'openPaxCancelModal(' + rideId + ',');
                if (fixed !== block.innerHTML) block.innerHTML = fixed;
            }
        }

        window._paxCanceledInPlace = false;
        window._paxStatusPollPaused = false;
        window._paxExitedToBookingDone = false;
        if (window.passengerStatusInterval) clearInterval(window.passengerStatusInterval);
    }

    // In-place hook consumed by the shared submitRideAction helper (Driver Hub parity).
    // Ride creation (rides.store) transitions in place; every other ride action keeps
    // the legacy full navigation.
    window.handleDriverRideActionSuccess = function(url, method, data) {
        const path = String(url || '').split('?')[0].replace(/\/+$/, '');
        const isBookingStore = (method === 'POST' && /\/rides$/.test(path) && data && data.status === 'searching');
        if (!isBookingStore) {
            if (window.clearPageCache) window.clearPageCache();
            const target = (data && data.redirect) || window.location.href;
            if (window.navigateTo) window.navigateTo(target, false, false, true);
            else window.location.reload();
            return;
        }
        if (window.paxResetTripGuards) window.paxResetTripGuards();
        enterWaitInPlace(data);
    };

    window._paxStatusPollPaused = false;

    // Shared active-map resolver used by the Driver-Hub floating buttons
    // (recenter / compass) across the booking, wait and track views.
    window.paxActiveMapForButtons = function() {
        try {
            if (window._paxWaitMapInst) return window._paxWaitMapInst;
            if (window.bookingMapInstance) return window.bookingMapInstance;
            if (window.paxMapInstance) return window.paxMapInstance;
            if (typeof window.bookingMap !== 'undefined' && window.bookingMap) return window.bookingMap;
            if (typeof window.paxMap !== 'undefined' && window.paxMap) return window.paxMap;
            if (window.map) return window.map;
        } catch(e) {}
        return null;
    };

    // ---- Cancel / Decline ride request (searching & fare_proposed) ----
    // Re-run on every SPA navigateTo, so the holder must be a window property
    // (top-level let re-declaration throws "already been declared").
    window._paxPendingCancelRideId = null;

    window.openPaxCancelModal = function(rideId, mode) {
        const effectiveRideId = rideId || window._currentPaxRideId;
        window._paxPendingCancelRideId = effectiveRideId;
        const modal = document.getElementById('pax-cancel-confirm-modal');
        if (!modal) { window.confirmPaxCancel(); return; }
        const titleEl = document.getElementById('pax-cancel-modal-title');
        const descEl = document.getElementById('pax-cancel-modal-desc');
        if (titleEl) titleEl.textContent = (mode === 'decline') ? 'Decline this fare proposal?' : 'Cancel this ride request?';
        if (descEl) {
            descEl.textContent = (mode === 'decline')
                ? 'The driver will be released back to the top of the queue and this fare offer will be removed.'
                : 'Your ride request will be cancelled. Any driver already reviewing your route will be released back to the queue.';
        }
        document.body.style.overflow = 'hidden';
        modal.classList.remove('hidden');
    };

    window.closePaxCancelModal = function() {
        document.body.style.overflow = '';
        const modal = document.getElementById('pax-cancel-confirm-modal');
        if (modal) modal.classList.add('hidden');
    };

    window.confirmPaxCancel = function() {
        const rideId = window._paxPendingCancelRideId || window._currentPaxRideId;
        closePaxCancelModal();
        window._paxPendingCancelRideId = null;
        if (window.animatePaxFloatingButtonsSync) window.animatePaxFloatingButtonsSync(350);
        if (!rideId) {
            if (window.exitWaitBackToBooking) window.exitWaitBackToBooking();
            return;
        }

        // Direct loads into the wait/track view have no stashed booking form to restore
        // â€” keep the legacy full navigation for that rare edge case.
        if (!window._paxBookingSheetHtml) {
            if (window.submitRideAction) {
                window.submitRideAction('/rides/' + rideId + '/cancel', 'PATCH', {}, null, null);
            } else {
                fetch('/rides/' + rideId + '/cancel', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]')||{}).content || '', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: JSON.stringify({ _method: 'PATCH' })
                }).then(()=>{ if (window.navigateTo) window.navigateTo(window.location.href, false, false, true); else window.location.reload(); });
            }
            return;
        }

        // In-place cancel: no page reload. The map zooms in on the pickup, slides to the
        // drop-off, and the booking form is restored smoothly.
        window._paxStatusPollPaused = true;
        window._paxCanceledInPlace = true;

        fetch('/rides/' + rideId + '/cancel', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]')||{}).content || '', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: JSON.stringify({ _method: 'PATCH' })
        })
        .then(function(res) { if (!res.ok) throw new Error('cancel failed'); return res.json().catch(function() { return {}; }); })
        .then(function() {
            if (window.exitWaitBackToBooking) window.exitWaitBackToBooking();
        })
        .catch(function() {
            if (window.navigateTo) window.navigateTo(window.location.href, false, false, true);
            else window.location.reload();
        });
    };

    // ---- In-place cancel/complete — back to the booking dashboard (no reload) ----
    window.exitWaitBackToBooking = function(isCompleted = false) {
        if (window._paxExitedToBookingDone) return;
        window._paxExitedToBookingDone = true;
        if (window.clearPageCache) window.clearPageCache();
        if (window.passengerStatusInterval) { clearInterval(window.passengerStatusInterval); window.passengerStatusInterval = null; }
        window._paxStatusPollPaused = true;
        window._paxCanceledInPlace = true;

        const wrapper = document.getElementById('passenger-status-wrapper');
        if (wrapper) wrapper.dataset.status = 'none';
        const mapEl = document.getElementById('pax-home-map');
        if (mapEl) mapEl.dataset.status = '';

        // 1. Smoothly fade out active trip view (HUD, Driver card, active controls)
        const activeTripView = document.getElementById('pax-active-trip-view');
        if (activeTripView) {
            activeTripView.style.transition = 'opacity 0.4s ease, transform 0.4s cubic-bezier(0.16, 1, 0.3, 1)';
            activeTripView.style.opacity = '0';
            activeTripView.style.transform = 'translateY(35px)';
            activeTripView.style.pointerEvents = 'none';
            setTimeout(() => { try { activeTripView.remove(); } catch(e){} }, 420);
        }

        // 2. Clean up wait HUD / controls if present
        const waitView = document.getElementById('pax-wait-view');
        if (waitView) {
            waitView.style.transition = 'opacity 0.4s ease, transform 0.4s cubic-bezier(0.16, 1, 0.3, 1)';
            waitView.style.opacity = '0';
            waitView.style.transform = 'translateY(35px)';
            waitView.style.pointerEvents = 'none';
            setTimeout(() => { try { waitView.remove(); } catch(e){} }, 420);
        }

        // 3. Smoothly reveal the booking view (Profile button, Hint, Notifications, Floating controls, Bottom sheet)
        const bookingView = document.getElementById('pax-booking-view');
        if (bookingView) {
            bookingView.style.setProperty('display', 'block', 'important');
            bookingView.style.setProperty('opacity', '1', 'important');
            bookingView.style.setProperty('visibility', 'visible', 'important');
            bookingView.style.setProperty('pointer-events', 'none', 'important');
            bookingView.style.setProperty('transform', 'none', 'important');
        }

        // 4. Restore booking form shell inside sheet if coming from wait mode
        const sheet = document.getElementById('pax-booking-sheet');
        if (sheet && window._paxBookingSheetHtml) {
            sheet.innerHTML = window._paxBookingSheetHtml;
            sheet.style.transform = 'translate3d(0, 0px, 0)';
            void sheet.offsetHeight;
        }

        // 5. Ensure booking chrome elements are visible
        if (typeof window.restorePaxBookingChrome === 'function') {
            window.restorePaxBookingChrome();
        } else {
            ['pax-booking-profile-btn', 'booking-map-hint', 'pax-booking-top-right', 'pax-booking-view'].forEach(function(id) {
                const el = document.getElementById(id);
                if (el) { el.style.display = ''; el.style.opacity = '1'; }
            });
            const bookControls = document.getElementById('pax-floating-map-controls-container');
            if (bookControls) bookControls.style.display = 'flex';
        }

        // 6. Clean up any wait HUD / controls
        const waitHud = document.getElementById('pax-wait-hud-icon');
        const hudWrap = waitHud && typeof waitHud.closest === 'function' ? waitHud.closest('.absolute.top-3') : null;
        if (hudWrap) hudWrap.remove();
        const waitCtl = document.getElementById('pax-wait-map-controls-container');
        if (waitCtl) waitCtl.remove();
        const trackCtl = document.getElementById('pax-active-trip-map-controls-container');
        if (trackCtl) trackCtl.remove();

        // 7. Sync padding & floating controls
        if (window.syncPaxMapPadding) window.syncPaxMapPadding();
        if (window.syncPaxFloatingButtons) window.syncPaxFloatingButtons();
        try { window.initPaxSheetSwipeGesture(); } catch(e) {}

        // 8. Clear all ride pins & markers
        const ridePins = window._paxRidePinMarkers || [];
        ridePins.forEach(function(m) { try { m.remove(); } catch(e) {} });
        window._paxRidePinMarkers = [];

        if (typeof destMarker !== 'undefined' && destMarker) { try { destMarker.remove(); } catch(e) {} destMarker = null; }
        if (window.destMarker) { try { window.destMarker.remove(); } catch(e) {} window.destMarker = null; }

        if (typeof window.paxClearDriverMarker === 'function') { try { window.paxClearDriverMarker(); } catch(e) {} }
        if (typeof window.paxClearDriverRoute === 'function') { try { window.paxClearDriverRoute(); } catch(e) {} }

        // 9. Restore pickup pin silently
        window._paxSuppressPickupJump = true;
        const cachedLat = parseFloat(localStorage.getItem('srh_last_pickup_lat'));
        const cachedLng = parseFloat(localStorage.getItem('srh_last_pickup_lng'));
        const pLat = !isNaN(cachedLat) ? cachedLat : (parseFloat(window._paxWaitPickupLat) || 15.429550175641715);
        const pLng = !isNaN(cachedLng) ? cachedLng : (parseFloat(window._paxWaitPickupLng) || 120.92240292427664);
        if (window.applyPickupPosition) {
            window.applyPickupPosition(pLat, pLng, { silent: true });
        }
        window._paxSuppressPickupJump = false;

        const map = window.paxHomeMapInstance || window.bookingMapInstance || window.paxMapInstance || window.map;
        const sheetEl = document.getElementById('pax-booking-sheet');
        let finalBottomPad = 220;
        if (sheetEl && sheetEl.offsetHeight > 0) {
            finalBottomPad = Math.min(sheetEl.offsetHeight + 12, Math.round(window.innerHeight / 2));
        }

        if (isCompleted) {
            // TRIP COMPLETED: Clear destination fields, pins, and glide to pickup in visible strip
            window._paxWaitDestLat = null;
            window._paxWaitDestLng = null;
            window._paxWaitDestStr = '';
            const dInput = document.getElementById('passenger-dest-text-input');
            const dLatEl = document.getElementById('destination_lat_input');
            const dLngEl = document.getElementById('destination_lng_input');
            if (dInput) dInput.value = '';
            if (dLatEl) dLatEl.value = '';
            if (dLngEl) dLngEl.value = '';

            window._isMapCameraAnimating = true;
            try {
                if (map && typeof map.easeTo === 'function') {
                    map.easeTo({
                        center: [pLng, pLat],
                        zoom: 16.5,
                        pitch: 0,
                        bearing: 0,
                        padding: { top: 0, bottom: finalBottomPad, left: 0, right: 0 },
                        duration: 700,
                        easing: function(t) { return t * (2 - t); }
                    });
                }
            } catch(e) {}
            if (window.animatePaxFloatingButtonsSync) window.animatePaxFloatingButtonsSync(380);
            setTimeout(() => {
                window._isMapCameraAnimating = false;
                if (window.syncPaxMapPadding) window.syncPaxMapPadding();
                if (window.syncPaxFloatingButtons) window.syncPaxFloatingButtons();
            }, 750);
        } else {
            // TRIP CANCELLED: Re-apply the real ride coords onto the restored booking form
            const dLat = parseFloat(window._paxWaitDestLat), dLng = parseFloat(window._paxWaitDestLng);
            if (!isNaN(dLat) && !isNaN(dLng)) {
                const dLatEl = document.getElementById('destination_lat_input');
                const dLngEl = document.getElementById('destination_lng_input');
                if (dLatEl) dLatEl.value = dLat;
                if (dLngEl) dLngEl.value = dLng;
                window._paxSuppressDestFit = true;
                if (window.setDestinationPinOnMap) window.setDestinationPinOnMap(dLat, dLng, true);
                window._paxSuppressDestFit = false;

                if (map && typeof map.easeTo === 'function') {
                    if (!isNaN(pLat) && typeof window.paxFramePinsInStrip === 'function') {
                        window.paxFramePinsInStrip(map, pLat, pLng, dLat, dLng, 650);
                    } else {
                        map.easeTo({ center: [dLng, dLat], zoom: 16.5, duration: 650 });
                    }
                }
            }

            const pickupText = document.getElementById('passenger-pickup-text-input');
            if (pickupText && window._paxWaitPickupStr) pickupText.value = window._paxWaitPickupStr;
            const destInput = document.getElementById('passenger-dest-text-input');
            if (destInput && window._paxWaitDestStr) destInput.value = window._paxWaitDestStr;
        }

        // Re-bind the Find Available Driver map animation to the restored form
        const bForm = document.querySelector('#pax-booking-sheet form, #pax-sheet-details-content form');
        if (bForm && !bForm.dataset.paxAnimBound) {
            bForm.dataset.paxAnimBound = '1';
            bForm.addEventListener('submit', function() {
                if (window.animatePaxMapForAction) window.animatePaxMapForAction('book');
                if (window.animatePaxFloatingButtonsSync) window.animatePaxFloatingButtonsSync(380);
            });
        }
        if (window.initPlacesAutocomplete) window.initPlacesAutocomplete();

        // Re-attach the sheet gesture so the restored form snaps/drags normally
        try { window.initPaxSheetSwipeGesture(); } catch(e) {}
        if (window.syncPaxFloatingButtons) window.syncPaxFloatingButtons();

        if (!isCompleted && window.createSlidingToast) window.createSlidingToast('Ride request cancelled.', 'info');
    };

    // ---- In-place trip COMPLETION (drop-off): zero reload, zero map reboot ----
    window._paxCompletedInPlace = false;
    window._paxInPlaceCompletion = false;
    window.completePaxTripInPlace = function(ride) {
        if (window._paxCompletedInPlace) return;
        window._paxCompletedInPlace = true;
        window._paxInPlaceCompletion = true;
        if (window.exitWaitBackToBooking) {
            window.exitWaitBackToBooking(true);
        }
        const rideId = ride && ride.id ? ride.id : 0;
        if (!rideId) return;
        try {
            if (window.dismissedRatingRides && window.dismissedRatingRides[rideId]) return;
            if (localStorage.getItem('dismissed_rating_modal_' + rideId) === 'true' ||
                sessionStorage.getItem('dismissed_rating_modal_' + rideId) === 'true') return;
        } catch(ee) {}
        setTimeout(function() { window.buildPaxRatingModal && window.buildPaxRatingModal(ride); }, 650);
    };

    window.buildPaxRatingModal = function(ride) {
        if (document.getElementById('trip-completed-modal')) return;
        const rideId = ride && ride.id ? ride.id : 0;
        if (!rideId) return;
        const dName = String(ride.driver_name || 'TODA Driver').replace(/'/g, "\\'");
        const dId = ride.driver_id ? Number(ride.driver_id) : 0;
        const dLetter = (dName.charAt(0) || 'D').toUpperCase();
        const fare = ride.fare !== undefined && ride.fare !== null ? Number(ride.fare).toFixed(2) : '0.00';
        const pickup = String(ride.pickup_location || 'Pickup').replace(/'/g, "\\'");
        const dest = String(ride.destination || 'Destination').replace(/'/g, "\\'");
        const dAvatar = ride.driver_avatar || ride.driver_photo_url || '';
        const el = document.createElement('div');
        el.id = 'trip-completed-modal';
        el.dataset.rideId = String(rideId);
        el.className = 'fixed inset-0 flex items-center justify-center p-4';
        el.style.cssText = 'position:fixed;inset:0;top:0;left:0;right:0;bottom:0;z-index:9999999;background-color:rgba(15,23,42,0.85);backdrop-filter:blur(12px);';
        el.onclick = function(e) { if (e.target === el && window.skipTripRating) window.skipTripRating(rideId); };
        el.innerHTML = `
            <div class="bg-white rounded-3xl max-w-md w-full p-6 text-center shadow-2xl border border-slate-100 space-y-4 relative z-[10000000]" style="animation: paxModalIn 0.4s cubic-bezier(0.16, 1, 0.2, 1);">
                <div class="flex items-center justify-between">
                    <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Trip Completed
                    </span>
                    <button type="button" onclick="skipTripRating(${rideId})" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 flex items-center justify-center font-black text-sm transition-colors cursor-pointer" title="Close">&#10005;</button>
                </div>
                <div>
                    <h3 class="text-xl font-black text-slate-900">How was your ride?</h3>
                    <p class="text-xs font-semibold text-slate-500 mt-1">Rate your experience with ${dName}</p>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3 text-left">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-full overflow-hidden shrink-0 bg-blue-600 text-white font-black text-base flex items-center justify-center relative shadow-sm border border-slate-200/60">
                            <span class="absolute inset-0 flex items-center justify-center">${dLetter}</span>
                            ${dAvatar ? `<img src="${dAvatar}" alt="${dName}" class="relative z-10 w-full h-full object-cover" onerror="this.remove();">` : ''}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-black text-slate-900 truncate">${dName}</p>
                            <p class="text-[11px] font-bold text-slate-500 truncate">${pickup} to ${dest}</p>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-[10px] font-black uppercase text-emerald-600 block">Paid</span>
                        <span class="text-sm font-black text-emerald-600">&#8369;${fare}</span>
                    </div>
                </div>
                <form action="/rides/${rideId}/rate" method="POST" id="rating-form-${rideId}" onsubmit="handleRatingSubmit(event,this)" class="no-spa space-y-4">
                    <input type="hidden" name="rating" id="selected-rating-val-${rideId}" value="">
                    <input type="hidden" name="feedback_tags" id="selected-tags-val-${rideId}" value="">
                    <div>
                        <div class="flex items-center justify-center gap-2" id="star-rating-container-${rideId}">
                            <button type="button" onclick="setTripStarRating(${rideId},1)" id="star-btn-${rideId}-1" class="text-4xl hover:scale-110 transition-all focus:outline-none cursor-pointer" style="color:#cbd5e1;background:transparent;border:none;">&#9734;</button>
                            <button type="button" onclick="setTripStarRating(${rideId},2)" id="star-btn-${rideId}-2" class="text-4xl hover:scale-110 transition-all focus:outline-none cursor-pointer" style="color:#cbd5e1;background:transparent;border:none;">&#9734;</button>
                            <button type="button" onclick="setTripStarRating(${rideId},3)" id="star-btn-${rideId}-3" class="text-4xl hover:scale-110 transition-all focus:outline-none cursor-pointer" style="color:#cbd5e1;background:transparent;border:none;">&#9734;</button>
                            <button type="button" onclick="setTripStarRating(${rideId},4)" id="star-btn-${rideId}-4" class="text-4xl hover:scale-110 transition-all focus:outline-none cursor-pointer" style="color:#cbd5e1;background:transparent;border:none;">&#9734;</button>
                            <button type="button" onclick="setTripStarRating(${rideId},5)" id="star-btn-${rideId}-5" class="text-4xl hover:scale-110 transition-all focus:outline-none cursor-pointer" style="color:#cbd5e1;background:transparent;border:none;">&#9734;</button>
                        </div>
                        <p id="star-rating-label-${rideId}" class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-2">Tap stars to rate</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-2">Quick Compliments</span>
                        <div class="flex flex-wrap gap-1.5 justify-center">
                            <button type="button" onclick="toggleTripFeedbackTag(this,${rideId},'Safe Driving')" class="py-1.5 px-3 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 transition-colors">Safe Driving</button>
                            <button type="button" onclick="toggleTripFeedbackTag(this,${rideId},'Polite Driver')" class="py-1.5 px-3 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 transition-colors">Polite Driver</button>
                            <button type="button" onclick="toggleTripFeedbackTag(this,${rideId},'Clean Vehicle')" class="py-1.5 px-3 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 transition-colors">Clean Vehicle</button>
                            <button type="button" onclick="toggleTripFeedbackTag(this,${rideId},'Fast & Punctual')" class="py-1.5 px-3 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 transition-colors">Fast &amp; Punctual</button>
                        </div>
                    </div>
                    <textarea name="review_comment" aria-label="Write a review comment (optional)" rows="2" placeholder="Write a brief comment (optional)..." class="w-full rounded-2xl border-slate-200 p-3 text-xs font-semibold text-slate-800 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    <button type="submit" class="w-full py-3.5 px-4 rounded-2xl font-black text-sm uppercase tracking-wider text-white shadow-lg flex items-center justify-center gap-2 active:scale-95 transition-transform cursor-pointer" style="background-color:#2563eb;border:none;">Submit Rating</button>
                    <div class="flex items-center justify-between pt-1">
                        <button type="button" onclick="skipTripRating(${rideId})" class="text-xs font-bold text-slate-400 hover:text-slate-600">Skip for now</button>
                        <button type="button" onclick="openPassengerReportModalFromRide(${rideId}, ${dId}, '${dName}')" class="text-xs font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.5 3h3a1 1 0 01.87.5l5 8.66a1 1 0 010 1l-5 8.66a1 1 0 01-.87.5h-3a1 1 0 01-.87-.5l-5-8.66a1 1 0 010-1l5-8.66a1 1 0 01.87-.5z"/></svg>
                            Report Issue
                        </button>
                    </div>
                </form>
            </div>`;
        if (!document.getElementById('pax-modal-anim-style')) {
            const st = document.createElement('style');
            st.id = 'pax-modal-anim-style';
            st.textContent = '@keyframes paxModalIn { from { opacity: 0; transform: translateY(24px) scale(0.96); } to { opacity: 1; transform: translateY(0) scale(1); } }';
            document.head.appendChild(st);
        }
        document.body.appendChild(el);
    };

    // Reset trip-lifecycle guards whenever a NEW ride is booked in place
    window.paxResetTripGuards = function() {
        window._paxExitedToBookingDone = false;
        window._paxCompletedInPlace = false;
        window._paxInPlaceCompletion = false;
        window._paxPickupPinFaded = false;
        window._paxDriverMarker = null;
        window._paxRidePinMarkers = [];
        window._paxHasCenteredOnDriver = false;
        window._isMapCameraAnimating = false;
    };

    // Accept Fare: confirm the fare with a lightweight PATCH, transition state and pan animate to driver smoothly
    window.acceptPaxFare = function(fareConfirmUrl) {
        window._paxHasCenteredOnDriver = false;
        window._paxPickupPinFaded = false;
        if (typeof patchWaitGroupUI === 'function') {
            patchWaitGroupUI('fare_accepted', window._paxLastRideSnapshot || {});
        }
        if (window.animatePaxMapForAction) window.animatePaxMapForAction('accept_fare');
        if (window.animatePaxFloatingButtonsSync) window.animatePaxFloatingButtonsSync(350);

        // Pre-ease towards known driver location if available right away
        const knownDrv = window._paxLastDriverLocation;
        const map = (typeof bookingMap !== 'undefined' ? bookingMap : null) || window.paxHomeMapInstance || window.bookingMapInstance || window.paxMapInstance || window.map;
        if (map && knownDrv && isRealCoordinate(knownDrv.lat, knownDrv.lng)) {
            try {
                map.easeTo({
                    center: [knownDrv.lng, knownDrv.lat],
                    zoom: 17.5,
                    duration: 900,
                    easing: function(t) { return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2; }
                });
            } catch(e) {}
        }

        window._paxFormSubmitting = true;
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        fetch(fareConfirmUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: JSON.stringify({ _method: 'PATCH' })
        })
        .then(r => r.json().catch(() => ({})))
        .then(d => {
            window._paxFormSubmitting = false;
            const ride = (d && d.ride) ? d.ride : null;
            if (ride && isRealCoordinate(parseFloat(ride.driver_lat), parseFloat(ride.driver_lng))) {
                window._paxLastDriverLocation = { lat: parseFloat(ride.driver_lat), lng: parseFloat(ride.driver_lng) };
            }
            if (ride && typeof window.handlePaxRideStatusChange === 'function') {
                try { window.handlePaxRideStatusChange('accepted', ride); } catch(e) {}
            }

            const targetDrv = window._paxLastDriverLocation || (ride && isRealCoordinate(parseFloat(ride.driver_lat), parseFloat(ride.driver_lng)) ? { lat: parseFloat(ride.driver_lat), lng: parseFloat(ride.driver_lng) } : null);
            if (map && targetDrv && isRealCoordinate(targetDrv.lat, targetDrv.lng)) {
                try {
                    map.easeTo({
                        center: [targetDrv.lng, targetDrv.lat],
                        zoom: 17.8,
                        duration: 850,
                        easing: function(t) { return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2; }
                    });
                } catch(e) {}
            }

            setTimeout(() => {
                if (window.clearPageCache) window.clearPageCache();
                if (window.navigateTo) {
                    window.navigateTo(window.location.href, false, false, true);
                } else {
                    window.location.reload();
                }
            }, 600);
        })
        .catch(() => {
            window._paxFormSubmitting = false;
            if (window.clearPageCache) window.clearPageCache();
            if (window.navigateTo) window.navigateTo(window.location.href, false, false, true);
            else window.location.reload();
        });
    };

    function fetchPassengerStatus() {
        const wrapper = document.getElementById('passenger-status-wrapper');
        if (!wrapper || !document.body.contains(wrapper)) { if(window.passengerStatusInterval) { clearInterval(window.passengerStatusInterval); window.passengerStatusInterval = null; } return; }
        if (window._paxFormSubmitting || window._paxStatusPollPaused || window._paxCanceledInPlace) return;
        const active = document.activeElement;
        if (active && (active.tagName==='INPUT'||active.tagName==='TEXTAREA'||active.tagName==='SELECT')) return;

        fetch('/passenger/ride-status', { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then(r => {
                if (!r.ok) {
                    // Server error â€” pause polling for 5s to avoid hammering
                    window._paxStatusPollPaused = true;
                    setTimeout(() => { window._paxStatusPollPaused = false; }, 5000);
                    return null;
                }
                return r.json();
            })
            .then(data => {
                if (window._paxCanceledInPlace) return;
                if (!data || !data.status) return;

                // Cache the freshest ride snapshot - the final 'none' status comes
                // WITHOUT ride data, but the rating modal needs the ride it just ended.
                if (data.ride) window._paxLastRideSnapshot = data.ride;

                const oldStatus = wrapper.dataset.status || 'none';
                const newStatus = data.status;

                // Always update the live driver marker (position updates even when status is unchanged)
                if (['accepted', 'arrived', 'in_transit'].includes(newStatus) && data.ride && typeof window.updatePaxDriverMarker === 'function') {
                    const r = data.ride;
                    window.updatePaxDriverMarker(
                        parseFloat(r.driver_lat), parseFloat(r.driver_lng),
                        newStatus,
                        parseFloat(r.pickup_lat), parseFloat(r.pickup_lng),
                        parseFloat(r.destination_lat), parseFloat(r.destination_lng),
                        r.driver_heading !== undefined && r.driver_heading !== null ? parseFloat(r.driver_heading) : null
                    );
                }

                if (oldStatus === newStatus) return; // Status unchanged â€” no UI state jump needed

                const oldGroup = getMapGroup(oldStatus);
                const newGroup = getMapGroup(newStatus);

                wrapper.dataset.status = newStatus;

                // ---------- SAME MAP GROUP: only patch text/UI, never touch the map ----------
                if (oldGroup === newGroup && newGroup !== 'none') {
                    if (newGroup === 'wait') {
                        patchWaitGroupUI(newStatus, data.ride);
                    } else if (newGroup === 'track') {
                        if (typeof window.handlePaxRideStatusChange === 'function') {
                            window.handlePaxRideStatusChange(newStatus, data.ride);
                        } else {
                            patchTrackGroupUI(newStatus);
                            if (data.ride && typeof window.updatePaxDriverMarker === 'function') {
                                const r = data.ride;
                                const drvLat = parseFloat(r.driver_lat), drvLng = parseFloat(r.driver_lng);
                                const pLat = parseFloat(r.pickup_lat), pLng = parseFloat(r.pickup_lng);
                                const dLat = parseFloat(r.destination_lat), dLng = parseFloat(r.destination_lng);
                                const drvHeading = r.driver_heading !== undefined && r.driver_heading !== null ? parseFloat(r.driver_heading) : null;
                                window.updatePaxDriverMarker(drvLat, drvLng, newStatus, pLat, pLng, dLat, dLng, drvHeading);
                            }
                        }
                    }
                    // Show toast
                    if (newStatus === 'arrived' && window.createSlidingToast)
                        window.createSlidingToast('Your driver has arrived outside!', 'completed');
                    if (newStatus === 'fare_proposed' && window.createSlidingToast)
                        window.createSlidingToast('A driver proposed a fare! Tap to view.', 'info');
                    return;
                }

                // ---------- CROSS-GROUP: a real state machine jump happened ----------
                // Stop polling before the jump â€” it is restarted below for in-place
                // transitions, or the new page starts its own interval after a reload.
                clearInterval(window.passengerStatusInterval);
                window.passengerStatusInterval = null;

                if (newStatus === 'arrived' && window.createSlidingToast)
                    window.createSlidingToast('Your driver has arrived outside!', 'completed');
                if (newStatus === 'fare_proposed' && window.createSlidingToast)
                    window.createSlidingToast('A driver proposed a fare! Tap to view.', 'info');

                // --- wait → track (driver assigned): Transition to the clean, simple active trip UI ---
                if (oldGroup === 'wait' && newGroup === 'track') {
                    if (window.clearPageCache) window.clearPageCache();
                    if (window.navigateTo) {
                        window.navigateTo(window.location.href, false, false, true);
                    } else {
                        window.location.reload();
                    }
                    return;
                }

                if (newStatus === 'none' && oldStatus !== 'none' && window.createSlidingToast) {
                    const lastSt = data.last_status;
                    if (lastSt === 'cancelled') {
                        window.createSlidingToast('Your trip was cancelled.', 'danger');
                    } else if (lastSt === 'completed' || oldStatus === 'in_transit' || oldStatus === 'arrived') {
                        window.createSlidingToast('Trip Completed! Thank you for riding.', 'completed');
                    }
                }

                // IN-PLACE ride ending (drop-off / completion / cancellation): NO page reload
                // and NO map re-creation - the same map instance glides back to the
                // booking view, the ride pins drop away, and (for completed trips)
                // the rating modal appears over the still-warm dashboard. Legacy full
                // navigation is only the fallback when in-place prerequisites are gone.
                if (newStatus === 'none' && oldStatus !== 'none') {
                    if (window.passengerStatusInterval) {
                        clearInterval(window.passengerStatusInterval);
                        window.passengerStatusInterval = null;
                    }
                    const lastSt = data.last_status;
                    const isCompleted = (lastSt !== 'cancelled' && (lastSt === 'completed' || oldStatus === 'in_transit' || oldStatus === 'arrived'));

                    if (typeof window.exitWaitBackToBooking === 'function'
                        && document.getElementById('pax-booking-sheet')
                        && window._paxBookingSheetHtml) {
                        try {
                            if (window._paxRidePinMarkers && window._paxRidePinMarkers.length) {
                                window._paxRidePinMarkers.forEach(function(m) { if (m && typeof m.remove === 'function') { try { m.remove(); } catch(e2) {} } });
                                window._paxRidePinMarkers = [];
                            }
                        } catch(e2) {}

                        if (isCompleted) {
                            const endedRide = data.ride || window._paxLastRideSnapshot;
                            if (window.completePaxTripInPlace && endedRide) {
                                window.completePaxTripInPlace(endedRide);
                            } else {
                                window.exitWaitBackToBooking(true);
                            }
                        } else {
                            window.exitWaitBackToBooking(false);
                        }
                        return;
                    }
                    if (window.clearPageCache) window.clearPageCache();
                    if (window.navigateTo) window.navigateTo(window.location.href, false, false, true);
                    else window.location.reload();
                    return;
                }
            }).catch(() => {
                // Network error — pause polling briefly
                window._paxStatusPollPaused = true;
                setTimeout(() => { window._paxStatusPollPaused = false; }, 3000);
            });
    }
    window.fetchPassengerStatus = fetchPassengerStatus;
    if (window.passengerStatusInterval) clearInterval(window.passengerStatusInterval);
    var paxWrapperInit = document.getElementById('passenger-status-wrapper');
    if (paxWrapperInit && paxWrapperInit.dataset.status !== 'none') {
        setTimeout(fetchPassengerStatus, 600);
    }



    (function() {
        const SRH_LAT = 15.429550175641715, SRH_LNG = 120.92240292427664;
        let bookingMap = null, pickupMarker = null, destMarker = null;

        function pickupMarkerElement() {
            const el = document.createElement('div');
            el.className = 'pax-pickup-pin-marker';
            el.innerHTML = `<div style="width:36px;height:36px;background:linear-gradient(135deg,#2563eb,#1d4ed8);border:3px solid #fff;border-radius:50%;box-shadow:0 6px 16px rgba(37,99,235,0.5);display:flex;align-items:center;justify-content:center;"><svg style="width:18px;height:18px;fill:#fff;" viewBox="0 0 15 15"><path d="M7.5 1C5.42312 1 3 2.2883 3 5.56759C3 7.79276 6.46156 12.7117 7.5 14C8.42309 12.7117 12 7.90993 12 5.56759C12 2.2883 9.57688 1 7.5 1Z"/></svg></div>`;
            return el;
        }

        function destMarkerElement() {
            const el = document.createElement('div');
            el.className = 'pax-dest-pin-marker';
            el.innerHTML = `<div style="width:38px;height:38px;background:linear-gradient(135deg,#10b981,#047857);border:3px solid #fff;border-radius:50%;box-shadow:0 6px 20px rgba(16,185,129,0.55);display:flex;align-items:center;justify-content:center;"><svg style="width:20px;height:20px;fill:#fff;" viewBox="0 0 15 15"><path d="M7.5 1C5.42312 1 3 2.2883 3 5.56759C3 7.79276 6.46156 12.7117 7.5 14C8.42309 12.7117 12 7.90993 12 5.56759C12 2.2883 9.57688 1 7.5 1Z"/></svg></div>`;
            return el;
        }

        window.recenterBookingMap = function() {
            if (bookingMap && pickupMarker && destMarker) {
                const pl = pickupMarker.getLngLat();
                const dl = destMarker.getLngLat();
                const bounds = [[Math.min(pl.lng, dl.lng), Math.min(pl.lat, dl.lat)], [Math.max(pl.lng, dl.lng), Math.max(pl.lat, dl.lat)]];
                bookingMap.fitBounds(bounds, { padding: 60, animate: true });
            } else if (bookingMap && pickupMarker) {
                const p = pickupMarker.getLngLat();
                bookingMap.easeTo({ center: [p.lng, p.lat], zoom: 16, duration: 500 });
            }
        };

        function reverseGeocodePickup(lat, lng) {
            // Pickup address is typed manually by the passenger; do not autofill.
        }

        function fetchNominatimPickup() {
            // Kept as a no-op for backwards compatibility.
        }

        function isRealCoordinate(lat, lng) {
            return typeof lat === 'number' && typeof lng === 'number' &&
                   !isNaN(lat) && !isNaN(lng) &&
                   lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180 &&
                   (lat !== 0 || lng !== 0);
        }

        function applyPickupPosition(lat, lng, opts) {
            opts = opts || {};
            const btnText = document.getElementById('gps-btn-text');
            const btnDot = document.getElementById('gps-dot');
            const pLatEl = document.getElementById('pickup_lat_input');
            const pLngEl = document.getElementById('pickup_lng_input');

            if (isRealCoordinate(lat, lng)) {
                if (pLatEl) pLatEl.value = lat;
                if (pLngEl) pLngEl.value = lng;
            }

            if (bookingMap) {
                if (window.pickupMarker && window.pickupMarker !== pickupMarker) {
                    try { window.pickupMarker.remove(); } catch(e){}
                }
                if (!pickupMarker) {
                    pickupMarker = new maplibregl.Marker({ element: pickupMarkerElement(), draggable: false })
                        .setLngLat([lng, lat])
                        .addTo(bookingMap);
                    window.pickupMarker = pickupMarker;
                } else {
                    pickupMarker.setLngLat([lng, lat]);
                }
                if (opts.silent) {
                    // Silent placement never steers the camera unless explicitly allowed
                    // â€” the cancel-restore path suppresses the jump so the drop-off glide
                    // below is the ONLY camera movement.
                    if (!window._paxSuppressPickupJump) {
                        try { bookingMap.jumpTo({ center: [lng, lat], zoom: Math.max(bookingMap.getZoom() || 15, 16) }); } catch(e) {}
                    }
                } else {
                    bookingMap.flyTo({ center: [lng, lat], zoom: 17, duration: 600 });
                }
            }

            reverseGeocodePickup(lat, lng);

            if (btnText) {
                btnText.textContent = opts.userInitiated ? 'GPS Pinned' : 'GPS Auto';
            }
            if (btnDot) {
                btnDot.className = 'w-2 h-2 rounded-full bg-emerald-500';
            }

            try {
                localStorage.setItem('srh_last_pickup_lat', String(lat));
                localStorage.setItem('srh_last_pickup_lng', String(lng));
            } catch(e) {}
        }
        window.applyPickupPosition = applyPickupPosition;

        window.requestLocationPermissionAndPin = function(userInitiated = false) {
            const btnText = document.getElementById('gps-btn-text');
            const btnDot = document.getElementById('gps-dot');

            if (btnText) btnText.textContent = 'Locating...';
            if (btnDot) btnDot.className = 'w-2 h-2 rounded-full bg-amber-500 animate-pulse';

            if (!navigator.geolocation) {
                if (btnText) btnText.textContent = 'GPS Auto';
                if (btnDot) btnDot.className = 'w-2 h-2 rounded-full bg-rose-500';
                if (userInitiated && window.createSlidingToast) {
                    window.createSlidingToast('Geolocation is not supported by your browser.', 'error');
                }
                return;
            }

            navigator.geolocation.getCurrentPosition(
                pos => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;

                    if (isRealCoordinate(lat, lng)) {
                        applyPickupPosition(lat, lng, { silent: !userInitiated, userInitiated: userInitiated });

                        if (userInitiated && window.createSlidingToast) {
                            window.createSlidingToast('Live GPS location pinned!', 'success');
                        }
                    } else {
                        if (btnText) btnText.textContent = 'GPS Auto';
                        if (btnDot) btnDot.className = 'w-2 h-2 rounded-full bg-emerald-500';
                    }
                },
                err => {
                    if (btnText) btnText.textContent = 'GPS Auto';
                    if (btnDot) btnDot.className = 'w-2 h-2 rounded-full bg-emerald-500';

                    const defaultLat = 15.429550175641715, defaultLng = 120.92240292427664;
                    if (!pickupMarker) {
                        applyPickupPosition(defaultLat, defaultLng, { silent: true });
                    }

                    if (userInitiated && window.createSlidingToast) {
                        if (err.code === err.PERMISSION_DENIED) {
                            window.createSlidingToast('Mobile HTTP IP blocks GPS prompts. Pinned to Santa Rosa Homes default.', 'warning');
                        } else {
                            window.createSlidingToast('GPS location unavailable.', 'error');
                        }
                    }
                },
                { enableHighAccuracy: true, timeout: 7000, maximumAge: 30000 }
            );
        };

        window.pinCurrentGPSForPickup = window.requestLocationPermissionAndPin;

        // ---- Pullable Grab-style booking form sheet (drag/swipe to expand & collapse) ----
        // Driver-Hub parity: TWO snap states only â€”
        //   Expanded (0): full form, sheet top edge at the container top.
        //   Semi-hidden: only the drag handle + header strip of the form is visible;
        //   the contents begin right where the visible edge stops ("prior to the
        //   contents"). Heights are computed from the real handle height so the snap
        //   always hugs just above the form contents on any screen size.
        var PAX_SHEET_CONTENT_MARGIN = 12;
        function getPaxHandleHeight() {
            var h = document.getElementById('pax-sheet-drag-handle');
            if (h) {
                var r = h.getBoundingClientRect();
                if (r.height > 0) return r.height;
            }
            return 74;
        }
        function getPaxPageHeight() {
            const card = document.getElementById('pax-booking-sheet');
            const wrap = card ? card.parentElement : null;
            return (wrap && wrap.parentElement && wrap.parentElement.offsetHeight > 0)
                ? wrap.parentElement.offsetHeight : window.innerHeight;
        }
        function getPaxSemiHiddenY() {
            // Booking mode: rest the sheet LOW â€” semi-hidden sliver (handle + the
            // first button-sized row of the form, like the driver hub's collapsed
            // state). The sheet is bottom-anchored, so the visible portion equals
            // sheetHeight âˆ’ translateY: the translate is derived from the SHEET's own
            // height (not the screen), keeping the same visible strip on every
            // resolution â€” the sheet can never fall below the fold.
            // Wait/track modes keep the content-hugging pull-down limit.
            if (!document.getElementById('pickup-row')) {
                const details = document.getElementById('pax-sheet-details-content');
                if (details) return Math.max(120, details.scrollHeight + 30);
                return 260;
            }
            const card = document.getElementById('pax-booking-sheet');
            const sheetH = card && card.offsetHeight > 0 ? card.offsetHeight : 542;
            const strip = getPaxHandleHeight() + PAX_SHEET_CONTENT_MARGIN + 56;
            const bySheet = sheetH - strip;
            const byPage = getPaxPageHeight() - strip;
            return Math.max(0, Math.min(bySheet, byPage));
        }
        function getPaxSheetMaxAllowed() {
            return getPaxSemiHiddenY();
        }
        function getPaxExpandedAnchorY() {
            return 0;
        }
        function getPaxCollapsedAnchorY() {
            return getPaxSemiHiddenY();
        }
        function getPaxDefaultPeekY() {
            // The booking form rests fully expanded by default (all modes).
            return getPaxExpandedAnchorY();
        }
        function getPaxExpandedContentPx() {
            const details = document.getElementById('pax-sheet-details-content');
            if (details) {
                const inner = details.firstElementChild || details;
                const innerH = Math.max(inner.scrollHeight, inner.offsetHeight, details.scrollHeight);
                if (innerH > 0) {
                    return Math.max(340, innerH + 24);
                }
            }
            if (document.getElementById('pickup-row')) {
                return 340;
            }
            return Math.max(280, Math.round(window.innerHeight * 0.72));
        }
        function getPaxExpandedContentMax() {
            return getPaxExpandedContentPx() + 'px';
        }

        function syncPaxFloatingButtons() {
            const containers = [
                document.getElementById('pax-floating-map-controls-container'),
                document.getElementById('pax-active-trip-map-controls-container'),
                document.getElementById('pax-wait-map-controls-container')
            ].filter(Boolean);

            if (containers.length === 0) return;

            const minSafeBottom = (window.innerWidth < 640) ? 76 : 84;
            let targetBottomNum = minSafeBottom;

            // 1. Prioritize active tracking Driver Card if visible
            const driverCard = document.getElementById('pax-active-driver-card');
            if (driverCard && driverCard.offsetParent !== null) {
                const dcRect = driverCard.getBoundingClientRect();
                if (dcRect.width > 0 && dcRect.height > 0) {
                    targetBottomNum = Math.max(minSafeBottom, Math.round(window.innerHeight - dcRect.top + 14));
                }
            } else {
                // 2. Otherwise calculate above the visible bottom sheet (wait or booking)
                const sheet = [
                    document.getElementById('pax-wait-sheet'),
                    document.getElementById('pax-booking-sheet'),
                    document.querySelector('.srh-wait-glass')
                ].find(el => el && el.offsetParent !== null);

                if (sheet) {
                    const sheetRect = sheet.getBoundingClientRect();
                    if (sheetRect.width > 0 && sheetRect.height > 0) {
                        targetBottomNum = Math.max(minSafeBottom, Math.round(window.innerHeight - sheetRect.top + 15));
                    }
                }
            }

            const targetBottom = targetBottomNum + 'px';

            containers.forEach(container => {
                container.style.transition = 'none';
                container.style.bottom = targetBottom;
                container.style.zIndex = '9999';
                container.style.opacity = '1';
                container.style.pointerEvents = 'auto';
            });

            if (window.syncPaxMapPadding) window.syncPaxMapPadding();
        }
        window.syncPaxFloatingButtons = syncPaxFloatingButtons;

        // ---- Driver-Hub-style rAF-synced floating buttons (springs into place on actions) ----
        // Driver-Hub parity: like animateFloatingButtonsSync on the hub, the spring run
        // ends with a single atomic camera recenter (centerDriverOnTricycle port).
        window.animatePaxFloatingButtonsSync = function(durationMs = 380) {
            const startTime = performance.now();
            function step(now) {
                if (window.syncPaxFloatingButtons) window.syncPaxFloatingButtons();
                if (now - startTime < durationMs) {
                    requestAnimationFrame(step);
                } else {
                    if (window.syncPaxFloatingButtons) window.syncPaxFloatingButtons();
                }
            }
            requestAnimationFrame(step);
        };

        // ---- Driver-Hub-style camera choreography for passenger actions (debounced 1.5s) ----
        window.animatePaxMapForAction = function(actionType) {
            if (window._lastPaxActionAnimType === actionType && (Date.now() - (window._lastPaxActionAnimTime || 0)) < 1500) {
                return;
            }
            window._lastPaxActionAnimType = actionType;
            window._lastPaxActionAnimTime = Date.now();

            let targetMap = null;
            try {
                if (typeof window._paxWaitMapInst !== 'undefined' && window._paxWaitMapInst) targetMap = window._paxWaitMapInst;
                else if (window.bookingMapInstance) targetMap = window.bookingMapInstance;
                else if (window.paxMapInstance) targetMap = window.paxMapInstance;
                else if (window.map) targetMap = window.map;
            } catch(e) {}
            if (!targetMap || typeof targetMap.easeTo !== 'function') return;

            let pin = null;
            try { pin = (typeof getPaxTargetPin === 'function') ? getPaxTargetPin() : null; } catch(e) {}
            if (!pin && typeof window._paxWaitPickupLat === 'number' && !isNaN(window._paxWaitPickupLat)) {
                pin = { lat: window._paxWaitPickupLat, lng: window._paxWaitPickupLng };
            }

            window._isMapCameraAnimating = true;

            let center = null, pitch = 0, bottomPad = 180, zoom = 16.5;
            if (actionType === 'book' && typeof pickupMarker !== 'undefined' && pickupMarker && typeof pickupMarker.getLngLat === 'function') {
                const p = pickupMarker.getLngLat();
                center = [p.lng, p.lat];
                zoom = 16.5;
                bottomPad = 220;
            } else if (actionType === 'cancel' || actionType === 'decline') {
                // Glide toward the drop-off pin so the cancel lands back on the destination
                if (typeof window._paxWaitDestLat === 'number' && !isNaN(window._paxWaitDestLat)) {
                    center = [window._paxWaitDestLng, window._paxWaitDestLat];
                } else if (pin) {
                    center = [pin.lng, pin.lat];
                }
                zoom = 16.5;
                bottomPad = 220;
            } else if (actionType === 'accept_fare' || actionType === 'fare_confirm') {
                if (pin) center = [pin.lng, pin.lat];
                zoom = 17;
                pitch = 20;
                bottomPad = 200;
            }
            if (!center) return;

            try {
                targetMap.easeTo({
                    center: center,
                    zoom: zoom,
                    pitch: pitch,
                    padding: { top: 0, bottom: bottomPad, left: 0, right: 0 },
                    duration: 700,
                    easing: function(t) { return t * (2 - t); }
                });
            } catch(e) {}

            setTimeout(() => { window._isMapCameraAnimating = false; }, 900);
        };

        function getPaxTargetPin() {
            try {
                const paxWrapper = document.getElementById('passenger-status-wrapper');
                const paxStatus = paxWrapper ? paxWrapper.dataset.status : 'none';

                // 1. Active trip view
                if (['in_transit', 'arrived', 'accepted'].includes(paxStatus)) {
                    if (typeof _paxDriverMarker !== 'undefined' && _paxDriverMarker && typeof _paxDriverMarker.getLngLat === 'function') {
                        const p = _paxDriverMarker.getLngLat();
                        return { lat: p.lat, lng: p.lng };
                    }
                    const canvas = document.getElementById('pax-home-map');
                    if (canvas && canvas.dataset.driverLat) {
                        const dlat = parseFloat(canvas.dataset.driverLat);
                        const dlng = parseFloat(canvas.dataset.driverLng);
                        if (!isNaN(dlat) && !isNaN(dlng)) return { lat: dlat, lng: dlng };
                    }
                    if (window.srhTricycle && typeof window.srhTricycle.lat === 'number' && !isNaN(window.srhTricycle.lat)) {
                        return { lat: window.srhTricycle.lat, lng: window.srhTricycle.lng };
                    }
                }

                // 2. Wait / searching / fare proposed view (MUST precede booking pickupMarker)
                if (['searching', 'fare_proposed', 'fare_accepted'].includes(paxStatus)) {
                    const pLat = parseFloat(window._paxWaitPickupLat);
                    const pLng = parseFloat(window._paxWaitPickupLng);
                    const dLat = parseFloat(window._paxWaitDestLat) || pLat;
                    const dLng = parseFloat(window._paxWaitDestLng) || pLng;
                    if (!isNaN(pLat) && !isNaN(pLng)) {
                        return { lat: (pLat + dLat) / 2, lng: (pLng + dLng) / 2, isPair: true, pLat: pLat, pLng: pLng, dLat: dLat, dLng: dLng };
                    }
                }

                // 3. Pickup pin marker on booking map
                const pm = window.pickupMarker || (typeof pickupMarker !== 'undefined' ? pickupMarker : null);
                if (pm && typeof pm.getLngLat === 'function') {
                    const p = pm.getLngLat();
                    if (p && typeof p.lat === 'number' && !isNaN(p.lat)) return { lat: p.lat, lng: p.lng };
                }

                const cLat = parseFloat(localStorage.getItem('srh_last_pickup_lat'));
                const cLng = parseFloat(localStorage.getItem('srh_last_pickup_lng'));
                if (isRealCoordinate(cLat, cLng)) {
                    return { lat: cLat, lng: cLng };
                }
            } catch(e) {}

            // Guaranteed fallback: Santa Rosa Homes default coordinates
            const defaultLat = (typeof SRH_LAT !== 'undefined' ? SRH_LAT : 15.429550175641715);
            const defaultLng = (typeof SRH_LNG !== 'undefined' ? SRH_LNG : 120.92240292427664);
            return { lat: defaultLat, lng: defaultLng };
        }

        // ---- Driver-Hub parity: syncHomeMapPadding port (verbatim semantics) ----
        // Bottom padding is derived purely from the sheet's live Y position over the map
        // (mapRect.bottom - sheetRect.top, + 12) and applied instantly via srhSetMapPadding
        // (duration 0). The camera NEVER moves here — snap/choreography animations run
        // through centerPaxCamera, exactly like the hub splits syncHomeMapPadding from
        // centerDriverOnTricycle. No throttle, no debounce, no args.
        function syncPaxMapPadding() {
            if (window._isMapCameraAnimating) return;
            const activeEl = document.activeElement;
            if (activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA' || activeEl.tagName === 'SELECT')) {
                return; // Never recalculate or shift map padding while user is interacting with form inputs
            }

            let activeMap = null;
            try {
                if (typeof bookingMap !== 'undefined' && bookingMap) activeMap = bookingMap;
                else if (window.bookingMapInstance) activeMap = window.bookingMapInstance;
                else if (window._paxWaitMapInst) activeMap = window._paxWaitMapInst;
                else if (window.paxMapInstance) activeMap = window.paxMapInstance;
                else if (typeof waitMap !== 'undefined' && waitMap) activeMap = waitMap;
                else if (typeof paxMap !== 'undefined' && paxMap) activeMap = paxMap;
                else if (window.paxMap) activeMap = window.paxMap;
                else if (window.map) activeMap = window.map;
            } catch(e) {}

            if (!activeMap || !activeMap.getContainer) return;

            let bottomPad = 60;
            const pState = window.__srhPaxPadState || (window.__srhPaxPadState = { source: null, applied: null });

            // 1. Prioritize active tracking Driver Card if visible
            const driverCard = document.getElementById('pax-active-driver-card');
            if (driverCard && driverCard.offsetParent !== null) {
                try {
                    const mapRect = activeMap.getContainer().getBoundingClientRect();
                    const dcRect = driverCard.getBoundingClientRect();
                    const cardTopFromMapBottom = mapRect.bottom - dcRect.top;
                    if (cardTopFromMapBottom > 40) {
                        const rawSource = cardTopFromMapBottom + 12;
                        const candidate = Math.min(Math.round(rawSource), Math.round(window.innerHeight / 2));
                        if (pState.applied === candidate && pState.source !== null && Math.abs(rawSource - pState.source) < 1) {
                            pState.source = rawSource;
                            return;
                        }
                        pState.source = rawSource;
                        pState.applied = candidate;
                        bottomPad = candidate;
                    }
                } catch(e) {}
            } else {
                // 2. Otherwise calculate from visible bottom sheet
                const sheet = [
                    document.getElementById('pax-wait-sheet'),
                    document.getElementById('pax-booking-sheet'),
                    document.querySelector('.srh-wait-glass')
                ].find(el => el && el.offsetParent !== null);

                if (sheet) {
                    try {
                        const mapRect = activeMap.getContainer().getBoundingClientRect();
                        const sheetRect = sheet.getBoundingClientRect();
                        const sheetTopFromMapBottom = mapRect.bottom - sheetRect.top;
                        if (sheetTopFromMapBottom > 40) {
                            const rawSource = sheetTopFromMapBottom + 12;
                            const candidate = Math.min(Math.round(rawSource), Math.round(window.innerHeight / 2));
                            if (pState.applied === candidate && pState.source !== null && Math.abs(rawSource - pState.source) < 1) {
                                pState.source = rawSource;
                                return;
                            }
                            pState.source = rawSource;
                            pState.applied = candidate;
                            bottomPad = candidate;
                        }
                    } catch(e) {}
                }
            }
            bottomPad = Math.min(bottomPad, Math.round(window.innerHeight / 2));

            try {
                if (typeof window.srhSetMapPadding === 'function') {
                    window.srhSetMapPadding(activeMap, bottomPad);
                } else if (typeof activeMap.setPadding === 'function') {
                    activeMap.setPadding({ top: 0, bottom: bottomPad, left: 0, right: 0 });
                }
            } catch(e) {
                try { if (typeof window.srhSetMapPadding === 'function') window.srhSetMapPadding(activeMap, bottomPad); } catch(e2) {}
            }
        }
        window.syncBookingSheetPadding = syncPaxMapPadding;
        window.syncPaxMapPadding = syncPaxMapPadding;

        // ---- Driver-Hub parity: centerDriverOnTricycle port (verbatim) ----
        // Single, atomic camera ease that recenters the target pin while preserving the
        // current zoom / pitch / bearing. Used on sheet snaps, status changes and the
        // floating-button spring — but never inside syncPaxMapPadding.
        window.centerPaxCamera = function(duration = 550) {
            if (window._isMapCameraAnimating) return;
            const activeEl = document.activeElement;
            if (activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA')) return;

            let activeMap = null;
            try {
                if (typeof bookingMap !== 'undefined' && bookingMap) activeMap = bookingMap;
                else if (window.bookingMapInstance) activeMap = window.bookingMapInstance;
                else if (window._paxWaitMapInst) activeMap = window._paxWaitMapInst;
                else if (window.paxMapInstance) activeMap = window.paxMapInstance;
                else if (window.map) activeMap = window.map;
            } catch(e) {}
            if (!activeMap || typeof activeMap.easeTo !== 'function') return;

            let pin = null;
            try { pin = (typeof getPaxTargetPin === 'function') ? getPaxTargetPin() : null; } catch(e) {}
            if (!pin) return;

            if (pin.isPair && typeof window.paxFramePinsInStrip === 'function') {
                window.paxFramePinsInStrip(activeMap, pin.pLat, pin.pLng, pin.dLat, pin.dLng, duration);
                return;
            }

            try {
                activeMap.easeTo({
                    center: [pin.lng, pin.lat],
                    zoom: currentZoom,
                    pitch: currentPitch,
                    bearing: currentBearing,
                    duration: duration,
                    easing: function(t) { return t * (2 - t); }
                });
            } catch(e) {}
        };

        window.recenterPassengerLocation = function() {
            if (window.syncPaxMapPadding) window.syncPaxMapPadding();
            if (window.centerPaxCamera) window.centerPaxCamera(550);
        };
        window.recenterPassengerMap = window.recenterPassengerLocation;
        window.recenterWaitMap = window.recenterPassengerLocation;

        function centerPickupOnVisible() {
            const activeEl = document.activeElement;
            if (activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA')) return;
            if (window.syncPaxMapPadding) window.syncPaxMapPadding();
            if (window.centerPaxCamera) window.centerPaxCamera(450);
        }
        window.centerPickupOnVisible = centerPickupOnVisible;

        syncPaxMapPadding();
        window.addEventListener('resize', function() {
            const activeEl = document.activeElement;
            if (activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA' || activeEl.tagName === 'SELECT')) {
                return; // Suppress viewport jumps during mobile virtual keyboard show/hide
            }
            syncPaxMapPadding();
        });

        // The details panel keeps overflow: visible permanently so texts and dropdowns never shift
        function setPaxSheetOverflow(hidden) {
            const d = document.getElementById('pax-sheet-details-content');
            if (d && d.style.overflow !== 'visible') d.style.overflow = 'visible';
        }

        window.togglePaxBookingSheet = function() {
            const handle = document.getElementById('pax-sheet-drag-handle');
            if (handle && typeof handle.srhToggleSheet === 'function') {
                handle.srhToggleSheet();
            }
        };

        function initPaxSheetSwipeGesture() {
            const card = document.getElementById('pax-booking-sheet');
            if (!card) return;

            if (card._paxDirectDragInit) return;
            card._paxDirectDragInit = true;

            let startY = 0;
            let startTranslateY = 0;
            let currentTranslateY = 0;
            let isDragging = false;
            let hasMoved = false;

            let maxAllowed = 0;
            let minButtonsBottom = 84;
            let startButtonsBottom = 84;
            let sheetTopStart = 0;
            let mapBottomStart = null;
            let mapHeightStart = 0;
            let padMap = null;
            let padState = { source: null, applied: null };
            let btnContainers = [];

            let pendingY = 0;
            let pendingButtonDelta = 0;
            let pendingMapPad = null;
            let rafId = 0;
            let settleRafId = 0;

            let lastMoveY = 0;
            let lastMoveT = 0;
            let velocity = 0;

            function getCurrentCardY() {
                const style = window.getComputedStyle(card);
                const transform = style.transform || style.webkitTransform;
                if (transform && transform !== 'none') {
                    const matrix = transform.match(/^matrix\((.+)\)$/);
                    if (matrix) return parseFloat(matrix[1].split(',')[5]) || 0;
                    const matrix3d = transform.match(/^matrix3d\((.+)\)$/);
                    if (matrix3d) return parseFloat(matrix3d[1].split(',')[13]) || 0;
                }
                return 0;
            }

            function getTouchY(e) {
                if (e.touches && e.touches.length > 0) return e.touches[0].clientY;
                if (e.changedTouches && e.changedTouches.length > 0) return e.changedTouches[0].clientY;
                if (typeof e.clientY === 'number') return e.clientY;
                return 0;
            }

            function getActiveDragMap() {
                try {
                    if (typeof bookingMap !== 'undefined' && bookingMap) return bookingMap;
                    if (window.bookingMapInstance) return window.bookingMapInstance;
                    if (window._paxWaitMapInst) return window._paxWaitMapInst;
                    if (window.paxMapInstance) return window.paxMapInstance;
                    if (typeof waitMap !== 'undefined' && waitMap) return waitMap;
                    if (typeof paxMap !== 'undefined' && paxMap) return paxMap;
                    if (window.paxMap) return window.paxMap;
                    if (window.map) return window.map;
                } catch (e) {}
                return null;
            }

            function applyMapPad(candidate, rawSource) {
                const applies = !(padState.applied === candidate && padState.source !== null && Math.abs(rawSource - padState.source) < 1);
                padState.source = rawSource;
                if (!applies) return;
                padState.applied = candidate;
                try {
                    if (typeof window.srhSetMapPadding === 'function') {
                        window.srhSetMapPadding(padMap, candidate);
                    } else if (padMap && typeof padMap.setPadding === 'function') {
                        padMap.setPadding({ top: 0, bottom: candidate, left: 0, right: 0 });
                    }
                } catch (e) {}
            }

            function applyFrame() {
                rafId = 0;
                card.style.transform = 'translate3d(0, ' + pendingY.toFixed(2) + 'px, 0)';
                currentTranslateY = pendingY;
                if (pendingButtonDelta !== 0) {
                    const bottom = Math.max(minButtonsBottom, startButtonsBottom + pendingButtonDelta);
                    for (let i = 0; i < btnContainers.length; i++) {
                        if (btnContainers[i]) btnContainers[i].style.bottom = bottom + 'px';
                    }
                    pendingButtonDelta = 0;
                }
                if (pendingMapPad) {
                    applyMapPad(pendingMapPad.candidate, pendingMapPad.raw);
                    pendingMapPad = null;
                }
            }

            function scheduleFrame() {
                if (!rafId) rafId = requestAnimationFrame(applyFrame);
            }

            function beginGesture(clientY) {
                startY = clientY;
                // Freeze the LIVE position first (the CSS snap transition may still be
                // mid-flight), then kill the transition â€” otherwise the sheet would jump
                // to the inline (target) transform when the user grabs it mid-snap.
                const liveY = getCurrentCardY();
                startTranslateY = liveY;
                currentTranslateY = liveY;
                lastMoveY = clientY;
                lastMoveT = performance.now();
                velocity = 0;
                isDragging = false;
                hasMoved = false;

                if (settleRafId) { cancelAnimationFrame(settleRafId); settleRafId = 0; }
                setPaxSheetOverflow(true);
                card.style.transition = 'none';
                card.style.transform = 'translate3d(0, ' + liveY.toFixed(2) + 'px, 0)';

                maxAllowed = getPaxSheetMaxAllowed();
                minButtonsBottom = (window.innerWidth < 640) ? 76 : 84;

                if (window.syncPaxFloatingButtons) window.syncPaxFloatingButtons();

                const rect = card.getBoundingClientRect();
                sheetTopStart = rect.top;
                startButtonsBottom = Math.max(minButtonsBottom, Math.round(window.innerHeight - sheetTopStart + 15));

                btnContainers = [
                    document.getElementById('pax-floating-map-controls-container'),
                    document.getElementById('pax-active-trip-map-controls-container'),
                    document.getElementById('pax-wait-map-controls-container')
                ].filter(Boolean);

                padState = { source: null, applied: null };
                padMap = window._isMapCameraAnimating ? null : getActiveDragMap();
                mapBottomStart = null;
                mapHeightStart = 0;
                if (padMap && padMap.getContainer) {
                    try {
                        const mr = padMap.getContainer().getBoundingClientRect();
                        if (mr.width > 0 && mr.height > 0) {
                            mapBottomStart = mr.bottom;
                            mapHeightStart = mr.height;
                        }
                    } catch (e) {}
                }
            }

            function onMove(clientY, evt) {
                if (evt && evt.cancelable) {
                    evt.preventDefault();
                }
                if (lastMoveT === 0) return;

                const now = (evt && evt.timeStamp && !isNaN(evt.timeStamp)) ? evt.timeStamp : performance.now();
                const dt = Math.max(1, now - lastMoveT);
                velocity = (clientY - lastMoveY) / dt;
                lastMoveY = clientY;
                lastMoveT = now;

                const deltaY = clientY - startY;
                if (Math.abs(deltaY) <= 4) return;

                if (!isDragging) {
                    isDragging = true;
                    hasMoved = true;
                    card.style.transition = 'none';
                    card.style.willChange = 'transform';
                }

                let targetY = startTranslateY + deltaY;
                if (targetY < 0) targetY = targetY * 0.22;
                if (targetY > maxAllowed) targetY = maxAllowed + (targetY - maxAllowed) * 0.22;

                pendingY = targetY;
                pendingButtonDelta = -(targetY - startTranslateY);
                if (mapBottomStart !== null) {
                    const sheetTop = sheetTopStart + (targetY - startTranslateY);
                    const rawSource = mapBottomStart - sheetTop + 12;
                    if (rawSource > 40) {
                        const candidate = Math.min(Math.round(rawSource), Math.round(window.innerHeight / 2));
                        pendingMapPad = { candidate: candidate, raw: rawSource };
                    } else {
                        pendingMapPad = null;
                    }
                } else {
                    pendingMapPad = null;
                }
                scheduleFrame();
            }

            function onEnd() {
                if (rafId) { cancelAnimationFrame(rafId); rafId = 0; applyFrame(); }

                if (!hasMoved || !isDragging) {
                    isDragging = false;
                    lastMoveT = 0;
                    velocity = 0;
                    return;
                }
                isDragging = false;
                lastMoveT = 0;
                card.style.willChange = 'auto';

                let anchorY;
                if (velocity > 1.6) anchorY = maxAllowed;
                else if (velocity < -1.6) anchorY = 0;
                else anchorY = (currentTranslateY > maxAllowed / 2) ? maxAllowed : 0;
                settleTo(anchorY);
                velocity = 0;
            }

            function syncCompanions(y) {
                const bottom = Math.max(minButtonsBottom, startButtonsBottom - (y - startTranslateY));
                for (let i = 0; i < btnContainers.length; i++) {
                    if (btnContainers[i]) btnContainers[i].style.bottom = bottom + 'px';
                }
                if (mapBottomStart !== null) {
                    const sheetTop = sheetTopStart + (y - startTranslateY);
                    const rawSource = mapBottomStart - sheetTop + 12;
                    if (rawSource > 40) {
                        const candidate = Math.min(Math.round(rawSource), Math.round(window.innerHeight / 2));
                        applyMapPad(candidate, rawSource);
                    }
                }
            }

            function glideBackToClamp() {
                const fromY = currentTranslateY;
                const targetY = currentTranslateY < 0 ? 0 : maxAllowed;
                if (Math.abs(targetY - fromY) < 0.5) {
                    currentTranslateY = targetY;
                    card.style.transform = 'translate3d(0, ' + targetY.toFixed(2) + 'px, 0)';
                    finalizeSettle();
                    return;
                }
                let dur = 700;
                card.style.willChange = 'transform';
                card.style.transition = 'none';
                card.style.transform = 'translate3d(0, ' + fromY.toFixed(2) + 'px, 0)';
                void card.offsetHeight;
                card.style.transition = 'transform ' + dur + 'ms cubic-bezier(0.16, 1, 0.2, 1)';
                card.style.transform = 'translate3d(0, ' + targetY.toFixed(2) + 'px, 0)';
                const startTime = performance.now();
                function snapSyncLoop() {
                    const elapsed = performance.now() - startTime;
                    const liveY = getCurrentCardY();
                    currentTranslateY = liveY;
                    syncCompanions(liveY);
                    if (elapsed < dur) {
                        settleRafId = requestAnimationFrame(snapSyncLoop);
                    } else {
                        settleRafId = 0;
                        currentTranslateY = targetY;
                        card.style.transform = 'translate3d(0, ' + targetY.toFixed(2) + 'px, 0)';
                        card.style.transition = 'none';
                        card.style.willChange = 'auto';
                        finalizeSettle();
                    }
                }
                settleRafId = requestAnimationFrame(snapSyncLoop);
            }

            function settleTo(anchorY, duration) {
                if (settleRafId) { cancelAnimationFrame(settleRafId); settleRafId = 0; }

                // Released while rubber-banded: the stretch glides back to the boundary first.
                if (currentTranslateY < 0 || currentTranslateY > maxAllowed) {
                    glideBackToClamp();
                    return;
                }

                const fromY = currentTranslateY;
                const targetY = anchorY;
                if (Math.abs(targetY - fromY) < 0.5) {
                    currentTranslateY = targetY;
                    card.style.transform = 'translate3d(0, ' + targetY.toFixed(2) + 'px, 0)';
                    finalizeSettle();
                    return;
                }

                // Driver-hub parity: IDENTICAL to the hub sheet's animateToSnapState â€”
                // the same silky ultra-slow bezier glide, GPU-composited via a CSS
                // transition, with the real transform read back every frame to drive
                // the floating buttons + map padding.
                const dur = duration || 1250;
                card.style.willChange = 'transform';
                card.style.transition = 'none';
                card.style.transform = 'translate3d(0, ' + fromY.toFixed(2) + 'px, 0)';
                void card.offsetHeight;
                card.style.transition = 'transform ' + dur + 'ms cubic-bezier(0.16, 1, 0.2, 1)';
                card.style.transform = 'translate3d(0, ' + targetY.toFixed(2) + 'px, 0)';
                const startTime = performance.now();
                function snapSyncLoop() {
                    const elapsed = performance.now() - startTime;
                    const liveY = getCurrentCardY();
                    currentTranslateY = liveY;
                    syncCompanions(liveY);
                    if (elapsed < dur) {
                        settleRafId = requestAnimationFrame(snapSyncLoop);
                    } else {
                        settleRafId = 0;
                        currentTranslateY = targetY;
                        card.style.transform = 'translate3d(0, ' + targetY.toFixed(2) + 'px, 0)';
                        card.style.transition = 'none';
                        card.style.willChange = 'auto';
                        finalizeSettle();
                    }
                }
                settleRafId = requestAnimationFrame(snapSyncLoop);
            }

            function finalizeSettle() {
                card.style.transition = 'none';
                if (window.syncPaxFloatingButtons) window.syncPaxFloatingButtons();
                if (window.syncPaxMapPadding) window.syncPaxMapPadding();
            }

            const canUsePointer = 'PointerEvent' in window;

            function isFormInteractionTarget(target) {
                if (!target || typeof target.closest !== 'function') return false;
                return !!(
                    target.closest('input') ||
                    target.closest('button') ||
                    target.closest('select') ||
                    target.closest('textarea') ||
                    target.closest('form') ||
                    target.closest('#pickup-row') ||
                    target.closest('#dest-row') ||
                    target.closest('#dest-autocomplete-dropdown') ||
                    target.closest('#pax-sheet-details-content') ||
                    target.closest('[data-no-sheet-drag]')
                );
            }

            function handlePointerDown(e) {
                if (e.pointerType === 'mouse' && e.button !== 0) return;
                if (isFormInteractionTarget(e.target)) return;
                beginGesture(e.clientY);
                try { card.setPointerCapture(e.pointerId); } catch (err) {}
            }

            function handlePointerMove(e) {
                onMove(e.clientY, e);
            }

            function handlePointerEnd(e) {
                onEnd();
                try { card.releasePointerCapture(e.pointerId); } catch (err) {}
            }

            const targets = [document.getElementById('pax-sheet-drag-handle'), card].filter(Boolean);
            targets.forEach(target => {
                if (canUsePointer) {
                    target.addEventListener('pointerdown', handlePointerDown);
                } else {
                    target.addEventListener('touchstart', function(e) {
                        if (!e.touches || !e.touches.length) return;
                        if (isFormInteractionTarget(e.target)) return;
                        beginGesture(e.touches[0].clientY);
                        window.addEventListener('touchmove', legacyTouchMove, { passive: false });
                        window.addEventListener('touchend', legacyTouchEnd, { capture: true });
                        window.addEventListener('touchcancel', legacyTouchEnd, { capture: true });
                    }, { passive: false });
                    target.addEventListener('mousedown', function(e) {
                        if (e.button !== 0) return;
                        if (isFormInteractionTarget(e.target)) return;
                        beginGesture(e.clientY);
                        window.addEventListener('mousemove', legacyMouseMove, { passive: true });
                        window.addEventListener('mouseup', legacyMouseEnd, { capture: true });
                    }, { passive: true });
                }
            });

            if (canUsePointer) {
                card.addEventListener('pointermove', handlePointerMove, { passive: false });
                card.addEventListener('pointerup', handlePointerEnd, { capture: true });
                card.addEventListener('pointercancel', handlePointerEnd, { capture: true });
            }

            function legacyTouchMove(e) {
                if (e.touches && e.touches.length) onMove(e.touches[0].clientY, e);
            }
            function legacyTouchEnd(e) {
                window.removeEventListener('touchmove', legacyTouchMove, { passive: false });
                window.removeEventListener('touchend', legacyTouchEnd, { capture: true });
                window.removeEventListener('touchcancel', legacyTouchEnd, { capture: true });
                onEnd();
            }
            function legacyMouseMove(e) {
                onMove(e.clientY, e);
            }
            function legacyMouseEnd(e) {
                window.removeEventListener('mousemove', legacyMouseMove, { passive: true });
                window.removeEventListener('mouseup', legacyMouseEnd, { capture: true });
                onEnd();
            }

            const handle = document.getElementById('pax-sheet-drag-handle');
            if (handle) {
                handle.srhToggleSheet = function() {
                    if (isDragging) return;
                    const curY = getCurrentCardY();
                    const targetY = (curY > maxAllowed / 2) ? 0 : maxAllowed;
                    settleTo(targetY, 400);
                };
                handle.addEventListener('click', function(e) {
                    if (hasMoved) return;
                    if (isFormInteractionTarget(e.target)) return;
                    if (handle.srhToggleSheet) handle.srhToggleSheet();
                });
            }

            card.addEventListener('click', function(e) {
                if (hasMoved) {
                    e.preventDefault();
                    e.stopPropagation();
                    hasMoved = false;
                }
            }, true);

            // Set initial position (peek)
            const initialY = getPaxDefaultPeekY();
            card.style.transform = 'translate3d(0, ' + initialY + 'px, 0)';
            if (window.syncPaxFloatingButtons) window.syncPaxFloatingButtons();
        }
        window.initPaxSheetSwipeGesture = initPaxSheetSwipeGesture;
        window.getPaxExpandedAnchorY = getPaxExpandedAnchorY;
        window.getPaxCollapsedAnchorY = getPaxCollapsedAnchorY;
        window.getPaxDefaultPeekY = getPaxDefaultPeekY;

        initPaxSheetSwipeGesture();
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPaxSheetSwipeGesture);
        } else {
            initPaxSheetSwipeGesture();
        }

        // Stash the booking-mode sheet HTML (pickup-row only renders in booking mode)
        // so a cancel can restore the booking form in place without reloading the page.
        var bookingSheetForReset = document.getElementById('pax-booking-sheet');
        if (bookingSheetForReset && document.getElementById('pickup-row')) {
            window._paxBookingSheetHtml = bookingSheetForReset.innerHTML;
        }

        // After any SPA content swap the passenger sheet can land on a stale
        // transform. Ensure it rests at expanded position (0) and floating buttons are synced.
        window.__paxSpaLoadedRehug = function() {
            const card = document.getElementById('pax-booking-sheet');
            if (card) {
                card.style.transform = 'translate3d(0, ' + (window.getPaxDefaultPeekY ? window.getPaxDefaultPeekY() : 0) + 'px, 0)';
            }
            if (typeof initPaxSheetSwipeGesture === 'function') initPaxSheetSwipeGesture();
            if (typeof initPaxHomeMapGlobal === 'function') initPaxHomeMapGlobal();
            if (typeof syncPaxFloatingButtons === 'function') {
                setTimeout(function() { syncPaxFloatingButtons(); }, 150);
            }
        };
        window.removeEventListener('spa:page-loaded', window.__paxSpaLoadedRehug);
        window.addEventListener('spa:page-loaded', window.__paxSpaLoadedRehug);

        function initPaxHomeMap() {
            const el = document.getElementById('pax-home-map');
            if (!el) return;
            if (typeof maplibregl === 'undefined' || typeof window.srhCreateMap !== 'function') {
                if (window.srhEnsureMaplibre && typeof window.srhEnsureMaplibre === 'function') {
                    window.srhEnsureMaplibre(initPaxHomeMap);
                } else {
                    setTimeout(initPaxHomeMap, 100);
                }
                return;
            }

            window.initPaxHomeMapGlobal = initPaxHomeMap;

            // ---- Mode detection (one map, three views — Driver-Hub style) ----
            const status = el.dataset.status || '';
            const isTrack = ['accepted','arrived','in_transit'].includes(status);
            const isWait = status !== '' && !isTrack;
            const isBooking = status === '';

            // ---- Initial camera state: cached GPS / ride data / SRH default ----
            const pLat = parseFloat(el.dataset.pickupLat), pLng = parseFloat(el.dataset.pickupLng);
            const dLat = parseFloat(el.dataset.destLat),   dLng = parseFloat(el.dataset.destLng);
            const drvLat = parseFloat(el.dataset.driverLat), drvLng = parseFloat(el.dataset.driverLng);
            const cachedLat = parseFloat(localStorage.getItem('srh_last_pickup_lat'));
            const cachedLng = parseFloat(localStorage.getItem('srh_last_pickup_lng'));

            let initialLat, initialLng, initialZoom = 18.2;
            if (isBooking) {
                initialLat = isRealCoordinate(cachedLat, cachedLng) ? cachedLat : SRH_LAT;
                initialLng = isRealCoordinate(cachedLat, cachedLng) ? cachedLng : SRH_LNG;
                initialZoom = 18.2;
            } else if (isTrack && isRealCoordinate(drvLat, drvLng)) {
                initialLat = drvLat;
                initialLng = drvLng;
                initialZoom = status === 'in_transit' ? 18.5 : 18.2;
            } else if (isWait && isRealCoordinate(pLat, pLng)) {
                initialLat = pLat;
                initialLng = pLng;
                initialZoom = 18.2;
            } else {
                initialLat = SRH_LAT;
                initialLng = SRH_LNG;
                initialZoom = 18.2;
            }

            if (window.paxHomeMapInstance) {
                try { window.paxHomeMapInstance.remove(); } catch(e){}
                window.paxHomeMapInstance = null;
                pickupMarker = null;
                destMarker = null;
            }
            if (window.bookingMapInstance) {
                try { window.bookingMapInstance.remove(); } catch(e){}
                window.bookingMapInstance = null;
            }
            if (window.bookingMap) {
                try { window.bookingMap.remove(); } catch(e){}
                window.bookingMap = null;
            }
            // Old map is gone: drop driver marker refs so updatePaxDriverMarker
            // recreates the tricycle on the fresh map instead of gliding a detached one.
            if (window._paxDriverMarker) { try { window._paxDriverMarker.remove(); } catch(e) {} window._paxDriverMarker = null; }
            _paxDriverMarker = null;

            // Frame the camera with the correct bottom padding BEFORE the map boots, so
            // the initial view is final from the very first frame (Driver-Hub parity: a
            // post-load setPadding would otherwise animate the camera and make the map
            // look like it's dropping down).
            const driverCard = document.getElementById('pax-active-driver-card');
            const mapSheet = [
                document.getElementById('pax-wait-sheet'),
                document.getElementById('pax-booking-sheet'),
                document.querySelector('.srh-wait-glass')
            ].find(s => s && s.offsetParent !== null);

            let initialPaddingBottom = 60;
            if (driverCard && driverCard.offsetParent !== null) {
                try {
                    const dcRect = driverCard.getBoundingClientRect();
                    const hubRect = el.parentElement.getBoundingClientRect();
                    const cardTopFromMapBottom = hubRect.bottom - dcRect.top;
                    if (cardTopFromMapBottom > 40 && cardTopFromMapBottom < hubRect.height) {
                        initialPaddingBottom = Math.round(cardTopFromMapBottom + 12);
                    }
                } catch(e) {}
            } else if (mapSheet) {
                try {
                    const sheetRect = mapSheet.getBoundingClientRect();
                    if (sheetRect.height > 0 && sheetRect.width > 0) {
                        const hubRect = el.parentElement.getBoundingClientRect();
                        const sheetTopFromMapBottom = hubRect.bottom - sheetRect.top;
                        if (sheetTopFromMapBottom > 40) {
                            initialPaddingBottom = Math.round(sheetTopFromMapBottom + 12);
                        }
                    }
                } catch(e) {}
            }
            initialPaddingBottom = Math.min(initialPaddingBottom, Math.round(window.innerHeight / 2));

            bookingMap = window.srhCreateMap(el, {
                lat: initialLat,
                lng: initialLng,
                zoom: initialZoom,
                pitch: 0,
                bearing: 0,
                touchZoomRotate: true,
                doubleClickZoom: false,
                boxZoom: false,
                paddingBottom: initialPaddingBottom
            });
            if (!bookingMap) return;
            window.paxHomeMapInstance = bookingMap;
            window.bookingMapInstance = bookingMap;
            window._paxWaitMapInst = bookingMap;
            window.paxMapInstance = bookingMap;
            window.paxMap = bookingMap;
            window.map = bookingMap;
            try { bookingMap.resize(); } catch(e){}
            try { syncBookingSheetPadding(); } catch(e){}
            // Floating buttons boot in their normal resting position (above the sheet top)
            if (window.syncPaxFloatingButtons) window.syncPaxFloatingButtons();

            // Track-view compass/bearing helper state (camera-only, no tricycle marker)
            if (isTrack && isRealCoordinate(drvLat, drvLng)) {
                window.srhTricycle = { lng: drvLng, lat: drvLat, heading: 0 };
            }

            if (typeof syncPaxMapPadding === 'function') syncPaxMapPadding();
            try {
                if (!window._isMapCameraAnimating) {
                    bookingMap.jumpTo({ center: [initialLng, initialLat], zoom: initialZoom });
                }
            } catch(e) {}

            // Driver-Hub parity: any user tap closes announcements / notifications.
            ['touchstart', 'click', 'dragstart', 'movestart'].forEach(ev => {
                bookingMap.on(ev, (e) => {
                    if ((ev === 'movestart' || ev === 'move') && e && !e.originalEvent) return;
                    if (window.closeAnnouncementModal) window.closeAnnouncementModal();
                    window.dispatchEvent(new CustomEvent('srh-close-notifications'));
                });
            });

            // ---- Pins: booking is interactive (form), wait/track are read-only ride pins ----
            if (isBooking) {
                if (typeof window.paxClearDriverMarker === 'function') window.paxClearDriverMarker();
                if (typeof window.paxClearDriverRoute === 'function') window.paxClearDriverRoute();
                if (typeof destMarker !== 'undefined' && destMarker) { try { destMarker.remove(); } catch(e){} destMarker = null; }
                if (window.destMarker) { try { window.destMarker.remove(); } catch(e){} window.destMarker = null; }
                (window._paxRidePinMarkers || []).forEach(m => { try { m.remove(); } catch(e){} });
                window._paxRidePinMarkers = [];

                if (typeof window.restorePaxBookingChrome === 'function') window.restorePaxBookingChrome();

                initBookingPins();
            } else if (isWait || isTrack) {
                frameRidePinsWithEase(pLat, pLng, dLat, dLng, isTrack && status === 'in_transit');
                if (isTrack && isRealCoordinate(drvLat, drvLng)) {
                    if (typeof window.handlePaxRideStatusChange === 'function') {
                        window.handlePaxRideStatusChange(status, { pickup_lat: pLat, pickup_lng: pLng, dest_lat: dLat, dest_lng: dLng, driver_lat: drvLat, driver_lng: drvLng });
                    } else if (typeof window.updatePaxDriverMarker === 'function') {
                        window.updatePaxDriverMarker(drvLat, drvLng, status, pLat, pLng, dLat, dLng);
                    }
                }
                if (isWait) {
                    // Wait states reuse the booking sheet shell: keep its content open
                    // (status card visible) up to the expanded cap.
                    try {
                        const waitContent = document.getElementById('pax-sheet-details-content');
                        if (waitContent && waitContent.style.maxHeight && waitContent.style.maxHeight !== '0px') {
                            // The fare-proposal card (driver info + fare + Accept/Decline)
                            // must not be clipped by the generic 400px wait cap: let it use
                            // the full expanded height so its buttons are always visible.
                            const hasFareCard = waitContent.querySelector('.pax-fare-proposal-card');
                            waitContent.style.maxHeight = Math.min(hasFareCard ? 100000 : 400, getPaxExpandedContentPx()) + 'px';
                        }
                    } catch(e) {}
                }
            }

            setTimeout(() => { try { if (window.map) { window.map.resize(); } } catch(e){} }, 200);
            // Re-sync after the layout fully settles so the buttons sit above the sheet top
            setTimeout(() => { if (window.syncPaxFloatingButtons) window.syncPaxFloatingButtons(); }, 500);

            try { initPaxSheetSwipeGesture(); } catch(e){}
        }

        // ---- Booking mode: interactive pickup + drop-off pinning (only passenger feature kept) ----
        function initBookingPins() {
            let _reverseGeocodeTimer = null;
            let _reverseGeocodeAbort = null;

            function reverseGeocodeDestination(lat, lng) {
                const destInput = document.getElementById('passenger-dest-text-input');
                if (!destInput) return;

                const originalVal = destInput.value.trim();
                if (!originalVal || originalVal.includes('(') || originalVal.toLowerCase().includes('pinned') || originalVal.toLowerCase().includes('locating')) {
                    destInput.value = 'Locating pinned place...';
                }

                if (_reverseGeocodeTimer) clearTimeout(_reverseGeocodeTimer);
                if (_reverseGeocodeAbort) {
                    try { _reverseGeocodeAbort.abort(); } catch(e){}
                    _reverseGeocodeAbort = null;
                }

                _reverseGeocodeTimer = setTimeout(() => {
                    _reverseGeocodeAbort = new AbortController();
                    const proxyUrl = `/api/reverse-geocode?lat=${lat}&lng=${lng}`;
                    fetch(proxyUrl, { signal: _reverseGeocodeAbort.signal })
                        .then(r => r.json())
                        .then(data => {
                            if (data && data.display_name) {
                                const parts = data.display_name.split(',').map(s=>s.trim());
                                const mainName = parts[0];
                                const sub = window.srhCleanSubtitle ? window.srhCleanSubtitle(mainName, null, data.display_name) : '';
                                let fullAddress = mainName;
                                if (sub && !mainName.toLowerCase().includes(sub.toLowerCase())) {
                                    fullAddress = `${mainName}, ${sub}`;
                                }
                                destInput.value = fullAddress;
                            } else {
                                destInput.value = `Pinned Location (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
                            }
                        })
                        .catch(err => {
                            if (err && err.name === 'AbortError') return;
                            destInput.value = `Pinned Location (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
                        });
                }, 250);
            }

            function createDestMarker(lat, lng) {
                if (window.destMarker && window.destMarker !== destMarker) {
                    try { window.destMarker.remove(); } catch(e){}
                }
                if (destMarker) {
                    try { destMarker.remove(); } catch(e){}
                }
                const marker = new maplibregl.Marker({ element: destMarkerElement(), draggable: true })
                    .setLngLat([lng, lat])
                    .addTo(bookingMap);
                window.destMarker = marker;
                destMarker = marker;
                marker.on('dragend', () => {
                    const p = marker.getLngLat();
                    document.getElementById('destination_lat_input').value = p.lat;
                    document.getElementById('destination_lng_input').value = p.lng;
                    reverseGeocodeDestination(p.lat, p.lng);
                });
                return marker;
            }

            // Map click ALWAYS places/updates the Drop-off destination pin without auto-zooming
            bookingMap.on('click', e => {
                const wrapper = document.getElementById('passenger-status-wrapper');
                const status = wrapper ? wrapper.dataset.status : 'none';
                if (['searching', 'fare_proposed', 'fare_accepted', 'accepted', 'arrived', 'in_transit', 'returning'].includes(status)) return;

                const lat = e.lngLat.lat;
                const lng = e.lngLat.lng;
                const latInput = document.getElementById('destination_lat_input');
                const lngInput = document.getElementById('destination_lng_input');
                if (latInput) latInput.value = lat;
                if (lngInput) lngInput.value = lng;

                if (!destMarker) {
                    destMarker = createDestMarker(lat, lng);
                } else {
                    destMarker.setLngLat([lng, lat]);
                }
                reverseGeocodeDestination(lat, lng);
            });

            window.setDestinationPinOnMap = function(lat, lng, skipReverseGeocode = false) {
                if (!bookingMap) return;
                const latInput = document.getElementById('destination_lat_input');
                const lngInput = document.getElementById('destination_lng_input');
                if (latInput) latInput.value = lat;
                if (lngInput) lngInput.value = lng;
                if (!destMarker) {
                    destMarker = createDestMarker(lat, lng);
                } else {
                    destMarker.setLngLat([lng, lat]);
                }
                if (!skipReverseGeocode) {
                    reverseGeocodeDestination(lat, lng);
                }
                if (!window._paxSuppressDestFit) {
                    if (pickupMarker && destMarker) {
                        const pl = pickupMarker.getLngLat();
                        const dl = destMarker.getLngLat();
                        const bounds = [[Math.min(pl.lng, dl.lng), Math.min(pl.lat, dl.lat)], [Math.max(pl.lng, dl.lng), Math.max(pl.lat, dl.lat)]];
                        bookingMap.fitBounds(bounds, { padding: 60, animate: true });
                    } else {
                        bookingMap.easeTo({ center: [lng, lat], zoom: 16, duration: 500 });
                    }
                }
            };

            pinCurrentGPSForPickup();
            // Show pickup instantly: cached last GPS fix when available, else the
            // Santa Rosa Homes default, then let the live GPS fix overwrite it.
            const cachedLat = parseFloat(localStorage.getItem('srh_last_pickup_lat'));
            const cachedLng = parseFloat(localStorage.getItem('srh_last_pickup_lng'));
            if (isRealCoordinate(cachedLat, cachedLng)) {
                applyPickupPosition(cachedLat, cachedLng, { silent: true });
            } else {
                applyPickupPosition(SRH_LAT, SRH_LNG, { silent: true });
            }

            // Immediately check for destination params from Saved Places tab
            setTimeout(function() {
                if (typeof window.checkAndApplyUrlDestinationParams === 'function') {
                    window.checkAndApplyUrlDestinationParams();
                }
            }, 200);
        }

        // ---- Wait / Track mode: read-only ride pins + Driver-Hub-style ease framing ----
        function frameRidePinsWithEase(pLat, pLng, dLat, dLng, hidePickup) {
            window._paxRidePins = { pickup: { lat: pLat, lng: pLng }, dest: { lat: dLat, lng: dLng } };
            (window._paxRidePinMarkers || []).forEach(m => { try { m.remove(); } catch(e){} });
            window._paxRidePinMarkers = [];
            if (!bookingMap) return;

            // Wait / track ride pins: pickup pin is hidden when heading to dropoff (in_transit)
            if (!hidePickup && isRealCoordinate(pLat, pLng)) {
                const pMarker = new maplibregl.Marker({ element: pickupMarkerElement() }).setLngLat([pLng, pLat]).addTo(bookingMap);
                pMarker._isPickupPin = true;
                window._paxRidePinMarkers.push(pMarker);
            }
            if (isRealCoordinate(dLat, dLng)) {
                const dMarker = new maplibregl.Marker({ element: destMarkerElement() }).setLngLat([dLng, dLat]).addTo(bookingMap);
                dMarker._isDestPin = true;
                window._paxRidePinMarkers.push(dMarker);
            }

            const wrapper = document.getElementById('passenger-status-wrapper');
            const status = wrapper ? (wrapper.dataset.status || '') : '';
            const isTrack = ['accepted', 'arrived', 'in_transit'].includes(status);

            // In active track mode, do NOT frame the pickup/dest pair away from the driver.
            // The camera centers directly on the driver's tricycle!
            if (isTrack) {
                return;
            }

            // Soft camera ease to frame the ride pins in wait mode
            setTimeout(() => {
                if (!bookingMap || !window._paxRidePinMarkers || !window._paxRidePinMarkers.length) return;
                try {
                    const P = window._paxRidePinMarkers[0];
                    const D = window._paxRidePinMarkers[1];
                    if (P && D) {
                        const a = P.getLngLat(), b = D.getLngLat();
                        // Strip-aware framing (same math as the searching zoom-out): both
                        // pins glide centered in the visible map above the sheet.
                        try { window.paxFramePinsInStrip(bookingMap, a.lat, a.lng, b.lat, b.lng, 900); } catch(e) {}
                    } else if (P) {
                        const pos = P.getLngLat();
                        bookingMap.easeTo({ center: [pos.lng, pos.lat], zoom: 16.5, duration: 700, easing: function(t) { return t * (2 - t); } });
                    }
                } catch(e) {}
            }, 250);
        }

        // ---- Live Driver Tricycle Marker on Passenger Tracking Map ----
        // ---- Live Driver Location Marker on Passenger Tracking Map ----
        // Shows the driver's real-time position as a precision blue dot + forward facing glow beam,
        // snaps coordinates to the road polyline, and locks heading to the pathway direction forward.
        let _paxDriverMarker = null;
        window._paxActiveRouteCoords = null;

        function paxTricycleMarkerElement() {
            const el = document.createElement('div');
            el.id = 'pax-driver-live-marker';
            el.style.cssText = 'width:70px;height:70px;position:relative;pointer-events:none;z-index:9999;';
            el.innerHTML = `
                <!-- Pulsing GPS Aura Ring (Centered at 35px, 35px) -->
                <div class="pax-driver-pulse" style="position:absolute;top:13px;left:13px;width:44px;height:44px;border-radius:50%;background:rgba(37,99,235,0.22);border:2px solid rgba(59,130,246,0.6);animation:ping 2s cubic-bezier(0,0,0.2,1) infinite;pointer-events:none;"></div>
                
                <!-- Directional Facing Glow Beam (Rotates 360° strictly around center 35px, 35px) -->
                <div id="pax-driver-tricycle-img" style="position:absolute;top:0;left:0;width:70px;height:70px;pointer-events:none;transform:rotate(0deg);transform-origin:35px 35px;will-change:transform;z-index:5;">
                    <svg viewBox="0 0 70 70" style="width:70px;height:70px;position:absolute;top:0;left:0;overflow:visible;pointer-events:none;">
                        <defs>
                            <radialGradient id="paxBeamGradient" cx="35" cy="35" r="35" fx="35" fy="35" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.8"/>
                                <stop offset="55%" stop-color="#60a5fa" stop-opacity="0.35"/>
                                <stop offset="100%" stop-color="#3b82f6" stop-opacity="0"/>
                            </radialGradient>
                        </defs>
                        <!-- 60° Directional Facing Glow Cone Beam emanating from (35,35) -->
                        <path d="M 35 35 L 14 3 A 38 38 0 0 1 56 3 Z" fill="url(#paxBeamGradient)"/>
                        <!-- Directional Pointer Arrow Notch -->
                        <polygon points="35,15 30,23 40,23" fill="#2563eb" opacity="0.95"/>
                    </svg>
                </div>

                <!-- Central High-Precision Blue Location Dot (Fixed at center 35px, 35px) -->
                <div style="position:absolute;top:26px;left:26px;width:18px;height:18px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#1d4ed8);border:3px solid #ffffff;box-shadow:0 3px 10px rgba(0,0,0,0.35), 0 0 10px rgba(37,99,235,0.6);pointer-events:none;z-index:10;"></div>
            `;
            return el;
        }

        let _paxLastRouteDrvLat = null;
        let _paxLastRouteDrvLng = null;
        let _paxLastRouteToLat = null;
        let _paxLastRouteToLng = null;

        let _paxRouteInFlight = false;
        let _paxLastRouteKey = null;
        let _paxRouteAbortCtrl = null;
        let _paxRouteRetryTimer = null;

        function paxSnapToRoadPolyline(lat, lng) {
            const coords = window._paxActiveRouteCoords;
            if (!coords || !Array.isArray(coords) || coords.length < 2) return null;

            let minSqDist = Infinity;
            let bestPoint = null;
            let bestBearing = 0;

            const x = lng;
            const y = lat;

            for (let i = 0; i < coords.length - 1; i++) {
                const x1 = coords[i][0];
                const y1 = coords[i][1];
                const x2 = coords[i + 1][0];
                const y2 = coords[i + 1][1];

                const dx = x2 - x1;
                const dy = y2 - y1;
                if (dx === 0 && dy === 0) continue;

                let t = ((x - x1) * dx + (y - y1) * dy) / (dx * dx + dy * dy);
                t = Math.max(0, Math.min(1, t));

                const projLng = x1 + t * dx;
                const projLat = y1 + t * dy;

                const sqDist = (x - projLng) * (x - projLng) + (y - projLat) * (y - projLat);
                if (sqDist < minSqDist) {
                    minSqDist = sqDist;
                    bestPoint = { lat: projLat, lng: projLng };
                    bestBearing = paxCalculateBearing(y1, x1, y2, x2);
                }
            }

            // Snap within ~600m radius of active route
            if (bestPoint && minSqDist < 0.00008) {
                return { lat: bestPoint.lat, lng: bestPoint.lng, bearing: bestBearing };
            }
            if (coords.length > 0) {
                const p0 = coords[0];
                const p1 = coords[1] || coords[0];
                return { lat: p0[1], lng: p0[0], bearing: paxCalculateBearing(p0[1], p0[0], p1[1], p1[0]) };
            }
            return null;
        }

        function paxDrawDriverRoute(drvLat, drvLng, toLat, toLng, color, force = false) {
            const wrapper = document.getElementById('passenger-status-wrapper');
            const curStatus = wrapper ? (wrapper.dataset.status || '') : '';
            if (!['accepted', 'arrived', 'in_transit'].includes(curStatus)) {
                paxClearDriverRoute();
                _paxLastRouteDrvLat = null;
                _paxLastRouteDrvLng = null;
                _paxLastRouteKey = null;
                return;
            }
            const map = bookingMap || window.bookingMapInstance || window.paxMapInstance;
            if (!map || !isRealCoordinate(drvLat, drvLng) || !isRealCoordinate(toLat, toLng)) return;

            const originLng = drvLng.toFixed(5);
            const originLat = drvLat.toFixed(5);
            const destLngStr = toLng.toFixed(5);
            const destLatStr = toLat.toFixed(5);
            const routeKey = `${originLng},${originLat}->${destLngStr},${destLatStr}-${color}`;

            if (_paxLastRouteKey === routeKey && !force) {
                return;
            }
            _paxLastRouteKey = routeKey;

            const doFetch = () => {
                _paxLastRouteDrvLat = drvLat;
                _paxLastRouteDrvLng = drvLng;
                _paxLastRouteToLat = toLat;
                _paxLastRouteToLng = toLng;

                if (_paxRouteAbortCtrl) {
                    try { _paxRouteAbortCtrl.abort(); } catch(e){}
                }
                _paxRouteAbortCtrl = new AbortController();

                const url = `https://router.project-osrm.org/route/v1/driving/${originLng},${originLat};${destLngStr},${destLatStr}?overview=full&geometries=geojson`;

                fetch(url, { signal: _paxRouteAbortCtrl.signal })
                    .then(r => r.json())
                    .then(d => {
                        const wrapper = document.getElementById('passenger-status-wrapper');
                        const curStatus = wrapper ? (wrapper.dataset.status || '') : '';
                        if (!['accepted', 'arrived', 'in_transit'].includes(curStatus)) {
                            paxClearDriverRoute();
                            return;
                        }
                        if (d && d.routes && d.routes[0] && d.routes[0].geometry) {
                            const coords = d.routes[0].geometry.coordinates; // [lng, lat]
                            window._paxActiveRouteCoords = coords;
                            if (Array.isArray(coords) && coords.length >= 2) {
                                const ahead = coords[Math.min(4, coords.length - 1)];
                                _paxRoadHeading = paxCalculateBearing(coords[0][1], coords[0][0], ahead[1], ahead[0]);
                            }
                            if (window.srhSetLineSource) {
                                window.srhSetLineSource(map, 'pax-driver-route', coords, [
                                    { id: 'pax-driver-route-glow', color: color, width: 5, opacity: 0.3, blur: 4 },
                                    { id: 'pax-driver-route-core', color: color, width: 3, opacity: 0.85 }
                                ]);
                            }
                        }
                    })
                    .catch(err => {
                        if (err && err.name === 'AbortError') return;
                    });
            };

            clearTimeout(_paxRouteRetryTimer);
            if (force) {
                doFetch();
            } else {
                _paxRouteRetryTimer = setTimeout(doFetch, 150);
            }
        }

        function paxClearDriverRoute() {
            _paxRouteInFlight = false;
            _paxRoadHeading = null;
            window._paxActiveRouteCoords = null;
            const map = bookingMap || window.bookingMapInstance || window.paxMapInstance;
            if (!map) return;
            try {
                ['pax-driver-route-core', 'pax-driver-route-glow'].forEach(id => {
                    try { if (map.getLayer && map.getLayer(id)) map.removeLayer(id); } catch(e) {}
                });
                try { if (map.getSource && map.getSource('pax-driver-route')) map.removeSource('pax-driver-route'); } catch(e) {}
            } catch(e) {}
            if (window.srhClearLineSource) {
                try { window.srhClearLineSource(map, 'pax-driver-route'); } catch(e) {}
            }
        }
        window.paxClearDriverRoute = paxClearDriverRoute;
        window.paxDrawDriverRoute = paxDrawDriverRoute;

        let _paxGlideRaf = null;
        let _paxDrvPrevLat = null;
        let _paxDrvPrevLng = null;
        let _paxMarkerHeading = 0;
        let _paxLastKnownHeading = null;

        window.paxClearDriverMarker = function() {
            if (_paxGlideRaf) { cancelAnimationFrame(_paxGlideRaf); _paxGlideRaf = null; }
            if (window._paxDriverMarker) {
                try { window._paxDriverMarker.remove(); } catch(e) {}
                window._paxDriverMarker = null;
            }
            if (_paxDriverMarker && _paxDriverMarker !== window._paxDriverMarker) {
                try { _paxDriverMarker.remove(); } catch(e) {}
            }
            _paxDriverMarker = null;
            _paxDrvPrevLat = null;
            _paxDrvPrevLng = null;
            _paxLastKnownHeading = null;
            window.srhTricycle = null;
        };

        function paxCalculateDistanceMeters(lat1, lng1, lat2, lng2) {
            const R = 6371000;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLng = (lng2 - lng1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLng / 2) * Math.sin(dLng / 2);
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        }

        function paxCalculateBearing(lat1, lng1, lat2, lng2) {
            const dLng = (lng2 - lng1) * Math.PI / 180;
            const y = Math.sin(dLng) * Math.cos(lat2 * Math.PI / 180);
            const x = Math.cos(lat1 * Math.PI / 180) * Math.sin(lat2 * Math.PI / 180) -
                      Math.sin(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.cos(dLng);
            const brng = Math.atan2(y, x) * 180 / Math.PI;
            return (brng + 360) % 360;
        }

        function paxRotateMarkerTo(headingDeg) {
            const img = document.querySelector('#pax-driver-tricycle-img');
            if (!img || typeof headingDeg !== 'number' || !isFinite(headingDeg)) return;

            // Normalize current and target angles into [0, 360)
            const cur = ((_paxMarkerHeading % 360) + 360) % 360;
            const tgt = ((headingDeg % 360) + 360) % 360;

            // Calculate the shortest angular difference (-180 to +180 degrees)
            let diff = (tgt - cur) % 360;
            if (diff > 180) diff -= 360;
            if (diff < -180) diff += 360;

            // Monotonically accumulate heading on continuous axis to prevent CSS 360-spin transitions
            if (Math.abs(diff) > 0.4) {
                _paxMarkerHeading += diff;
                img.style.transform = 'rotate(' + _paxMarkerHeading + 'deg)';
            }
        }

        // Update the driver's position marker on the passenger map.
        // Snaps coordinates to the road polyline and locks orientation along the road.
        window.updatePaxDriverMarker = function(drvLat, drvLng, rideStatus, pickupLat, pickupLng, destLat, destLng, drvHeading) {
            const map = bookingMap || window.bookingMapInstance || window.paxMapInstance;
            if (!map || !isRealCoordinate(drvLat, drvLng)) return;

            const wrapper = document.getElementById('passenger-status-wrapper');
            const curStatus = wrapper ? (wrapper.dataset.status || '') : '';
            if (!['accepted', 'arrived', 'in_transit'].includes(curStatus)) {
                if (window.paxClearDriverMarker) window.paxClearDriverMarker();
                return;
            }

            // Bind user-interaction grace: never steal the camera while the passenger
            // is panning/zooming the map (auto-follow resumes after 10s of no input).
            if (!window._paxCameraInteractBound && typeof map.on === 'function') {
                window._paxCameraInteractBound = true;
                map.on('dragstart', function() { window._paxCameraInteractUntil = Date.now() + 10000; });
                map.on('touchstart', function() { window._paxCameraInteractUntil = Date.now() + 10000; });
                map.on('wheel', function() { window._paxCameraInteractUntil = Date.now() + 10000; });
            }

            let endpointLat = null, endpointLng = null;
            if (rideStatus === 'accepted' && isRealCoordinate(pickupLat, pickupLng)) {
                endpointLat = pickupLat;
                endpointLng = pickupLng;
            } else if (isRealCoordinate(destLat, destLng)) {
                endpointLat = destLat;
                endpointLng = destLng;
            }

            // 🛣️ Road-Lock Snapping & Orientation for Passenger View
            let renderLat = drvLat;
            let renderLng = drvLng;
            let resolvedHeading = null;

            const snapped = paxSnapToRoadPolyline(drvLat, drvLng);
            if (snapped) {
                renderLat = snapped.lat;
                renderLng = snapped.lng;
                resolvedHeading = snapped.bearing;
            } else if (_paxDrvPrevLat !== null && _paxDrvPrevLng !== null) {
                const moveDist = paxCalculateDistanceMeters(_paxDrvPrevLat, _paxDrvPrevLng, drvLat, drvLng);
                if (moveDist >= 1.0) {
                    resolvedHeading = paxCalculateBearing(_paxDrvPrevLat, _paxDrvPrevLng, drvLat, drvLng);
                }
            }

            if (resolvedHeading === null && typeof drvHeading === 'number' && isFinite(drvHeading) && drvHeading > 0.5) {
                resolvedHeading = drvHeading;
            }

            if (resolvedHeading !== null) {
                _paxLastKnownHeading = resolvedHeading;
                paxRotateMarkerTo(resolvedHeading);
            } else if (_paxLastKnownHeading !== null) {
                paxRotateMarkerTo(_paxLastKnownHeading);
            } else if (endpointLat !== null && endpointLng !== null) {
                _paxLastKnownHeading = paxCalculateBearing(renderLat, renderLng, endpointLat, endpointLng);
                paxRotateMarkerTo(_paxLastKnownHeading);
            }
            _paxDrvPrevLat = renderLat;
            _paxDrvPrevLng = renderLng;

            if (_paxGlideRaf) cancelAnimationFrame(_paxGlideRaf);
            const glideTarget = { lat: renderLat, lng: renderLng };
            let m = window._paxDriverMarker || _paxDriverMarker;
            // Discard stale markers detached from the live map
            if (m && !(m.getElement && m.getElement() && m.getElement().isConnected)) {
                try { m.remove(); } catch(e) {}
                window._paxDriverMarker = null;
                _paxDriverMarker = null;
                m = null;
            }

            if (!m) {
                if (window._paxDriverMarker) {
                    try { window._paxDriverMarker.remove(); } catch(e) {}
                }
                _paxDriverMarker = new maplibregl.Marker({ element: paxTricycleMarkerElement(), anchor: 'center' })
                    .setLngLat([renderLng, renderLat])
                    .addTo(map);
                window._paxDriverMarker = _paxDriverMarker;
                if (typeof paxRotateMarkerTo === 'function') paxRotateMarkerTo(_paxMarkerHeading);
            } else {
                _paxDriverMarker = m;
                window._paxDriverMarker = m;
                const startPos = _paxDriverMarker.getLngLat();
                const startLat = startPos.lat;
                const startLng = startPos.lng;
                const moveDistMeters = paxCalculateDistanceMeters(startLat, startLng, renderLat, renderLng);
                if (moveDistMeters < 0.2) {
                    _paxDriverMarker.setLngLat([renderLng, renderLat]);
                    _paxGlideRaf = null;
                } else {
                    const startTime = performance.now();
                    const duration = Math.min(800, Math.max(350, moveDistMeters * 45)); // smooth micro-interpolation
                    function paxGlideFrame(now) {
                        const progress = Math.min(1, (now - startTime) / duration);
                        const ease = 1 - Math.pow(1 - progress, 3);
                        const curLat = startLat + (glideTarget.lat - startLat) * ease;
                        const curLng = startLng + (glideTarget.lng - startLng) * ease;
                        if (_paxDriverMarker) {
                            _paxDriverMarker.setLngLat([curLng, curLat]);
                        }
                        if (progress < 1) {
                            _paxGlideRaf = requestAnimationFrame(paxGlideFrame);
                        } else {
                            _paxGlideRaf = null;
                        }
                    }
                    _paxGlideRaf = requestAnimationFrame(paxGlideFrame);
                }
            }

            // Auto-follow driver: Smooth precision panning to driver's location
            const nowTime = Date.now();
            const isUserPanning = window._paxCameraInteractUntil && nowTime < window._paxCameraInteractUntil;
            if (!isUserPanning && !window._isMapCameraAnimating) {
                try {
                    const center = map.getCenter();
                    const distFromCenter = paxCalculateDistanceMeters(center.lat, center.lng, drvLat, drvLng);
                    const curZoom = map.getZoom ? map.getZoom() : 17.5;
                    const followZoom = curZoom < 16.5 ? 17.5 : curZoom;

                    // Initial lock on driver (even when not moving yet) or when drift exceeds 20m
                    if (!window._paxHasCenteredOnDriver || distFromCenter > 20) {
                        window._paxHasCenteredOnDriver = true;
                        window._isMapCameraAnimating = true;
                        if (window.syncPaxMapPadding) window.syncPaxMapPadding();
                        map.easeTo({
                            center: [drvLng, drvLat],
                            zoom: followZoom,
                            duration: 750,
                            easing: function(t) { return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2; }
                        });
                        setTimeout(() => { window._isMapCameraAnimating = false; }, 800);
                    } else if (distFromCenter > 2) {
                        // Smoothly ease camera to follow driver with 1-2m precision
                        map.easeTo({
                            center: [drvLng, drvLat],
                            zoom: followZoom,
                            duration: 650,
                            easing: function(t) { return t * (2 - t); }
                        });
                    }
                } catch(e) {}
            }

            // Dynamic ETA and HUD update
            const hud = typeof getPaxHudEls === 'function' ? getPaxHudEls() : null;
            const etaBadge = document.getElementById('pax-hud-eta') || (hud ? hud.badge : null);
            if (hud && hud.title) {
                if (rideStatus === 'accepted' && hud.title.textContent !== 'Driver on the way') {
                    hud.title.textContent = 'Driver on the way';
                } else if (rideStatus === 'arrived' && hud.title.textContent !== 'Driver has arrived!') {
                    hud.title.textContent = 'Driver has arrived!';
                } else if (rideStatus === 'in_transit' && hud.title.textContent !== 'In transit to destination') {
                    hud.title.textContent = 'In transit to destination';
                }
            }
            if (etaBadge) {
                if (rideStatus === 'arrived') {
                    etaBadge.textContent = 'ARRIVED';
                    etaBadge.className = 'px-2.5 py-1 rounded-full text-xs font-black shrink-0 bg-emerald-950/80 text-emerald-300 border border-emerald-500/50 shadow-sm';
                } else if (endpointLat !== null && endpointLng !== null) {
                    const distMeters = paxCalculateDistanceMeters(drvLat, drvLng, endpointLat, endpointLng);
                    const mins = Math.max(1, Math.ceil(distMeters / 300));
                    const distRounded = Math.round(distMeters);
                    if (distRounded < 40 && rideStatus === 'accepted') {
                        etaBadge.textContent = 'Arriving now';
                    } else if (distRounded < 1000) {
                        etaBadge.textContent = mins === 1 ? '1 min (' + distRounded + 'm)' : mins + ' mins (' + distRounded + 'm)';
                    } else {
                        const km = (distMeters / 1000).toFixed(1);
                        etaBadge.textContent = mins + ' mins (' + km + 'km)';
                    }
                    etaBadge.className = 'px-2.5 py-1 rounded-full text-xs font-black shrink-0 bg-emerald-950/80 text-emerald-300 border border-emerald-500/50 shadow-sm';
                } else if (rideStatus === 'in_transit') {
                    etaBadge.textContent = 'In Transit';
                    etaBadge.className = 'px-2.5 py-1 rounded-full text-xs font-black shrink-0 bg-emerald-950/80 text-emerald-300 border border-emerald-500/50 shadow-sm';
                } else if (rideStatus === 'accepted') {
                    etaBadge.textContent = 'On the way';
                    etaBadge.className = 'px-2.5 py-1 rounded-full text-xs font-black shrink-0 bg-blue-950/80 text-blue-300 border border-blue-500/50 shadow-sm';
                }
            }

            // In transit to drop-off: pickup pin fades out smoothly (one-shot per ride)
            // so only the destination pin remains - no pop, no removal jolt.
            if (rideStatus === 'in_transit') {
                if (!window._paxPickupPinFaded) {
                    window._paxPickupPinFaded = true;
                    const fadePinOut = function(marker) {
                        if (!marker) return;
                        try {
                            const el = typeof marker.getElement === 'function' ? marker.getElement() : null;
                            if (el) {
                                el.style.transition = 'opacity 0.4s ease';
                                el.style.opacity = '0';
                            }
                        } catch(e) {}
                        setTimeout(function() { try { marker.remove(); } catch(e) {} }, 420);
                    };
                    if (window._paxRidePinMarkers && Array.isArray(window._paxRidePinMarkers)) {
                        window._paxRidePinMarkers = window._paxRidePinMarkers.filter(marker => {
                            if (marker._isPickupPin) { fadePinOut(marker); return false; }
                            return true;
                        });
                    }
                    if (pickupMarker) { fadePinOut(pickupMarker); pickupMarker = null; }
                }
            }

            // Draw route based on ride status (arrived = waiting at pickup, no pathway yet)
            if (rideStatus === 'accepted' && isRealCoordinate(pickupLat, pickupLng)) {
                paxDrawDriverRoute(drvLat, drvLng, pickupLat, pickupLng, '#2563eb'); // blue â†’ pickup
            } else if (rideStatus === 'in_transit' && isRealCoordinate(destLat, destLng)) {
                paxDrawDriverRoute(drvLat, drvLng, destLat, destLng, '#10b981'); // green â†’ destination
            } else {
                paxClearDriverRoute();
            }
        };

        // Global Passenger Re-center Helper
        window.recenterPassengerMap = function() {
            const map = bookingMap || window.paxHomeMapInstance || window.bookingMapInstance || window.paxMapInstance;
            if (!map) return;
            window._paxCameraInteractUntil = 0;

            // Driver-Hub parity: spin the target icon while fetching fresh driver location
            const btn = document.getElementById('pax-active-recenter-btn');
            const svg = btn ? btn.querySelector('svg') : null;
            if (window._paxRecenterFetching) return;
            window._paxRecenterFetching = true;
            if (svg) {
                svg.classList.add('animate-spin', 'text-blue-600');
                svg.classList.remove('text-slate-700');
            }
            const stopSpin = function() {
                window._paxRecenterFetching = false;
                if (svg) {
                    svg.classList.remove('animate-spin', 'text-blue-600');
                    svg.classList.add('text-slate-700');
                }
            };

            const glideTo = function(lat, lng) {
                if (!isRealCoordinate(lat, lng)) return;
                if (window.syncPaxMapPadding) window.syncPaxMapPadding();
                window._isMapCameraAnimating = true;
                map.easeTo({
                    center: [lng, lat],
                    zoom: 17.5,
                    duration: 650,
                    easing: function(t) { return t * (2 - t); }
                });
                setTimeout(() => { window._isMapCameraAnimating = false; }, 700);
            };

            const resolveLocalAndGlide = function() {
                let dLat = null, dLng = null;
                if (_paxDriverMarker && typeof _paxDriverMarker.getLngLat === 'function') {
                    try {
                        const p = _paxDriverMarker.getLngLat();
                        dLat = p.lat;
                        dLng = p.lng;
                    } catch(e) {}
                } else if (window.srhTricycle && isRealCoordinate(window.srhTricycle.lat, window.srhTricycle.lng)) {
                    dLat = window.srhTricycle.lat;
                    dLng = window.srhTricycle.lng;
                } else {
                    const mapEl = document.getElementById('pax-home-map');
                    if (mapEl && isRealCoordinate(parseFloat(mapEl.dataset.driverLat), parseFloat(mapEl.dataset.driverLng))) {
                        dLat = parseFloat(mapEl.dataset.driverLat);
                        dLng = parseFloat(mapEl.dataset.driverLng);
                    } else if (mapEl && isRealCoordinate(parseFloat(mapEl.dataset.pickupLat), parseFloat(mapEl.dataset.pickupLng))) {
                        dLat = parseFloat(mapEl.dataset.pickupLat);
                        dLng = parseFloat(mapEl.dataset.pickupLng);
                    }
                }
                glideTo(dLat, dLng);
            };

            // Fetch the freshest driver snapshot, then recenter (falls back locally on failure)
            fetch('/passenger/ride-status', { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                .then(r => (r.ok ? r.json() : null))
                .then(data => {
                    let usedFresh = false;
                    if (data && data.ride && data.status && ['accepted', 'arrived', 'in_transit'].includes(data.status)) {
                        const fLat = parseFloat(data.ride.driver_lat), fLng = parseFloat(data.ride.driver_lng);
                        if (isRealCoordinate(fLat, fLng)) {
                            usedFresh = true;
                            if (typeof window.updatePaxDriverMarker === 'function') {
                                window.updatePaxDriverMarker(fLat, fLng, data.status,
                                    parseFloat(data.ride.pickup_lat), parseFloat(data.ride.pickup_lng),
                                    parseFloat(data.ride.destination_lat), parseFloat(data.ride.destination_lng),
                                    (data.ride.driver_heading !== undefined && data.ride.driver_heading !== null) ? parseFloat(data.ride.driver_heading) : null);
                            } else {
                                glideTo(fLat, fLng);
                            }
                        }
                    }
                    if (!usedFresh) resolveLocalAndGlide();
                })
                .catch(() => { resolveLocalAndGlide(); })
                .finally(stopSpin);
        };
        window.recenterPassengerLocation = window.recenterPassengerMap;

        // Form interaction helper: camera stays calm and stationary
        window.paxRecenterOnFormInteraction = function() {
            // Disabled to ensure map view never shifts or jumps while touching the form
            return;
        };

        window.paxClearDriverMarker = function() {
            if (_paxGlideRaf) { cancelAnimationFrame(_paxGlideRaf); _paxGlideRaf = null; }
            if (window._paxDriverMarker && window._paxDriverMarker !== _paxDriverMarker) {
                try { window._paxDriverMarker.remove(); } catch(e) {}
            }
            window._paxDriverMarker = null;
            if (_paxDriverMarker) {
                try { _paxDriverMarker.remove(); } catch(e) {}
                _paxDriverMarker = null;
            }
            if (typeof destMarker !== 'undefined' && destMarker) {
                try { destMarker.remove(); } catch(e) {}
                destMarker = null;
            }
            if (window.destMarker) {
                try { window.destMarker.remove(); } catch(e) {}
                window.destMarker = null;
            }
            document.querySelectorAll('.pax-driver-pulse, #pax-driver-tricycle-img, .pax-dest-pin-marker').forEach(el => {
                const m = el.closest('.maplibregl-marker') || el;
                if (m) try { m.remove(); } catch(e) {}
            });
            paxClearDriverRoute();
        };

        window.initPaxHomeMapGlobal = initPaxHomeMap;
        window.initBookingMapGlobal = initPaxHomeMap;
        initPaxHomeMap();

        // On initial track-view page load (server-rendered with accepted/arrived/in_transit),
        // place the tricycle marker immediately using the DOM data attrs on the status wrapper.
        (function() {
            const wrapper = document.getElementById('passenger-status-wrapper');
            if (!wrapper) return;
            const st = wrapper.dataset.status || '';
            if (!['accepted', 'arrived', 'in_transit'].includes(st)) return;
            // Read ride coordinates from Blade-rendered data attrs on the wrapper
            const _drvLat = parseFloat(wrapper.dataset.driverLat);
            const _drvLng = parseFloat(wrapper.dataset.driverLng);
            const _pLat   = parseFloat(wrapper.dataset.pickupLat);
            const _pLng   = parseFloat(wrapper.dataset.pickupLng);
            const _dLat   = parseFloat(wrapper.dataset.destLat);
            const _dLng   = parseFloat(wrapper.dataset.destLng);
            if (!isRealCoordinate(_drvLat, _drvLng)) return;
            const applyInitialTrack = () => {
                if (typeof window.handlePaxRideStatusChange === 'function') {
                    window.handlePaxRideStatusChange(st, {
                        pickup_lat: _pLat, pickup_lng: _pLng,
                        dest_lat: _dLat, dest_lng: _dLng,
                        driver_lat: _drvLat, driver_lng: _drvLng
                    });
                } else if (typeof window.updatePaxDriverMarker === 'function') {
                    window.updatePaxDriverMarker(_drvLat, _drvLng, st, _pLat, _pLng, _dLat, _dLng);
                }
            };
            if (bookingMap && bookingMap.loaded()) {
                applyInitialTrack();
            } else if (bookingMap) {
                bookingMap.once('idle', applyInitialTrack);
            }
        })();

        // Global Reverb WebSocket Listener for live driver location updates
        function initPassengerGpsEcho() {
            if (window._paxGpsEchoBound) return;
            if (typeof window.Echo !== 'undefined' && window.Echo) {
                try {
                    window._paxGpsEchoBound = true;
                    window.Echo.channel('srh-toda-gps').listen('.tricycle.location', function(e) {
                        if (!e || typeof e.lat === 'undefined' || typeof e.lng === 'undefined') return;
                        const wrapper = document.getElementById('passenger-status-wrapper');
                        const status = wrapper ? (wrapper.dataset.status || '') : '';
                        if (!['accepted', 'arrived', 'in_transit'].includes(status)) return;

                        const myDriverId = parseInt(wrapper.dataset.driverId || 0);
                        const myRideId = parseInt(wrapper.dataset.rideId || 0);
                        const eventDriverId = parseInt(e.driverId || 0);
                        const eventRideId = parseInt(e.rideId || 0);

                        if (eventRideId && myRideId && eventRideId !== myRideId) return;
                        if (eventDriverId && myDriverId && eventDriverId !== myDriverId && !myRideId) return;

                        const pLat = parseFloat(wrapper.dataset.pickupLat || window._paxWaitPickupLat);
                        const pLng = parseFloat(wrapper.dataset.pickupLng || window._paxWaitPickupLng);
                        const dLat = parseFloat(wrapper.dataset.destLat || window._paxWaitDestLat);
                        const dLng = parseFloat(wrapper.dataset.destLng || window._paxWaitDestLng);
                        const heading = (e.heading !== undefined && e.heading !== null) ? parseFloat(e.heading) : null;

                        if (typeof window.updatePaxDriverMarker === 'function') {
                            window.updatePaxDriverMarker(parseFloat(e.lat), parseFloat(e.lng), status, pLat, pLng, dLat, dLng, heading);
                        }
                    });
                } catch(err) {
                    window._paxGpsEchoBound = false;
                }
            } else {
                setTimeout(initPassengerGpsEcho, 300);
            }
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPassengerGpsEcho);
        } else {
            initPassengerGpsEcho();
        }
        window.addEventListener('spa:page-loaded', () => {
            window._paxGpsEchoBound = false;
            initPassengerGpsEcho();
        });
        // Vault restore: the page was parked while away, so ride events that fired
        // in the meantime never reached the detached wrapper. Resync once now.
        window.addEventListener('spa:page-restored', (e) => {
            const p = (e && e.detail && e.detail.path) || '';
            if (p !== '/dashboard') return;
            if (typeof fetchPassengerStatus === 'function') {
                try { fetchPassengerStatus(); } catch(err) {}
            }
        });

        // Fire the driver-style map action animation the moment the passenger taps
        // "Find Available Driver" (runs alongside the shared submit helper).
        const bookingForm = document.getElementById('pax-booking-sheet') && document.querySelector('#pax-booking-sheet form, #pax-sheet-details-content form');
        if (bookingForm && !bookingForm.dataset.paxAnimBound) {
            bookingForm.dataset.paxAnimBound = '1';
            bookingForm.addEventListener('submit', function() {
                if (window.animatePaxMapForAction) window.animatePaxMapForAction('book');
                if (window.animatePaxFloatingButtonsSync) window.animatePaxFloatingButtonsSync(380);
            });
        }
    })();

    // --- REAL PLACES SEARCH AUTOCOMPLETE ENGINE ---
    (function() {
        function initPlacesAutocomplete() {
            const destInput = document.getElementById('passenger-dest-text-input');
            const dropdown = document.getElementById('dest-autocomplete-dropdown');
            let debounceTimer = null;

            if (!destInput || !dropdown) return;

            function escapeHtml(str) {
                return String(str||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
            }

            destInput.addEventListener('input', function() {
                const query = this.value.trim();
                clearTimeout(debounceTimer);
                if (query.length < 2) {
                    hideDropdownAndClear();
                    dropdown.innerHTML = '';
                    return;
                }

                debounceTimer = setTimeout(() => {
                    fetchPlacesAutocomplete(query);
                }, 220);
            });

            function isNuevaEcija(lat, lng, subtitle) {
                // Geographic Bounding Box for Nueva Ecija Province: Lat [15.10, 16.15], Lng [120.70, 121.45]
                const inBox = (lat >= 15.10 && lat <= 16.15 && lng >= 120.70 && lng <= 121.45);
                if (!inBox) return false;

                const s = (subtitle || '').toLowerCase();
                if (s.includes('tarlac') || s.includes('pampanga') || s.includes('bulacan') || s.includes('bataan') || s.includes('zambales') || s.includes('pangasinan')) {
                    if (!s.includes('nueva ecija') && !s.includes('santa rosa') && !s.includes('cabanatuan')) {
                        return false;
                    }
                }
                return true;
            }

            function cleanSubtitle(name, p, fallbackFull) {
                const mainName = (name || '').toLowerCase().trim();
                const blacklistedTags = ['isla', 'unassigned', 'poblacion norte', 'poblacion sur'];
                const santaRosaBrgys = ['la fuente', 'zamora', 'lourdes', 'san jose', 'maliwalo', 'rizal', 'mapalad', 'soledad', 'aguinaldo', 'rajal centro', 'rajal norte', 'rajal sur', 'santo rosario', 'inspector', 'del pilar', 'tomeco', 'poblacion', 'valenzuela', 'san mariano', 'san gregorio'];
                const cabanatuanBrgys = ['sumacab', 'sumacab este', 'sumacab sur', 'sumacab norte', 'bangad', 'bitas', 'sangitan', 'mabini', 'san roque', 'zulueta', 'barrera', 'hermogenes', 'camps'];

                window.srhCleanSubtitle = cleanSubtitle;

                function formatCityName(rawCity) {
                    if (!rawCity) return 'Santa Rosa';
                    const c = rawCity.trim();
                    if (c.toLowerCase().includes('cabanatuan')) return 'Cabanatuan City';
                    if (c.toLowerCase().includes('santa rosa') || c.toLowerCase().includes('sta. rosa') || c.toLowerCase().includes('sta rosa')) return 'Santa Rosa';
                    if (c.toLowerCase().includes('san leonardo')) return 'San Leonardo';
                    if (c.toLowerCase().includes('jaen')) return 'Jaen';
                    if (c.toLowerCase().includes('palayan')) return 'Palayan City';
                    if (c.toLowerCase().includes('gapan')) return 'Gapan City';
                    if (c.toLowerCase().includes('talavera')) return 'Talavera';
                    if (c.toLowerCase().includes('zaragoza')) return 'Zaragoza';
                    return c;
                }

                if (p) {
                    const rawCity = p.city || p.town || p.municipality || p.county || 'Santa Rosa';
                    const city = formatCityName(rawCity);
                    const state = p.state || 'Nueva Ecija';
                    let parts = [];

                    if (p.street && p.street.toLowerCase() !== mainName && !blacklistedTags.includes(p.street.toLowerCase().trim())) {
                        parts.push(p.street);
                    }

                    const dist = (p.district || p.suburb || p.quarter || p.neighbourhood || '').trim();
                    if (dist && dist.toLowerCase() !== mainName) {
                        const distLower = dist.toLowerCase();
                        const isBlacklisted = blacklistedTags.some(b => distLower === b || distLower.startsWith('zone') || distLower.startsWith('district'));
                        
                        let isCrossTownBleed = false;
                        if (city === 'Cabanatuan City' && santaRosaBrgys.some(b => distLower.includes(b))) {
                            isCrossTownBleed = true;
                        }
                        if (city === 'Santa Rosa' && cabanatuanBrgys.some(b => distLower.includes(b))) {
                            isCrossTownBleed = true;
                        }

                        if (!isBlacklisted && !isCrossTownBleed && !mainName.includes(distLower) && !distLower.includes(mainName)) {
                            parts.push(dist);
                        }
                    }

                    if (city && !parts.some(pt => pt.toLowerCase().includes(city.toLowerCase()))) parts.push(city);
                    if (state && !parts.some(pt => pt.toLowerCase().includes(state.toLowerCase()))) parts.push(state);
                    return parts.filter(Boolean).join(', ');
                }

                if (fallbackFull) {
                    const rawParts = fallbackFull.split(',').map(s=>s.trim()).filter(Boolean);
                    if (rawParts.length <= 1) return 'Santa Rosa, Nueva Ecija';

                    let detectedCity = 'Santa Rosa';
                    if (rawParts.some(pt => pt.toLowerCase().includes('cabanatuan'))) detectedCity = 'Cabanatuan City';
                    else if (rawParts.some(pt => pt.toLowerCase().includes('santa rosa') || pt.toLowerCase().includes('sta rosa'))) detectedCity = 'Santa Rosa';

                    const restParts = rawParts.slice(1).filter(pt => {
                        const ptLower = pt.toLowerCase();
                        if (ptLower === mainName || mainName.includes(ptLower) || ptLower.includes(mainName)) return false;
                        if (blacklistedTags.some(b => ptLower === b || ptLower.startsWith('zone') || ptLower.startsWith('district'))) return false;
                        if (detectedCity === 'Cabanatuan City' && santaRosaBrgys.some(b => ptLower.includes(b))) return false;
                        if (detectedCity === 'Santa Rosa' && cabanatuanBrgys.some(b => ptLower.includes(b))) return false;
                        if (/^\d{4}$/.test(pt)) return false;
                        if (ptLower === 'philippines' || ptLower === 'ph') return false;
                        return true;
                    });

                    const cleanedParts = restParts.map(pt => {
                        if (pt.toLowerCase().includes('cabanatuan')) return 'Cabanatuan City';
                        if (pt.toLowerCase().includes('sta. rosa') || pt.toLowerCase().includes('sta rosa')) return 'Santa Rosa';
                        return pt;
                    });

                    const uniqueParts = [];
                    cleanedParts.forEach(pt => {
                        if (!uniqueParts.some(existing => existing.toLowerCase() === pt.toLowerCase())) {
                            uniqueParts.push(pt);
                        }
                    });

                    if (!uniqueParts.some(pt => pt.toLowerCase().includes(detectedCity.toLowerCase()))) {
                        uniqueParts.unshift(detectedCity);
                    }

                    return uniqueParts.slice(0, 3).join(', ');
                }

                return 'Santa Rosa, Nueva Ecija';
            }

            const NUEVA_ECIJA_LOCAL_PLACES = [
                // Santa Rosa Homes Subdivision & TODA
                { name: 'Santa Rosa Homes Phase 1', subtitle: 'Santa Rosa Homes, Santa Rosa, Nueva Ecija', lat: 15.42955, lng: 120.92240, type: 'residential', class: 'residential', keywords: ['phase 1', 'srh', 'santa rosa homes', 'subdivision', 'block', 'lot', 'main'] },
                { name: 'Santa Rosa Homes Phase 2', subtitle: 'Santa Rosa Homes, Santa Rosa, Nueva Ecija', lat: 15.43120, lng: 120.92310, type: 'residential', class: 'residential', keywords: ['phase 2', 'srh', 'santa rosa homes', 'subdivision'] },
                { name: 'Santa Rosa Homes Phase 3', subtitle: 'Santa Rosa Homes, Santa Rosa, Nueva Ecija', lat: 15.43250, lng: 120.92400, type: 'residential', class: 'residential', keywords: ['phase 3', 'srh', 'santa rosa homes', 'subdivision'] },
                { name: 'Santa Rosa Homes Clubhouse & Court', subtitle: 'Phase 1, Santa Rosa Homes, Santa Rosa, Nueva Ecija', lat: 15.42980, lng: 120.92280, type: 'stadium', class: 'sports_centre', keywords: ['clubhouse', 'court', 'basketball', 'pool', 'srh'] },
                { name: 'Santa Rosa Homes Main Gate / Guardhouse', subtitle: 'Maharlika Highway, Santa Rosa, Nueva Ecija', lat: 15.42850, lng: 120.92150, type: 'town-hall', class: 'barrier', keywords: ['gate', 'guardhouse', 'entrance', 'srh'] },
                { name: 'SRH TODA Tricycle Terminal', subtitle: 'Main Gate, Santa Rosa Homes, Nueva Ecija', lat: 15.42800, lng: 120.92120, type: 'bus', class: 'bus_station', keywords: ['toda', 'terminal', 'tricycle', 'station', 'srh'] },

                // Santa Rosa Town Proper & Commercial
                { name: 'Santa Rosa Public Market (Palengke)', subtitle: 'Poblacion, Santa Rosa, Nueva Ecija', lat: 15.42580, lng: 120.93820, type: 'grocery', class: 'supermarket', keywords: ['market', 'palengke', 'public market', 'pamilihan'] },
                { name: 'Santa Rosa Municipal Hall (Munisipyo)', subtitle: 'Poblacion, Santa Rosa, Nueva Ecija', lat: 15.42450, lng: 120.93700, type: 'town-hall', class: 'town_hall', keywords: ['municipal hall', 'munisipyo', 'town hall', 'city hall', 'plaza'] },
                { name: 'Santa Rosa Police Station (PNP)', subtitle: 'Poblacion, Santa Rosa, Nueva Ecija', lat: 15.42480, lng: 120.93720, type: 'police', class: 'police', keywords: ['police', 'pnp', 'station', 'pulis'] },
                { name: 'Holy Cross Parish Church', subtitle: 'Poblacion, Santa Rosa, Nueva Ecija', lat: 15.42520, lng: 120.93880, type: 'place-of-worship', class: 'church', keywords: ['church', 'holy cross', 'simbahan', 'parish'] },
                { name: 'Puregold Santa Rosa', subtitle: 'Maharlika Highway, Santa Rosa, Nueva Ecija', lat: 15.42650, lng: 120.93950, type: 'grocery', class: 'supermarket', keywords: ['puregold', 'grocery', 'supermarket', 'mall'] },
                { name: '7-Eleven Santa Rosa Town Proper', subtitle: 'Poblacion, Santa Rosa, Nueva Ecija', lat: 15.42550, lng: 120.93800, type: 'grocery', class: 'convenience', keywords: ['7-eleven', '711', 'seven eleven', 'store'] },
                { name: '7-Eleven Santa Rosa Maharlika', subtitle: 'Maharlika Highway, Santa Rosa, Nueva Ecija', lat: 15.43020, lng: 120.94100, type: 'grocery', class: 'convenience', keywords: ['7-eleven', '711', 'seven eleven', 'maharlika'] },
                { name: 'AlfaMart Santa Rosa', subtitle: 'Maharlika Highway, Santa Rosa, Nueva Ecija', lat: 15.42700, lng: 120.93900, type: 'grocery', class: 'convenience', keywords: ['alfamart', 'grocery', 'mart'] },
                { name: 'Santa Rosa National High School (SRNHS)', subtitle: 'Santa Rosa, Nueva Ecija', lat: 15.42300, lng: 120.93500, type: 'school', class: 'school', keywords: ['srnhs', 'national high school', 'high school', 'eskuwela'] },
                { name: 'Santa Rosa Central School', subtitle: 'Poblacion, Santa Rosa, Nueva Ecija', lat: 15.42600, lng: 120.93750, type: 'school', class: 'school', keywords: ['central school', 'elementary', 'school'] },
                { name: 'Petron Santa Rosa Maharlika', subtitle: 'Maharlika Highway, Santa Rosa, Nueva Ecija', lat: 15.42850, lng: 120.94050, type: 'fuel', class: 'fuel', keywords: ['petron', 'gas', 'gasoline', 'gas station'] },
                { name: 'Shell Santa Rosa', subtitle: 'Maharlika Highway, Santa Rosa, Nueva Ecija', lat: 15.42900, lng: 120.94120, type: 'fuel', class: 'fuel', keywords: ['shell', 'gas', 'gas station'] },
                { name: 'Caltex Santa Rosa', subtitle: 'Santa Rosa, Nueva Ecija', lat: 15.42400, lng: 120.93650, type: 'fuel', class: 'fuel', keywords: ['caltex', 'gas', 'gas station'] },
                { name: 'Santa Rosa Rural Health Unit (RHU)', subtitle: 'Poblacion, Santa Rosa, Nueva Ecija', lat: 15.42490, lng: 120.93680, type: 'hospital', class: 'clinic', keywords: ['rhu', 'health center', 'clinic', 'center'] },

                // Santa Rosa Barangays
                { name: 'Barangay La Fuente Hall', subtitle: 'Brgy. La Fuente, Santa Rosa, Nueva Ecija', lat: 15.43500, lng: 120.94500, type: 'town-hall', class: 'government', keywords: ['la fuente', 'brgy la fuente', 'barangay'] },
                { name: 'Barangay Soledad Hall', subtitle: 'Brgy. Soledad, Santa Rosa, Nueva Ecija', lat: 15.41800, lng: 120.93200, type: 'town-hall', class: 'government', keywords: ['soledad', 'brgy soledad', 'barangay'] },
                { name: 'Barangay Maliwalo Hall', subtitle: 'Brgy. Maliwalo, Santa Rosa, Nueva Ecija', lat: 15.41200, lng: 120.92500, type: 'town-hall', class: 'government', keywords: ['maliwalo', 'brgy maliwalo', 'barangay'] },
                { name: 'Barangay San Jose Hall', subtitle: 'Brgy. San Jose, Santa Rosa, Nueva Ecija', lat: 15.44100, lng: 120.95200, type: 'town-hall', class: 'government', keywords: ['san jose', 'brgy san jose', 'barangay'] },
                { name: 'Barangay Rizal Hall', subtitle: 'Brgy. Rizal, Santa Rosa, Nueva Ecija', lat: 15.44800, lng: 120.95800, type: 'town-hall', class: 'government', keywords: ['rizal', 'brgy rizal', 'barangay'] },
                { name: 'Barangay Lourdes Hall', subtitle: 'Brgy. Lourdes, Santa Rosa, Nueva Ecija', lat: 15.43900, lng: 120.93100, type: 'town-hall', class: 'government', keywords: ['lourdes', 'brgy lourdes', 'barangay'] },
                { name: 'Barangay Rajal Centro', subtitle: 'Brgy. Rajal Centro, Santa Rosa, Nueva Ecija', lat: 15.45200, lng: 120.96500, type: 'town-hall', class: 'government', keywords: ['rajal centro', 'rajal', 'barangay'] },
                { name: 'Barangay Rajal Norte', subtitle: 'Brgy. Rajal Norte, Santa Rosa, Nueva Ecija', lat: 15.45800, lng: 120.96900, type: 'town-hall', class: 'government', keywords: ['rajal norte', 'rajal', 'barangay'] },
                { name: 'Barangay Rajal Sur', subtitle: 'Brgy. Rajal Sur, Santa Rosa, Nueva Ecija', lat: 15.44700, lng: 120.96200, type: 'town-hall', class: 'government', keywords: ['rajal sur', 'rajal', 'barangay'] },
                { name: 'Barangay Mapalad Hall', subtitle: 'Brgy. Mapalad, Santa Rosa, Nueva Ecija', lat: 15.43100, lng: 120.91500, type: 'town-hall', class: 'government', keywords: ['mapalad', 'brgy mapalad', 'barangay'] },
                { name: 'Barangay Aguinaldo Hall', subtitle: 'Brgy. Aguinaldo, Santa Rosa, Nueva Ecija', lat: 15.42100, lng: 120.92800, type: 'town-hall', class: 'government', keywords: ['aguinaldo', 'brgy aguinaldo', 'barangay'] },
                { name: 'Barangay Zamora Hall', subtitle: 'Brgy. Zamora, Santa Rosa, Nueva Ecija', lat: 15.41500, lng: 120.94000, type: 'town-hall', class: 'government', keywords: ['zamora', 'brgy zamora', 'barangay'] },
                { name: 'Barangay Santo Rosario', subtitle: 'Brgy. Santo Rosario, Santa Rosa, Nueva Ecija', lat: 15.40800, lng: 120.93500, type: 'town-hall', class: 'government', keywords: ['santo rosario', 'sta rosario', 'barangay'] },
                { name: 'Barangay Valenzuela', subtitle: 'Brgy. Valenzuela, Santa Rosa, Nueva Ecija', lat: 15.41900, lng: 120.94800, type: 'town-hall', class: 'government', keywords: ['valenzuela', 'brgy valenzuela', 'barangay'] },
                { name: 'Barangay San Gregorio', subtitle: 'Brgy. San Gregorio, Santa Rosa, Nueva Ecija', lat: 15.42800, lng: 120.95400, type: 'town-hall', class: 'government', keywords: ['san gregorio', 'brgy san gregorio', 'barangay'] },

                // Cabanatuan City (Malls, Universities, Hospitals)
                { name: 'SM City Cabanatuan', subtitle: 'Maharlika Highway, Cabanatuan City, Nueva Ecija', lat: 15.46780, lng: 120.95350, type: 'shop', class: 'mall', keywords: ['sm', 'sm city', 'sm cabanatuan', 'mall', 'shopping'] },
                { name: 'Robinsons Townville Cabanatuan', subtitle: 'Maharlika Highway, Cabanatuan City, Nueva Ecija', lat: 15.47400, lng: 120.95700, type: 'shop', class: 'mall', keywords: ['robinsons', 'townville', 'mall', 'robinsons cabanatuan'] },
                { name: 'SM Megacenter Cabanatuan', subtitle: 'General Tinio St, Cabanatuan City, Nueva Ecija', lat: 15.48620, lng: 120.96780, type: 'shop', class: 'mall', keywords: ['megacenter', 'sm mega', 'sm megacenter', 'mall'] },
                { name: 'NEUST Main Campus (Sumacab)', subtitle: 'Sumacab Este, Cabanatuan City, Nueva Ecija', lat: 15.45500, lng: 120.94750, type: 'school', class: 'university', keywords: ['neust', 'nueva ecija university', 'sumacab campus', 'college', 'university'] },
                { name: 'NEUST Gen. Tinio Campus', subtitle: 'General Tinio St, Cabanatuan City, Nueva Ecija', lat: 15.48550, lng: 120.96800, type: 'school', class: 'university', keywords: ['neust gen tinio', 'neust bayan', 'university'] },
                { name: 'Wesleyan University - Philippines (WUP)', subtitle: 'Mabini Ext, Cabanatuan City, Nueva Ecija', lat: 15.48200, lng: 120.96200, type: 'school', class: 'university', keywords: ['wesleyan', 'wup', 'university', 'college'] },
                { name: 'PHINMA - Araullo University', subtitle: 'Maharlika Highway, Cabanatuan City, Nueva Ecija', lat: 15.47850, lng: 120.95900, type: 'school', class: 'university', keywords: ['araullo', 'phinma', 'araullo university', 'college'] },
                { name: 'Dr. Paulino J. Garcia Hospital (PJGMRMC)', subtitle: 'Mabini Ext, Cabanatuan City, Nueva Ecija', lat: 15.48800, lng: 120.97100, type: 'hospital', class: 'hospital', keywords: ['pjg', 'paulino garcia', 'pjgmrmc', 'provincial hospital', 'hospital', 'doktor'] },
                { name: 'Premiere Medical Center', subtitle: 'Maharlika Highway, Cabanatuan City, Nueva Ecija', lat: 15.47100, lng: 120.95500, type: 'hospital', class: 'hospital', keywords: ['premiere', 'premiere medical center', 'hospital'] },
                { name: 'Nueva Ecija Doctors Hospital', subtitle: 'Maharlika Highway, Cabanatuan City, Nueva Ecija', lat: 15.48100, lng: 120.96500, type: 'hospital', class: 'hospital', keywords: ['ne doctors', 'doctors hospital', 'hospital'] },
                { name: 'Cabanatuan Central Transport Terminal', subtitle: 'Cabanatuan City, Nueva Ecija', lat: 15.49500, lng: 120.97800, type: 'bus', class: 'bus_station', keywords: ['cabanatuan terminal', 'central terminal', 'bus terminal', 'uv express'] },
                { name: 'Cabanatuan City Hall', subtitle: 'Kapt. Pepe, Cabanatuan City, Nueva Ecija', lat: 15.48950, lng: 120.97300, type: 'town-hall', class: 'town_hall', keywords: ['cabanatuan city hall', 'city hall'] },
                { name: 'Plaza Lucero & St. Nicholas Cathedral', subtitle: 'Del Pilar St, Cabanatuan City, Nueva Ecija', lat: 15.48520, lng: 120.96680, type: 'place-of-worship', class: 'church', keywords: ['cathedral', 'plaza lucero', 'st nicholas', 'simbahan cabanatuan'] },
                { name: 'WalterMart Cabanatuan', subtitle: 'Maharlika Highway, Cabanatuan City, Nueva Ecija', lat: 15.49100, lng: 120.97200, type: 'shop', class: 'supermarket', keywords: ['waltermart', 'waltermart cabanatuan', 'supermarket'] },
                { name: 'Pacific Mall Cabanatuan', subtitle: 'Zulueta St, Cabanatuan City, Nueva Ecija', lat: 15.48700, lng: 120.96850, type: 'shop', class: 'mall', keywords: ['pacific mall', 'metro pacific', 'mall'] },
                { name: 'Sangitan Public Market', subtitle: 'Sangitan, Cabanatuan City, Nueva Ecija', lat: 15.49300, lng: 120.97400, type: 'grocery', class: 'supermarket', keywords: ['sangitan', 'palengke sangitan', 'market'] },
                { name: 'Sumacab Este / Sur / Norte', subtitle: 'Cabanatuan City, Nueva Ecija', lat: 15.45600, lng: 120.94900, type: 'residential', class: 'residential', keywords: ['sumacab', 'sumacab este', 'sumacab sur', 'sumacab norte'] },

                // San Leonardo & Surrounding Nueva Ecija Towns
                { name: 'San Leonardo Municipal Hall', subtitle: 'San Leonardo, Nueva Ecija', lat: 15.36100, lng: 120.96200, type: 'town-hall', class: 'town_hall', keywords: ['san leonardo', 'munisipyo san leonardo'] },
                { name: 'San Leonardo Public Market', subtitle: 'San Leonardo, Nueva Ecija', lat: 15.36250, lng: 120.96350, type: 'grocery', class: 'supermarket', keywords: ['san leonardo market', 'palengke san leonardo'] },
                { name: 'Jaen Municipal Hall', subtitle: 'Jaen, Nueva Ecija', lat: 15.34100, lng: 120.90800, type: 'town-hall', class: 'town_hall', keywords: ['jaen', 'munisipyo jaen'] },
                { name: 'Gapan City Plaza & Lumang Gapan', subtitle: 'Gapan City, Nueva Ecija', lat: 15.30800, lng: 120.94700, type: 'town-hall', class: 'park', keywords: ['gapan', 'lumang gapan', 'little rome', 'plaza gapan'] },
                { name: 'WalterMart Gapan', subtitle: 'Maharlika Highway, Gapan City, Nueva Ecija', lat: 15.31200, lng: 120.94900, type: 'shop', class: 'supermarket', keywords: ['waltermart gapan', 'gapan mall'] },
                { name: 'Zaragoza Municipal Hall', subtitle: 'Zaragoza, Nueva Ecija', lat: 15.45200, lng: 120.79500, type: 'town-hall', class: 'town_hall', keywords: ['zaragoza', 'munisipyo zaragoza'] },
                { name: 'Palayan City Provincial Capitol', subtitle: 'Palayan City, Nueva Ecija', lat: 15.54100, lng: 121.08500, type: 'town-hall', class: 'government', keywords: ['capitol', 'provincial capitol', 'palayan city', 'palayan'] },
                { name: 'Talavera Municipal Hall', subtitle: 'Talavera, Nueva Ecija', lat: 15.58400, lng: 120.92100, type: 'town-hall', class: 'town_hall', keywords: ['talavera', 'munisipyo talavera'] },
            ];

            @php
                $savedPlacesData = isset($savedLocations) ? $savedLocations->map(function($sl) {
                    return [
                        'name' => $sl->name,
                        'subtitle' => $sl->address,
                        'lat' => (float)$sl->latitude,
                        'lng' => (float)$sl->longitude,
                        'type' => $sl->type,
                        'isSavedPlace' => true,
                    ];
                })->values()->all() : [];
            @endphp
            const USER_SAVED_PLACES = {!! json_encode($savedPlacesData) !!};

            let _searchAbortCtrl = null;

            function fetchPlacesAutocomplete(query) {
                const qLower = query.toLowerCase().trim();
                const terms = qLower.split(/\s+/).filter(Boolean);

                // 1. Personal Saved Places match first (highest priority)
                const savedMatches = USER_SAVED_PLACES.filter(item => {
                    const haystack = (item.name + ' ' + item.subtitle).toLowerCase();
                    return terms.every(t => haystack.includes(t));
                });

                // 2. Instant local Nueva Ecija landmarks match (0ms response)
                const localMatches = NUEVA_ECIJA_LOCAL_PLACES.filter(item => {
                    const haystack = (item.name + ' ' + item.subtitle + ' ' + (item.keywords || []).join(' ')).toLowerCase();
                    return terms.every(t => haystack.includes(t));
                }).map(item => ({
                    name: item.name,
                    subtitle: item.subtitle,
                    lat: item.lat,
                    lng: item.lng,
                    type: item.type,
                    class: item.class,
                    isLocal: true,
                }));

                const immediateMatches = [...savedMatches, ...localMatches];

                // Immediately display instant local matches
                if (immediateMatches.length > 0) {
                    renderSuggestions(immediateMatches.slice(0, 6));
                }

                // 3. Query our server endpoint /api/geocode/search for live OpenStreetMap/Photon search
                if (_searchAbortCtrl) {
                    try { _searchAbortCtrl.abort(); } catch(e){}
                }
                _searchAbortCtrl = new AbortController();

                fetch(`/api/geocode/search?q=${encodeURIComponent(query)}`, { signal: _searchAbortCtrl.signal })
                    .then(r => r.json())
                    .then(remoteResults => {
                        if (!remoteResults || !Array.isArray(remoteResults)) {
                            if (immediateMatches.length === 0) hideDropdownAndClear();
                            return;
                        }

                        // Filter strictly within Nueva Ecija bounds
                        const validRemote = remoteResults.filter(r => isNuevaEcija(r.lat, r.lng, r.subtitle));

                        // Merge saved + local + remote without duplicates
                        const combined = [...savedMatches, ...localMatches];
                        validRemote.forEach(rem => {
                            const isDup = combined.some(ex => {
                                const dLat = Math.abs(ex.lat - rem.lat);
                                const dLng = Math.abs(ex.lng - rem.lng);
                                return (dLat < 0.0015 && dLng < 0.0015) || (ex.name.toLowerCase() === rem.name.toLowerCase());
                            });
                            if (!isDup) combined.push(rem);
                        });

                        if (combined.length > 0) {
                            renderSuggestions(combined.slice(0, 6));
                        } else {
                            hideDropdownAndClear();
                        }
                    })
                    .catch(err => {
                        if (err.name === 'AbortError') return;
                        if (immediateMatches.length === 0) hideDropdownAndClear();
                    });
            }

            // --- MAPBOX MAKI ICONS (per place category) ---
            const MAKI = {
                'fast-food': 'M14,8c0,0.5523-0.4477,1-1,1H2C1.4477,9,1,8.5523,1,8s0.4477-1,1-1h11C13.5523,7,14,7.4477,14,8z M3.5,10H2c0,1.6569,1.3431,3,3,3h5c1.6569,0,3-1.3431,3-3H3.5z M3,6H2V4c0-1.1046,0.8954-2,2-2h7c1.1046,0,2,0.8954,2,2v2H3z M11,4.5C11,4.7761,11.2239,5,11.5,5S12,4.7761,12,4.5S11.7761,4,11.5,4S11,4.2239,11,4.5z M9,3.5C9,3.7761,9.2239,4,9.5,4S10,3.7761,10,3.5S9.7761,3,9.5,3S9,3.2239,9,3.5z M7,4.5C7,4.7761,7.2239,5,7.5,5S8,4.7761,8,4.5S7.7761,4,7.5,4S7,4.2239,7,4.5z M5,3.5C5,3.7761,5.2239,4,5.5,4S6,3.7761,6,3.5S5.7761,3,5.5,3S5,3.2239,5,3.5z M3,4.5C3,4.7761,3.2239,5,3.5,5S4,4.7761,4,4.5S3.7761,4,3.5,4S3,4.2239,3,4.5z',
                'fuel': 'm14 6v5.5c0 .2761-.2239.5-.5.5s-.5-.2239-.5-.5v-2c0-.8284-.6716-1.5-1.5-1.5h-1.5v-6c0-.5523-.4477-1-1-1h-6c-.5523 0-1 .4477-1 1v11c0 .5523.4477 1 1 1h6c.5523 0 1-.4477 1-1v-4h1.5c.2761 0 .5.2239.5.5v2c0 .8284.6716 1.5 1.5 1.5s1.5-.6716 1.5-1.5v-6.5c0-.5523-.4477-1-1-1v-1.51c-.0054-.2722-.2277-.4901-.5-.49-.2816.0047-.5062.2367-.5015.5184.0002.0105.0007.0211.0015.0316v2.45c0 .5523.4477 1 1 1s1-.4477 1-1-.4477-1-1-1zm-5 .5c0 .2761-.2239.5-.5.5h-5c-.2761 0-.5-.2239-.5-.5v-3c0-.2761.2239-.5.5-.5h5c.2761 0 .5.2239.5.5z',
                'restaurant': 'M3.5,0l-1,5.5c-0.1464,0.805,1.7815,1.181,1.75,2L4,14c-0.0384,0.9993,1,1,1,1s1.0384-0.0007,1-1L5.75,7.5c-0.0314-0.8176,1.7334-1.1808,1.75-2L6.5,0H6l0.25,4L5.5,4.5L5.25,0h-0.5L4.5,4.5L3.75,4L4,0H3.5z M12,0c-0.7364,0-1.9642,0.6549-2.4551,1.6367C9.1358,2.3731,9,4.0182,9,5v2.5c0,0.8182,1.0909,1,1.5,1L10,14c-0.0905,0.9959,1,1,1,1s1,0,1-1V0z',
                'cafe': 'M12,5h-2V3H2v4c0.0133,2.2091,1.8149,3.9891,4.024,3.9758C7.4345,10.9673,8.7362,10.2166,9.45,9H12c1.1046,0,2-0.8954,2-2S13.1046,5,12,5z M12,8H9.86C9.9487,7.6739,9.9958,7.3379,10,7V6h2c0.5523,0,1,0.4477,1,1S12.5523,8,12,8z M10,12.5c0,0.2761-0.2239,0.5-0.5,0.5h-7C2.2239,13,2,12.7761,2,12.5S2.2239,12,2.5,12h7C9.7761,12,10,12.2239,10,12.5z',
                'bar': 'M7.5,1c-2,0-7,0.25-6.5,0.75L7,8v4c0,1-3,0.5-3,2h7c0-1.5-3-1-3-2V8l6-6.25C14.5,1.25,9.5,1,7.5,1z M7.5,2c2.5,0,4.75,0.25,4.75,0.25L11.5,3h-8L2.75,2.25C2.75,2.25,5,2,7.5,2z',
                'school': 'M5.542 3.647 3.106 3l.443-1.63a.505.505 0 0 1 .618-.352l1.46.392a.5.5 0 0 1 .355.613l-.44 1.624Zm-4.52 7.356a.496.496 0 0 1-.005-.276l1.819-6.726 2.435.647-1.819 6.726a.499.499 0 0 1-.143.237l-1.457 1.347a.152.152 0 0 1-.247-.066l-.583-1.889ZM10 5c-2.25 0-3-.75-3-3 2.25 0 3 .75 3 3Zm-1.4 7.984c-1.37.21-3.126-1.706-3.52-3.8L5.969 5.9c.399-.35.903-.533 1.419-.533a2.71 2.71 0 0 1 1.564.489.964.964 0 0 0 1.089-.01 2.438 2.438 0 0 1 1.46-.479c.77 0 1.643.489 2.05 1.201 1.536 2.696-1.194 6.709-3.144 6.417a.867.867 0 0 1-.255-.093 1.427 1.427 0 0 0-1.302 0 .866.866 0 0 1-.25.092Z',
                'hospital': 'M7,1C6.4,1,6,1.4,6,2v4H2C1.4,6,1,6.4,1,7v1c0,0.6,0.4,1,1,1h4v4c0,0.6,0.4,1,1,1h1c0.6,0,1-0.4,1-1V9h4c0.6,0,1-0.4,1-1V7c0-0.6-0.4-1-1-1H9V2c0-0.6-0.4-1-1-1H7z',
                'doctor': 'M5.5,7C4.1193,7,3,5.8807,3,4.5l0,0v-2C3,2.2239,3.2239,2,3.5,2H4c0.2761,0,0.5-0.2239,0.5-0.5S4.2761,1,4,1H3.5C2.6716,1,2,1.6716,2,2.5v2c0.0013,1.1466,0.5658,2.2195,1.51,2.87l0,0C4.4131,8.1662,4.9514,9.297,5,10.5C5,12.433,6.567,14,8.5,14s3.5-1.567,3.5-3.5V9.93c1.0695-0.2761,1.7126-1.367,1.4365-2.4365C13.1603,6.424,12.0695,5.7809,11,6.057C9.9305,6.3332,9.2874,7.424,9.5635,8.4935C9.7454,9.198,10.2955,9.7481,11,9.93v0.57c0,1.3807-1.1193,2.5-2.5,2.5S6,11.8807,6,10.5c0.0511-1.2045,0.5932-2.3356,1.5-3.13l0,0C8.4404,6.7172,9.001,5.6448,9,4.5v-2C9,1.6716,8.3284,1,7.5,1H7C6.7239,1,6.5,1.2239,6.5,1.5S6.7239,2,7,2h0.5C7.7761,2,8,2.2239,8,2.5v2l0,0C8,5.8807,6.8807,7,5.5,7 M11.5,9c-0.5523,0-1-0.4477-1-1s0.4477-1,1-1s1,0.4477,1,1S12.0523,9,11.5,9z',
                'pharmacy': 'M9.5,4l1.07-1.54c0.0599,0.0046,0.1201,0.0046,0.18,0c0.6904-0.0004,1.2497-0.5603,1.2494-1.2506C11.999,0.519,11.4391-0.0404,10.7487-0.04C10.0584-0.0396,9.499,0.5203,9.4994,1.2106c0,0.0131,0.0002,0.0262,0.0006,0.0394c0,0,0,0.07,0,0.1L7,4H9.5z M12,6V5H3v1l1.5,3.5L3,13v1h9v-1l-1-3.5L12,6z M10,10H8v2H7v-2H5V9h2V7h1v2h2V10z',
                'bank': 'M1,3C0.446,3,0,3.446,0,4v7c0,0.554,0.446,1,1,1h13c0.554,0,1-0.446,1-1V4c0-0.554-0.446-1-1-1H1z M7.5,4C8.8807,4,10,5.567,10,7.5l0,0C10,9.433,8.8807,11,7.5,11S5,9.433,5,7.5S6.1193,4,7.5,4z M7.5,5.5c-0.323,0-0.5336,0.1088-0.6816,0.25h1.3633C8.0336,5.6088,7.823,5.5,7.5,5.5z M6.625,6C6.5795,6.091,6.5633,6.1711,6.5449,6.25h1.9102C8.4367,6.1711,8.4205,6.091,8.375,6H6.625z M6.5,6.5v0.25h2V6.5H6.5z M6.5,7v0.25h2V7H6.5z M6.5,7.5v0.25h2V7.5H6.5z M6.5,8L6.25,8.25h2L8.5,8H6.5z M6,8.5c0,0,0.0353,0.1024,0.1016,0.25H8.375L8,8.5H6z',
                'lodging': 'M0.5,2.5C0.2,2.5,0,2.7,0,3v7.5v2C0,12.8,0.2,13,0.5,13S1,12.8,1,12.5V11h13v1.5c0,0.3,0.2,0.5,0.5,0.5s0.5-0.2,0.5-0.5v-2c0-0.3-0.2-0.5-0.5-0.5H1V3C1,2.7,0.8,2.5,0.5,2.5z M3.5,3C2.7,3,2,3.7,2,4.5l0,0C2,5.3,2.7,6,3.5,6l0,0C4.3,6,5,5.3,5,4.5l0,0C5,3.7,4.3,3,3.5,3L3.5,3z M7,4C5.5,4,5.5,5.5,5.5,5.5V7h-3C2.2,7,2,7.2,2,7.5v1C2,8.8,2.2,9,2.5,9H6h9V6.5C15,4,12.5,4,12.5,4H7z',
                'bus': 'M2 3C2 1.9 2.9 1 4 1H11C12.1 1 13 1.9 13 3V11C13 12 12 12 12 12V13C12 13.55 11.55 14 11 14C10.45 14 10 13.55 10 13V12H5V13C5 13.55 4.55 14 4 14C3.45 14 3 13.55 3 13V12C2 12 2 11 2 11V3ZM3.5 4C3.22 4 3 4.22 3 4.5V7.5C3 7.78 3.22 8 3.5 8H11.5C11.78 8 12 7.78 12 7.5V4.5C12 4.22 11.78 4 11.5 4H3.5ZM4 9C3.45 9 3 9.45 3 10C3 10.55 3.45 11 4 11C4.55 11 5 10.55 5 10C5 9.45 4.55 9 4 9ZM11 9C10.45 9 10 9.45 10 10C10 10.55 10.45 11 11 11C11.55 11 12 10.55 12 10C12 9.45 11.55 9 11 9ZM4 2.5C4 2.78 4.22 3 4.5 3H10.5C10.78 3 11 2.78 11 2.5C11 2.22 10.78 2 10.5 2H4.5C4.22 2 4 2.22 4 2.5Z',
                'parking': 'M4 2V13H6V9H8.5C10.433 9 12 7.433 12 5.5C12 3.567 10.433 2 8.5 2H4ZM6 7V4H8.5C9.32843 4 10 4.67157 10 5.5C10 6.32843 9.32843 7 8.5 7H6Z',
                'shop': 'm13.33 5h-1.83l-.39-2.33c-.1601-.7182-.7017-1.2905-1.41-1.49-.3493-.1124-.7131-.173-1.08-.18h-2.24c-.3669.007-.7307.0676-1.08.18-.7083.1995-1.2499.7718-1.41 1.49l-.39 2.33h-1.83c-.2761-.0017-.5013.2208-.503.497-.0003.0519.0074.1035.023.153l1.88 6.3c.1964.6246.7753 1.0496 1.43 1.05h6c.651-.0047 1.2247-.4289 1.42-1.05l1.88-6.3c.0829-.2634-.0635-.5441-.3269-.627-.0463-.0146-.0945-.0223-.1431-.023zm-8.81 0 .36-2.17c.0807-.3625.3736-.6395.74-.7.2463-.0776.5019-.1213.76-.13h2.24c.2614.0078.5205.0515.77.13.3664.0605.6593.3375.74.7l.35 2.17h-6z',
                'grocery': 'M13.199219 1.5C13.199219 1.5 11.808806 1.4588 11.253906 2C10.720406 2.5202 10.5 2.9177 10.5 4L1.1992188 4L2.59375 8.8144531C2.59725 8.8217531 2.6036219 8.8287375 2.6074219 8.8359375C2.8418219 9.4932375 3.4545469 9.9666406 4.1855469 9.9941406C4.1885469 9.9954406 4.1992187 10 4.1992188 10L10.699219 10L10.699219 10.199219C10.699219 10.199219 10.7 10.500391 10.5 10.900391C10.3 11.300391 10.200391 11.5 9.4003906 11.5L2.9003906 11.5C1.9003906 11.5 1.9003906 13 2.9003906 13L4.0996094 13L4.1992188 13L9.0996094 13L9.1992188 13L9.3007812 13C10.500781 13 11.399219 12.299609 11.699219 11.599609C11.999219 10.899609 12 10.300781 12 10.300781L12 10L12 4C12 3.4764 12.228619 3 12.699219 3L13.25 3C13.6642 3 14 2.6642 14 2.25C14 1.8358 13.6642 1.5 13.25 1.5L13.199219 1.5z M9.1992188 13C8.5992188 13 8.1992188 13.4 8.1992188 14C8.1992188 14.6 8.5992187 15 9.1992188 15C9.7992187 15 10.199219 14.6 10.199219 14C10.199219 13.4 9.7992188 13 9.1992188 13z M4.1992188 13C3.5992188 13 3.1992188 13.4 3.1992188 14C3.1992188 14.6 3.5992187 15 4.1992188 15C4.7992188 15 5.1992188 14.6 5.1992188 14C5.1992188 13.4 4.7992187 13 4.1992188 13z',
                'police': 'M5.5,1L6,2h5l0.5-1H5.5z M6,2.5v1.25c0,0,0,2.75,2.5,2.75S11,3.75,11,3.75V2.5H6z M1.9844,3.9863C1.4329,3.9949,0.9924,4.4485,1,5v4c-0.0001,0.6398,0.5922,1.1152,1.2168,0.9766L5,9.3574V14l5.8789-6.9297C10.7391,7.0294,10.5947,7,10.4414,7H6.5L3,7.7539V5C3.0077,4.4362,2.5481,3.9775,1.9844,3.9863z M11.748,7.7109L6.4121,14H12V8.5586C12,8.2451,11.9061,7.9548,11.748,7.7109z',
                'fire-station': 'M7.5 14C11.0899 14 14 11 14 7.50003C14 4.5 11.5 2 11.5 2L10.5 5.5L7.5 1L4.5 5.5L3.5 2C3.5 2 1 4.5 1 7.50003C1 11 3.91015 14 7.5 14ZM7.5 12.5C6.11929 12.5 5 11.3807 5 10C5 8.61929 7.5 5.5 7.5 5.5C7.5 5.5 10 8.61929 10 10C10 11.3807 8.88071 12.5 7.5 12.5Z',
                'park': 'M14,5.75c0.0113-0.6863-0.3798-1.3159-1-1.61C12.9475,3.4906,12.4014,2.9926,11.75,3c-0.0988,0.0079-0.1962,0.0281-0.29,0.06c-0.0607-0.66-0.6449-1.1458-1.3048-1.0851C9.8965,1.9987,9.6526,2.1058,9.46,2.28l0,0c0-0.6904-0.5596-1.25-1.25-1.25S6.96,1.5896,6.96,2.28C6.96,2.28,7,2.3,7,2.33C6.4886,1.8913,5.7184,1.9503,5.2797,2.4618C5.1316,2.6345,5.0347,2.8451,5,3.07C4.8417,3.0195,4.6761,2.9959,4.51,3C3.6816,2.9931,3.0044,3.659,2.9975,4.4874C2.9958,4.6872,3.0341,4.8852,3.11,5.07C2.3175,5.2915,1.8546,6.1136,2.0761,6.9061C2.2163,7.4078,2.6083,7.7998,3.11,7.94c0.2533,0.7829,1.0934,1.2123,1.8763,0.959C5.5216,8.7258,5.9137,8.2659,6,7.71C6.183,7.8691,6.4093,7.9701,6.65,8v5L5,14h5l-1.6-1v-2c0.7381-0.8915,1.6915-1.5799,2.77-2c0.8012,0.1879,1.603-0.3092,1.7909-1.1103C12.9893,7.7686,13.0025,7.6444,13,7.52c0.0029-0.0533,0.0029-0.1067,0-0.16C13.6202,7.0659,14.0113,6.4363,14,5.75z M8.4,10.26V6.82C8.6703,7.3007,9.1785,7.5987,9.73,7.6h0.28c0.0156,0.4391,0.2242,0.849,0.57,1.12C9.7643,9.094,9.0251,9.6162,8.4,10.26z',
                'stadium': 'M7,1v2v1.5v0.5098C4.1695,5.1037,2.0021,5.9665,2,7v4.5c0,1.1046,2.4624,2,5.5,2s5.5-0.8954,5.5-2V7c-0.0021-1.0335-2.1695-1.8963-5-1.9902V4.0625L11,2.75L7,1z M3,8.1465c0.5148,0.2671,1.2014,0.4843,2,0.6328v2.9668C3.7948,11.477,3,11.0199,3,10.5V8.1465z M12,8.1484V10.5c0,0.5199-0.7948,0.977-2,1.2461V8.7812C10.7986,8.6328,11.4852,8.4155,12,8.1484z M6,8.9219C6.4877,8.973,6.9925,8.9992,7.5,9C8.0073,8.9999,8.5121,8.9743,9,8.9238v2.9844C8.5287,11.964,8.0288,12,7.5,12S6.4713,11.964,6,11.9082V8.9219z',
                'cinema': 'M14,7.5v2c0,0.2761-0.2239,0.5-0.5,0.5S13,9.7761,13,9.5c0,0,0.06-0.5-1-0.5h-1v2.5c0,0.2761-0.2239,0.5-0.5,0.5h-8C2.2239,12,2,11.7761,2,11.5v-4C2,7.2239,2.2239,7,2.5,7h8C10.7761,7,11,7.2239,11,7.5V8h1c1.06,0,1-0.5,1-0.5C13,7.2239,13.2239,7,13.5,7S14,7.2239,14,7.5z M4,3C2.8954,3,2,3.8954,2,5s0.8954,2,2,2s2-0.8954,2-2S5.1046,3,4,3z M4,6C3.4477,6,3,5.5523,3,5s0.4477-1,1-1s1,0.4477,1,1S4.5523,6,4,6z M8.5,2C7.1193,2,6,3.1193,6,4.5S7.1193,7,8.5,7S11,5.8807,11,4.5S9.8807,2,8.5,2z M8.5,6C7.6716,6,7,5.3284,7,4.5S7.6716,3,8.5,3S10,3.6716,10,4.5S9.3284,6,8.5,6z',
                'toilet': 'M3 1.5a1.5 1.5 0 1 0 3 0 1.5 1.5 0 0 0-3 0ZM11.5 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM3.29 4a1 1 0 0 0-.868.504L.566 7.752a.5.5 0 1 0 .868.496l1.412-2.472A345.048 345.048 0 0 0 1 11h2v2.5a.5.5 0 0 0 1 0V11h1v2.5a.5.5 0 0 0 1 0V11h2L6.103 5.687l1.463 2.561a.5.5 0 1 0 .868-.496L6.578 4.504A1 1 0 0 0 5.71 4H3.29ZM9 4.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v5a.5.5 0 0 1-1 0v4a.5.5 0 0 1-1 0v-4h-1v4a.5.5 0 0 1-1 0v-4a.5.5 0 0 1-1 0v-5Z',
                'place-of-worship': 'M7.5,0l-2,2v2h4V2L7.5,0z M5.5,4.5L4,6h7L9.5,4.5H5.5z M2,6.5c-0.5523,0-1,0.4477-1,1V13h2V7.5C3,6.9477,2.5523,6.5,2,6.5z M4,6.5V13h7V6.5H4z M13,6.5c-0.5523,0-1,0.4477-1,1V13h2V7.5C14,6.9477,13.5523,6.5,13,6.5z',
                'town-hall': 'M13,4H9l0-3L7.5,0L6,1v3H2L1,5v1h13V5L13,4z M7.5,1.5c0.4,0,0.7,0.3,0.7,0.8S7.9,3,7.5,3S6.7,2.7,6.7,2.2C6.7,1.8,7.1,1.5,7.5,1.5z M13,7H2v4l-1,1.5V14h13v-1.5L13,11V7z M5,12.5H4V8h1V12.5z M8,12.5H7V8h1V12.5z M11,12.5h-1V8h1V12.5z',
                'post': 'M13.5 3.65139C13.5 3.86918 13.3912 4.07257 13.2099 4.19338L7.5 8L1.79006 4.19338C1.60885 4.07257 1.5 3.86918 1.5 3.65139C1.5 3.29164 1.79164 3 2.15139 3L12.8486 3C13.2084 3 13.5 3.29164 13.5 3.65139Z M13.5 5.96713V11C13.5 11.5523 13.0523 12 12.5 12H2.5C1.94772 12 1.5 11.5523 1.5 11L1.5 5.96713C1.5 5.76746 1.72254 5.64836 1.88868 5.75912L7.5 9.5L13.1113 5.75912C13.2775 5.64836 13.5 5.76746 13.5 5.96713Z',
                'library': 'M1.0819,9.9388C0.9871,9.867,1.0007,9.7479,1.0007,9.7479L1.5259,3.5c0,0,0.0082-0.0688,0.0388-0.104C1.584,3.374,1.6084,3.342,1.6544,3.3232C2.1826,3.1072,5.0537,1.5519,6.5,3c0.2397,0.2777,0.4999,0.6876,0.4999,1v5.2879c0,0,0.0062,0.1122-0.0953,0.1801c-0.0239,0.016-0.124,0.0616-0.242,0.0026c-2.2253-1.1134-4.711,0.1546-5.3381,0.4871C1.1987,10.0244,1.1006,9.9531,1.0819,9.9388z M13.6754,9.9577c-0.6271-0.3325-3.1128-1.6005-5.3381-0.4871c-0.118,0.059-0.2181,0.0134-0.242-0.0026C7.9939,9.4001,8.0001,9.2879,8.0001,9.2879V4c0-0.3124,0.2602-0.7223,0.4999-1c1.4463-1.4481,4.2991,0.1071,4.8273,0.3232c0.046,0.0188,0.0704,0.0508,0.0897,0.0728C13.4476,3.4312,13.4558,3.5,13.4558,3.5l0.5435,6.2479c0,0,0.0136,0.1191-0.0812,0.1909C13.8994,9.9531,13.8013,10.0244,13.6754,9.9577z M8.8647,12.6863c0.0352-0.0085,0.0964-0.0443,0.1179-0.0775c0.0236-0.0364,0.0378-0.0617,0.0423-0.1088c0.0495-0.9379,1.6245-1.8119,4.6477-0.0298c0.0775,0.0441,0.1666,0.0396,0.2425-0.0155C14.0014,12.392,14,12.2859,14,12.2859v-0.5542c0,0,0.0003-0.0764-0.0272-0.1184c-0.0205-0.0312-0.0476-0.0643-0.0926-0.0858c-2.0254-1.3145-4.5858-1.8972-5.8854-0.1592c-0.0181,0.0423-0.0353,0.0613-0.0728,0.0905C7.8654,11.5028,7.7964,11.5,7.7964,11.5H7.2109c0,0-0.069,0.0028-0.1256-0.0412c-0.0375-0.0292-0.0547-0.0482-0.0728-0.0905c-1.2996-1.738-3.86-1.1828-5.8854,0.1317c-0.045,0.0215-0.0721,0.0546-0.0926,0.0858c-0.0275,0.042-0.0272,0.1184-0.0272,0.1184v0.5542c0,0-0.0014,0.1061,0.0849,0.1688c0.0759,0.0551,0.165,0.0596,0.2425,0.0155c3.0232-1.7821,4.5982-0.8806,4.6477,0.0573c0.0045,0.0471,0.0187,0.0724,0.0423,0.1088c0.0215,0.0332,0.0827,0.069,0.1179,0.0775C6.8645,12.8656,7.9112,12.9363,8.8647,12.6863z',
                'charging-station': 'M2.64585 7.80112L7.75248 0.837532C7.90807 0.625354 8.15545 0.5 8.41856 0.5C8.9632 0.5 9.35876 1.01788 9.21546 1.54333L8.08612 5.68422C8.04275 5.84326 8.16247 6 8.32731 6H11.7466C12.1627 6 12.5 6.3373 12.5 6.75337C12.5 6.91361 12.4489 7.06967 12.3542 7.19888L7.24752 14.1625C7.09193 14.3746 6.84455 14.5 6.58144 14.5C6.0368 14.5 5.64124 13.9821 5.78454 13.4567L6.91388 9.31578C6.95725 9.15674 6.83753 9 6.67269 9H3.25337C2.83729 9 2.5 8.66271 2.5 8.24663C2.5 8.08639 2.55109 7.93033 2.64585 7.80112Z',
                'car': 'M13.84,6.852,12.6,5.7,11.5,3.5a1.05,1.05,0,0,0-.9-.5H4.4a1.05,1.05,0,0,0-.9.5L2.4,5.7,1.16,6.852A.5.5,0,0,0,1,7.219V11.5a.5.5,0,0,0,.5.5h2c.2,0,.5-.2.5-.4V11h7v.5c0,.2.2.5.4.5h2.1a.5.5,0,0,0,.5-.5V7.219A.5.5,0,0,0,13.84,6.852ZM4.5,4h6l1,2h-8ZM5,8.6c0,.2-.3.4-.5.4H2.4C2.2,9,2,8.7,2,8.5V7.4c.1-.3.3-.5.6-.4l2,.4c.2,0,.4.3.4.5Zm8-.1c0,.2-.2.5-.4.5H10.5c-.2,0-.5-.2-.5-.4V7.9c0-.2.2-.5.4-.5l2-.4c.3-.1.5.1.6.4Z',
                'water': 'M7.5 14C9.57688 14 12 12.7117 12 9.43241C12 7.20724 8.53844 2.2883 7.5 1C6.57691 2.2883 3 7.09007 3 9.43241C3 12.7117 5.42312 14 7.5 14Z',
                'marker': 'M7.5 1C5.42312 1 3 2.2883 3 5.56759C3 7.79276 6.46156 12.7117 7.5 14C8.42309 12.7117 12 7.90993 12 5.56759C12 2.2883 9.57688 1 7.5 1Z'
            };

            const PLACE_CATEGORIES = [
                { tags: ['fastfood','fast_food','food_court','drive_through'], icon: 'fast-food', fg: '#ea580c', bg: '#ffedd5' },
                { tags: ['restaurant','bbq','grill','pizzeria','noodle','ice_cream','diner','burger','steakhouse','sushi','ramen','fish_and_chips'], icon: 'restaurant', fg: '#b45309', bg: '#fef3c7' },
                { tags: ['cafe','coffee','tea','coffee_shop'], icon: 'cafe', fg: '#92400e', bg: '#fef3c7' },
                { tags: ['bar','pub','nightclub','biergarten','karaoke'], icon: 'bar', fg: '#9d174d', bg: '#fce7f3' },
                { tags: ['fuel','petrol','gas_station','fuel_station'], icon: 'fuel', fg: '#0284c7', bg: '#e0f2fe' },
                { tags: ['charging_station','ev_station'], icon: 'charging-station', fg: '#059669', bg: '#d1fae5' },
                { tags: ['bus_station','bus_stop','bus','terminal','transport','ferry_terminal'], icon: 'bus', fg: '#2563eb', bg: '#dbeafe' },
                { tags: ['parking','parking_space','parking_entrance'], icon: 'parking', fg: '#475569', bg: '#e2e8f0' },
                { tags: ['car_repair','car_wash','car_dealership','car_rental','car'], icon: 'car', fg: '#334155', bg: '#e2e8f0' },
                { tags: ['supermarket','grocery','greengrocer','butcher','market','convenience','mini_supermarket','sari_sari','sari-sari'], icon: 'grocery', fg: '#7c3aed', bg: '#ede9fe' },
                { tags: ['shop','mall','department_store','clothes','shoes','electronics','general','variety_store','bakery','florist','hairdresser','beauty','hardware','furniture','optician'], icon: 'shop', fg: '#6d28d9', bg: '#ede9fe' },
                { tags: ['pharmacy','chemist','drugstore'], icon: 'pharmacy', fg: '#dc2626', bg: '#fee2e2' },
                { tags: ['hospital'], icon: 'hospital', fg: '#dc2626', bg: '#fee2e2' },
                { tags: ['doctors','clinic','physician','dentist','veterinary','medical','health_centre'], icon: 'doctor', fg: '#be123c', bg: '#ffe4e6' },
                { tags: ['hotel','hostel','motel','guest_house','bed_and_breakfast','resort','lodging'], icon: 'lodging', fg: '#7c3aed', bg: '#ede9fe' },
                { tags: ['bank','atm','money_transfer'], icon: 'bank', fg: '#1d4ed8', bg: '#dbeafe' },
                { tags: ['town_hall','government','courthouse','municipality','public_building','community_centre'], icon: 'town-hall', fg: '#475569', bg: '#e2e8f0' },
                { tags: ['police'], icon: 'police', fg: '#1d4ed8', bg: '#dbeafe' },
                { tags: ['fire_station','fire_brigade'], icon: 'fire-station', fg: '#dc2626', bg: '#fee2e2' },
                { tags: ['post_office','post_box','post_depot'], icon: 'post', fg: '#b45309', bg: '#fef3c7' },
                { tags: ['library'], icon: 'library', fg: '#6d28d9', bg: '#ede9fe' },
                { tags: ['school','kindergarten','college','university','music_school','language_school'], icon: 'school', fg: '#0d9488', bg: '#ccfbf1' },
                { tags: ['park','garden','playground','cemetery','recreation_ground'], icon: 'park', fg: '#059669', bg: '#d1fae5' },
                { tags: ['stadium','sports_centre','pitch','basketball','tennis_court','gym','fitness_centre','track'], icon: 'stadium', fg: '#059669', bg: '#d1fae5' },
                { tags: ['cinema','theatre','arts_centre','theater'], icon: 'cinema', fg: '#7c3aed', bg: '#ede9fe' },
                { tags: ['place_of_worship','church','chapel','mosque','temple','synagogue','parish'], icon: 'place-of-worship', fg: '#4f46e5', bg: '#e0e7ff' },
                { tags: ['toilet','toilets','restroom'], icon: 'toilet', fg: '#0f766e', bg: '#ccfbf1' },
                { tags: ['drinking_water','water','spring','fountain'], icon: 'water', fg: '#0284c7', bg: '#e0f2fe' }
            ];

            function getPlaceIcon(item) {
                if (item.isSavedPlace) {
                    const svgs = {
                        home: { d: 'M7.5 1.5l-6 5.5h1.5v6.5h4v-4h3v4h4V7h1.5l-6-5.5z', fg: '#2563eb', bg: '#dbeafe' },
                        work: { d: 'M13.5 4h-2.5V2.5c0-.8-.7-1.5-1.5-1.5h-4c-.8 0-1.5.7-1.5 1.5V4H1.5C.7 4 0 4.7 0 5.5v7c0 .8.7 1.5 1.5 1.5h12c.8 0 1.5-.7 1.5-1.5v-7c0-.8-.7-1.5-1.5-1.5zm-8-1.5h4V4h-4V2.5z', fg: '#4f46e5', bg: '#e0e7ff' },
                        school: { d: 'M7.5 1L0 5l7.5 4 6.5-3.5v4.5h1V5L7.5 1zm0 9.2L2.5 7.5v2.8c0 2.2 2.2 4 5 4s5-1.8 5-4V7.5l-5 2.7z', fg: '#d97706', bg: '#fef3c7' },
                        shopping: { d: 'M11 4V3c0-1.7-1.3-3-3-3S5 1.3 5 3v1H1.5C.7 4 0 4.7 0 5.5l1.2 8.5c.1.6.6 1 1.2 1h10.2c.6 0 1.1-.4 1.2-1l1.2-8.5c0-.8-.7-1.5-1.5-1.5H11zM6.5 3c0-.8.7-1.5 1.5-1.5s1.5.7 1.5 1.5v1h-3V3z', fg: '#e11d48', bg: '#ffe4e6' },
                        favorite: { d: 'M7.5 1.5l2 4 4.5.7-3.25 3.2.75 4.6L7.5 12l-4 2 .75-4.6L1 6.2l4.5-.7 2-4z', fg: '#059669', bg: '#d1fae5' },
                        custom: { d: 'M7.5 0C4.5 0 2 2.5 2 5.5c0 4.1 5.5 9.5 5.5 9.5s5.5-5.4 5.5-9.5C13 2.5 10.5 0 7.5 0zm0 7.5c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z', fg: '#475569', bg: '#f1f5f9' }
                    };
                    const match = svgs[item.type] || svgs.custom;
                    return { d: match.d, fg: match.fg, bg: match.bg };
                }
                const values = [item.osm_key, item.osm_value, item.type, item.class]
                    .filter(Boolean)
                    .map(s => String(s).toLowerCase());
                for (const cat of PLACE_CATEGORIES) {
                    if (cat.tags.some(t => values.indexOf(t) !== -1)) {
                        return { d: MAKI[cat.icon] || MAKI.marker, fg: cat.fg, bg: cat.bg };
                    }
                }
                return { d: MAKI.marker, fg: '#10b981', bg: '#d1fae5' };
            }

            window.srhGetPlaceIcon = getPlaceIcon;
            window.srhClearPlaceResults = function() {
                // Map feature removed — placeholder kept for callers
            };
            function hideDropdownAndClear() {
                dropdown.classList.add('hidden');
                window.srhClearPlaceResults();
            }

            function renderSuggestions(items) {
                if (!items || items.length === 0) {
                    dropdown.classList.add('hidden');
                    return;
                }

                dropdown.innerHTML = items.map((item, idx) => {
                    const icon = getPlaceIcon(item);
                    return `
                    <div data-idx="${idx}" class="p-3 hover:bg-slate-50 cursor-pointer flex items-center gap-3 transition-colors text-left place-item-row">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 shadow-2xs" style="background-color:${icon.bg}; color:${icon.fg};">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 15 15"><path d="${icon.d}"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5">
                                <p class="text-xs font-black text-slate-900 truncate">${escapeHtml(item.name)}</p>
                                ${item.isSavedPlace ? `<span class="px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-100 text-[9px] font-black uppercase tracking-wider shrink-0">Saved</span>` : ''}
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 truncate">${escapeHtml(item.subtitle)}</p>
                        </div>
                    </div>
                `;
                }).join('');

                dropdown.querySelectorAll('.place-item-row').forEach((row, i) => {
                    row.addEventListener('click', function() {
                        const item = items[i];
                        destInput.value = item.name;
                        hideDropdownAndClear();
                        document.getElementById('destination_lat_input').value = item.lat;
                        document.getElementById('destination_lng_input').value = item.lng;
                        if (window.setDestinationPinOnMap) {
                            window.setDestinationPinOnMap(item.lat, item.lng, true);
                        }
                    });
                });

                dropdown.classList.remove('hidden');
                try {
                    const dRect = dropdown.getBoundingClientRect();
                    const availableAbove = Math.max(160, (dRect.bottom || window.innerHeight) - 80);
                    dropdown.style.maxHeight = Math.min(280, availableAbove) + 'px';
                } catch(e) {}
            }

            // Global 1-Tap Saved Place Selector
            window.selectSavedLocationDirectly = function(loc) {
                if (!loc) return;
                const lat = parseFloat(loc.latitude || loc.lat);
                const lng = parseFloat(loc.longitude || loc.lng);
                let label = loc.name || 'Saved Place';
                if (loc.address && loc.address !== loc.name) {
                    label = loc.name + ' (' + loc.address + ')';
                }

                // 1. Fill all destination text inputs on the page
                document.querySelectorAll('input[name="destination"]').forEach(function(el) {
                    el.value = label;
                    try {
                        el.dispatchEvent(new Event('input', { bubbles: true }));
                        el.dispatchEvent(new Event('change', { bubbles: true }));
                    } catch(e) {}
                });
                // 2. Fill all destination coordinates inputs on the page
                document.querySelectorAll('input[name="destination_lat"]').forEach(function(el) {
                    el.value = lat;
                    try { el.dispatchEvent(new Event('change', { bubbles: true })); } catch(e) {}
                });
                document.querySelectorAll('input[name="destination_lng"]').forEach(function(el) {
                    el.value = lng;
                    try { el.dispatchEvent(new Event('change', { bubbles: true })); } catch(e) {}
                });

                // 3. Trigger the destination pin & map fitBounds animation
                function triggerExistingFitAnimation() {
                    if (window.setDestinationPinOnMap && !isNaN(lat) && !isNaN(lng)) {
                        window.setDestinationPinOnMap(lat, lng, true);
                    }
                }

                triggerExistingFitAnimation();
                setTimeout(triggerExistingFitAnimation, 100);
                setTimeout(triggerExistingFitAnimation, 300);
                setTimeout(triggerExistingFitAnimation, 600);
                setTimeout(triggerExistingFitAnimation, 1000);

                // 4. Ensure the booking sheet is visible & resting at the top
                const sheet = document.getElementById('pax-booking-sheet');
                if (sheet) {
                    sheet.style.transform = 'translate3d(0, 0px, 0)';
                }
                const content = document.getElementById('pax-sheet-details-content');
                if (content) {
                    content.style.opacity = '1';
                    content.style.pointerEvents = 'auto';
                }

                if (window.createSlidingToast) {
                    window.createSlidingToast('📍 Quick Booking: Set destination to ' + loc.name, 'info');
                }
            };

            // Global Quick Save Destination as Favorite
            window.quickSaveCurrentDestinationAsFavorite = function() {
                const destInput = document.getElementById('passenger-dest-text-input');
                const latInput = document.getElementById('destination_lat_input');
                const lngInput = document.getElementById('destination_lng_input');
                const address = destInput ? destInput.value.trim() : '';
                const lat = latInput ? parseFloat(latInput.value) : 0;
                const lng = lngInput ? parseFloat(lngInput.value) : 0;

                if (!address || !lat || !lng) {
                    if (window.createSlidingToast) window.createSlidingToast('Please search or pin a destination first to save it.', 'warning');
                    return;
                }

                const defaultName = address.split(',')[0].split('(')[0].trim() || 'Saved Place';
                const placeName = prompt('Enter a label for this saved place (e.g. Home, Work, School, Mall):', defaultName);
                if (!placeName) return;

                fetch("{{ route('api.saved-locations.quick-save') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        name: placeName,
                        address: address,
                        latitude: lat,
                        longitude: lng,
                        type: 'favorite'
                    })
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        if (window.createSlidingToast) window.createSlidingToast('⭐ ' + placeName + ' added to your Saved Places!', 'success');
                    }
                })
                .catch(() => {
                    if (window.createSlidingToast) window.createSlidingToast('Failed to save place.', 'error');
                });
            };

            // Boot URL param listener for direct 1-tap bookings from Saved Places page
            function checkAndApplyUrlDestinationParams() {
                try {
                    const params = new URLSearchParams(window.location.search);
                    const destLat = params.get('dest_lat');
                    const destLng = params.get('dest_lng');
                    const destName = params.get('dest_name');
                    const destAddr = params.get('dest_addr') || '';
                    if (destLat && destLng && destName) {
                        const parsedLat = parseFloat(destLat);
                        const parsedLng = parseFloat(destLng);
                        const parsedName = decodeURIComponent(destName);
                        const parsedAddr = decodeURIComponent(destAddr);
                        if (!isNaN(parsedLat) && !isNaN(parsedLng)) {
                            // Clean the URL query params so reload/refresh starts fresh
                            try {
                                if (window.history && window.history.replaceState) {
                                    window.history.replaceState({}, document.title, window.location.pathname);
                                }
                            } catch(e){}

                            window.selectSavedLocationDirectly({
                                name: parsedName,
                                address: parsedAddr,
                                latitude: parsedLat,
                                longitude: parsedLng
                            });
                        }
                    }
                } catch(e){}
            }
            window.checkAndApplyUrlDestinationParams = checkAndApplyUrlDestinationParams;

            // Hook into every page load & SPA transition
            ['DOMContentLoaded', 'spa:page-loaded', 'spa:page-restored', 'pageshow', 'popstate'].forEach(function(ev) {
                window.addEventListener(ev, function() {
                    const params = new URLSearchParams(window.location.search);
                    if (params.get('dest_lat')) {
                        setTimeout(checkAndApplyUrlDestinationParams, 50);
                        setTimeout(checkAndApplyUrlDestinationParams, 200);
                        setTimeout(checkAndApplyUrlDestinationParams, 500);
                    }
                });
            });

            // If destination params exist right now at script run, invoke immediately
            const currentUrlParams = new URLSearchParams(window.location.search);
            if (currentUrlParams.get('dest_lat')) {
                setTimeout(checkAndApplyUrlDestinationParams, 80);
                setTimeout(checkAndApplyUrlDestinationParams, 300);
            }

            if (destInput.dataset.autoDocBound !== '1') {
                destInput.dataset.autoDocBound = '1';
                document.addEventListener('click', function(e) {
                    if (!destInput.contains(e.target) && !dropdown.contains(e.target)) {
                        hideDropdownAndClear();
                    }
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPlacesAutocomplete);
        } else {
            initPlacesAutocomplete();
        }
        window.initPlacesAutocompleteGlobal = function() { initPlacesAutocomplete(); };
    })();

    {{-- ===== WAIT MAP JS (searching / fare_proposed / fare_accepted) ===== --}}
    (function() {
        const W_PICKUP_LAT = {{ $waitPickupLat ?? 15.429550175641715 }};
        const W_PICKUP_LNG = {{ $waitPickupLng ?? 120.92240292427664 }};
        const W_DEST_LAT   = {{ $waitDestLat ?? 15.4265 }};
        const W_DEST_LNG   = {{ $waitDestLng ?? 120.9285 }};
        // Expose pickup coords globally so syncPaxMapPadding / getPaxTargetPin can find them
        window._paxWaitPickupLat = W_PICKUP_LAT;
        window._paxWaitPickupLng = W_PICKUP_LNG;
        window._paxWaitDestLat   = W_DEST_LAT;
        window._paxWaitDestLng   = W_DEST_LNG;


        // ---- Bottom sheet ----
        // The wait states reuse the booking-form sheet shell (#pax-booking-sheet with
        // #pax-sheet-drag-handle + #pax-sheet-details-content) â€” its gesture and
        // two-state snapping are handled by the shared initPaxSheetSwipeGesture.

        // Floating buttons load in their normal resting position (above the sheet top)
        if (window.syncPaxFloatingButtons) window.syncPaxFloatingButtons();

        // ---- Map init ----
        // The single driver-style map boots from the shared init (#pax-home-map).
        // The wait view renders read-only ride pins + Driver-Hub ease framing there.
        window.initWaitMapGlobal = function() {
            if (window.initPaxHomeMapGlobal) window.initPaxHomeMapGlobal();
        };
        if (window.initPaxHomeMapGlobal) window.initPaxHomeMapGlobal();

        // Trigger resize after layout settles (sheet content is now open by default)
        setTimeout(() => { try { if (window.paxHomeMapInstance) window.paxHomeMapInstance.resize(); } catch(e){} }, 350);
    })();

    (function() {
        // ---- Track mode ----
        // The single driver-style map boots from the shared init (#pax-home-map) with
        // read-only ride pins + Driver-Hub ease framing. No driver marker, no route
        // lines, no live-location polling â€” the map stays clean by design.
        window.initPassengerMapGlobal = function() {
            if (window.initPaxHomeMapGlobal) window.initPaxHomeMapGlobal();
        };
        window.initPassengerMapGlobal();
    })();

</script>

@include('passenger.partials.report-modal')
</x-app-layout>
