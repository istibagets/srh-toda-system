<x-app-layout>
    <style>
        /* 🌟 Driver Hub Non-Scrollable Full-Bleed Map Viewport */
        html, body {
            overflow: hidden !important;
            height: 100% !important;
        }

        /* Dynamic viewport height — set by JS to match window.innerHeight exactly */
        :root { --sheet-h: 100dvh; }

        #app-main-content {
            padding-bottom: 0 !important;
        }

        /* 🌟 Duty Status Transition Animations */
        #duty-toggle-container {
            transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1), transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }
        .duty-animating-out {
            opacity: 0 !important;
            transform: scale(0.95) translateY(8px) !important;
        }
        .duty-animating-in {
            opacity: 0 !important;
            transform: scale(0.96) translateY(-8px) !important;
        }

        /* ⚡ 1-Shot Click Ripple Feedback (0.6s duration) */
        @@keyframes clickRipplePulse {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7), 0 10px 25px rgba(15, 23, 42, 0.25); transform: scale(0.88); }
            50% { box-shadow: 0 0 0 20px rgba(16, 185, 129, 0), 0 15px 35px rgba(16, 185, 129, 0.4); transform: scale(1.05); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0), 0 10px 25px rgba(15, 23, 42, 0.25); transform: scale(1); }
        }
        .click-ripple-pulse {
            animation: clickRipplePulse 0.6s ease-out 1 !important;
        }
        
        /* 🌟 Smooth Form & Sheet Content Transition Animation */
        @@keyframes sheetContentFadeIn {
            0% {
                opacity: 0;
                transform: translateY(8px) scale(0.98);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        .sheet-content-animate-in {
            animation: sheetContentFadeIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
            will-change: opacity, transform;
        }

        /* Grab-Style Tooltip Callout Arrow & Smooth Floating Animation */
        @@keyframes grabSubtleFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        .grab-callout-float {
            animation: grabSubtleFloat 2.8s ease-in-out infinite !important;
        }

        .grab-tooltip-arrow::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 24px;
            width: 12px;
            height: 12px;
            background-color: inherit;
            border-right: 1px solid rgba(226, 232, 240, 0.8);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            transform: rotate(45deg);
        }

        /* Glassmorphism Panels */
        .grab-glass-panel {
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.18);
        }

        /* 120Hz ProMotion Floating Action Buttons Spring Motion */
        .floating-action-btn {
            transition: transform 0.15s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: bottom, transform;
            transform: translate3d(0, 0, 0);
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
        }

        /* 120Hz ProMotion GPU-Accelerated Sliding Sheets */
        #active-trip-details-content, #driver-bottom-sheet, #active-trip-wrapper, #incoming-ride-wrapper {
            will-change: transform;
            transform: translate3d(0, 0, 0);
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            transition-timing-function: cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        #sheet-details-content {
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior-y: contain;
            touch-action: pan-y;
            transform: translateZ(0);
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
        }

        /* 🎴 Dual-Stack Duty Status Cards — both cards stay mounted and a CSS
           crossfade (flip on #queue-card-states[data-online]) swaps which one
           is visible. No re-mount, no flicker, no blank frame on duty flips
           or when returning/active UI hands back to the queue. */
        #queue-card-states {
            position: relative;
            width: 100%;
            height: 168px;
        }
        @media (min-width: 640px) {
            #queue-card-states { height: 192px; }
        }
        #queue-card-states > #online-queue-card,
        #queue-card-states > #offline-queue-card {
            position: absolute;
            inset: 0;
            width: 100%;
            max-width: 100%;
            margin: 0;
            opacity: 0;
            transition: opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #queue-card-states > #online-queue-card {
            opacity: 1;
            z-index: 2;
        }
        #queue-card-states > #offline-queue-card {
            opacity: 0;
            z-index: 1;
        }
        #queue-card-states[data-online="false"] > #online-queue-card {
            opacity: 0;
            pointer-events: none;
        }
        #queue-card-states[data-online="false"] > #offline-queue-card {
            opacity: 1;
            pointer-events: auto;
        }
        #queue-card-states[data-online="true"] > #online-queue-card {
            opacity: 1;
            pointer-events: auto;
        }
        #queue-card-states[data-online="true"] > #offline-queue-card {
            pointer-events: none;
        }

        /* ============================================================
           INCOMING RIDE TAKEOVER OVERLAY
           Grab-style full-screen offer sheet (driver side)
           Managed client-side by srhShowIncomingOverlay / srhHideIncomingOverlay
           ============================================================ */
        .srh-inc-overlay {
            position: fixed;
            inset: 0;
            z-index: 10000;
            pointer-events: none;
        }
        .srh-inc-overlay.srh-inc-visible {
            pointer-events: auto;
        }
        .srh-inc-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(10, 12, 18, 0.55);
            -webkit-backdrop-filter: blur(6px);
            backdrop-filter: blur(6px);
            opacity: 0;
            transition: opacity 0.35s ease;
        }
        .srh-inc-visible .srh-inc-backdrop {
            opacity: 1;
        }
        .srh-inc-card {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 28px 28px 0 0;
            background: #ffffff;
            box-shadow: 0 -18px 60px rgba(0, 0, 0, 0.28);
            padding: 20px 18px calc(22px + env(safe-area-inset-bottom));
            transform: translate3d(0, 110%, 0);
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: transform;
            max-height: 92dvh;
            overflow-y: auto;
            overscroll-behavior: contain;
        }
        .srh-inc-visible .srh-inc-card {
            transform: translate3d(0, 0, 0);
        }
        .srh-inc-overlay.srh-inc-hiding .srh-inc-card {
            transform: translate3d(0, 110%, 0);
            transition-duration: 0.32s;
            transition-timing-function: cubic-bezier(0.55, 0.06, 0.68, 0.19);
        }
        .srh-inc-timer-wrap {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            border-radius: 0 0 6px 0;
            background: rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .srh-timer-bar {
            height: 100%;
            width: 100%;
            background: linear-gradient(90deg, #22c55e, #16a34a);
            border-radius: 0 6px 6px 0;
            transform-origin: left center;
        }
        .srh-timer-run .srh-timer-bar {
            animation: srhTimerShrink 60s linear forwards;
        }
        .srh-timer-paused .srh-timer-bar {
            animation-play-state: paused;
        }
        @@keyframes srhTimerShrink {
            from { transform: scaleX(1); }
            to   { transform: scaleX(0); }
        }
        .srh-inc-head {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }
        .srh-inc-badge {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.4px;
            padding: 6px 10px;
            border-radius: 999px;
            text-transform: uppercase;
        }
        .srh-inc-badge-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #16a34a;
            animation: srhIncDotPulse 1.2s ease-in-out infinite;
        }
        @@keyframes srhIncDotPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%      { opacity: 0.35; transform: scale(0.72); }
        }
        .srh-inc-pos {
            margin-left: auto;
            font-size: 12px;
            color: #94a3b8;
            font-weight: 600;
            white-space: nowrap;
        }
        .srh-inc-title {
            font-size: 19px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.3px;
            margin: 4px 0 14px;
        }
        .srh-inc-routes {
            display: flex;
            flex-direction: column;
            gap: 0;
            margin-bottom: 18px;
        }
        .srh-inc-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            opacity: 0;
            animation: srhStaggerIn 0.55s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .srh-inc-row:nth-child(2) { animation-delay: 0.10s; }
        .srh-inc-row:nth-child(3) { animation-delay: 0.16s; }
        .srh-inc-line {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 10px;
            flex-shrink: 0;
            padding-top: 4px;
        }
        .srh-inc-line .srh-inc-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #16a34a;
            border: 2px solid #bbf7d0;
            flex-shrink: 0;
        }
        .srh-inc-line .srh-inc-dot.srh-inc-dot-red {
            background: #ef4444;
            border-color: #fecaca;
        }
        .srh-inc-line::after {
            content: '';
            flex: 1;
            width: 3px;
            min-height: 26px;
            background: linear-gradient(180deg, #bbf7d0, #fecaca);
            border-radius: 3px;
            margin: 4px 0;
        }
        .srh-inc-row:last-of-type .srh-inc-line::after {
            display: none;
        }
        .srh-inc-body {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 2px;
            padding: 2px 0 12px;
        }
        .srh-inc-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #94a3b8;
        }
        .srh-inc-address {
            font-size: 15px;
            font-weight: 600;
            color: #0f172a;
            line-height: 1.35;
            word-break: break-word;
        }
        .srh-inc-distance {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }
        .srh-inc-fare-wrap {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 14px;
            margin-bottom: 16px;
        }
        .srh-inc-fare-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 10px;
        }
        .srh-inc-fare-chips {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }
        .srh-fare-chip {
            border: 1.5px solid #cbd5e1;
            background: #ffffff;
            border-radius: 12px;
            padding: 9px 4px;
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            cursor: pointer;
            transition: all 0.18s ease;
        }
        .srh-fare-chip:active { transform: scale(0.96); }
        .srh-fare-chip.srh-fare-chip-active {
            border-color: #16a34a;
            background: #f0fdf4;
            color: #15803d;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.18);
        }
        .srh-inc-custom-fare {
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .srh-inc-custom-fare .srh-cf-peso {
            font-size: 14px;
            font-weight: 800;
            color: #64748b;
        }
        .srh-inc-custom-fare input {
            flex: 1;
            border: 1.5px solid #cbd5e1;
            border-radius: 12px;
            padding: 9px 12px;
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            outline: none;
            background: #ffffff;
            -moz-appearance: textfield;
            appearance: textfield;
        }
        .srh-inc-custom-fare input::-webkit-outer-spin-button,
        .srh-inc-custom-fare input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .srh-inc-custom-fare input:focus {
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.18);
        }
        .srh-inc-actions {
            display: flex;
            gap: 10px;
        }
        .srh-inc-send {
            flex: 1;
            background: #16a34a;
            color: #ffffff;
            border: none;
            border-radius: 14px;
            padding: 14px 10px;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.18s ease;
            box-shadow: 0 8px 20px rgba(22, 163, 74, 0.25);
        }
        .srh-inc-send:active { transform: scale(0.97); }
        .srh-inc-send.srh-inc-send-disabled,
        .srh-inc-send:disabled {
            background: #86efac;
            box-shadow: none;
            cursor: not-allowed;
        }
        .srh-inc-decline {
            background: #fef2f2;
            color: #b91c1c;
            border: 1.5px solid #fecaca;
            border-radius: 14px;
            padding: 14px 18px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.18s ease;
        }
        .srh-inc-decline:active { transform: scale(0.97); }
        .srh-inc-decline:disabled { opacity: 0.55; cursor: not-allowed; }

        /* Sent (waiting for passenger approval) state */
        .srh-inc-sent {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 8px 0 4px;
            animation: srhStaggerIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .srh-inc-sent-label {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            color: #64748b;
        }
        .srh-inc-sent-fare {
            font-size: 28px;
            font-weight: 800;
            color: #047857;
            margin: 6px 0;
            letter-spacing: -0.5px;
        }
        .srh-inc-wait-dots {
            display: inline-flex;
            gap: 5px;
            margin: 4px 0 10px;
        }
        .srh-inc-wait-dots span {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #16a34a;
            animation: srhWaitDot 1s ease-in-out infinite;
        }
        .srh-inc-wait-dots span:nth-child(2) { animation-delay: 0.18s; }
        .srh-inc-wait-dots span:nth-child(3) { animation-delay: 0.36s; }
        @@keyframes srhWaitDot {
            0%, 100% { opacity: 0.3; transform: translateY(0); }
            50%      { opacity: 1; transform: translateY(-4px); }
        }
        .srh-inc-sent-hint {
            font-size: 12.5px;
            color: #94a3b8;
            line-height: 1.45;
            max-width: 300px;
        }
        .srh-inc-sent-hint button {
            background: none;
            border: none;
            color: #b91c1c;
            font-weight: 700;
            text-decoration: underline;
            cursor: pointer;
            padding: 0 2px;
        }
        @@keyframes srhStaggerIn {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @media (min-width: 640px) {
            .srh-inc-card {
                left: 50%;
                right: auto;
                width: 420px;
                transform: translate3d(-50%, 115%, 0);
                border-radius: 28px;
                bottom: 26px;
            }
            .srh-inc-visible .srh-inc-card {
                transform: translate3d(-50%, 0, 0);
            }
            .srh-inc-overlay.srh-inc-hiding .srh-inc-card {
                transform: translate3d(-50%, 115%, 0);
            }
            .srh-inc-timer-wrap {
                top: 0;
                border-radius: 0 0 6px 6px;
            }
        }
    </style>

    @if (session('status'))
        <script>
            showDynamicStatus("{{ session('status') }}");
        </script>
    @endif

    @if (session('error'))
        <script>
            showDynamicError("{{ session('error') }}");
        </script>
    @endif

    <script>
        (function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('missed')) {
                showDynamicError("The passenger cancelled the request.");
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        })();
    </script>

    @php
        $driver = \App\Models\Driver::where('user_id', auth()->id())->first();
        
        // Active trip or returning status reference
        $activeAcceptedTrip = \App\Models\Ride::where('driver_id', auth()->id())
            ->whereIn('status', ['accepted', 'arrived', 'in_transit', 'returning'])
            ->latest('updated_at')
            ->first();

        $incomingRide = $incomingRide ?? \App\Models\Ride::where(function($query) {
            $query->where('status', 'searching')
                  ->orWhere(function($q) {
                      $q->where('status', 'fare_proposed')
                        ->where('driver_id', auth()->id());
                  });
        })->orderBy('created_at', 'asc')->first();

        $hasPendingOffer = $incomingRide && (int)$incomingRide->driver_id === (int)auth()->id() && $incomingRide->status === 'fare_proposed';
        $showOnline = (bool)($driver && $driver->is_online) || (bool)$activeAcceptedTrip || $hasPendingOffer;

        $isFirstInLine = $isFirstInLine ?? ($driver && $driver->queue_position === 1);
        $todayEarnings = \App\Models\Ride::where('driver_id', auth()->id())
            ->where('status', 'completed')
            ->whereDate('created_at', \Carbon\Carbon::today())
            ->sum('fare');
        $totalQueueCount = \App\Models\Driver::where('is_online', true)->count();
        $activeQueue = \App\Models\Driver::where('is_online', true)
            ->whereNotNull('queue_position')
            ->orderBy('queue_position', 'asc')
            ->get();
        $driversInTransit = \App\Models\Ride::with(['driver', 'passenger', 'driverProfile'])
            ->whereIn('status', ['fare_proposed', 'fare_accepted', 'accepted', 'arrived', 'in_transit', 'returning'])
            ->latest('updated_at')
            ->get();
        
        $userAvatar = auth()->user()->profile_photo_url;
        if (!$userAvatar) {
            $userAvatar = 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=2563eb&color=fff';
        }
    @endphp

    @if($driver && $driver->compliance_status === 'Suspended')
        <style>
            html, body {
                overflow-y: auto !important;
                overflow-x: hidden !important;
                height: auto !important;
            }
            #app-main-content {
                padding-bottom: 6rem !important;
                overflow-y: auto !important;
            }
        </style>

        {{-- RESPONSIVE STREAMLINED SUSPENDED DRIVER VIEW (FULL PAGE, NO MAP) --}}
        <div class="w-full bg-slate-50 min-h-[calc(100vh-60px)] sm:min-h-screen flex flex-col justify-center items-center px-3 sm:px-6 py-6 pb-36 sm:pb-24 overflow-y-auto my-auto">
            <div class="w-full max-w-2xl mx-auto my-auto space-y-4">
                
                <!-- Sleek Compact Banner Header -->
                <div class="bg-white border border-rose-200 rounded-3xl p-4 sm:p-6 shadow-xs relative overflow-hidden space-y-3 text-left">
                    <div class="flex items-center justify-between gap-3 border-b border-rose-100 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 border border-rose-200 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                            </div>
                            <div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-200/80 uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                    Account Suspended
                                </span>
                                <h3 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight mt-0.5">Your Driver Account is Suspended</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Official Suspension Reason -->
                    <div class="space-y-1">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-wider block">Official Suspension Reason</span>
                        <p class="text-xs sm:text-sm font-semibold text-slate-800 bg-rose-50/60 p-3.5 rounded-2xl border border-rose-200/60 leading-relaxed">
                            "{{ $driver->suspension_reason ?? 'Administrative Suspension by TODA Admin. Please submit an appeal below or visit the TODA office.' }}"
                        </p>
                    </div>
                </div>

                <!-- Reinstatement Appeal Section -->
                <div class="bg-white border border-slate-200/90 rounded-3xl p-4 sm:p-6 shadow-xs space-y-4 text-left">
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div>
                            <h4 class="text-base font-extrabold text-slate-900">Reinstatement Appeal</h4>
                            <p class="text-xs text-slate-500 font-medium">Provide explanation or attach supporting proof for TODA Admin review.</p>
                        </div>
                    </div>

                    @if($driver->appeal_status === 'Pending')
                        <div class="p-4 rounded-2xl bg-amber-50/90 border border-amber-200/90 space-y-3">
                            <div class="flex items-center gap-2 text-amber-900 font-black text-xs uppercase tracking-wider">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                                <span>Appeal Pending Review</span>
                            </div>

                            <div class="bg-white p-3 rounded-xl border border-amber-200/80 space-y-2">
                                <p class="text-xs font-semibold text-amber-950 italic leading-relaxed">"{{ $driver->appeal_message }}"</p>
                                
                                @if(is_array($driver->appeal_attachments) && count($driver->appeal_attachments) > 0)
                                    <div class="pt-2 border-t border-amber-100 space-y-1.5">
                                        <span class="text-[10px] font-black text-amber-900 uppercase tracking-wider block">Submitted Attachments ({{ count($driver->appeal_attachments) }}):</span>
                                        <div class="flex items-center gap-2 flex-wrap w-full max-w-full overflow-hidden">
                                            @foreach($driver->appeal_attachments as $att)
                                                <a href="{{ $att['url'] }}" onclick="openAttachmentPreviewModal('{{ $att['url'] }}', '{{ addslashes($att['name']) }}', event)" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 border border-amber-300 text-amber-950 rounded-xl text-xs font-black hover:bg-amber-100 transition shadow-2xs text-decoration-none cursor-pointer max-w-full overflow-hidden">
                                                    <span class="text-amber-600 shrink-0">📎</span>
                                                    <span class="truncate min-w-0 max-w-[140px] sm:max-w-[200px] shrink-1">{{ $att['name'] }}</span>
                                                    <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <p class="text-[11px] text-amber-700 font-medium">Submitted {{ $driver->appealed_at ? $driver->appealed_at->diffForHumans() : 'recently' }}. TODA Admin will review your appeal shortly.</p>
                        </div>
                    @else
                        <form action="{{ route('drivers.appeal') }}" method="POST" enctype="multipart/form-data" class="space-y-4" onsubmit="handleRideActionSubmit(event, this)" x-data="{ attachedFiles: [] }">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Your Appeal / Explanation <span class="text-rose-500">*</span></label>
                                
                                <div class="relative rounded-2xl border border-slate-300 bg-slate-50/50 focus-within:bg-white focus-within:border-blue-600 focus-within:ring-2 focus-within:ring-blue-500/20 transition-all p-3 space-y-2">
                                    <div x-show="attachedFiles.length > 0" class="flex items-center gap-1.5 flex-wrap pb-2 border-b border-slate-200/80 w-full max-w-full overflow-hidden">
                                        <template x-for="(file, idx) in attachedFiles" :key="idx">
                                            <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded-xl bg-blue-50 text-blue-900 border border-blue-200 text-xs font-extrabold shadow-2xs max-w-full overflow-hidden">
                                                <span class="text-blue-600 shrink-0">📎</span>
                                                <span class="truncate min-w-0 max-w-[110px] sm:max-w-[180px] shrink-1 text-[11px] sm:text-xs" x-text="file.name"></span>
                                                <button type="button" @click="attachedFiles.splice(idx, 1)" class="text-slate-400 hover:text-rose-600 font-bold ml-1 cursor-pointer shrink-0">&times;</button>
                                            </div>
                                        </template>
                                    </div>

                                    <textarea name="appeal_message" required aria-label="Appeal explanation message" rows="3" placeholder="Explain why your account should be reinstated or detail corrected documents/details..." class="w-full bg-transparent text-xs sm:text-sm font-medium text-slate-900 outline-none border-none resize-none p-0 focus:ring-0 leading-relaxed"></textarea>

                                    <div class="flex items-center justify-between pt-2 border-t border-slate-200/60">
                                        <button type="button" @click="$refs.appealFileInputMain.click()" title="Attach document or proof file" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-200/80 hover:bg-blue-600 hover:text-white text-slate-700 text-xs font-black transition cursor-pointer border-none group">
                                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                            <span>Attach Proof</span>
                                        </button>

                                        <span class="text-[10px] text-slate-400 font-medium">Images, PDF (Max 10MB)</span>

                                        <input x-ref="appealFileInputMain" type="file" name="appeal_attachments[]" multiple accept="image/*,.pdf,.doc,.docx" class="hidden" aria-label="Appeal file attachments" @change="attachedFiles = Array.from($event.target.files)">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-3.5 px-6 rounded-2xl font-black text-xs text-white bg-blue-600 hover:bg-blue-700 shadow-md transition active:scale-98 cursor-pointer border-none uppercase tracking-wider">
                                Submit Appeal to TODA Admin
                            </button>
                        </form>
                    @endif
                </div>

            </div>
        </div>
    @else
        {{-- GRAB DRIVER IMMERSIVE MAP HUB (100% Non-Scrollable Max-Height Viewport - Mobile 100dvh) --}}
        <div class="relative w-full overflow-hidden bg-[#f4f3f0] h-[calc(100dvh-3.5rem-env(safe-area-inset-bottom,0px))] sm:h-[calc(100dvh-4rem)] min-h-[calc(100dvh-3.5rem-env(safe-area-inset-bottom,0px))] sm:min-h-[calc(100dvh-4rem)] max-h-[calc(100dvh-3.5rem-env(safe-area-inset-bottom,0px))] sm:max-h-[calc(100dvh-4rem)]">

        {{-- Interactive Full-Screen Map Background --}}
        <div id="grab-home-map" class="absolute inset-0 z-0 w-full h-full bg-[#f4f3f0]"></div>

        {{-- MAP BACKDROP DIMMER OVERLAY (Smooth Dimming when Sheet Expands All the Way Up) --}}
        <div id="map-backdrop-dimmer" class="absolute inset-0 z-10 w-full h-full bg-slate-950/40 backdrop-blur-[2px] transition-opacity duration-200 pointer-events-none opacity-0 cursor-pointer" onclick="if(window.toggleDriverSheet) window.toggleDriverSheet()"></div>

        {{-- TOP FLOATING HEADER (Left: Single Profile Circle | Right: Rating Badge & Notifications) --}}
        <div class="absolute z-[100] pointer-events-none flex items-center justify-between gap-3 pt-safe" style="top: max(0.75rem, env(safe-area-inset-top, 0px)); left: 14px; right: 14px; width: calc(100% - 28px);">
            
            {{-- Left: Single Standalone Profile Circle --}}
            <a href="{{ route('profile.edit') }}" class="pointer-events-auto relative block w-11 h-11 rounded-full bg-white/95 backdrop-blur-md p-0.5 border-2 border-white shadow-xl hover:scale-105 active:scale-95 transition-all text-decoration-none group" title="Profile Settings">
                <div class="w-full h-full rounded-full bg-gradient-to-tr from-blue-600 to-blue-400 flex items-center justify-center overflow-hidden">
                    @if(Auth::user()->profile_photo_url)
                        <img src="{{ route('user.avatar', [Auth::user(), 'v' => optional(Auth::user()->updated_at)->timestamp]) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover rounded-full" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                        <span class="w-full h-full text-white font-black text-sm flex items-center justify-center bg-blue-600 hidden">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                    @else
                        <span class="text-white font-black text-sm">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                    @endif
                </div>
                <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white {{ ($driver && $driver->is_online) ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
            </a>

            {{-- Right Group: Rating Badge + Notifications --}}
            <div class="pointer-events-auto flex items-center gap-2">
                
                {{-- Separated Rating Badge --}}
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-full bg-white/95 backdrop-blur-md border border-slate-200/90 shadow-xl hover:bg-slate-50 active:scale-95 transition-all text-decoration-none cursor-pointer" title="Driver Rating">
                    <span class="text-xs font-black text-slate-800 tracking-tight">{{ number_format($driver->average_rating ?? 5.0, 1) }}</span>
                    <svg class="w-4 h-4 text-amber-400 fill-current drop-shadow-sm" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </a>

                {{-- Notifications Floating Button & Dropdown --}}
                <div x-data="notificationSystem()" @srh-close-notifications.window="open = false" class="relative">
                    <button @click.stop="toggleDropdown()" type="button" class="flex items-center justify-center w-11 h-11 rounded-full bg-white/95 backdrop-blur-md border border-slate-200/90 shadow-xl text-slate-700 hover:text-blue-600 active:scale-95 transition-all cursor-pointer relative" title="Notifications">
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
                         class="fixed left-3 right-3 sm:absolute sm:left-auto sm:right-0 top-16 sm:w-96 bg-white border border-gray-200 rounded-2xl shadow-2xl overflow-hidden z-[9999]"
                         x-cloak>
                        <div class="p-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                            <span class="font-black text-xs text-gray-900 uppercase">Announcements</span>
                            <button @click="markAllAsRead()" type="button" class="text-xs font-bold text-blue-600 hover:underline">Mark as read</button>
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

        </div>
        
        {{-- FLOATING POWER TOGGLE BUTTON (Grab Duty Toggle - HIDE ON ACTIVE TRIPS & WHEN SUSPENDED) --}}
        <div id="floating-power-container" class="absolute z-30 flex flex-col items-start gap-2 floating-action-btn" style="bottom: 295px; left: 16px; {{ ($activeAcceptedTrip || ($driver && $driver->compliance_status === 'Suspended')) ? 'display: none;' : '' }}">
            
            {{-- Tooltip Callout Bubble --}}
            <div class="relative bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-2xl border border-slate-200 shadow-lg grab-tooltip-arrow text-[11px] font-black text-slate-800 flex items-center gap-1.5 grab-callout-float">
                <span id="tooltip-status-dot" class="w-2 h-2 rounded-full {{ ($driver && $driver->is_online) ? 'bg-emerald-500 animate-pulse inline-block' : 'hidden' }}"></span>
                <span id="power-tooltip-text">{{ ($driver && $driver->is_online) ? 'Take a break?' : 'Go On Duty?' }}</span>
            </div>

            {{-- Power Toggle Button (Tactile Shrink Push Effect) --}}
            <button type="button" id="power-toggle-btn" onclick="toggleDutyStatus(this)" 
                    class="relative flex items-center justify-center w-16 h-16 rounded-full border-4 border-white shadow-2xl active:scale-85 hover:scale-105 transition-all duration-200 ease-out cursor-pointer {{ ($driver && $driver->is_online) ? 'bg-emerald-500 text-white shadow-emerald-500/40' : 'bg-slate-900 text-white shadow-slate-900/40' }}">
                <svg id="power-icon" class="w-8 h-8 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 5.636a9 9 0 11-12.728 0M12 3v9" />
                </svg>
                <svg id="power-spinner" class="w-7 h-7 animate-spin text-white hidden" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
            </button>
        </div>

        {{-- FLOATING MAP CONTROLS STACK (Bottom Right - Google Maps Unified Single Action Button) --}}
        <div id="floating-map-controls-container" class="absolute z-30 flex flex-col gap-2.5 floating-action-btn" style="bottom: 295px; right: 16px;">
            {{-- Google Maps Style Unified Single Map Button (State 0: Gray Off, State 1: Blue Recenter, State 2: White 2D Compass, State 3: Cyan 3D Compass) --}}
            <button type="button" id="srh-unified-map-btn" onclick="handleUnifiedMapButtonClick(homeMap, this)" class="w-11 h-11 rounded-2xl bg-white/95 text-slate-700 shadow-md border border-slate-200 flex items-center justify-center transition-all transform active:scale-90 cursor-pointer hover:bg-slate-50" title="Map Action Control">
                <svg class="w-6 h-6 text-slate-700 leading-none select-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><polygon points="12,6 15,15 12,13 9,15" fill="currentColor" stroke="none"/></svg>
            </button>
        </div>



        {{-- LIFE360 / GRAB COLLAPSIBLE SLIDING BOTTOM SHEET --}}
        <div class="absolute bottom-16 sm:bottom-4 left-0 right-0 z-20 pointer-events-none">
            
            @if($driver && $driver->compliance_status === 'Suspended')
                {{-- SUSPENDED ACCOUNT CARD & APPEAL SECTION --}}
                <div id="suspended-driver-wrapper" class="grab-glass-panel rounded-3xl p-5 sm:p-6 border-2 border-rose-500 text-left space-y-4 shadow-2xl max-w-md mx-auto pointer-events-auto bg-white/95 backdrop-blur-md">
                    <!-- Banner -->
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                        <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-200 shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                        </div>
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                Account Suspended
                            </span>
                            <h3 class="text-lg font-black text-slate-900 mt-0.5">Driver Duty Restricted</h3>
                        </div>
                    </div>

                    <!-- Suspension Reason Notice -->
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Official Suspension Reason</span>
                        <p class="text-xs font-semibold text-slate-800 bg-slate-50/90 p-3 rounded-xl border border-slate-200/80 leading-relaxed">
                            "{{ $driver->suspension_reason ?? 'Administrative Suspension by TODA Admin. Please submit an appeal below or visit the TODA office.' }}"
                        </p>
                    </div>

                    <!-- Appeal Section -->
                    <div class="pt-1">
                        @if($driver->appeal_status === 'Pending')
                            <div class="p-3.5 rounded-2xl bg-amber-50/90 border border-amber-200 space-y-1.5">
                                <div class="flex items-center gap-2 text-amber-900 font-bold text-xs">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span>Appeal Pending Review</span>
                                </div>
                                <p class="text-xs font-medium text-amber-950 italic leading-relaxed">"{{ $driver->appeal_message }}"</p>
                                <p class="text-[10px] text-amber-700 font-medium">Submitted {{ $driver->appealed_at ? $driver->appealed_at->diffForHumans() : 'recently' }}. TODA Admin will review your appeal shortly.</p>
                            </div>
                        @else
                            <div x-data="{ openAppeal: false }">
                                <template x-if="!openAppeal">
                                    <button type="button" @click="openAppeal = true" class="w-full py-3 px-4 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-md transition flex items-center justify-center gap-2 cursor-pointer border-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        <span>Submit Reinstatement Appeal</span>
                                    </button>
                                </template>

                                <div x-show="openAppeal" x-transition class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-sm space-y-3" x-data="{ attachedFilesMain: [] }">
                                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider">Appeal For Reinstatement</h4>
                                    <form action="{{ route('drivers.appeal') }}" method="POST" enctype="multipart/form-data" class="space-y-3" onsubmit="handleRideActionSubmit(event, this)">
                                        @csrf
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Your Appeal / Explanation <span class="text-rose-500">*</span></label>
                                            
                                            <div class="relative rounded-2xl border border-slate-300 bg-slate-50/50 focus-within:bg-white focus-within:border-rose-600 focus-within:ring-2 focus-within:ring-rose-500/20 transition-all p-3 space-y-2">
                                                <div x-show="attachedFilesMain.length > 0" class="flex items-center gap-1.5 flex-wrap pb-1 border-b border-slate-200/80">
                                                    <template x-for="(file, idx) in attachedFilesMain" :key="idx">
                                                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-rose-50 text-rose-900 border border-rose-200 text-[11px] font-bold">
                                                            <span class="text-rose-600">📎</span>
                                                            <span class="max-w-[120px] truncate" x-text="file.name"></span>
                                                            <button type="button" @click="attachedFilesMain.splice(idx, 1)" class="text-slate-400 hover:text-rose-600 font-bold ml-0.5 cursor-pointer">&times;</button>
                                                        </div>
                                                    </template>
                                                </div>

                                                <textarea name="appeal_message" required aria-label="Appeal explanation message" rows="3" placeholder="Explain your side, detail corrective actions, or request account reinstatement..." class="w-full bg-transparent text-xs font-medium text-slate-900 outline-none border-none resize-none p-0 focus:ring-0"></textarea>

                                                <div class="flex items-center justify-between pt-1 border-t border-slate-200/60">
                                                    <button type="button" @click="$refs.mainAppealFileInput.click()" title="Attach proof document" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-200/80 hover:bg-rose-600 hover:text-white text-slate-700 text-[11px] font-black transition cursor-pointer border-none group">
                                                        <svg class="w-3.5 h-3.5 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                                        </svg>
                                                        <span>Attach Proof</span>
                                                    </button>
                                                    <input x-ref="mainAppealFileInput" type="file" name="appeal_attachments[]" multiple accept="image/*,.pdf,.doc,.docx" class="hidden" aria-label="Appeal file attachments" @change="attachedFilesMain = Array.from($event.target.files)">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2 pt-1">
                                            <button type="button" @click="openAppeal = false" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                                                Cancel
                                            </button>
                                            <button type="submit" class="flex-1 py-2.5 px-4 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-extrabold text-xs rounded-xl shadow-sm transition border-none cursor-pointer">
                                                Submit Appeal
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            @elseif(false && $driver && $driver->is_online && $isFirstInLine && $incomingRide)
                {{-- INCOMING RIDE REQUEST DRAWER (Passenger Booking Found!) --}}
                {{-- INCOMING RIDE REQUEST TAKEOVER (Grab-style overlay, managed client-side by the overlay manager) --}}
                {{-- DISABLED SERVER-SIDE RENDER (2026-08-15): the ride is NEVER embedded
                     in page HTML anymore. A stale/cached copy of an HTML page that still
                     carried the ride was what re-hydrated the popup after a decline and
                     looped forever. The popup now comes 100% from the poller, so a page
                     can never resurrect a cancelled ride. --}}
                <div id="incoming-ride-wrapper" class="srh-inc-overlay"
                     data-ride-id="{{ $incomingRide->id }}"
                     data-ride-status="{{ $incomingRide->status }}"
                     data-ride-fare="{{ $incomingRide->fare ?? '' }}"
                     data-pickup-location="{{ $incomingRide->pickup_location }}"
                     data-destination="{{ $incomingRide->destination }}"
                     data-pickup-lat="{{ $incomingRide->pickup_lat ?? 15.4265 }}"
                     data-pickup-lng="{{ $incomingRide->pickup_lng ?? 120.9405 }}"
                     data-dest-lat="{{ $incomingRide->destination_lat ?? 15.4215 }}"
                     data-dest-lng="{{ $incomingRide->destination_lng ?? 120.9350 }}">
                    <div class="srh-inc-backdrop"></div>
                    <div id="incoming-ride-card" class="srh-inc-card">
                        <div id="incoming-ride-timer-wrap" class="srh-inc-timer-wrap"><div class="srh-timer-bar"></div></div>
                        <div id="incoming-ride-content"></div>
                    </div>
                    <div id="incoming-ride-marker" class="hidden" data-status="{{ $incomingRide->status }}" data-fare="{{ $incomingRide->fare ?? '' }}" data-pickup-lat="{{ $incomingRide->pickup_lat ?? 15.4265 }}" data-pickup-lng="{{ $incomingRide->pickup_lng ?? 120.9405 }}" data-dest-lat="{{ $incomingRide->destination_lat ?? 15.4215 }}" data-dest-lng="{{ $incomingRide->destination_lng ?? 120.9350 }}"></div>
                </div>
                <script>
                    (function () {
                        var tryHydrate = function () {
                            try {
                                var __srhHydrateRide = {!! Illuminate\Support\Js::from([
                                    'id' => $incomingRide->id,
                                    'status' => $incomingRide->status,
                                    'fare' => (float) ($incomingRide->fare ?? 0),
                                    'pickup_location' => $incomingRide->pickup_location,
                                    'destination' => $incomingRide->destination,
                                    'pickup_lat' => (float) ($incomingRide->pickup_lat ?? 15.4265),
                                    'pickup_lng' => (float) ($incomingRide->pickup_lng ?? 120.9405),
                                    'destination_lat' => (float) ($incomingRide->destination_lat ?? 15.4215),
                                    'destination_lng' => (float) ($incomingRide->destination_lng ?? 120.9350),
                                ]) !!};
                                if (window.srhShowIncomingOverlay) {
                                    if (window.__srhDbg && window.__srhDbg.push) window.__srhDbg.push('SERVER-HYDRATE-SCRIPT run ride=' + {{ $incomingRide->id }});
                                    window.srhShowIncomingOverlay(__srhHydrateRide, { hydrate: true });
                                }
                            } catch (e) {
                                if (window.console) { window.console.error('incoming overlay hydration error', e); }
                            }
                        };
                        if (window.srhShowIncomingOverlay) {
                            tryHydrate();
                        } else if (document.readyState === 'loading') {
                            document.addEventListener('DOMContentLoaded', tryHydrate);
                        } else {
                            window.addEventListener('load', tryHydrate);
                        }
                    })();
                </script>


            @else
                {{-- UNIFIED LIFE360 / GRAB COLLAPSIBLE SHEET CONTAINER --}}
                <div id="driver-bottom-sheet" class="fixed inset-x-0 top-0 z-[9999] srh-edge-bottom-sheet px-3 sm:px-6 pt-1.5 pb-16 shadow-2xl pointer-events-auto border-t border-slate-200/90 border-x-0 border-b-0 bg-white flex flex-col relative after:content-[''] after:absolute after:top-full after:inset-x-0 after:h-[1000px] after:bg-white after:pointer-events-none"
                     style="height: 100dvh; min-height: 100dvh; transform: translate3d(0, calc(100dvh - 280px), 0);"
                     data-ride-id="{{ $activeAcceptedTrip ? $activeAcceptedTrip->id : '' }}"
                     data-is-walkin="{{ ($activeAcceptedTrip && str_contains(strtolower($activeAcceptedTrip->pickup_location ?? ''), 'terminal')) ? 'true' : 'false' }}"
                     data-active-status="{{ $activeAcceptedTrip ? $activeAcceptedTrip->status : '' }}"
                     data-complete-return-url="{{ $activeAcceptedTrip ? route('rides.complete-return', $activeAcceptedTrip) : '' }}"
                     data-pickup-lat="{{ $activeAcceptedTrip ? ($activeAcceptedTrip->pickup_lat ?? '') : '' }}"
                     data-pickup-lng="{{ $activeAcceptedTrip ? ($activeAcceptedTrip->pickup_lng ?? '') : '' }}"
                     data-dest-lat="{{ $activeAcceptedTrip ? ($activeAcceptedTrip->destination_lat ?? '') : '' }}"
                     data-dest-lng="{{ $activeAcceptedTrip ? ($activeAcceptedTrip->destination_lng ?? '') : '' }}">
                    
                    {{-- TOP DRAG HANDLE BAR (Life360 / Grab Style - Tap & Touch Swipe Pullable) --}}
                    <div id="sheet-drag-handle" class="w-full pt-0.5 pb-1 flex flex-col items-center justify-center cursor-pointer touch-none select-none group pointer-events-auto">
                        <div class="w-12 h-1 bg-slate-300 rounded-full mb-1"></div>
                        
                        {{-- CONCISE COLLAPSED STATUS HEADER ROW --}}
                        <div class="w-full flex items-center justify-between px-1 gap-1 min-w-0">
                            <div class="flex items-center gap-1.5 min-w-0 flex-1">
                                <span id="status-indicator-dot" class="w-2.5 h-2.5 rounded-full shrink-0 {{ ($activeAcceptedTrip && $activeAcceptedTrip->status === 'returning') ? 'bg-amber-500 animate-pulse' : (($activeAcceptedTrip && $activeAcceptedTrip->status === 'arrived') ? 'bg-amber-500 animate-pulse' : (($activeAcceptedTrip && $activeAcceptedTrip->status === 'accepted' && $activeAcceptedTrip->passenger_id) ? 'bg-blue-500 animate-pulse' : ($activeAcceptedTrip ? 'bg-emerald-500 animate-pulse' : ($showOnline ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500')))) }}"></span>
                                <span id="status-indicator-text" class="text-xs sm:text-sm font-black uppercase tracking-tight text-slate-800 truncate">
                                    @if($activeAcceptedTrip && $activeAcceptedTrip->status === 'returning')
                                        RETURNING TO TERMINAL
                                    @elseif($activeAcceptedTrip && $activeAcceptedTrip->status === 'accepted' && $activeAcceptedTrip->passenger_id)
                                        EN ROUTE TO PICKUP
                                    @elseif($activeAcceptedTrip && $activeAcceptedTrip->status === 'arrived')
                                        ARRIVED AT PICKUP
                                    @elseif($activeAcceptedTrip && $activeAcceptedTrip->status === 'in_transit')
                                        IN TRANSIT TO DESTINATION
                                    @elseif($activeAcceptedTrip)
                                        ON ACTIVE RIDE
                                    @else
                                        {{ $showOnline ? "You're online" : "You're offline" }}
                                    @endif
                                </span>
                            </div>

                            {{-- Compact Status / Fare / Queue Badge --}}
                            <div class="flex items-center gap-1 shrink-0">
                                @if($activeAcceptedTrip && $activeAcceptedTrip->status === 'returning')
                                    <span id="compact-queue-badge" style="display: none !important;"></span>
                                @elseif($activeAcceptedTrip && $activeAcceptedTrip->status === 'accepted' && $activeAcceptedTrip->passenger_id)
                                    <span id="compact-queue-badge" class="px-2.5 py-0.5 sm:px-3 sm:py-1 bg-blue-600 text-white rounded-full text-[10px] sm:text-xs font-black uppercase tracking-wider shadow-sm shrink-0">
                                        PICKING UP
                                    </span>
                                @elseif($activeAcceptedTrip && $activeAcceptedTrip->status === 'arrived')
                                    <span id="compact-queue-badge" class="px-2.5 py-0.5 sm:px-3 sm:py-1 bg-amber-500 text-white rounded-full text-[10px] sm:text-xs font-black uppercase tracking-wider shadow-sm shrink-0">
                                        ARRIVED
                                    </span>
                                @elseif($activeAcceptedTrip && $activeAcceptedTrip->status === 'in_transit')
                                    <span id="compact-queue-badge" class="px-2.5 py-0.5 sm:px-3 sm:py-1 bg-emerald-600 text-white rounded-full text-[10px] sm:text-xs font-black uppercase tracking-wider shadow-sm shrink-0">
                                        IN TRANSIT
                                    </span>
                                @elseif($activeAcceptedTrip)
                                    <span id="compact-queue-badge" class="px-2.5 py-0.5 sm:px-3 sm:py-1 bg-emerald-600 text-white rounded-full text-[10px] sm:text-xs font-black uppercase tracking-wider shadow-sm shrink-0">
                                        ACTIVE RIDE
                                    </span>
                                @elseif($showOnline)
                                    <span id="compact-queue-badge" class="px-2.5 py-0.5 sm:px-3 sm:py-1 text-white rounded-full text-[10px] sm:text-xs font-black uppercase tracking-wider shadow-sm flex items-center gap-1 shrink-0" style="background: {{ (int)($driver->queue_position ?? 0) === 1 ? 'linear-gradient(to right, #10b981, #0d9488, #2563eb)' : 'linear-gradient(to right, #60a5fa, #3b82f6, #1d4ed8)' }};">
                                        #{{ $driver->queue_position }} IN QUEUE
                                    </span>
                                @else
                                    <span id="compact-queue-badge" class="px-2.5 py-0.5 sm:px-3 sm:py-1 bg-slate-200 text-slate-600 rounded-full text-[10px] sm:text-xs font-black uppercase tracking-wider shrink-0">
                                        OFF DUTY
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- COLLAPSIBLE DETAILS CONTENT --}}
                    <div id="sheet-details-content" class="flex-1 pb-36 overscroll-contain w-full" style="opacity: 1; margin-top: 6px; pointer-events: auto; overflow-y: hidden; overflow-x: hidden;">
                        
                        {{-- PROMINENT QUEUE / ACTIVE RIDE DISPLAY CARD WRAPPER --}}
                        <div id="queue-card-container" class="sticky top-0 z-30 bg-white pt-1 pb-2 shadow-xs rounded-2xl" data-is-online="{{ $showOnline ? 'true' : 'false' }}" data-position="{{ (int)($driver->queue_position ?? 1) }}">
                                @if($activeAcceptedTrip && $activeAcceptedTrip->status === 'returning')
                                    {{-- RETURNING TO TERMINAL UI CARD --}}
                                    <div style="background: #064e3b !important;" class="rounded-2xl p-4 text-center shadow-lg relative overflow-hidden sheet-content-animate-in">
                                        <div class="flex items-center justify-center gap-2 mb-1">
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                                            <p style="color: #a7f3d0 !important;" class="text-[10px] sm:text-[11px] font-black uppercase tracking-widest">Heading Back to TODA Terminal</p>
                                        </div>
                                        <h3 style="color: #ffffff !important;" class="text-base sm:text-lg font-black tracking-tight mt-1 mb-3">Auto-Joining Queue On Arrival</h3>

                                        {{-- Pick Up Wayside Passenger Action Button --}}
                                        <button type="button" onclick="openTerminalWalkInModal('wayside', event)" class="w-full py-3 px-4 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-white font-black text-xs uppercase tracking-wider shadow-md active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer border border-emerald-400/30">
                                            <svg class="w-4 h-4 text-white fill-none stroke-current" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                            <span>+ Add Wayside Passenger</span>
                                        </button>
                                    </div>
                                @elseif($activeAcceptedTrip)
                                    {{-- BESPOKE HIGH-FINISH ACTIVE RIDE CONTROLS --}}
                                    <div class="space-y-3 text-left sheet-content-animate-in">
                                        @if($activeAcceptedTrip->status === 'accepted' && $activeAcceptedTrip->passenger_id)
                                            {{-- 1. En Route to Pickup: I Have Arrived Button --}}
                                            <button type="button" onclick="submitRideAction('{{ route('rides.arrived', $activeAcceptedTrip) }}', 'PATCH', {}, this, event)" style="background: #2563eb !important; color: #ffffff !important;" class="w-full py-3.5 px-4 rounded-2xl font-black text-xs uppercase tracking-wider shadow-md shadow-blue-600/25 active:scale-[0.98] transition-all flex items-center justify-center gap-2.5 cursor-pointer">
                                                <span class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                                                    <svg class="w-3.5 h-3.5 text-white fill-none stroke-current" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                </span>
                                                <span style="color: #ffffff !important;" class="font-black">I Have Arrived at Pickup</span>
                                            </button>
                                        @elseif($activeAcceptedTrip->status === 'arrived')
                                            {{-- 2. Arrived at Pickup: Start Transit Button --}}
                                            <button type="button" onclick="submitRideAction('{{ route('rides.start-transit', $activeAcceptedTrip) }}', 'PATCH', {}, this, event)" style="background: #059669 !important; color: #ffffff !important;" class="w-full py-3.5 px-4 rounded-2xl font-black text-xs uppercase tracking-wider shadow-md shadow-emerald-600/25 active:scale-[0.98] transition-all flex items-center justify-center gap-2.5 cursor-pointer">
                                                <span class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                                                    <svg class="w-3.5 h-3.5 text-white fill-none stroke-current" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                </span>
                                                <span style="color: #ffffff !important;" class="font-black">Start Transit / Picked Up</span>
                                            </button>
                                        @else
                                            {{-- 3. In Transit (or Walk-In): Drop Off & Return to Queue Button --}}
                                            <button type="button" onclick="submitRideAction('{{ route('rides.start-returning', $activeAcceptedTrip) }}', 'PATCH', {}, this, event)" style="background: #0f172a !important; color: #ffffff !important;" class="w-full py-3.5 px-4 rounded-2xl font-black text-xs uppercase tracking-wider shadow-md shadow-slate-900/10 active:scale-[0.98] transition-all flex items-center justify-center gap-2.5 cursor-pointer">
                                                <span class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0">
                                                    <svg class="w-3.5 h-3.5 text-emerald-400 fill-none stroke-current" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                </span>
                                                <span style="color: #ffffff !important;" class="font-black">Drop Off & Return to Queue</span>
                                            </button>
                                        @endif

                                        {{-- Companion Chat Passenger Action Button (Visible during active passenger trips) --}}
                                        @if($activeAcceptedTrip && $activeAcceptedTrip->passenger_id)
                                            @php
                                                $tripPax = $activeAcceptedTrip->passenger;
                                                $tripPaxName = $tripPax ? $tripPax->name : 'Passenger';
                                                $tripPaxAvatar = ($tripPax && $tripPax->profile_photo_url) ? route('user.avatar', [$tripPax->id, 'v' => optional($tripPax->updated_at)->timestamp]) : '';
                                            @endphp
                                            <script>
                                                window._srhActiveChatRideId = {{ (int) $activeAcceptedTrip->id }};
                                            </script>
                                            <div class="pt-0.5">
                                                <button type="button" onclick="openRideChatModal({{ $activeAcceptedTrip->id }}, '{{ addslashes($tripPaxName) }}', '{{ $tripPaxAvatar }}', 'Passenger')" class="relative w-full py-3 px-4 rounded-2xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center gap-2 transition-colors cursor-pointer border border-blue-200 shadow-xs active:scale-[0.98]">
                                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                                    <span>Chat Passenger</span>
                                                    <span class="srh-chat-unread-badge hidden absolute -top-1.5 -right-1.5 px-2 py-0.5 rounded-full bg-rose-600 text-white font-black text-[10px] shadow-sm animate-bounce">1</span>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    {{-- UNIFIED DUTY STATUS STACK: BOTH CARDS MOUNTED, CSS CROSSFADES ON data-online --}}
                                    @php
                                        $queued = $showOnline;
                                        $pos = $queued ? (int)($driver->queue_position ?? 1) : 1;
                                        $ends = ['th','st','nd','rd','th','th','th','th','th','th'];
                                        if ((($pos % 100) >= 11) && (($pos % 100) <= 13)) {
                                            $ordStr = $pos . 'th';
                                        } else {
                                            $ordStr = $pos . $ends[$pos % 10];
                                        }
                                        $queueSubtext = ($pos === 1) ? "Next for TODA terminal & app passenger dispatch!" : ($ordStr . " for TODA terminal & app passenger dispatch!");
                                    @endphp
                                    <div id="queue-card-states" class="relative rounded-2xl" data-online="{{ $queued ? 'true' : 'false' }}" data-position="{{ $queued ? $pos : 0 }}">
                                        <div id="online-queue-card" class="text-white rounded-2xl p-3.5 sm:p-4 text-center shadow-lg shadow-emerald-500/25 overflow-hidden flex flex-col items-center justify-center" data-position="{{ $pos }}" data-theme="{{ $pos === 1 ? 'now' : 'wait' }}">
                                            <div class="queue-theme-layer queue-theme-now"></div>
                                            <div class="queue-theme-layer queue-theme-wait"></div>
                                            <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-white/10 rounded-full blur-lg pointer-events-none z-10"></div>
                                            <div class="relative z-10 w-full">
                                            <p class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-emerald-100 mb-1">Queue Position</p>
                                            <h1 id="queue-position-heading" class="text-2xl xs:text-3xl sm:text-5xl font-black tracking-tight my-0.5 truncate">#{{ $pos }} IN QUEUE</h1>
                                            <p id="queue-position-subtext" class="text-[10px] sm:text-xs font-bold text-emerald-50 truncate mt-1">{{ $queueSubtext }}</p>

                                            {{-- Terminal Walk-In Action Button (Visible for all queue positions, prompts if not #1) --}}
                                            <button type="button" id="start-walkin-btn" onclick="handleTerminalWalkInClick(event)" class="w-full flex items-center justify-center gap-2 p-2.5 sm:p-3 rounded-2xl bg-white hover:bg-emerald-50 text-emerald-900 border border-emerald-200/80 active:scale-95 transition cursor-pointer mt-2.5 sm:mt-3 shadow-sm">
                                                <span class="text-[11px] sm:text-xs font-black text-emerald-900 tracking-tight truncate">Start Terminal Walk-In Ride</span>
                                            </button>
                                            </div>
                                        </div>
<div id="offline-queue-card" style="background: linear-gradient(150deg, #334155 0%, #1e293b 45%, #0f172a 100%) !important; color: #ffffff !important;" class="w-full max-w-full rounded-2xl p-3.5 sm:p-4 text-center shadow-xl shadow-2xl overflow-hidden flex flex-col items-center justify-center border border-slate-700">
                                        <div class="absolute -right-4 -bottom-4 w-20 h-20 rounded-full blur-lg pointer-events-none z-10" style="background: rgba(148, 163, 184, 0.1) !important;"></div>
                                            <div class="relative z-10 w-full">
                                                <p style="color: #cbd5e1 !important;" class="w-full text-[9px] sm:text-[10px] font-black uppercase tracking-widest mb-1">Driver Status</p>
                                                <h1 style="color: #ffffff !important;" class="w-full text-2xl xs:text-3xl sm:text-5xl font-black tracking-tight my-0.5 truncate">YOU'RE OFFLINE</h1>
<p style="color: #94a3b8 !important;" class="w-full text-[10px] sm:text-xs font-bold truncate mt-1" title="Go online to enter TODA queue & receive passenger requests!">Go online to enter TODA queue & receive passenger requests!</p>
<div class="w-full flex items-center justify-center gap-2 p-2.5 sm:p-3 rounded-2xl bg-transparent text-white/50 mt-2.5 sm:mt-3">
                                    <span class="text-[11px] sm:text-xs font-black text-white/50 tracking-tight truncate">Toggle the power button to go on duty</span>
                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- HARDWARE CLIPPING CONTAINER (Guarantees zero overlap above status card) --}}
                            <div id="live-queue-clip-container" class="{{ ($driver && $driver->is_online) ? 'is-expanded' : '' }}">
                                {{-- EMBEDDED LIVE TERMINAL QUEUE LIST --}}
                                <div id="embedded-live-queue-wrapper" class="w-full overflow-x-hidden border-t border-slate-100 text-left space-y-3 pt-3">
                                    @include('drivers.partials.queue-list')
                                </div>
                            </div>

                    </div>

                </div>
            @endif

        </div>

    </div>

    {{-- TERMINAL WALK-IN FARE MODAL (Smooth Fade Dismiss) --}}
    {{-- Outer wrapper: fade only, NO transforms, NO backdrop-filter (avoids GPU UV-flip bug) --}}
    <div id="walkin-fare-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 transition-opacity duration-300 opacity-0">

        {{-- Backdrop layer: plain dark dim, no blur --}}
        <div class="absolute inset-0 bg-slate-900/60" aria-hidden="true"></div>

        {{-- Content panel: scale animation here only, relative so it sits above the backdrop --}}
        <div class="relative grab-glass-panel max-w-sm w-full rounded-3xl p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-black text-slate-900">Start Terminal Walk-In Ride</h3>
                <button type="button" onclick="closeTerminalWalkInModal()" class="text-slate-400 hover:text-slate-600 font-black text-lg p-1">&times;</button>
            </div>

            <div class="space-y-4">
                <div class="space-y-1.5 text-left">
                    <label for="terminal_fare" class="text-xs font-black text-slate-700 uppercase tracking-wider">Base Terminal Fare Rate</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-blue-600 font-black text-xl">₱</span>
                        <input type="number" step="0.50" min="1" max="500" name="fare" id="terminal_fare" value="30.00" oninput="if(parseFloat(this.value)>500) this.value=500;" class="w-full py-3 pl-10 pr-4 bg-white rounded-2xl border border-slate-300 font-black text-slate-900 text-2xl tracking-tight shadow-xs">
                    </div>
                </div>

                <div class="flex items-center justify-between gap-1.5">
                    <button type="button" onclick="setFare('terminal_fare', '30.00', this)" class="flex-1 py-2 bg-blue-600 text-white rounded-xl text-xs font-black border border-blue-600 transition">₱30</button>
                    <button type="button" onclick="setFare('terminal_fare', '50.00', this)" class="flex-1 py-2 bg-slate-100 hover:bg-blue-600 hover:text-white rounded-xl text-xs font-black text-slate-700 border border-slate-200 transition">₱50</button>
                    <button type="button" onclick="setFare('terminal_fare', '70.00', this)" class="flex-1 py-2 bg-slate-100 hover:bg-blue-600 hover:text-white rounded-xl text-xs font-black text-slate-700 border border-slate-200 transition">₱70</button>
                    <button type="button" onclick="setFare('terminal_fare', '100.00', this)" class="flex-1 py-2 bg-slate-100 hover:bg-blue-600 hover:text-white rounded-xl text-xs font-black text-slate-700 border border-slate-200 transition">₱100</button>
                </div>

                <button type="button" onclick="handleWalkInSubmit(event)" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-sm uppercase tracking-wider shadow-lg shadow-blue-600/30 transition cursor-pointer">
                    Start Trip Now
                </button>
            </div>
        </div>
    </div>


    {{-- SCRIPTS FOR GRAB-STYLE HOME MAP & DUTY TOGGLE --}}
    <script>
        (function() {
            const TERMINAL_LAT = 15.429550175641715;
            const TERMINAL_LNG = 120.92240292427664;
            let homeMap = null;
            let driverMarker = null;

            function centerDriverOnTricycle(duration = 550) {
                if (window._isMapCameraAnimating) return;
                const targetMap = homeMap || window._grabHomeMapInstance;
                if (!targetMap || typeof targetMap.easeTo !== 'function') return;
                const curLat = driverMarker ? driverMarker.getLngLat().lat : (parseFloat(localStorage.getItem('srh_simulated_lat')) || TERMINAL_LAT);
                const curLng = driverMarker ? driverMarker.getLngLat().lng : (parseFloat(localStorage.getItem('srh_simulated_lng')) || TERMINAL_LNG);
                
                const currentPitch = targetMap.getPitch();
                const currentBearing = targetMap.getBearing();
                const currentZoom = Math.max(targetMap.getZoom() || 17.5, 16.5);

                window._driverMapManualPan = false;
                targetMap.easeTo({
                    center: [curLng, curLat],
                    zoom: currentZoom,
                    pitch: currentPitch,
                    bearing: currentBearing,
                    duration: duration,
                    easing: function(t) { return t * (2 - t); }
                });
            }
            window.centerDriverOnTricycle = centerDriverOnTricycle;

            function syncHomeMapPadding() {
                if (window._isMapCameraAnimating) return;
                if (!homeMap || typeof window.srhSetMapPadding !== 'function') return;
                const sheetContainer = document.getElementById('active-trip-wrapper') || document.getElementById('driver-bottom-sheet') || document.getElementById('incoming-ride-wrapper');
                const mapHubEl = document.getElementById('grab-home-map');
                const mapHub = mapHubEl ? mapHubEl.parentElement : null;
                const minSafePad = (window.innerWidth < 640) ? 76 : 84;
                let top = 0;
                if (window._hasActivePathwayMode) {
                    top = Math.round(window.innerHeight * 0.22);
                    bottom = 0;
                } else if (sheetContainer && mapHub) {
                    try {
                        const sheetRect = sheetContainer.getBoundingClientRect();
                        if (sheetRect.height > 0 && sheetRect.width > 0) {
                            const mapRect = mapHub.getBoundingClientRect();
                            const sheetTopFromMapBottom = mapRect.bottom - sheetRect.top;
                            if (sheetTopFromMapBottom > 0) {
                                bottom = Math.max(minSafePad, Math.round(sheetTopFromMapBottom + 12));
                            }
                        }
                    } catch(e) {}
                }
                if (typeof homeMap.setPadding === 'function') {
                    homeMap.setPadding({ top: top, bottom: bottom, left: 0, right: 0 });
                }
            }
            window.syncHomeMapPadding = syncHomeMapPadding;

            function initGrabHomeMap() {
                window.initGrabHomeMap = initGrabHomeMap;
                const el = document.getElementById('grab-home-map');
                if (!el) return;
                // Keep-Alive Vault guard: when the hub is parked off-screen during SPA
                // tab switches, stale global spa:page-loaded listeners still fire this
                // function. Rebuilding here would destroy the preserved WebGL map and
                // re-download every tile. The parked map is already fully alive.
                if (el.closest('#srh-spa-vault')) return;
                if (typeof maplibregl === 'undefined' || typeof window.srhCreateMap !== 'function') {
                    if (window.srhEnsureMaplibre && typeof window.srhEnsureMaplibre === 'function') {
                        window.srhEnsureMaplibre(initGrabHomeMap);
                    } else {
                        setTimeout(initGrabHomeMap, 1000);
                    }
                    return;
                }

                const simLat = localStorage.getItem('srh_simulated_lat');
                const simLng = localStorage.getItem('srh_simulated_lng');
                const cachedRealLat = localStorage.getItem('srh_last_real_lat');
                const cachedRealLng = localStorage.getItem('srh_last_real_lng');
                const driverDbLat = {{ $driver && $driver->current_latitude ? $driver->current_latitude : 'null' }};
                const driverDbLng = {{ $driver && $driver->current_longitude ? $driver->current_longitude : 'null' }};

                let initialLat = simLat ? parseFloat(simLat) : 
                                 (cachedRealLat ? parseFloat(cachedRealLat) : 
                                 ((driverDbLat !== null && Math.abs(driverDbLat - TERMINAL_LAT) > 0.0001) ? parseFloat(driverDbLat) : TERMINAL_LAT));

                let initialLng = simLng ? parseFloat(simLng) : 
                                 (cachedRealLng ? parseFloat(cachedRealLng) : 
                                 ((driverDbLng !== null && Math.abs(driverDbLng - TERMINAL_LNG) > 0.0001) ? parseFloat(driverDbLng) : TERMINAL_LNG));

                const initialZoom = {{ $activeAcceptedTrip ? 18.5 : 18.2 }};

                if (window.driverMarker) {
                    try { window.driverMarker.remove(); } catch(e){}
                    window.driverMarker = null;
                }
                if (window._grabHomeMapInstance) {
                    try { window._grabHomeMapInstance.remove(); } catch(e){}
                    window._grabHomeMapInstance = null;
                }
                el.innerHTML = '';

                // Frame the camera with the correct bottom padding BEFORE the map
                // boots, so the initial view is final from the very first frame
                // (a post-load setPadding would otherwise animate the camera and
                // make the map look like it's dropping down).
                const mapSheet = document.getElementById('active-trip-wrapper') || document.getElementById('driver-bottom-sheet') || document.getElementById('incoming-ride-wrapper');
                let initialPaddingBottom = (window.innerWidth < 640) ? 260 : 180;
                if (mapSheet) {
                    try {
                        const sheetRect = mapSheet.getBoundingClientRect();
                        if (sheetRect.height > 0 && sheetRect.width > 0) {
                            const hubRect = el.parentElement ? el.parentElement.getBoundingClientRect() : document.body.getBoundingClientRect();
                            const sheetTopFromMapBottom = hubRect.bottom - sheetRect.top;
                            if (sheetTopFromMapBottom > 0) {
                                initialPaddingBottom = Math.max((window.innerWidth < 640 ? 260 : 180), Math.round(sheetTopFromMapBottom + 12));
                            }
                        }
                    } catch(e) {}
                }
                initialPaddingBottom = Math.min(initialPaddingBottom, Math.round(window.innerHeight / 2));

                homeMap = window.srhCreateMap(el, {
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
                if (!homeMap) return;
                window.homeMap = homeMap;
                window._grabHomeMapInstance = homeMap;

                if (typeof window.syncHomeMapPadding === 'function') window.syncHomeMapPadding();
                try {
                    if (!window._isMapCameraAnimating) {
                        homeMap.jumpTo({ center: [initialLng, initialLat], zoom: initialZoom });
                    }
                } catch(e) {}

                ['touchstart', 'click', 'dragstart', 'movestart'].forEach(ev => {
                    homeMap.on(ev, (e) => {
                        // Ignore programmatic camera movements (e.g. GPS updates, auto-center, camera easing)
                        if ((ev === 'movestart' || ev === 'move') && e && !e.originalEvent) {
                            return;
                        }
                        if (window.closeAnnouncementModal) window.closeAnnouncementModal();
                        window.dispatchEvent(new CustomEvent('srh-close-notifications'));
                    });
                });

                if (typeof syncHomeMapPadding === 'function') syncHomeMapPadding();

                window.getOpticalCenterLatLng = function(lat, lng, zoom) {
                    return { lat: parseFloat(lat), lng: parseFloat(lng) };
                };

                function applyInitialVisibleCenter(lat, lng) {
                    if (!homeMap) return;
                    const targetLatLng = window.getOpticalCenterLatLng(lat, lng, initialZoom);
                    const targetLat = typeof targetLatLng.lat === 'number' && !isNaN(targetLatLng.lat) ? targetLatLng.lat : (Array.isArray(targetLatLng) ? targetLatLng[0] : parseFloat(lat));
                    const targetLng = typeof targetLatLng.lng === 'number' && !isNaN(targetLatLng.lng) ? targetLatLng.lng : (Array.isArray(targetLatLng) ? targetLatLng[1] : parseFloat(lng));
                    homeMap.jumpTo({ center: [targetLng, targetLat], zoom: initialZoom });
                }

                applyInitialVisibleCenter(initialLat, initialLng);

                // Terminal Marker
                const terminalEl = document.createElement('div');
                terminalEl.innerHTML = `<div style="display:flex;flex-direction:column;align-items:center;">
                    <div style="background:#0f172a;color:#ffffff;font-size:11px;font-weight:800;padding:4px 10px;border-radius:999px;border:1.5px solid #3b82f6;white-space:nowrap;display:flex;align-items:center;gap:6px;box-shadow:0 4px 14px rgba(15,23,42,0.4);font-family:system-ui,-apple-system,sans-serif;letter-spacing:-0.2px;">
                        <svg style="width:14px;height:14px;fill:#60a5fa;flex-shrink:0;" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5-2.5z"/></svg>
                        <span>SRH TODA Terminal</span>
                    </div>
                    <div style="width:10px;height:10px;background:#2563eb;border:2px solid #ffffff;border-radius:50%;margin-top:2px;box-shadow:0 2px 6px rgba(0,0,0,0.3);"></div>
                </div>`;
                new maplibregl.Marker({ element: terminalEl.firstElementChild, offset: [0, -18], pitchAlignment: 'viewport', rotationAlignment: 'viewport' })
                    .setLngLat([TERMINAL_LNG, TERMINAL_LAT])
                    .addTo(homeMap);

                // Native WebGL 35m Geographic Terminal Boundary Circle (Never shrinks on 3D tilt/pitch)
                function createTerminalGeoJSONCircle(centerLng, centerLat, radiusMeters, points = 64) {
                    const coords = [];
                    const km = radiusMeters / 1000;
                    const distanceX = km / (111.320 * Math.cos(centerLat * Math.PI / 180));
                    const distanceY = km / 110.574;

                    for (let i = 0; i < points; i++) {
                        const theta = (i / points) * (2 * Math.PI);
                        const x = distanceX * Math.cos(theta);
                        const y = distanceY * Math.sin(theta);
                        coords.push([centerLng + x, centerLat + y]);
                    }
                    coords.push(coords[0]);

                    return {
                        type: 'Feature',
                        geometry: {
                            type: 'Polygon',
                            coordinates: [coords]
                        },
                        properties: {}
                    };
                }

                function initTerminalGeofenceCircle() {
                    if (!homeMap) return;
                    const circleGeoJSON = createTerminalGeoJSONCircle(TERMINAL_LNG, TERMINAL_LAT, 35);

                    if (homeMap.getSource('terminal-geofence-source')) {
                        homeMap.getSource('terminal-geofence-source').setData(circleGeoJSON);
                        return;
                    }

                    homeMap.addSource('terminal-geofence-source', {
                        type: 'geojson',
                        data: circleGeoJSON
                    });

                    homeMap.addLayer({
                        id: 'terminal-geofence-fill',
                        type: 'fill',
                        source: 'terminal-geofence-source',
                        paint: {
                            'fill-color': '#3b82f6',
                            'fill-opacity': 0.12
                        }
                    });

                    homeMap.addLayer({
                        id: 'terminal-geofence-line',
                        type: 'line',
                        source: 'terminal-geofence-source',
                        paint: {
                            'line-color': '#2563eb',
                            'line-width': 2,
                            'line-dasharray': [3, 3],
                            'line-opacity': 0.95
                        }
                    });
                }

                if (window.srhStyleIsReady ? window.srhStyleIsReady(homeMap) : homeMap.isStyleLoaded()) {
                    initTerminalGeofenceCircle();
                } else {
                    homeMap.on('load', initTerminalGeofenceCircle);
                    homeMap.on('styledata', initTerminalGeofenceCircle);
                }

                // Driver Position Marker (Precision Blue Dot + Directional Facing Glow Beam)
                const isDriverOnline = {{ ($driver && $driver->is_online) ? 'true' : 'false' }};
                const initialGlowDisplay = isDriverOnline ? 'block' : 'none';
                const driverEl = document.createElement('div');
                driverEl.id = 'driver-live-map-marker';
                driverEl.style.width = '70px';
                driverEl.style.height = '70px';
                driverEl.style.position = 'relative';
                driverEl.style.zIndex = '99999';
                driverEl.style.pointerEvents = 'none';
                driverEl.innerHTML = `
                    <!-- Pulsing GPS Aura Ring (Centered at 35px, 35px) -->
                    <div id="driver-marker-ping" style="position:absolute;top:13px;left:13px;width:44px;height:44px;border-radius:50%;background:rgba(37,99,235,0.22);border:2px solid rgba(59,130,246,0.6);animation:ping 2s cubic-bezier(0,0,0.2,1) infinite;pointer-events:none;display:${initialGlowDisplay};"></div>
                    
                    <!-- Directional Facing Glow Beam (Rotates 360° strictly around center 35px, 35px) -->
                    <div id="driver-tricycle-marker-img" style="position:absolute;top:0;left:0;width:70px;height:70px;pointer-events:none;transform:rotate(0deg);transform-origin:35px 35px;will-change:transform;z-index:5;">
                        <svg viewBox="0 0 70 70" style="width:70px;height:70px;position:absolute;top:0;left:0;overflow:visible;pointer-events:none;">
                            <defs>
                                <radialGradient id="beamGradient" cx="35" cy="35" r="35" fx="35" fy="35" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.8"/>
                                    <stop offset="55%" stop-color="#60a5fa" stop-opacity="0.35"/>
                                    <stop offset="100%" stop-color="#3b82f6" stop-opacity="0"/>
                                </radialGradient>
                            </defs>
                            <!-- 60° Directional Facing Glow Cone Beam emanating from (35,35) -->
                            <path d="M 35 35 L 14 3 A 38 38 0 0 1 56 3 Z" fill="url(#beamGradient)"/>
                            <!-- Directional Pointer Arrow Notch -->
                            <polygon points="35,15 30,23 40,23" fill="#2563eb" opacity="0.95"/>
                        </svg>
                    </div>

                    <!-- Central High-Precision Blue Location Dot (Fixed at center 35px, 35px) -->
                    <div style="position:absolute;top:26px;left:26px;width:18px;height:18px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#1d4ed8);border:3px solid #ffffff;box-shadow:0 3px 10px rgba(0,0,0,0.35), 0 0 10px rgba(37,99,235,0.6);pointer-events:none;z-index:10;"></div>
                `;

                window.setDriverMarkerOpacity = function(m, opacity) {
                    if (m && typeof m.getElement === 'function') {
                        m.getElement().style.opacity = opacity;
                    }
                };
                const setDriverMarkerOpacity = window.setDriverMarkerOpacity;

                function handleDriverLocationChanged(dLat, dLng) {
                    if (typeof updateActiveTripRoute === 'function') {
                        updateActiveTripRoute(dLat, dLng);
                    }
                    if (typeof checkAutoTerminalGeofenceReturn === 'function') {
                        checkAutoTerminalGeofenceReturn(dLat, dLng);
                    }
                }

                if (window.driverMarker) {
                    try { window.driverMarker.remove(); } catch(e){}
                }
                driverMarker = new maplibregl.Marker({ element: driverEl, draggable: false, pitchAlignment: 'map', rotationAlignment: 'map' })
                    .setLngLat([initialLng, initialLat])
                    .addTo(homeMap);
                window.driverMarker = driverMarker;
                setDriverMarkerOpacity(driverMarker, 1);

                // 🎯 Active trip PICKUP / DESTINATION pins — read from the sheet's runtime
                // data attrs so the map reflects trips even after SPA drawer swaps (no reload).
                window.drawActiveTripPins = function() {
                    const map = homeMap || window._grabHomeMapInstance;
                    (window._srhActivePins || []).forEach(p => { try { p.remove(); } catch(e){} });
                    window._srhActivePins = [];

                    const incomingMarkerEl = document.getElementById('incoming-ride-marker');
                    if (!incomingMarkerEl && window._incomingPickupMarker) {
                        try { window._incomingPickupMarker.remove(); } catch(e){}
                        window._incomingPickupMarker = null;
                    }
                    if (window.srhClearLineSource && !incomingMarkerEl) {
                        try { window.srhClearLineSource(map, 'incoming-ride-route'); } catch(e){}
                    }

                    if (!map || typeof maplibregl === 'undefined') return;
                    const sheet = document.getElementById('driver-bottom-sheet') || document.getElementById('active-trip-wrapper');
                    const activeStatus = sheet ? sheet.dataset.activeStatus : null;
                    if (!activeStatus || activeStatus === 'returning') return;

                    const pLat = parseFloat(sheet.dataset.pickupLat);
                    const pLng = parseFloat(sheet.dataset.pickupLng);
                    const dLat = parseFloat(sheet.dataset.destLat);
                    const dLng = parseFloat(sheet.dataset.destLng);

                    if ((activeStatus === 'in_transit' || activeStatus === 'returning') && window._incomingPickupMarker) {
                        try { window._incomingPickupMarker.remove(); } catch(e){}
                        window._incomingPickupMarker = null;
                    }

                    if (activeStatus !== 'in_transit' && activeStatus !== 'returning' && isFinite(pLat) && isFinite(pLng)) {
                        const el = document.createElement('div');
                        el.innerHTML = `<div style="display:flex;flex-direction:column;align-items:center;"><div style="background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;font-size:10px;font-weight:900;padding:3px 9px;border-radius:999px;border:2px solid #fff;box-shadow:0 4px 12px rgba(37,99,235,0.5);white-space:nowrap;margin-bottom:3px;">PICKUP</div><div style="width:14px;height:14px;background:#2563eb;border:3px solid #fff;border-radius:50%;box-shadow:0 0 0 3px rgba(37,99,235,0.35);"></div></div>`;
                        window._srhActivePins.push(new maplibregl.Marker({ element: el.firstElementChild }).setLngLat([pLng, pLat]).addTo(map));
                    }
                    if ((activeStatus === 'in_transit' || activeStatus === 'arrived') && isFinite(dLat) && isFinite(dLng)) {
                        const el = document.createElement('div');
                        el.innerHTML = `<div style="display:flex;flex-direction:column;align-items:center;"><div style="background:linear-gradient(135deg,#10b981,#047857);color:#fff;font-size:10px;font-weight:900;padding:3px 9px;border-radius:999px;border:2px solid #fff;box-shadow:0 4px 12px rgba(16,185,129,0.5);white-space:nowrap;margin-bottom:3px;">DROP OFF</div><div style="width:14px;height:14px;background:#10b981;border:3px solid #fff;border-radius:50%;box-shadow:0 0 0 3px rgba(16,185,129,0.35);"></div></div>`;
                        window._srhActivePins.push(new maplibregl.Marker({ element: el.firstElementChild }).setLngLat([dLng, dLat]).addTo(map));
                    }

                    // Frame the trip: destination (if in_transit), or pickup (accepted/arrived)
                    try {
                        const driverPos = driverMarker ? driverMarker.getLngLat() : null;
                        if (activeStatus === 'returning') {
                            return;
                        }
                        let tLat, tLng;
                        if (activeStatus === 'in_transit' && isFinite(dLat) && isFinite(dLng)) { tLat = dLat; tLng = dLng; }
                        else if (isFinite(pLat) && isFinite(pLng)) { tLat = pLat; tLng = pLng; }
                        else { return; }
                        const oLat = driverPos ? driverPos.lat : tLat;
                        const oLng = driverPos ? driverPos.lng : tLng;

                        // Dynamic bottom padding to ensure vehicle and pins are centered in open map above the card
                        const bottomPad = (typeof initialPaddingBottom === 'number' && initialPaddingBottom > 100) ? (initialPaddingBottom + 25) : (window.innerWidth < 640 ? 250 : 180);

                        if (Math.abs(oLat - tLat) < 0.0001 && Math.abs(oLng - tLng) < 0.0001) {
                            map.easeTo({ center: [tLng, tLat], zoom: 17.5, padding: { top: 70, bottom: bottomPad, left: 30, right: 30 }, duration: 600 });
                        } else {
                            // Single smooth, unified framing flight fitting vehicle and pin with zero fighting
                            try { map.stop(); } catch(e){}
                            map.fitBounds(
                                [[Math.min(tLng, oLng) - 0.0008, Math.min(tLat, oLat) - 0.0008], 
                                 [Math.max(tLng, oLng) + 0.0008, Math.max(tLat, oLat) + 0.0008]], 
                                { 
                                    padding: { top: 85, bottom: bottomPad, left: 45, right: 45 }, 
                                    maxZoom: 17.2, 
                                    animate: true, 
                                    duration: 750, 
                                    essential: true 
                                }
                            );
                        }
                    } catch(e) {}
                };
                window.drawActiveTripPins();
                if (typeof updateActiveTripRoute === 'function') {
                    updateActiveTripRoute(initialLat, initialLng);
                }

                // Handle native MapLibre top-right geolocate control events
                homeMap.on('geolocate', function(e) {
                    if (e && e.coords) {
                        const realLat = e.coords.latitude;
                        const realLng = e.coords.longitude;
                        const heading = (typeof e.coords.heading === 'number' && !isNaN(e.coords.heading)) ? e.coords.heading : (window.srhTricycle?.heading || 0);

                        localStorage.removeItem('srh_simulated_lat');
                        localStorage.removeItem('srh_simulated_lng');

                        if (window.srhTricycle) {
                            window.srhTricycle.lat = realLat;
                            window.srhTricycle.lng = realLng;
                            window.srhTricycle.heading = heading;
                        }

                        if (driverMarker) {
                            driverMarker.setLngLat([realLng, realLat]);
                            if (window.setDriverMarkerOpacity) window.setDriverMarkerOpacity(driverMarker, 1);
                        } else {
                            driverMarker = new maplibregl.Marker({ element: driverEl, draggable: false, pitchAlignment: 'map', rotationAlignment: 'map' })
                                .setLngLat([realLng, realLat])
                                .addTo(homeMap);
                        }

                        if (typeof window.centerOnTricycle === 'function') {
                            window.centerOnTricycle(homeMap, { lng: realLng, lat: realLat, zoom: 18, heading: heading, duration: 800 });
                        } else {
                            homeMap.easeTo({ center: [realLng, realLat], zoom: 18, duration: 500 });
                        }
                        handleDriverLocationChanged(realLat, realLng);
                    }
                });

                // Global click delegation for MapLibre Geolocate button: force real hardware GPS snap & zoom in
                document.addEventListener('click', function(e) {
                    const geoBtn = e.target ? e.target.closest('.maplibregl-ctrl-geolocate') : null;
                    if (geoBtn) {
                        localStorage.removeItem('srh_simulated_lat');
                        localStorage.removeItem('srh_simulated_lng');

                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(pos => {
                                const realLat = pos.coords.latitude;
                                const realLng = pos.coords.longitude;
                                const heading = (typeof pos.coords.heading === 'number' && !isNaN(pos.coords.heading)) ? pos.coords.heading : (window.srhTricycle?.heading || 0);

                                if (window.srhTricycle) {
                                    window.srhTricycle.lat = realLat;
                                    window.srhTricycle.lng = realLng;
                                    window.srhTricycle.heading = heading;
                                }

                                if (driverMarker) {
                                    driverMarker.setLngLat([realLng, realLat]);
                                    if (window.setDriverMarkerOpacity) window.setDriverMarkerOpacity(driverMarker, 1);
                                }

                                if (homeMap && typeof window.centerOnTricycle === 'function') {
                                    window.centerOnTricycle(homeMap, { lng: realLng, lat: realLat, zoom: 18, heading: heading, duration: 600 });
                                } else if (homeMap) {
                                    homeMap.easeTo({ center: [realLng, realLat], zoom: 18, duration: 500 });
                                }
                                handleDriverLocationChanged(realLat, realLng);
                            }, () => {}, { enableHighAccuracy: true, maximumAge: 0, timeout: 5000 });
                        }
                    }
                });

                // Pause live 3D compass auto-rotation when user manually drags/rotates map.
                // MapLibre also fires rotatestart/pitchstart for PROGRAMMATIC camera moves
                // (e.g. centerOnTricycle easeTo, syncCameraToLiveCompass setBearing) which
                // carry no originalEvent - only pause tracking on genuine user gestures so
                // the live compass tracking loop isn't killed the moment Mode 2 activates.
                // User map panning / dragging: immediately switch to mode 1 (Blue Target / Recenter)
                ['dragstart', 'touchstart', 'wheel', 'boxzoomstart', 'rotatestart', 'pitchstart'].forEach(ev => {
                    homeMap.on(ev, function(e) {
                        const isUserGesture = !!(e && e.originalEvent);
                        if (!isUserGesture && ev !== 'dragstart' && ev !== 'touchstart') return;

                        window._driverMapManualPan = true;
                        window._driverMapUserPanningUntil = Date.now() + 4000;

                        if (ev === 'rotatestart' || ev === 'rotate') {
                            window._userCustomMapBearing = homeMap.getBearing();
                        }
                        if (typeof window.setUnifiedMapButtonState === 'function') {
                            window.setUnifiedMapButtonState(1); // Turn Blue Recenter button ON immediately
                        }
                        if (window.srhCompassMode === 1) {
                            window.srhCompassMode = 0;
                        }
                    });
                });

                homeMap.on('rotateend', function(e) {
                    if (e && e.originalEvent) {
                        window._userCustomMapBearing = homeMap.getBearing();
                    }
                });

                // Seed shared tricycle state so centerOnTricycle() can re-focus on demand
                window.srhTricycle = { lng: initialLng, lat: initialLat, heading: 0, source: 'driver' };

                // When a trip begins, snap the camera into the 3D over-the-shoulder view
                const activeSheetInit = document.getElementById('driver-bottom-sheet');
                const activeSheetInitStatus = activeSheetInit ? activeSheetInit.dataset.activeStatus : null;
                if (activeSheetInitStatus) {
                    if (typeof window.centerOnTricycle === 'function') {
                        setTimeout(() => window.centerOnTricycle(homeMap, { flyTo: true, zoom: 18 }), 150);
                    }
                } else {
                    setTimeout(() => {
                        if (typeof clearHomeRouteLines === 'function') clearHomeRouteLines();
                    }, 200);
                }

                // Tap-to-Move Map Position Override (Stationary tap on map only, never on drag)
                homeMap.on('click', function(e) {
                    if (!e || !e.lngLat) return;
                    srhTapOverrideAtLngLat(e.lngLat.lat, e.lngLat.lng);
                });

                let realGpsWatchId = null;
                let currentGlideAnimId = null;

                // 📍 Smart GPS Stationary Deadband & Noise Filter
                let _lastGpsFilteredLat = null;
                let _lastGpsFilteredLng = null;
                let _lastGpsFilteredHeading = 0;
                let _lastGpsTimestamp = 0;
                let _isVehicleStationary = true;

                function _gpsDistanceMeters(lat1, lon1, lat2, lon2) {
                    const dLat = (lat2 - lat1) * 111139;
                    const dLon = (lon2 - lon1) * 111139 * Math.cos(lat1 * 0.017453292519943295);
                    return Math.sqrt(dLat * dLat + dLon * dLon);
                }

                function animateDriverMarkerGlide(targetLat, targetLng, targetHeading = null) {
                    if (!driverMarker) return;
                    const startPos = driverMarker.getLngLat();
                    const startLat = startPos.lat;
                    const startLng = startPos.lng;
                    
                    if (Math.abs(targetLat - startLat) < 0.000002 && Math.abs(targetLng - startLng) < 0.000002) {
                        driverMarker.setLngLat([targetLng, targetLat]);
                        if (!window._driverMapManualPan && !window._isMapCameraAnimating && homeMap) {
                            try {
                                const isPathway3D = window._hasActivePathwayMode || (window.srhCompassMode === 1);
                                if (isPathway3D) {
                                    const targetPitch = 55;
                                    const targetBearing = (window.srhCompassMode === 1 && typeof currentBearing === 'number') ? currentBearing : (homeMap.getBearing ? homeMap.getBearing() : 0);
                                    homeMap.jumpTo({ center: [targetLng, targetLat], bearing: targetBearing, pitch: targetPitch, padding: { top: Math.round(window.innerHeight * 0.22), bottom: 0, left: 0, right: 0 } });
                                }
                            } catch(e) {}
                        }
                        return;
                    }

                    if (currentGlideAnimId) cancelAnimationFrame(currentGlideAnimId);
                    
                    const startTime = performance.now();
                    const duration = 400; // 400ms ultra-responsive 120Hz lerp

                    function glideFrame(now) {
                        const elapsed = now - startTime;
                        const progress = Math.min(1, elapsed / duration);
                        const ease = 1 - Math.pow(1 - progress, 3);

                        const curLat = startLat + (targetLat - startLat) * ease;
                        const curLng = startLng + (targetLng - startLng) * ease;

                        driverMarker.setLngLat([curLng, curLat]);

                        // ⚡ Camera follows vehicle ONLY in 3D active tracking/pathway mode (never jumps when idle on dashboard)
                        if (!window._driverMapManualPan && !window._isMapCameraAnimating && homeMap) {
                            try {
                                const isPathway3D = window._hasActivePathwayMode || (window.srhCompassMode === 1);
                                if (isPathway3D) {
                                    let targetBearing = 0;
                                    if (window._userCustomMapBearing !== null && window._userCustomMapBearing !== undefined) {
                                        targetBearing = window._userCustomMapBearing;
                                    } else if (typeof targetHeading === 'number' && isFinite(targetHeading)) {
                                        targetBearing = targetHeading;
                                    } else if (window.srhCompassMode === 1 && typeof currentBearing === 'number') {
                                        targetBearing = currentBearing;
                                    } else {
                                        targetBearing = homeMap.getBearing ? homeMap.getBearing() : 0;
                                    }
                                    const targetTopPad = Math.round(window.innerHeight * 0.22);
                                    homeMap.jumpTo({
                                        center: [curLng, curLat],
                                        bearing: targetBearing,
                                        pitch: 55,
                                        padding: { top: targetTopPad, bottom: 0, left: 0, right: 0 }
                                    });
                                }
                            } catch(e) {}
                        }

                        if (progress < 1) {
                            currentGlideAnimId = requestAnimationFrame(glideFrame);
                        } else {
                            currentGlideAnimId = null;
                        }
                    }

                    currentGlideAnimId = requestAnimationFrame(glideFrame);
                }

                function _dispatchSmoothedGpsPosition(lat, lng, heading, isMoving) {
                    window._driverGpsIsMoving = !!isMoving;
                    if (localStorage.getItem('srh_simulated_lat')) return;

                    let renderLat = lat;
                    let renderLng = lng;
                    let renderHeading = heading;

                    // 🛣️ Road-Lock Snapping: Lock coordinates strictly onto the road centerline when a pathway is active!
                    if (window._hasActivePathwayMode && typeof snapToPathwayPolyline === 'function') {
                        const snapped = snapToPathwayPolyline(lat, lng);
                        if (snapped) {
                            renderLat = snapped.lat;
                            renderLng = snapped.lng;
                            renderHeading = snapped.bearing;
                        }
                    }

                    try {
                        localStorage.setItem('srh_last_real_lat', String(renderLat));
                        localStorage.setItem('srh_last_real_lng', String(renderLng));
                    } catch(e) {}

                    if (window.srhTricycle) {
                        window.srhTricycle.lng = renderLng;
                        window.srhTricycle.lat = renderLat;
                        if (typeof renderHeading === 'number' && !isNaN(renderHeading)) {
                            window.srhTricycle.heading = renderHeading;
                            if (window._syncCompassStateToHeading) window._syncCompassStateToHeading(renderHeading);
                        }
                    }

                    const markerImg = document.querySelector('#driver-tricycle-marker-img');
                    if (markerImg && typeof renderHeading === 'number' && isFinite(renderHeading)) {
                        markerImg.style.transform = 'rotate(' + renderHeading.toFixed(1) + 'deg)';
                    }

                    const m = driverMarker || window.driverMarker;
                    if (m) {
                        setDriverMarkerOpacity(m, 1);
                        if (isMoving) {
                            animateDriverMarkerGlide(renderLat, renderLng, renderHeading);
                        } else {
                            m.setLngLat([renderLng, renderLat]);
                        }
                    } else if (homeMap) {
                        if (window.driverMarker) {
                            try { window.driverMarker.remove(); } catch(e){}
                        }
                        driverMarker = new maplibregl.Marker({ element: driverEl, draggable: false, pitchAlignment: 'map', rotationAlignment: 'map' })
                            .setLngLat([renderLng, renderLat])
                            .addTo(homeMap);
                        window.driverMarker = driverMarker;
                    }

                    document.dispatchEvent(new CustomEvent('srh:real-location', { detail: { lat: renderLat, lng: renderLng } }));
                    handleDriverLocationChanged(renderLat, renderLng);

                    if (window.sendDriverLocationToBackend) {
                        window.sendDriverLocationToBackend(renderLat, renderLng);
                    }
                    if (typeof checkAutoTerminalGeofenceReturn === 'function') {
                        checkAutoTerminalGeofenceReturn(renderLat, renderLng);
                    }
                    if (typeof updateActiveTripRoute === 'function') {
                        updateActiveTripRoute(renderLat, renderLng);
                    }
                    if (typeof update3rdPersonNavigationCamera === 'function') {
                        update3rdPersonNavigationCamera(renderLat, renderLng, false);
                    }
                    if (typeof window.preFetchReturnRoute === 'function') {
                        window.preFetchReturnRoute(renderLat, renderLng);
                    }
                }

                let _hasAccurateGpsLock = false;

                function processGpsPositionUpdate(pos) {
                    if (!pos || !pos.coords) return;
                    const mapEl = document.getElementById('grab-home-map');
                    if (!mapEl || !document.body.contains(mapEl)) return;
                    if (localStorage.getItem('srh_simulated_lat')) return;

                    const rawLat = pos.coords.latitude;
                    const rawLng = pos.coords.longitude;
                    const accuracy = typeof pos.coords.accuracy === 'number' ? pos.coords.accuracy : 10;
                    const speed = (typeof pos.coords.speed === 'number' && !isNaN(pos.coords.speed) && pos.coords.speed >= 0) ? pos.coords.speed : null;
                    const rawHeading = (typeof pos.coords.heading === 'number' && !isNaN(pos.coords.heading)) ? pos.coords.heading : null;
                    const now = pos.timestamp || Date.now();

                    // 1. Inaccurate reading filter: If accuracy is very poor (>60m) and we already have a lock, discard
                    if (accuracy > 60 && _hasAccurateGpsLock) {
                        return;
                    }

                    // 2. Mark accurate lock and activate button from Gray (State 0) to Centered (State 2)
                    if (accuracy <= 45) {
                        _hasAccurateGpsLock = true;
                        if (window.srhUnifiedMapState === 0 && typeof window.setUnifiedMapButtonState === 'function') {
                            window.setUnifiedMapButtonState(2);
                        }
                    }

                    // 3. First GPS fix or upgrading to accurate hardware GPS:
                    if (_lastGpsFilteredLat === null || _lastGpsFilteredLng === null || (!_hasAccurateGpsLock && accuracy <= 45)) {
                        _lastGpsFilteredLat = rawLat;
                        _lastGpsFilteredLng = rawLng;
                        _lastGpsFilteredHeading = rawHeading || (window.srhTricycle?.heading || 0);
                        _lastGpsTimestamp = now;
                        _dispatchSmoothedGpsPosition(_lastGpsFilteredLat, _lastGpsFilteredLng, _lastGpsFilteredHeading, false);
                        return;
                    }

                    // 4. Stationary Deadband & Speed Gate:
                    const distMoved = _gpsDistanceMeters(_lastGpsFilteredLat, _lastGpsFilteredLng, rawLat, rawLng);
                    const timeDelta = Math.max(0.2, (now - _lastGpsTimestamp) / 1000);
                    const computedSpeed = distMoved / timeDelta;
                    const effectiveSpeed = speed !== null ? speed : computedSpeed;

                    // If vehicle moved less than 3.5m and speed is below 1.0 m/s, keep position locked without drifting
                    if ((distMoved < 3.5 && effectiveSpeed < 1.0) || (speed !== null && speed < 0.3 && distMoved < 5.0)) {
                        _isVehicleStationary = true;
                        _lastGpsTimestamp = now;
                        return;
                    }

                    // 5. Movement Confirmed:
                    _isVehicleStationary = false;
                    _lastGpsTimestamp = now;

                    const alpha = effectiveSpeed > 3.5 ? 0.85 : 0.65;
                    _lastGpsFilteredLat = _lastGpsFilteredLat + alpha * (rawLat - _lastGpsFilteredLat);
                    _lastGpsFilteredLng = _lastGpsFilteredLng + alpha * (rawLng - _lastGpsFilteredLng);

                    if (rawHeading !== null && effectiveSpeed > 0.8) {
                        _lastGpsFilteredHeading = rawHeading;
                    }

                    _dispatchSmoothedGpsPosition(_lastGpsFilteredLat, _lastGpsFilteredLng, _lastGpsFilteredHeading, true);
                }

                function startContinuousRealGpsWatch() {
                    if (!navigator.geolocation) return;

                    if (realGpsWatchId !== null || window._driverWatchPositionId !== null) {
                        try { navigator.geolocation.clearWatch(realGpsWatchId || window._driverWatchPositionId); } catch(e){}
                        realGpsWatchId = null;
                        window._driverWatchPositionId = null;
                    }

                    realGpsWatchId = navigator.geolocation.watchPosition(
                        processGpsPositionUpdate,
                        (err) => {},
                        { enableHighAccuracy: true, maximumAge: 1000, timeout: 15000 }
                    );
                    window._driverWatchPositionId = realGpsWatchId;
                }

                // Initial State: Gray disabled/locating until verified hardware GPS fix
                if (!localStorage.getItem('srh_simulated_lat')) {
                    window._srhGpsFetching = true;
                    if (typeof window.setUnifiedMapButtonState === 'function') {
                        window.setUnifiedMapButtonState(1);
                    }
                }

                // Query live hardware GPS on load
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(pos => {
                        if (localStorage.getItem('srh_simulated_lat')) return;
                        processGpsPositionUpdate(pos);

                        const activeNow = document.getElementById('driver-bottom-sheet');
                        const hasActiveTrip = activeNow && activeNow.dataset.activeStatus;
                        if (!hasActiveTrip && homeMap && typeof window.centerOnTricycle === 'function' && _lastGpsFilteredLat !== null) {
                            window.centerOnTricycle(homeMap, { lng: _lastGpsFilteredLng, lat: _lastGpsFilteredLat, heading: _lastGpsFilteredHeading || 0, duration: 600 });
                        }

                        // Stop spinning only after centering finishes
                        setTimeout(() => {
                            window._srhGpsFetching = false;
                            if (typeof window.setUnifiedMapButtonState === 'function') {
                                window.setUnifiedMapButtonState(2);
                            }
                        }, 650);
                    }, (err) => {
                        window._srhGpsFetching = false;
                        // GPS permission denied or disabled: set State 0 (Gray disabled)
                        if (typeof window.setUnifiedMapButtonState === 'function') {
                            window.setUnifiedMapButtonState(0);
                        }
                        if (driverMarker) setDriverMarkerOpacity(driverMarker, 1);
                    }, { enableHighAccuracy: true, maximumAge: 0, timeout: 10000 });

                    startContinuousRealGpsWatch();
                } else {
                    window._srhGpsFetching = false;
                    if (typeof window.setUnifiedMapButtonState === 'function') {
                        window.setUnifiedMapButtonState(0);
                    }
                }

                window.addEventListener('srh-use-real-gps', function() {
                    localStorage.removeItem('srh_simulated_lat');
                    localStorage.removeItem('srh_simulated_lng');
                    _lastGpsFilteredLat = null;
                    _lastGpsFilteredLng = null;
                    startContinuousRealGpsWatch();
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(pos => {
                            processGpsPositionUpdate(pos);
                        }, () => {}, { enableHighAccuracy: true });
                    }
                });

            // ⚡ Shared Tap-to-Override core: attached to BOTH the map-level click
            // listener (inside initGrabHomeMap) and a document-level delegated
            // listener below, so the feature survives deferred map init and any
            // SPA partial swap without reinstalling closures.
            function srhTapOverrideAtLngLat(lat, lng) {
                if (typeof lat !== 'number' || typeof lng !== 'number' || !isFinite(lat) || !isFinite(lng)) return;

                try {
                    localStorage.setItem('srh_simulated_lat', String(lat));
                    localStorage.setItem('srh_simulated_lng', String(lng));
                } catch (err) {}

                const m = window._grabHomeMapInstance || window.homeMap;
                if (!m) return;

                let renderLat = lat;
                let renderLng = lng;
                let targetPitch = m.getPitch ? m.getPitch() : 0;
                let targetZoom = m.getZoom ? Math.max(m.getZoom(), 16.3) : 16.5;

                if (window._hasActivePathwayMode) {
                    const snapped = snapToPathwayPolyline(lat, lng);
                    let pathwayBearing = null;
                    if (snapped) {
                        renderLat = snapped.lat;
                        renderLng = snapped.lng;
                        pathwayBearing = snapped.bearing;
                    } else {
                        const pathBrng = calculatePathwayBearing(lat, lng);
                        if (typeof pathBrng === 'number' && isFinite(pathBrng)) pathwayBearing = pathBrng;
                    }
                    if (typeof pathwayBearing === 'number' && isFinite(pathwayBearing)) {
                        if (window.srhTricycle) window.srhTricycle.heading = pathwayBearing;
                        const markerImg = document.querySelector('#driver-tricycle-marker-img');
                        if (markerImg) markerImg.style.transform = 'rotate(' + pathwayBearing + 'deg)';
                    }
                } else {
                    if (typeof window.setUnifiedMapButtonState === 'function') window.setUnifiedMapButtonState(1);
                }

                if (window.srhTricycle) {
                    window.srhTricycle.lng = renderLng;
                    window.srhTricycle.lat = renderLat;
                }

                const marker = driverMarker || window.driverMarker;
                if (marker) {
                    marker.setLngLat([renderLng, renderLat]);
                    if (window.setDriverMarkerOpacity) window.setDriverMarkerOpacity(marker, 1);
                } else {
                    const driverEl = document.getElementById('driver-live-map-marker');
                    if (driverEl && typeof maplibregl !== 'undefined') {
                        driverMarker = new maplibregl.Marker({ element: driverEl, draggable: false, pitchAlignment: 'map', rotationAlignment: 'map' })
                            .setLngLat([renderLng, renderLat])
                            .addTo(m);
                        window.driverMarker = driverMarker;
                        if (window.setDriverMarkerOpacity) window.setDriverMarkerOpacity(marker, 1);
                    }
                }

                if (m.stop) {
                    try { m.stop(); } catch(e) {}
                }

                window._isMapCameraAnimating = true;
                const easeOptions = {
                    center: [renderLng, renderLat],
                    pitch: targetPitch,
                    zoom: targetZoom,
                    duration: 450,
                    easing: function(t) { return t * (2 - t); }
                };
                m.easeTo(easeOptions);
                setTimeout(() => { window._isMapCameraAnimating = false; }, 500);

                if (typeof handleDriverLocationChanged === 'function') {
                    handleDriverLocationChanged(renderLat, renderLng);
                }

                window.dispatchEvent(new CustomEvent('srh-location-updated', {
                    detail: { lat: renderLat, lng: renderLng, isSimulated: true }
                }));

                if (typeof window.sendDriverLocationToBackend === 'function') {
                    window.sendDriverLocationToBackend(lat, lng, true);
                }

                if (typeof window.preFetchReturnRoute === 'function') {
                    window.preFetchReturnRoute(lat, lng);
                }

                if (window.showDynamicStatus) {
                    window.showDynamicStatus("Location Overridden to Tapped Map Position!");
                }
            }

            function clearHomeRouteLines() {
                    const targetMap = homeMap || window._grabHomeMapInstance;
                    window._homeRouteGlow = null;
                    window._homeRouteCore = null;
                    window._hasActivePathwayMode = false;
                    window._srhActiveRouteCoordinates = null;
                    window._lastRouteOrigin = null;
                    window._lastRouteTarget = null;
                    window._lastRequestedRouteKey = null;
                    clearTimeout(window._driverRouteTimer);
                    if (window._driverRouteAbortCtrl) {
                        try { window._driverRouteAbortCtrl.abort(); } catch(e){}
                        window._driverRouteAbortCtrl = null;
                    }

            window.fetchRouteAsync = function(oLat, oLng, dLat, dLng) {
                const oLngStr = parseFloat(oLng).toFixed(5);
                const oLatStr = parseFloat(oLat).toFixed(5);
                const dLngStr = parseFloat(dLng).toFixed(5);
                const dLatStr = parseFloat(dLat).toFixed(5);
                const url = `https://router.project-osrm.org/route/v1/driving/${oLngStr},${oLatStr};${dLngStr},${dLatStr}?overview=full&geometries=geojson`;

                return fetch(url)
                    .then(r => r.json())
                    .then(d => {
                        if (d && d.routes && d.routes.length > 0 && d.routes[0].geometry && d.routes[0].geometry.coordinates) {
                            return d.routes[0].geometry.coordinates;
                        }
                        return null;
                    })
                    .catch(() => null);
            };

            window.preFetchReturnRoute = function(dLat, dLng) {
                if (!dLat || !dLng) return;
                const oLngStr = parseFloat(dLng).toFixed(5);
                const oLatStr = parseFloat(dLat).toFixed(5);
                const tLngStr = parseFloat(TERMINAL_LNG).toFixed(5);
                const tLatStr = parseFloat(TERMINAL_LAT).toFixed(5);
                const cacheKey = `${oLngStr},${oLatStr}->${tLngStr},${tLatStr}`;
                if (window._cachedReturnRouteKey === cacheKey && window._cachedReturnRouteCoords) return;

                const url = `https://router.project-osrm.org/route/v1/driving/${oLngStr},${oLatStr};${tLngStr},${tLatStr}?overview=full&geometries=geojson`;
                fetch(url)
                    .then(r => r.json())
                    .then(d => {
                        if (d && d.routes && d.routes.length > 0 && d.routes[0].geometry && d.routes[0].geometry.coordinates) {
                            window._cachedReturnRouteKey = cacheKey;
                            window._cachedReturnRouteCoords = d.routes[0].geometry.coordinates;
                        }
                    })
                    .catch(() => {});
            };
                    // Invalidate any in-flight OSRM fetch so it won't re-draw after we clear
                    window._routeFetchGen = (window._routeFetchGen || 0) + 1;
                    if (targetMap && typeof window.srhClearLineSource === 'function') {
                        window.srhClearLineSource(targetMap, 'home-route');
                        window.srhClearLineSource(targetMap, 'incoming-ride-route');
                    }
                }
                // Expose so app.blade.php event handlers can call it
                window.clearHomeRouteLines = clearHomeRouteLines;

                function drawHomeRouteLine(pts, color) {
                    const targetMap = homeMap || window._grabHomeMapInstance;
                    if (!targetMap || !pts || pts.length === 0) return;

                    const activeSheet = document.getElementById('driver-bottom-sheet') || document.getElementById('active-trip-wrapper');
                    const activeStatus = activeSheet ? activeSheet.dataset.activeStatus : null;
                    if (!activeStatus || activeStatus === '' || activeStatus === 'completed' || activeStatus === 'cancelled' || activeStatus === 'none') {
                        clearHomeRouteLines();
                        return;
                    }

                    // Normalize to [lat, lng] array for calculation and [lng, lat] for MapLibre
                    const latLngPts = pts.map(p => {
                        return (p[0] > 90) ? [p[1], p[0]] : [p[0], p[1]];
                    });
                    const routeCoords = latLngPts.map(p => [p[1], p[0]]); // [lng, lat] for MapLibre
                    window._srhActiveRouteCoordinates = routeCoords;
                    window._hasActivePathwayMode = true;

                    window._homeRouteGlow = true;
                    window._homeRouteCore = true;
                    window.srhSetLineSource(targetMap, 'home-route', routeCoords, [
                        { id: 'home-route-glow', color: color, weight: 10, opacity: 0.3 },
                        { id: 'home-route-core', color: color, weight: 6, opacity: 0.95 }
                    ]);

                    if (typeof window.setUnifiedMapButtonState === 'function') {
                        window.setUnifiedMapButtonState(3);
                    }

                    // 🎯 Instantly snap tricycle marker position to road center line
                    let rLat = latLngPts[0][0];
                    let rLng = latLngPts[0][1];
                    let targetBearing = 0;

                    const snapped = snapToPathwayPolyline(rLat, rLng);
                    if (snapped) {
                        rLat = snapped.lat;
                        rLng = snapped.lng;
                        targetBearing = snapped.bearing;
                    } else if (latLngPts.length > 1) {
                        targetBearing = calculateBearing(latLngPts[0][0], latLngPts[0][1], latLngPts[1][0], latLngPts[1][1]);
                    }

                    const m = driverMarker || window.driverMarker;
                    if (m) {
                        m.setLngLat([rLng, rLat]);
                        if (window.setDriverMarkerOpacity) window.setDriverMarkerOpacity(m, 1);
                    }

                    if (window.srhTricycle) {
                        window.srhTricycle.lat = rLat;
                        window.srhTricycle.lng = rLng;
                        window.srhTricycle.heading = targetBearing;
                    }

                    const markerImg = document.querySelector('#driver-tricycle-marker-img');
                    if (markerImg && typeof targetBearing === 'number' && isFinite(targetBearing)) {
                        markerImg.style.transform = 'rotate(' + targetBearing + 'deg)';
                    }

                    const targetTopPad = Math.round(window.innerHeight * 0.22);

                    if (targetMap.stop) { try { targetMap.stop(); } catch(e){} }
                    window._isMapCameraAnimating = true;
                    targetMap.easeTo({
                        center: [rLng, rLat],
                        pitch: 55,
                        bearing: targetBearing,
                        zoom: 17.2,
                        padding: { top: targetTopPad, bottom: 0, left: 0, right: 0 },
                        duration: 850,
                        easing: function(t) { return t * (2 - t); }
                    });

                    setTimeout(() => {
                        window._isMapCameraAnimating = false;
                    }, 900);
                }
                window.drawHomeRouteLine = drawHomeRouteLine;

                function calcDist(lat1, lon1, lat2, lon2) {
                    const R = 6371e3, φ1 = lat1 * Math.PI / 180, φ2 = lat2 * Math.PI / 180;
                    const Δφ = (lat2 - lat1) * Math.PI / 180, Δλ = (lon2 - lon1) * Math.PI / 180;
                    const a = Math.sin(Δφ / 2) ** 2 + Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) ** 2;
                    return Math.round(R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
                }
                window.calcDist = calcDist;

                function checkIsReturning() {
                    const activeSheet = document.getElementById('driver-bottom-sheet') || document.getElementById('active-trip-wrapper');
                    if (activeSheet && activeSheet.dataset && activeSheet.dataset.activeStatus) {
                        return activeSheet.dataset.activeStatus === 'returning';
                    }
                    return false;
                }

                function updateActiveTripRoute(dLat, dLng, force = false) {
                    const targetMap = homeMap || window._grabHomeMapInstance;
                    if (!targetMap) return;

                    const activeSheet = document.getElementById('driver-bottom-sheet') || document.getElementById('active-trip-wrapper');
                    let activeStatus = activeSheet ? activeSheet.dataset.activeStatus : null;

                    if (!activeStatus && checkIsReturning()) {
                        activeStatus = 'returning';
                    }

                    if (!activeStatus || activeStatus === '' || activeStatus === 'completed' || activeStatus === 'cancelled' || activeStatus === 'none') {
                        clearHomeRouteLines();
                        window._lastRouteOrigin = null;
                        window._lastRouteTarget = null;
                        window._srhActiveRouteCoordinates = null;
                        window._hasActivePathwayMode = false;
                        return;
                    }

                    if (activeStatus === 'in_transit' || activeStatus === 'returning') {
                        if (window._incomingPickupMarker) {
                            try { window._incomingPickupMarker.remove(); } catch(e){}
                            window._incomingPickupMarker = null;
                        }
                        if (typeof window.srhClearLineSource === 'function') {
                            window.srhClearLineSource(targetMap, 'incoming-ride-route');
                        }
                    }

                    if (!dLat || !dLng) {
                        const m = driverMarker || window.driverMarker;
                        if (m) {
                            const pos = m.getLngLat();
                            dLat = pos.lat;
                            dLng = pos.lng;
                        } else {
                            dLat = parseFloat(localStorage.getItem('srh_simulated_lat')) || TERMINAL_LAT;
                            dLng = parseFloat(localStorage.getItem('srh_simulated_lng')) || TERMINAL_LNG;
                        }
                    }

                    let destLat = null, destLng = null, routeColor = '#3b82f6';
                    if (activeStatus === 'returning') {
                        destLat = TERMINAL_LAT; 
                        destLng = TERMINAL_LNG; 
                        routeColor = '#3b82f6';
                    } else if (activeStatus === 'in_transit') {
                        const parsedLat = parseFloat(activeSheet?.dataset?.destLat);
                        const parsedLng = parseFloat(activeSheet?.dataset?.destLng);
                        if (isFinite(parsedLat) && parsedLat !== 0 && isFinite(parsedLng) && parsedLng !== 0) {
                            destLat = parsedLat;
                            destLng = parsedLng;
                            routeColor = '#10b981';
                        }
                    } else if (activeStatus === 'accepted') {
                        // While heading to the pickup, the pathway ALWAYS points at the pickup
                        const parsedLat = parseFloat(activeSheet?.dataset?.pickupLat);
                        const parsedLng = parseFloat(activeSheet?.dataset?.pickupLng);
                        if (isFinite(parsedLat) && parsedLat !== 0 && isFinite(parsedLng) && parsedLng !== 0) {
                            destLat = parsedLat;
                            destLng = parsedLng;
                            routeColor = '#2563eb';
                        }
                    } else if (activeStatus === 'arrived') {
                        const parsedLat = parseFloat(activeSheet?.dataset?.destLat);
                        const parsedLng = parseFloat(activeSheet?.dataset?.destLng);
                        if (isFinite(parsedLat) && parsedLat !== 0 && isFinite(parsedLng) && parsedLng !== 0) {
                            destLat = parsedLat;
                            destLng = parsedLng;
                            routeColor = '#10b981';
                        }
                    }

                    if (destLat === null || destLng === null) {
                        clearHomeRouteLines();
                        window._lastRouteOrigin = null;
                        window._lastRouteTarget = null;
                        window._lastRequestedRouteKey = null;
                        return;
                    }

                    const originLng = parseFloat(dLng).toFixed(5);
                    const originLat = parseFloat(dLat).toFixed(5);
                    const destLngStr = parseFloat(destLng).toFixed(5);
                    const destLatStr = parseFloat(destLat).toFixed(5);
                    const routeKey = `${originLng},${originLat}->${destLngStr},${destLatStr}-${routeColor}`;

                    // Deduplicate identical route queries to eliminate duplicate network calls
                    if (window._lastRequestedRouteKey === routeKey && !force) {
                        return;
                    }
                    window._lastRequestedRouteKey = routeKey;

                    const doFetch = () => {
                        window._lastDriverRouteLat = dLat;
                        window._lastDriverRouteLng = dLng;

                        if (window._driverRouteAbortCtrl) {
                            try { window._driverRouteAbortCtrl.abort(); } catch(e){}
                        }
                        window._driverRouteAbortCtrl = new AbortController();

                        const myFetchGen = (window._routeFetchGen = (window._routeFetchGen || 0) + 1);
                        const url = `https://router.project-osrm.org/route/v1/driving/${originLng},${originLat};${destLngStr},${destLatStr}?overview=full&geometries=geojson`;

                        fetch(url, { signal: window._driverRouteAbortCtrl.signal })
                            .then(r => r.json())
                            .then(d => {
                                if (window._routeFetchGen !== myFetchGen) return;
                                const sheetNow = document.getElementById('driver-bottom-sheet') || document.getElementById('active-trip-wrapper');
                                const stNow = sheetNow ? sheetNow.dataset.activeStatus : null;
                                if (!stNow || stNow === '' || stNow === 'completed' || stNow === 'cancelled' || stNow === 'none') {
                                    clearHomeRouteLines();
                                    return;
                                }
                                if (d.routes && d.routes.length > 0 && d.routes[0].geometry && d.routes[0].geometry.coordinates) {
                                    const rawCoords = d.routes[0].geometry.coordinates;
                                    window._srhActiveRouteCoordinates = rawCoords;
                                    const coords = rawCoords.map(c => [c[1], c[0]]);
                                    drawHomeRouteLine(coords, routeColor);
                                }
                            })
                            .catch(err => {
                                if (err && err.name === 'AbortError') return;
                            });
                    };

                    clearTimeout(window._driverRouteTimer);
                    if (force) {
                        doFetch();
                    } else {
                        window._driverRouteTimer = setTimeout(doFetch, 150);
                    }
                }
                window.updateActiveTripRoute = updateActiveTripRoute;

                window.autoReturnedToQueueTriggered = false;

                function checkAutoTerminalGeofenceReturn(dLat, dLng) {
                    if (!checkIsReturning() || window.autoReturnedToQueueTriggered) return;

                    if (!dLat || !dLng) {
                        if (driverMarker) {
                            const pos = driverMarker.getLngLat();
                            dLat = pos.lat;
                            dLng = pos.lng;
                        } else {
                            dLat = parseFloat(localStorage.getItem('srh_simulated_lat')) || TERMINAL_LAT;
                            dLng = parseFloat(localStorage.getItem('srh_simulated_lng')) || TERMINAL_LNG;
                        }
                    }

                    const distToTerminal = calcDist(dLat, dLng, TERMINAL_LAT, TERMINAL_LNG);

                    if (distToTerminal <= 35) {
                        const sheet = document.getElementById('driver-bottom-sheet');
                        const returnUrl = sheet?.dataset?.completeReturnUrl || (window._activeRideId ? `/rides/${window._activeRideId}/complete-return` : null);
                        if (returnUrl && !window.autoReturnedToQueueTriggered) {
                            window.autoReturnedToQueueTriggered = true;
                            clearHomeRouteLines();

                            if (window.submitRideAction) {
                                window.submitRideAction(returnUrl, 'PATCH', {});
                            }
                        }
                    }
                }

                window.checkAutoTerminalGeofenceReturn = checkAutoTerminalGeofenceReturn;

                window.animateMapForAction = function(actionType) {
                    if (window._lastActionAnimType === actionType && (Date.now() - (window._lastActionAnimTime || 0)) < 1500) {
                        return;
                    }
                    window._lastActionAnimType = actionType;
                    window._lastActionAnimTime = Date.now();

                    const targetMap = homeMap || window._grabHomeMapInstance;
                    if (!targetMap || typeof targetMap.easeTo !== 'function') return;

                    window._isMapCameraAnimating = true;
                    const curLat = driverMarker ? driverMarker.getLngLat().lat : (parseFloat(localStorage.getItem('srh_simulated_lat')) || TERMINAL_LAT);
                    const curLng = driverMarker ? driverMarker.getLngLat().lng : (parseFloat(localStorage.getItem('srh_simulated_lng')) || TERMINAL_LNG);
                    const targetPitch = (window.srhCompassMode === 1) ? 55 : (targetMap.getPitch ? targetMap.getPitch() : 0);
                    const curBearing = targetMap.getBearing ? targetMap.getBearing() : 0;

                    if (actionType === 'start_returning' || actionType === 'start_ride') {
                        const pathBrng = calculatePathwayBearing(curLat, curLng);
                        const targetBrng = (typeof pathBrng === 'number' && isFinite(pathBrng)) ? pathBrng : (targetMap.getBearing ? targetMap.getBearing() : 0);
                        targetMap.easeTo({
                            center: [curLng, curLat],
                            zoom: 17.2,
                            pitch: 55,
                            bearing: targetBrng,
                            duration: 650,
                            easing: function(t) { return t * (2 - t); }
                        });
                    } else if (actionType === 'wayside_start') {
                        window._hasActivePathwayMode = false;
                        window._srhActiveRouteCoordinates = null;
                        if (typeof clearHomeRouteLines === 'function') clearHomeRouteLines();
                        if (typeof window.setUnifiedMapButtonState === 'function') window.setUnifiedMapButtonState(2);
                        targetMap.easeTo({
                            center: [curLng, curLat],
                            zoom: 16.3,
                            pitch: 0,
                            bearing: 0,
                            duration: 650,
                            easing: function(t) { return t * (2 - t); }
                        });
                    } else if (actionType === 'cancel' || actionType === 'complete_return') {
                        window._hasActivePathwayMode = false;
                        window._srhActiveRouteCoordinates = null;
                        window.srhCompassMode = 0;
                        window._userCustomMapBearing = 0;
                        if (typeof clearHomeRouteLines === 'function') clearHomeRouteLines();
                        if (typeof window.setUnifiedMapButtonState === 'function') window.setUnifiedMapButtonState(2);
                    }

                    setTimeout(() => {
                        window._isMapCameraAnimating = false;
                    }, 50);
                };

                // High-frequency 250ms geofence poller during returning status
                if (!window._geofencePollerInterval) {
                    window._geofencePollerInterval = setInterval(() => {
                        if (typeof checkIsReturning === 'function' && checkIsReturning()) {
                            if (typeof checkAutoTerminalGeofenceReturn === 'function') {
                                checkAutoTerminalGeofenceReturn();
                            }
                        }
                    }, 250);
                }

                // Initial route & geofence check
                handleDriverLocationChanged(initialLat, initialLng);

                window.addEventListener('spa:page-loaded', function() {
                    window.autoReturnedToQueueTriggered = false;
                    clearHomeRouteLines();
                    window._lastRouteOrigin = null;
                    window._lastRouteTarget = null;
                    if (driverMarker) {
                        const pos = driverMarker.getLngLat();
                        handleDriverLocationChanged(pos.lat, pos.lng);
                    } else {
                        handleDriverLocationChanged();
                    }
                });

                window.addEventListener('srh-location-updated', function(e) {
                    if (e.detail && e.detail.lat && e.detail.lng) {
                        handleDriverLocationChanged(e.detail.lat, e.detail.lng);
                    }
                });

                let lastPos = null;
                let currentBearing = 0;

                function calculateBearing(lat1, lng1, lat2, lng2) {
                    const dLng = (lng2 - lng1) * Math.PI / 180;
                    const y = Math.sin(dLng) * Math.cos(lat2 * Math.PI / 180);
                    const x = Math.cos(lat1 * Math.PI / 180) * Math.sin(lat2 * Math.PI / 180) -
                              Math.sin(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.cos(dLng);
                    let brng = Math.atan2(y, x) * 180 / Math.PI;
                    return (brng + 360) % 360;
                }

                function calculatePathwayBearing(curLat, curLng) {
                    const coords = window._srhActiveRouteCoordinates;
                    if (!coords || !Array.isArray(coords) || coords.length < 2) {
                        return null;
                    }

                    let minSquareDist = Infinity;
                    let closestIndex = 0;

                    for (let i = 0; i < coords.length; i++) {
                        const ptLng = coords[i][0];
                        const ptLat = coords[i][1];
                        const dLng = ptLng - curLng;
                        const dLat = ptLat - curLat;
                        const squareDist = dLng * dLng + dLat * dLat;
                        if (squareDist < minSquareDist) {
                            minSquareDist = squareDist;
                            closestIndex = i;
                        }
                    }

                    let targetIndex = Math.min(closestIndex + 2, coords.length - 1);
                    if (targetIndex === closestIndex && closestIndex > 0) {
                        targetIndex = closestIndex;
                        closestIndex = closestIndex - 1;
                    }
                    if (closestIndex === targetIndex) return null;

                    const fromPt = coords[closestIndex];
                    const toPt = coords[targetIndex];
                    return calculateBearing(fromPt[1], fromPt[0], toPt[1], toPt[0]);
                }

                function snapToPathwayPolyline(curLat, curLng) {
                    const coords = window._srhActiveRouteCoordinates;
                    if (!coords || !Array.isArray(coords) || coords.length < 2) {
                        return null;
                    }

                    let minSqDist = Infinity;
                    let bestPoint = null;
                    let bestBearing = 0;

                    for (let i = 0; i < coords.length - 1; i++) {
                        const p1 = coords[i];     // [lng, lat]
                        const p2 = coords[i + 1]; // [lng, lat]

                        const x = curLng;
                        const y = curLat;
                        const x1 = p1[0], y1 = p1[1];
                        const x2 = p2[0], y2 = p2[1];

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
                            bestBearing = calculateBearing(y1, x1, y2, x2);
                        }
                    }

                    // Snap within ~600m radius of active pathway
                    if (bestPoint && minSqDist < 0.00008) {
                        return { lat: bestPoint.lat, lng: bestPoint.lng, bearing: bestBearing };
                    }

                    // Fallback to route start
                    if (coords.length > 0) {
                        const p0 = coords[0];
                        const p1 = coords[1] || coords[0];
                        return { lat: p0[1], lng: p0[0], bearing: calculateBearing(p0[1], p0[0], p1[1], p1[0]) };
                    }

                    return null;
                }

                let _driverMarkerAngle = 0;
                function update3rdPersonNavigationCamera(lat, lng, isSimulated = false) {
                    if (!homeMap) return;

                    let renderLat = lat;
                    let renderLng = lng;
                    const snapped = snapToPathwayPolyline(lat, lng);

                    if (snapped && window._srhActiveRouteCoordinates && window._srhActiveRouteCoordinates.length > 0) {
                        renderLat = snapped.lat;
                        renderLng = snapped.lng;
                        currentBearing = snapped.bearing;
                        window._hasActivePathwayMode = true;
                    } else {
                        const pathBearing = calculatePathwayBearing(lat, lng);
                        if (typeof pathBearing === 'number' && isFinite(pathBearing)) {
                            currentBearing = pathBearing;
                            window._hasActivePathwayMode = true;
                        } else {
                            window._hasActivePathwayMode = false;
                            if (lastPos && (lastPos.lat !== lat || lastPos.lng !== lng)) {
                                const distMoved = getDistanceMeters(lastPos.lat, lastPos.lng, lat, lng);
                                if (distMoved > 1.0) {
                                    currentBearing = calculateBearing(lastPos.lat, lastPos.lng, lat, lng);
                                }
                            }
                        }
                    }
                    lastPos = { lat, lng };

                    if (driverMarker) {
                        driverMarker.setLngLat([renderLng, renderLat]);
                    }

                    if (window.srhTricycle) {
                        window.srhTricycle.lng = renderLng;
                        window.srhTricycle.lat = renderLat;
                        window.srhTricycle.heading = currentBearing;
                    }

                    // Smooth rotation on tricycle marker with continuous shortest angular accumulation
                    const markerImg = document.querySelector('#driver-tricycle-marker-img');
                    if (markerImg && typeof currentBearing === 'number' && isFinite(currentBearing)) {
                        const cur = ((_driverMarkerAngle % 360) + 360) % 360;
                        const tgt = ((currentBearing % 360) + 360) % 360;
                        let diff = (tgt - cur) % 360;
                        if (diff > 180) diff -= 360;
                        if (diff < -180) diff += 360;
                        if (Math.abs(diff) > 0.4) {
                            _driverMarkerAngle += diff;
                            markerImg.style.transform = 'rotate(' + _driverMarkerAngle + 'deg)';
                        }
                    }

                    // Live auto-follow camera: smoothly glides and keeps tricycle centered ONLY when in active 3D navigation/pathway mode
                    if (!window._driverMapManualPan && !window._isMapCameraAnimating && homeMap) {
                        try {
                            const isPathway3D = window._hasActivePathwayMode || (window.srhCompassMode === 1);
                            if (isPathway3D) {
                                const targetTopPad = Math.round(window.innerHeight * 0.22);
                                homeMap.easeTo({
                                    center: [renderLng, renderLat],
                                    bearing: currentBearing,
                                    pitch: 55,
                                    zoom: 17.2,
                                    padding: { top: targetTopPad, bottom: 0, left: 0, right: 0 },
                                    duration: 550,
                                    easing: function(t) { return t * (2 - t); }
                                });
                            }
                        } catch(e) {}
                    }
                }

                window._srhMainMountTime = performance.now();
                window._lastReportedDriverLoc = window._lastReportedDriverLoc || { lat: 0, lng: 0, time: 0 };
                let _pendingLocTimer = null;
                let _driverLocPostInFlight = false;

                window.sendDriverLocationToBackend = function(lat, lng, force = false) {
                    if (!lat || !lng) return;
                    const now = Date.now();

                    // Deadband: If stationary (< 5 meters displacement) and not forced, DO NOT send network request
                    const distDeg = Math.hypot(lat - window._lastReportedDriverLoc.lat, lng - window._lastReportedDriverLoc.lng);
                    if (!force && distDeg < 0.000045) { // ~5 meters
                        return;
                    }

                    // Throttle: minimum 4.0s between location updates unless forced
                    if (!force && (now - window._lastReportedDriverLoc.time < 4000)) {
                        return;
                    }

                    if (_driverLocPostInFlight && !force) return;

                    clearTimeout(_pendingLocTimer);
                    window._lastReportedDriverLoc = { lat: lat, lng: lng, time: now };
                    _executeDriverLocPost(lat, lng);
                };

                function _executeDriverLocPost(lat, lng) {
                    if (_driverLocPostInFlight) return;
                    const mapEl = document.getElementById('grab-home-map');
                    if (!mapEl || !document.body.contains(mapEl)) return;
                    _driverLocPostInFlight = true;

                    const sheetContainer = document.getElementById('driver-bottom-sheet') || document.getElementById('active-trip-wrapper') || document.getElementById('incoming-ride-marker');
                    let rideId = sheetContainer ? (sheetContainer.dataset.rideId || sheetContainer.getAttribute('data-ride-id')) : null;
                    if (!rideId || rideId === '' || rideId === '0') {
                        rideId = '{{ $activeAcceptedTrip ? $activeAcceptedTrip->id : "" }}';
                    }
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                    const heading = (window.srhTricycle && typeof window.srhTricycle.heading === 'number') ? window.srhTricycle.heading : 0;
                    const endpoint = (rideId && rideId !== '' && rideId !== '0') ? `/rides/${rideId}/update-location` : `/driver/update-location`;
                    fetch(endpoint, {
                        method: 'POST',
                        keepalive: true,
                        headers: { 
                            'Content-Type': 'application/json', 
                            'X-CSRF-TOKEN': csrfToken, 
                            'X-Requested-With': 'XMLHttpRequest' 
                        },
                        body: JSON.stringify({ lat, lng, heading, ride_id: rideId || null })
                    })
                    .catch(() => {})
                    .finally(() => {
                        _driverLocPostInFlight = false;
                    });
                }

                // Clear any legacy interval timer
                if (window._driverActiveTripLocationTimer) {
                    clearInterval(window._driverActiveTripLocationTimer);
                    window._driverActiveTripLocationTimer = null;
                }

                window.__srhOnLocationUpdated = function(e) {
                    if (e.detail && e.detail.lat && e.detail.lng) {
                        const lat = parseFloat(e.detail.lat);
                        const lng = parseFloat(e.detail.lng);
                        const isSimulated = !!(e.detail && e.detail.isSimulated);
                        const m = driverMarker || window.driverMarker;
                        if (m) {
                            m.setLngLat([lng, lat]);
                        } else if (homeMap) {
                            if (window.driverMarker) {
                                try { window.driverMarker.remove(); } catch(e){}
                            }
                            driverMarker = new maplibregl.Marker({ element: driverEl, draggable: true, pitchAlignment: 'map', rotationAlignment: 'map' })
                                .setLngLat([lng, lat])
                                .addTo(homeMap);
                            window.driverMarker = driverMarker;
                            if (typeof makeDriverMarkerDraggable === 'function') makeDriverMarkerDraggable(driverMarker);
                        }
                        
                        update3rdPersonNavigationCamera(lat, lng, isSimulated);
                        checkAutoTerminalGeofenceReturn(lat, lng);
                        updateActiveTripRoute(lat, lng);
                    }
                };
                window.removeEventListener('srh-location-updated', window.__srhOnLocationUpdated);
                window.addEventListener('srh-location-updated', window.__srhOnLocationUpdated);

                window.addEventListener('srh-use-real-gps', () => {
                    localStorage.removeItem('srh_simulated_lat');
                    localStorage.removeItem('srh_simulated_lng');
                    if (typeof initCompassAndRealtimeGps === 'function') {
                        initCompassAndRealtimeGps();
                    }
                });
            }

            window.syncFloatingButtonsPosition = function() {
                let sheetContainer = document.getElementById('active-trip-wrapper') || document.getElementById('driver-bottom-sheet') || document.getElementById('suspended-driver-wrapper');
                if (!sheetContainer) {
                    const incWrap = document.getElementById('incoming-ride-wrapper');
                    if (incWrap && incWrap.classList.contains('srh-inc-visible')) sheetContainer = incWrap;
                }
                const powerBtn = document.getElementById('floating-power-container');
                const controlsBtn = document.getElementById('floating-map-controls-container');

                const isTripActive = (sheetContainer && (sheetContainer.dataset.activeStatus || sheetContainer.dataset.rideId)) || window.__srhActiveTrip;

                if (powerBtn) {
                    if (isTripActive) {
                        powerBtn.style.display = 'none';
                    } else {
                        powerBtn.style.display = 'flex';
                    }
                }
                if (controlsBtn) {
                    controlsBtn.style.display = 'flex';
                }

                if (!sheetContainer) return;

                const mapHub = document.getElementById('grab-home-map')?.parentElement || document.body;
                const mapHubRect = mapHub.getBoundingClientRect();
                const sheetRect = sheetContainer.getBoundingClientRect();

                const minSafeBottom = (window.innerWidth < 640) ? 76 : 84;
                let effectiveSheetTopFromMapBottom = 0;
                if (sheetRect.width > 0 && sheetRect.height > 0) {
                    const calculatedTop = mapHubRect.bottom - sheetRect.top;
                    effectiveSheetTopFromMapBottom = Math.max(0, calculatedTop);
                }

                const targetBottomNum = Math.max(minSafeBottom, Math.round(effectiveSheetTopFromMapBottom + 15));
                const targetBottom = targetBottomNum + 'px';

                // 🚀 Start fading floating buttons when position reaches the threshold (red line above resting position)
                const fadeStartPos = Math.max(380, Math.round(window.innerHeight * 0.52));
                let buttonOpacity = 1;
                if (targetBottomNum > fadeStartPos) {
                    const fadeProgress = (targetBottomNum - fadeStartPos) / 90;
                    buttonOpacity = Math.max(0, Math.min(1, 1 - fadeProgress));
                }

                if (powerBtn) {
                    powerBtn.style.transition = 'none';
                    powerBtn.style.bottom = targetBottom;
                    powerBtn.style.opacity = buttonOpacity;
                    powerBtn.style.pointerEvents = buttonOpacity > 0.3 ? 'auto' : 'none';
                }
                if (controlsBtn) {
                    controlsBtn.style.transition = 'none';
                    controlsBtn.style.bottom = targetBottom;
                    controlsBtn.style.opacity = buttonOpacity;
                    controlsBtn.style.pointerEvents = buttonOpacity > 0.3 ? 'auto' : 'none';
                }

                if (window.syncHomeMapPadding) window.syncHomeMapPadding();
            };

            window.useRealGpsAndCalibrateCompass = function() {
                // 1. Clear simulated location overrides from localStorage
                localStorage.removeItem('srh_simulated_lat');
                localStorage.removeItem('srh_simulated_lng');

                // 2. Calibrate marker orientation upright (0deg)
                const markerImg = document.querySelector('#driver-tricycle-marker-img');
                if (markerImg) {
                    markerImg.style.transform = 'rotate(0deg)';
                }
                if (window.srhTricycle) {
                    window.srhTricycle.heading = 0;
                }

                // 3. Request iOS/Android orientation permission explicitly on user gesture
                if (typeof DeviceOrientationEvent !== 'undefined' && typeof DeviceOrientationEvent.requestPermission === 'function') {
                    DeviceOrientationEvent.requestPermission()
                        .then(res => {
                            if (res === 'granted' && typeof handleCompassOrientation === 'function') {
                                window.removeEventListener('deviceorientation', handleCompassOrientation, true);
                                window.addEventListener('deviceorientation', handleCompassOrientation, true);
                                if (window.createSlidingToast) window.createSlidingToast("🧭 iPhone Compass Connected!", "success");
                            }
                        })
                        .catch(() => {});
                }

                // 4. Query real hardware GPS position & center camera upfront over-the-shoulder
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(pos => {
                        const realLat = pos.coords.latitude;
                        const realLng = pos.coords.longitude;
                        const heading = (typeof pos.coords.heading === 'number' && !isNaN(pos.coords.heading)) ? pos.coords.heading : 0;

                        if (window.srhTricycle) {
                            window.srhTricycle.lng = realLng;
                            window.srhTricycle.lat = realLat;
                            window.srhTricycle.heading = heading;
                        }

                        if (driverMarker) {
                            driverMarker.setLngLat([realLng, realLat]);
                            if (window.setDriverMarkerOpacity) window.setDriverMarkerOpacity(driverMarker, 1);
                        }

                        if (homeMap && typeof window.centerOnTricycle === 'function') {
                            window.centerOnTricycle(homeMap, { lng: realLng, lat: realLat, pitch: 60, heading: heading, duration: 800 });
                        }

                        if (typeof handleDriverLocationChanged === 'function') {
                            handleDriverLocationChanged(realLat, realLng);
                        }

                        window.dispatchEvent(new CustomEvent('srh-location-updated', {
                            detail: { lat: realLat, lng: realLng, isSimulated: false }
                        }));

                        if (window.createSlidingToast) {
                            window.createSlidingToast("📡 Switched to Real Live GPS & Calibrated Compass!", "success");
                        }
                    }, err => {
                        if (homeMap && typeof window.centerOnTricycle === 'function') {
                            window.centerOnTricycle(homeMap, { pitch: 60, heading: 0, duration: 800 });
                        }
                        if (window.createSlidingToast) {
                            window.createSlidingToast("🧭 Compass Orientation Calibrated to 0°", "success");
                        }
                    }, {
                        enableHighAccuracy: true,
                        maximumAge: 0,
                        timeout: 10000
                    });
                } else {
                    if (homeMap && typeof window.centerOnTricycle === 'function') {
                        window.centerOnTricycle(homeMap, { pitch: 60, heading: 0, duration: 800 });
                    }
                    if (window.createSlidingToast) {
                        window.createSlidingToast("🧭 Compass Orientation Calibrated to 0°", "success");
                    }
                }
            };

            function getOpticalCenterLatLng(lat, lng, targetZoom) {
                return { lat: parseFloat(lat), lng: parseFloat(lng) };
            }

            function animateFloatingButtonsSync(durationMs = 380) {
                const startTime = performance.now();
                function step(now) {
                    if (window.syncFloatingButtonsPosition) window.syncFloatingButtonsPosition();
                    if (now - startTime < durationMs) {
                        requestAnimationFrame(step);
                    } else {
                        if (window.syncFloatingButtonsPosition) window.syncFloatingButtonsPosition();
                        if (typeof window.centerDriverOnTricycle === 'function') window.centerDriverOnTricycle(550);
                    }
                }
                requestAnimationFrame(step);
            }
            window.animateFloatingButtonsSync = animateFloatingButtonsSync;

            window.toggleActiveTripSheet = function() {
                const sheet = document.getElementById('active-trip-details-content');
                const chevron = document.getElementById('active-trip-chevron');

                if (!sheet) return;

                const isExpanded = sheet.style.maxHeight !== '0px' && sheet.style.maxHeight !== '';

                sheet.style.transition = 'max-height 0.38s cubic-bezier(0.32, 0.72, 0, 1), opacity 0.28s ease-out, margin-top 0.38s ease-out';

                if (isExpanded) {
                    sheet.style.maxHeight = '0px';
                    sheet.style.opacity = '0';
                    sheet.style.marginTop = '0px';
                    sheet.style.pointerEvents = 'none';
                    if (chevron) chevron.style.transform = 'rotate(0deg)';
                } else {
                    sheet.style.maxHeight = '320px';
                    sheet.style.opacity = '1';
                    sheet.style.marginTop = '4px';
                    sheet.style.pointerEvents = 'auto';
                    if (chevron) chevron.style.transform = 'rotate(180deg)';
                }

                animateFloatingButtonsSync(400);
            };

            window.toggleDriverSheet = function() {
                const handle = document.getElementById('sheet-drag-handle');
                if (handle && typeof handle.srhToggleSheet === 'function') {
                    handle.srhToggleSheet();
                }
            };

            window.openTerminalWalkInModal = function(type, e) {
                if (type && typeof type === 'object' && !e) {
                    e = type;
                    type = 'walkin';
                }
                if (e) {
                    if (typeof e.preventDefault === 'function') e.preventDefault();
                    if (typeof e.stopPropagation === 'function') e.stopPropagation();
                }
                const modal = document.getElementById('walkin-fare-modal');
                if (modal) {
                    modal.dataset.submitting = 'false';
                    const isWayside = (type === 'wayside');
                    modal.dataset.rideType = isWayside ? 'wayside' : 'walkin';

                    const titleEl = modal.querySelector('h3');
                    const labelEl = modal.querySelector('label[for="terminal_fare"]');
                    if (titleEl) titleEl.innerText = isWayside ? 'Start Wayside Passenger Ride' : 'Start Terminal Walk-In Ride';
                    if (labelEl) labelEl.innerText = isWayside ? 'WAYSIDE FARE RATE' : 'BASE TERMINAL FARE RATE';

                    const submitBtn = modal.querySelector('button[onclick*="handleWalkInSubmit"]');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.style.opacity = '1';
                        submitBtn.innerHTML = 'Start Trip Now';
                    }

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    modal.style.display = 'flex';
                    requestAnimationFrame(() => {
                        modal.classList.remove('opacity-0');
                        modal.classList.add('opacity-100');
                    });
                }
            };

            window.closeTerminalWalkInModal = function() {
                const modal = document.getElementById('walkin-fare-modal');
                if (modal) {
                    modal.classList.remove('opacity-100');
                    modal.classList.add('opacity-0');

                    setTimeout(() => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        modal.style.display = 'none';
                        modal.dataset.submitting = 'false';
                        const submitBtn = modal.querySelector('button[onclick*="handleWalkInSubmit"]');
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.style.opacity = '1';
                            submitBtn.innerHTML = 'Start Trip Now';
                        }
                    }, 300);
                }
            };

            window.setFare = function(inputId, val, btn) {
                const inp = document.getElementById(inputId);
                let numVal = parseFloat(val) || 30;
                if (numVal > 500) numVal = 500;
                if (inp) inp.value = numVal.toFixed(2);

                if (btn && btn.parentNode) {
                    btn.parentNode.querySelectorAll('button').forEach(b => {
                        b.className = "flex-1 py-2 bg-slate-100 hover:bg-blue-600 hover:text-white rounded-xl text-xs font-black text-slate-700 border border-slate-200 transition";
                    });
                    btn.className = "flex-1 py-2 bg-blue-600 text-white rounded-xl text-xs font-black border border-blue-600 transition";
                }
            };

            window.handleWalkInSubmit = function(e) {
                if (e && e.preventDefault) e.preventDefault();
                const modal = document.getElementById('walkin-fare-modal');
                if (!modal || modal.dataset.submitting === 'true') return;
                modal.dataset.submitting = 'true';
                const isWayside = (modal.dataset.rideType === 'wayside');

                const submitBtn = modal.querySelector('button[onclick*="handleWalkInSubmit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.style.pointerEvents = 'none';
                    submitBtn.style.opacity = '0.85';
                    const spinner = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
                    submitBtn.innerHTML = `<span class="inline-flex items-center justify-center gap-1.5">${spinner} STARTING TRIP...</span>`;
                }

                const selectedFare = document.getElementById('terminal_fare')?.value || '30.00';
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
                formData.append('fare', selectedFare);
                if (isWayside) formData.append('type', 'wayside');

                fetch('{{ route("rides.walk-in") }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    if (data.error) {
                        if (window.createSlidingToast) window.createSlidingToast(data.error, 'danger');
                        modal.dataset.submitting = 'false';
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.style.pointerEvents = '';
                            submitBtn.style.opacity = '1';
                            submitBtn.innerHTML = 'Start Trip Now';
                        }
                        return;
                    }

                    const rideId = data.ride_id;
                    if (window.closeTerminalWalkInModal) window.closeTerminalWalkInModal();
                    if (window.createSlidingToast) window.createSlidingToast(data.message || 'Walk-in trip started!', 'success');
                    if (window.clearPageCache) window.clearPageCache();

                    // Now that server confirmed 200 OK and rideId, transition sheet
                    if (typeof clearHomeRouteLines === 'function') clearHomeRouteLines();
                    window._lastRouteOrigin = null;
                    window._lastRouteTarget = null;

                    const powerBtn = document.getElementById('floating-power-container');
                    if (powerBtn) {
                        powerBtn.style.transition = 'all 0.3s ease-out';
                        powerBtn.style.opacity = '0';
                        powerBtn.style.transform = 'scale(0.8)';
                        setTimeout(() => { powerBtn.style.display = 'none'; }, 300);
                    }

                    const statusDot = document.getElementById('status-indicator-dot');
                    const statusText = document.getElementById('status-indicator-text');
                    const queueBadge = document.getElementById('compact-queue-badge');
                    const detailsContent = document.getElementById('sheet-details-content');
                    const allSheets = [document.getElementById('driver-bottom-sheet'), document.getElementById('active-trip-wrapper')].filter(Boolean);

                    allSheets.forEach(s => {
                        s.dataset.activeStatus = isWayside ? 'in_transit' : 'accepted';
                        if (rideId) s.dataset.rideId = rideId;
                        if (isWayside) {
                            s.dataset.pickupLat = '';
                            s.dataset.pickupLng = '';
                            s.dataset.destLat = '';
                            s.dataset.destLng = '';
                        }
                    });

                    if (isWayside) {
                        (window._srhActivePins || []).forEach(p => { try { p.remove(); } catch(e){} });
                        window._srhActivePins = [];
                        window._srhActiveRouteCoordinates = null;
                        window._hasActivePathwayMode = false;
                        if (typeof clearHomeRouteLines === 'function') clearHomeRouteLines();
                        if (typeof window.resetSrhCompassMode === 'function') {
                            window.resetSrhCompassMode(homeMap);
                        } else if (typeof window.setUnifiedMapButtonState === 'function') {
                            window.setUnifiedMapButtonState(2);
                        }
                    }

                    if (statusDot) statusDot.className = 'w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse';
                    if (statusText) statusText.innerText = isWayside ? 'PASSENGER ON BOARD (WAYSIDE)' : 'ON ACTIVE RIDE';
                    if (queueBadge) {
                        if (isWayside) {
                            queueBadge.style.display = 'none';
                            queueBadge.innerHTML = '';
                        } else {
                            queueBadge.style.display = '';
                            queueBadge.innerHTML = 'ACTIVE RIDE';
                        }
                    }

                    if (detailsContent) {
                        detailsContent.innerHTML = `
                            <div id="active-ride-action-buttons" class="space-y-2.5 pt-0.5 sheet-content-animate-in">
                                <button type="button" id="btn-drop-off-action" data-ride-id="${rideId || ''}" onclick="submitRideAction('/rides/' + (this.dataset.rideId || '${rideId}') + '/start-returning', 'PATCH', {}, this, event)" style="background: #0f172a !important; color: #ffffff !important;" class="w-full py-3.5 px-4 rounded-2xl font-black text-xs uppercase tracking-wider shadow-md shadow-slate-900/10 active:scale-[0.98] transition-all flex items-center justify-center gap-2.5 cursor-pointer">
                                    <span class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5 text-emerald-400 fill-none stroke-current" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <span style="color: #ffffff !important;" class="font-black">Drop Off & Return to Queue</span>
                                </button>
                            </div>
                        `;
                        detailsContent.style.maxHeight = (window.innerWidth < 640) ? '260px' : '320px';
                        detailsContent.style.opacity = '1';
                        detailsContent.style.marginTop = '12px';
                        detailsContent.style.pointerEvents = 'auto';
                        const chevron = document.getElementById('sheet-chevron');
                        if (chevron) chevron.style.transform = 'rotate(180deg)';
                        if (window.syncFloatingButtonsPosition) window.syncFloatingButtonsPosition();
                        if (window.hugDriverActiveTripSheet) window.hugDriverActiveTripSheet(420);
                    }

                    modal.dataset.submitting = 'false';
                })
                .catch(err => {
                    modal.dataset.submitting = 'false';
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.style.pointerEvents = '';
                        submitBtn.style.opacity = '1';
                        submitBtn.innerHTML = 'Start Trip Now';
                    }
                    if (window.createSlidingToast) window.createSlidingToast('Connection error. Please try again.', 'danger');
                });
            };

            window.triggerDriverActionInstantUI = function(url) {
                const isCancel = url.includes('/cancel');
                const isArrived = url.includes('/arrived');
                const isStartTransit = url.includes('/start-transit');
                const isStartReturning = url.includes('/start-returning');
                const isCompleteReturn = url.includes('/complete-return') || url.includes('/complete');

                const statusDot = document.getElementById('status-indicator-dot');
                const statusText = document.getElementById('status-indicator-text');
                const queueBadge = document.getElementById('compact-queue-badge');
                const detailsContent = document.getElementById('sheet-details-content');
                const powerBtn = document.getElementById('floating-power-container');
                const activeSheet = document.getElementById('driver-bottom-sheet') || document.getElementById('active-trip-wrapper');
                const allSheets = [document.getElementById('driver-bottom-sheet'), document.getElementById('active-trip-wrapper')].filter(Boolean);

                const match = url.match(/\/rides\/([^\/]+)/);
                const rideId = match && match[1] ? match[1] : (activeSheet?.dataset?.rideId || '');

                if (isCancel || isCompleteReturn) {
                    window._justCompletedReturn = Date.now();
                    window.__srhActiveTrip = null;
                    if (typeof window.srhResetLastState === 'function') window.srhResetLastState();
                    allSheets.forEach(s => {
                        s.dataset.activeStatus = '';
                        s.dataset.rideId = '';
                    });
                    if (typeof clearHomeRouteLines === 'function') clearHomeRouteLines();

                    if (statusDot) statusDot.className = 'w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse';
                    if (statusText) statusText.innerText = "You're online";
                    if (queueBadge) {
                        queueBadge.style.display = '';
                        queueBadge.style.background = "linear-gradient(to right, #10b981, #0d9488, #2563eb)";
                        queueBadge.innerHTML = '<span>#1 IN QUEUE</span>';
                    }

                    if (detailsContent) {
                        detailsContent.innerHTML = `
                            <div id="queue-card-container" class="sticky top-0 z-30 bg-white pt-1 pb-2 shadow-xs rounded-2xl" data-is-online="true" data-position="1">
                                <div id="queue-card-states" class="relative rounded-2xl" data-online="true" data-position="1">
                                    <div id="online-queue-card" class="text-white rounded-2xl p-3.5 sm:p-4 text-center shadow-lg shadow-emerald-500/25 overflow-hidden flex flex-col items-center justify-center" data-position="1" data-theme="now">
                                        <div class="queue-theme-layer queue-theme-now"></div>
                                        <div class="queue-theme-layer queue-theme-wait"></div>
                                        <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-white/10 rounded-full blur-lg pointer-events-none z-10"></div>
                                        <div class="relative z-10 w-full">
                                        <p class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-emerald-100 mb-1">Queue Position</p>
                                        <h1 id="queue-position-heading" class="text-2xl xs:text-3xl sm:text-5xl font-black tracking-tight my-0.5 truncate">#1 IN QUEUE</h1>
                                        <p id="queue-position-subtext" class="text-[10px] sm:text-xs font-bold text-emerald-50 truncate mt-1">Next for TODA terminal & app passenger dispatch!</p>
                                        
                                        <button type="button" id="start-walkin-btn" onclick="handleTerminalWalkInClick(event)" class="w-full flex items-center justify-center gap-2 p-2.5 sm:p-3 rounded-2xl bg-white hover:bg-emerald-50 text-emerald-900 border border-emerald-200/80 active:scale-95 transition cursor-pointer mt-2.5 sm:mt-3">
                                            <span class="text-[11px] sm:text-xs font-black text-emerald-900 tracking-tight truncate">Start Terminal Walk-In Ride</span>
                                        </button>
                                        </div>
                                    </div>
                                    <div id="offline-queue-card" style="background: linear-gradient(150deg, #334155 0%, #1e293b 45%, #0f172a 100%) !important; color: #ffffff !important;" class="w-full max-w-full rounded-2xl p-3.5 sm:p-4 text-center shadow-xl shadow-2xl overflow-hidden flex flex-col items-center justify-center border border-slate-700">
                                        <div class="absolute -right-4 -bottom-4 w-20 h-20 rounded-full blur-lg pointer-events-none z-10" style="background: rgba(148, 163, 184, 0.1) !important;"></div>
                                        <div class="relative z-10 w-full">
                                        <p style="color: #cbd5e1 !important;" class="w-full text-[9px] sm:text-[10px] font-black uppercase tracking-widest mb-1">Driver Status</p>
                                        <h1 style="color: #ffffff !important;" class="w-full text-2xl xs:text-3xl sm:text-5xl font-black tracking-tight my-0.5 truncate">YOU'RE OFFLINE</h1>
                                        <p style="color: #94a3b8 !important;" class="w-full text-[10px] sm:text-xs font-bold truncate mt-1" title="Go online to enter TODA queue & receive passenger requests!">Go online to enter TODA queue & receive passenger requests!</p>
                                        <div class="w-full flex items-center justify-center gap-2 p-2.5 sm:p-3 rounded-2xl bg-transparent text-white/50 mt-2.5 sm:mt-3">
                                            <span class="text-[11px] sm:text-xs font-black text-white/50 tracking-tight truncate">Toggle the power button to go on duty</span>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        if (typeof restoreEmbeddedQueueSkeleton === 'function') {
                            restoreEmbeddedQueueSkeleton();
                        }
                        if (typeof fetchLiveQueue === 'function') {
                            fetchLiveQueue();
                        }
                    }

                    if (powerBtn) {
                        powerBtn.style.display = 'flex';
                        powerBtn.style.opacity = '1';
                        powerBtn.style.transform = 'scale(1)';
                    }
                    const controlsBtn = document.getElementById('floating-map-controls-container');
                    if (controlsBtn) {
                        controlsBtn.style.display = 'flex';
                        controlsBtn.style.opacity = '1';
                        controlsBtn.style.transform = 'scale(1)';
                    }

                } else if (isArrived) {
                    allSheets.forEach(s => s.dataset.activeStatus = 'arrived');
                    if (statusDot) statusDot.className = 'w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse';
                    if (statusText) statusText.innerText = 'ARRIVED AT PICKUP';
                    if (queueBadge) {
                        queueBadge.style.display = '';
                        queueBadge.className = 'px-2.5 py-0.5 sm:px-3 sm:py-1 bg-amber-500 text-white rounded-full text-[10px] sm:text-xs font-black uppercase tracking-wider shadow-sm shrink-0';
                        queueBadge.innerText = 'ARRIVED';
                    }
                    if (detailsContent) {
                        detailsContent.innerHTML = `
                            <div class="space-y-3 text-left sheet-content-animate-in">
                                <button type="button" onclick="submitRideAction('/rides/${rideId}/start-transit', 'PATCH', {}, this, event)" style="background: #059669 !important; color: #ffffff !important;" class="w-full py-3.5 px-4 rounded-2xl font-black text-xs uppercase tracking-wider shadow-md shadow-emerald-600/25 active:scale-[0.98] transition-all flex items-center justify-center gap-2.5 cursor-pointer">
                                    <span class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5 text-white fill-none stroke-current" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    </span>
                                    <span style="color: #ffffff !important;" class="font-black">Start Transit / Picked Up</span>
                                </button>
                            </div>
                        `;
                    }
                    window._lastRouteOrigin = null;
                    window._lastRouteTarget = null;
                    if (typeof updateActiveTripRoute === 'function') updateActiveTripRoute();
                    if (typeof drawActiveTripPins === 'function') drawActiveTripPins();

                } else if (isStartTransit) {
                    allSheets.forEach(s => s.dataset.activeStatus = 'in_transit');
                    if (statusDot) statusDot.className = 'w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse';
                    if (statusText) statusText.innerText = 'IN TRANSIT TO DESTINATION';
                    if (queueBadge) {
                        queueBadge.style.display = '';
                        queueBadge.className = 'px-2.5 py-0.5 sm:px-3 sm:py-1 bg-emerald-600 text-white rounded-full text-[10px] sm:text-xs font-black uppercase tracking-wider shadow-sm shrink-0';
                        queueBadge.innerText = 'IN TRANSIT';
                    }
                    if (detailsContent) {
                        detailsContent.innerHTML = `
                            <div class="space-y-3 text-left sheet-content-animate-in">
                                <button type="button" onclick="submitRideAction('/rides/${rideId}/start-returning', 'PATCH', {}, this, event)" style="background: #0f172a !important; color: #ffffff !important;" class="w-full py-3.5 px-4 rounded-2xl font-black text-xs uppercase tracking-wider shadow-md shadow-slate-900/10 active:scale-[0.98] transition-all flex items-center justify-center gap-2.5 cursor-pointer">
                                    <span class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5 text-emerald-400 fill-none stroke-current" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <span style="color: #ffffff !important;" class="font-black">Drop Off & Return to Queue</span>
                                </button>
                            </div>
                        `;
                    }
                    window._lastRouteOrigin = null;
                    window._lastRouteTarget = null;
                    if (typeof updateActiveTripRoute === 'function') updateActiveTripRoute();
                    if (typeof drawActiveTripPins === 'function') drawActiveTripPins();
                    if (window.animateMapForAction) window.animateMapForAction('start_ride');

                } else if (isStartReturning) {
                    window.autoReturnedToQueueTriggered = false;
                    const match = url.match(/\/rides\/([^\/]+)/);
                    const rideId = match && match[1] ? match[1] : null;
                    if (rideId) window._activeRideId = rideId;

                    allSheets.forEach(s => {
                        s.dataset.activeStatus = 'returning';
                        s.dataset.pickupLat = '';
                        s.dataset.pickupLng = '';
                        s.dataset.destLat = '';
                        s.dataset.destLng = '';
                        if (rideId) s.dataset.completeReturnUrl = `/rides/${rideId}/complete-return`;
                    });

                    // Remove drop off and pickup pins immediately upon starting return
                    (window._srhActivePins || []).forEach(p => { try { p.remove(); } catch(e){} });
                    window._srhActivePins = [];
                    if (typeof clearHomeRouteLines === 'function') clearHomeRouteLines();

                    // 🚀 Trigger pathway route fetch FIRST immediately on button tap
                    const curLat = driverMarker ? driverMarker.getLngLat().lat : (parseFloat(localStorage.getItem('srh_simulated_lat')) || TERMINAL_LAT);
                    const curLng = driverMarker ? driverMarker.getLngLat().lng : (parseFloat(localStorage.getItem('srh_simulated_lng')) || TERMINAL_LNG);
                    window._lastRouteOrigin = null;
                    window._lastRouteTarget = null;
                    if (typeof updateActiveTripRoute === 'function') {
                        updateActiveTripRoute(curLat, curLng);
                    } else if (typeof window.updateActiveTripRoute === 'function') {
                        window.updateActiveTripRoute(curLat, curLng);
                    }

                    if (statusDot) statusDot.className = 'w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse';
                    if (statusText) statusText.innerText = 'RETURNING TO TERMINAL';
                    if (queueBadge) {
                        queueBadge.style.display = 'none';
                        queueBadge.innerHTML = '';
                    }

                    if (detailsContent) {
                        detailsContent.innerHTML = `
                            <div style="background: #064e3b !important;" class="rounded-2xl p-4 text-center shadow-lg relative overflow-hidden sheet-content-animate-in">
                                <div class="flex items-center justify-center gap-2 mb-1">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                                    <p style="color: #a7f3d0 !important;" class="text-[10px] sm:text-[11px] font-black uppercase tracking-widest">Heading Back to TODA Terminal</p>
                                </div>
                                <h3 style="color: #ffffff !important;" class="text-base sm:text-lg font-black tracking-tight mt-1 mb-3">Auto-Joining Queue On Arrival</h3>

                                <button type="button" onclick="openTerminalWalkInModal('wayside', event)" class="w-full py-3 px-4 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-white font-black text-xs uppercase tracking-wider shadow-md active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer border border-emerald-400/30">
                                    <svg class="w-4 h-4 text-white fill-none stroke-current" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                    <span>+ Add Wayside Passenger</span>
                                </button>
                            </div>
                        `;
                        detailsContent.style.maxHeight = (window.innerWidth < 640) ? '220px' : '280px';
                        detailsContent.style.opacity = '1';
                        detailsContent.style.marginTop = '12px';
                        detailsContent.style.pointerEvents = 'auto';
                    }

                    if (typeof checkAutoTerminalGeofenceReturn === 'function') {
                        checkAutoTerminalGeofenceReturn(curLat, curLng);
                    }

                    window.animateMapForAction('start_returning');
                }

                if (window.syncFloatingButtonsPosition) window.syncFloatingButtonsPosition();
                // arrived / in-transit / returning / completed card swapped — sync sheet position.
                if (window.syncDriverDashboardSheet) window.syncDriverDashboardSheet(460);
                else if (window.hugDriverActiveTripSheet) window.hugDriverActiveTripSheet(460);
            };

            window.startWaysideRide = function(e) {
                if (e) {
                    if (typeof e.preventDefault === 'function') e.preventDefault();
                    if (typeof e.stopPropagation === 'function') e.stopPropagation();
                }

                const statusDot = document.getElementById('status-indicator-dot');
                const statusText = document.getElementById('status-indicator-text');
                const queueBadge = document.getElementById('compact-queue-badge');
                const detailsContent = document.getElementById('sheet-details-content');
                const activeSheet = document.getElementById('driver-bottom-sheet') || document.getElementById('active-trip-wrapper');
                const allSheets = [document.getElementById('driver-bottom-sheet'), document.getElementById('active-trip-wrapper')].filter(Boolean);

                allSheets.forEach(s => {
                    s.dataset.activeStatus = 'in_transit';
                    s.dataset.pickupLat = '';
                    s.dataset.pickupLng = '';
                    s.dataset.destLat = '';
                    s.dataset.destLng = '';
                });

                (window._srhActivePins || []).forEach(p => { try { p.remove(); } catch(e){} });
                window._srhActivePins = [];
                if (typeof clearHomeRouteLines === 'function') clearHomeRouteLines();

                if (statusDot) statusDot.className = 'w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse';
                if (statusText) statusText.innerText = 'PASSENGER ON BOARD (WAYSIDE)';
                if (queueBadge) {
                    queueBadge.style.display = 'none';
                    queueBadge.innerHTML = '';
                }

                if (detailsContent) {
                    const returnUrl = (activeSheet && activeSheet.dataset.completeReturnUrl) ? activeSheet.dataset.completeReturnUrl.replace('/complete-return', '/start-returning') : '/rides/active/start-returning';

                    detailsContent.innerHTML = `
                        <div class="space-y-3 text-left sheet-content-animate-in">
                            <div class="bg-gradient-to-r from-emerald-50 via-teal-50 to-emerald-100 border border-emerald-200/90 rounded-2xl p-3.5 flex items-center justify-between shadow-xs">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shadow-sm shrink-0">
                                        <svg class="w-4 h-4 fill-none stroke-current" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                    <div>
                                        <span class="text-[9px] font-black uppercase tracking-wider text-emerald-800 bg-emerald-200/80 px-2 py-0.5 rounded-full inline-block">Wayside Pickup</span>
                                        <h4 class="text-xs font-black text-slate-800 my-0.5">Additional Passenger On Board</h4>
                                    </div>
                                </div>
                            </div>

                            {{-- Primary Drop Off Button (NO CANCEL BUTTON as requested!) --}}
                            <button type="button" onclick="submitRideAction('${returnUrl}', 'PATCH', {}, this, event)" style="background: #0f172a !important; color: #ffffff !important;" class="w-full py-3.5 px-4 rounded-2xl font-black text-xs uppercase tracking-wider shadow-md shadow-slate-900/10 active:scale-[0.98] transition-all flex items-center justify-center gap-2.5 cursor-pointer">
                                <span class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5 text-emerald-400 fill-none stroke-current" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <span style="color: #ffffff !important;" class="font-black">Drop Off & Return to Queue</span>
                            </button>
                        </div>
                    `;
                    detailsContent.style.maxHeight = (window.innerWidth < 640) ? '260px' : '320px';
                    detailsContent.style.opacity = '1';
                    detailsContent.style.marginTop = '12px';
                    detailsContent.style.pointerEvents = 'auto';
                }

                if (typeof clearHomeRouteLines === 'function') clearHomeRouteLines();
                if (window.animateMapForAction) window.animateMapForAction('start_ride');
                if (window.syncFloatingButtonsPosition) window.syncFloatingButtonsPosition();
                // Wayside boarding card mounted — snug the sheet to the real content.
                if (window.hugDriverActiveTripSheet) window.hugDriverActiveTripSheet(460);
            };

            window.handleDriverRideActionSuccess = function(url, method, data) {
                // Incoming ride overlay: route the AJAX outcome to the overlay manager
                if (typeof window.srhOnRideActionSuccess === 'function') {
                    try {
                        window.srhOnRideActionSuccess(url, method, data);
                    } catch (e) {}
                }
                if (window.syncFloatingButtonsPosition) window.syncFloatingButtonsPosition();
            };



            function getDistanceMeters(lat1, lon1, lat2, lon2) {
                const R = 6371000;
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLng = (lon2 - lon1) * Math.PI / 180;
                const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                          Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                          Math.sin(dLng / 2) * Math.sin(dLng / 2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return R * c;
            }
            window.getDistanceMeters = getDistanceMeters;

            window.toggleDutyStatus = function(btn) {
                // If a toggle is already connecting/in flight, ignore spam clicks
                if (window.__srhDutyToggleInFlight) {
                    if (window.createSlidingToast) {
                        window.createSlidingToast('Connecting... Please wait.', 'info');
                    }
                    return;
                }

                const powerContainer = document.getElementById('power-toggle-btn');
                const powerIcon = document.getElementById('power-icon');
                const powerSpinner = document.getElementById('power-spinner');

                // ⚡ Trigger 1-Shot Momentary Click Ripple Animation (0.6s)
                if (powerContainer) {
                    powerContainer.classList.add('click-ripple-pulse');
                    setTimeout(() => {
                        powerContainer.classList.remove('click-ripple-pulse');
                    }, 600);
                }

                const isCurrentlyOnline = powerContainer ? powerContainer.classList.contains('bg-emerald-500') : false;
                const nextOnlineState = !isCurrentlyOnline;

                // Lock clicks immediately (0ms) to prevent spamming
                window.__srhDutyToggleInFlight = true;

                // ⏳ Delayed Spinner (350ms): Fast connections complete with zero spinner flicker;
                // slow connections show the spinner smoothly after 350ms so user knows it's working.
                clearTimeout(window.__srhDutySpinnerTimer);
                window.__srhDutySpinnerTimer = setTimeout(() => {
                    if (window.__srhDutyToggleInFlight) {
                        if (powerIcon) powerIcon.classList.add('hidden');
                        if (powerSpinner) powerSpinner.classList.remove('hidden');
                    }
                }, 350);

                const simLat = localStorage.getItem('srh_simulated_lat');
                const simLng = localStorage.getItem('srh_simulated_lng');
                const geoLat = localStorage.getItem('srh_last_real_lat');
                const geoLng = localStorage.getItem('srh_last_real_lng');

                if (nextOnlineState) {
                    if (window.srhRequestNotificationPermission) {
                        window.srhRequestNotificationPermission();
                    }
                    const TERMINAL_LAT = 15.429550175641715;
                    const TERMINAL_LNG = 120.92240292427664;
                    const TERMINAL_MAX_RADIUS = 45; // 35m + 10m mobile GPS tolerance

                    function verifyAndSend(lat, lng) {
                        if (lat !== null && lng !== null) {
                            const distance = getDistanceMeters(lat, lng, TERMINAL_LAT, TERMINAL_LNG);
                            if (distance > TERMINAL_MAX_RADIUS) {
                                window.__srhDutyToggleInFlight = false;
                                clearTimeout(window.__srhDutySpinnerTimer);
                                const powerIcon = document.getElementById('power-icon');
                                const powerSpinner = document.getElementById('power-spinner');
                                if (powerIcon) powerIcon.classList.remove('hidden');
                                if (powerSpinner) powerSpinner.classList.add('hidden');
                                if (window.createSlidingToast) {
                                    window.createSlidingToast(`Outside Terminal Area (${Math.round(distance)}m away). You must be at the TODA Terminal (within 35m) to go on duty.`, 'danger');
                                }
                                return;
                            }
                        }
                        sendDutyStatusRequest(true, lat, lng);
                    }

                    if (simLat && simLng) {
                        verifyAndSend(parseFloat(simLat), parseFloat(simLng));
                    } else if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(pos => {
                            verifyAndSend(pos.coords.latitude, pos.coords.longitude);
                        }, () => {
                            verifyAndSend(geoLat ? parseFloat(geoLat) : null, geoLng ? parseFloat(geoLng) : null);
                        }, { enableHighAccuracy: true, timeout: 3500 });
                    } else {
                        verifyAndSend(geoLat ? parseFloat(geoLat) : null, geoLng ? parseFloat(geoLng) : null);
                    }
                } else {
                    // Going OFFLINE is always allowed
                    sendDutyStatusRequest(false, null, null);
                }
            };

            function srhNextQueuePositionEstimate() {
                const wrapper = document.getElementById('live-queue-wrapper');
                const queueItems = wrapper ? wrapper.querySelector('#queue-items-container') : null;
                if (queueItems && queueItems.children.length > 0) {
                    return queueItems.children.length + 1;
                }
                if (window.__srhLastQueueTotal && window.__srhLastQueueTotal > 0) {
                    return window.__srhLastQueueTotal + 1;
                }
                return 1;
            }

            function sendDutyStatusRequest(nextOnlineState, lat, lng) {
                // ⚡ Instant 0ms Optimistic UI Update: Update toggle, dots, badges & card immediately on touch
                // Suppress the hub poller while the flip is in flight so a poll
                // racing with the optimistic update can't flick the queue card back.
                window.__srhDutyToggleInFlight = true;
                const optimisticPos = nextOnlineState ? srhNextQueuePositionEstimate() : null;
                updateDutyUI(nextOnlineState, optimisticPos, null);

                const formData = new FormData();
                formData.append('_token', "{{ csrf_token() }}");
                formData.append('is_online', nextOnlineState ? '1' : '0');
                if (lat !== null && lng !== null) {
                    formData.append('lat', lat);
                    formData.append('lng', lng);
                }

                fetch("{{ route('driver.toggle-status') }}", {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    if (data.error) {
                        if (window.createSlidingToast) {
                            window.createSlidingToast(data.error, 'danger');
                        }
                        // Revert UI if server returned error
                        updateDutyUI(!nextOnlineState, null, null);
                    } else {
                        // Invalidate the SPA page cache so the next navigation to
                        // the hub re-fetches server-rendered HTML with the new
                        // duty state instead of showing the stale cached page.
                        if (window.createSlidingToast) {
                            window.createSlidingToast(data.message, 'success');
                        }
                        updateDutyUI(data.is_online, data.queue_position, data.total_queue_count);
                        if (window.srhRefreshQueueList) {
                            window.srhRefreshQueueList();
                        }
                    }
                })
                .catch(err => {
                    if (window.createSlidingToast) {
                        window.createSlidingToast('Connection error. Please try again.', 'danger');
                    }
                })
                .finally(() => {
                    clearTimeout(window.__srhDutySpinnerTimer);
                    window.__srhDutySpinnerTimer = null;
                    window.__srhDutyToggleInFlight = false;
                    const powerIcon = document.getElementById('power-icon');
                    const powerSpinner = document.getElementById('power-spinner');
                    if (powerIcon) powerIcon.classList.remove('hidden');
                    if (powerSpinner) powerSpinner.classList.add('hidden');
                });
            }

            window.getOrdinalSuffix = function(n) {
                const num = parseInt(n, 10) || 1;
                const s = ["th", "st", "nd", "rd"];
                const v = num % 100;
                return num + (s[(v - 20) % 10] || s[v] || s[0]);
            };

            window.getQueueSubtext = function(n) {
                const num = parseInt(n, 10) || 1;
                if (num === 1) {
                    return "Next for TODA terminal & app passenger dispatch!";
                }
                return window.getOrdinalSuffix(num) + " for TODA terminal & app passenger dispatch!";
            };

            window.handleTerminalWalkInClick = function(event) {
                // Source of truth: the driver's own row in the live queue list,
                // then fall back to the card dataset only when no list is loaded.
                const wrapper = document.getElementById('live-queue-wrapper');
                const queueItems = wrapper ? wrapper.querySelector('#queue-items-container') : null;
                const hasItems = !!(queueItems && queueItems.children.length > 0);

                let pos = null;
                if (hasItems && window.srhMyDriverId) {
                    const rows = Array.prototype.slice.call(queueItems.children);
                    const myRow = rows.find(el => el.dataset && el.dataset.driverId && el.dataset.driverId === String(window.srhMyDriverId));
                    if (myRow) {
                        const badgeEl = myRow.querySelector('.queue-number-badge');
                        pos = badgeEl ? parseInt((badgeEl.textContent || '').replace(/[^\d]/g, ''), 10) : (rows.indexOf(myRow) + 1);
                        if (!pos || pos < 1) pos = null;
                    }
                }
                if (pos === null && !hasItems) {
                    const card = document.getElementById('online-queue-card');
                    const cPos = card && card.dataset ? parseInt(card.dataset.position || '', 10) : NaN;
                    if (!isNaN(cPos)) pos = cPos;
                }

                if (pos === 1) {
                    openTerminalWalkInModal(event);
                    return;
                }
                const ord = pos ? window.getOrdinalSuffix(pos) : 'not';
                const msg = pos
                    ? `You must be #1 in queue to start a terminal walk-in ride. You are currently ${ord} in queue.`
                    : 'You must be #1 in queue to start a terminal walk-in ride. You are currently not in queue.';
                if (window.createSlidingToast) {
                    window.createSlidingToast(msg, 'warning');
                } else {
                    alert(msg);
                }
            };

            function updateDutyUIImpl(isOnline, position, totalCount) {
                // Global source of truth for duty state (vault-restore resyncs and
                // queue-position syncs read this instead of guessing online).
                window.__srhIsOnline = !!isOnline;
                // 🚧 Active trip in progress or starting → the trip card/header is authoritative.
                // removeAndShift marks is_online=false in the DB while the driver is
                // on a ride, so duty-state flips from the poller must NEVER rewrite
                // the sheet texts (You're offline / OFF DUTY) over the trip UI.
                if (window.__srhActiveTrip) return;
                // 🚧 A fare offer is up or pending for this driver → the offer
                // overlay is authoritative. The driver is off the queue by design
                // while the passenger decides, so never flip the sheet to
                // "You're offline" underneath a live offer (declining restores
                // the online card via the cancel ack).
                const incState = window.__srhInc && window.__srhInc.state;
                if (incState && incState !== 'hidden') return;
                const dutySheet = document.getElementById('driver-bottom-sheet') || document.getElementById('active-trip-wrapper');
                const dutyTripStatus = dutySheet ? dutySheet.dataset.activeStatus : '';
                if (dutyTripStatus && ['accepted', 'arrived', 'in_transit', 'returning'].indexOf(dutyTripStatus) !== -1) {
                    return;
                }

                const powerContainer = document.getElementById('power-toggle-btn');
                const statusDot = document.getElementById('status-indicator-dot');
                const tooltipDot = document.getElementById('tooltip-status-dot');
                const statusText = document.getElementById('status-indicator-text');
                const tooltipText = document.getElementById('power-tooltip-text');
                const compactBadge = document.getElementById('compact-queue-badge');
                let queueCardContainer = document.getElementById('queue-card-container');
                const clipContainer = document.getElementById('live-queue-clip-container');

                if (powerContainer) {
                    powerContainer.classList.toggle('bg-emerald-500', isOnline);
                    powerContainer.classList.toggle('shadow-emerald-500/40', isOnline);
                    powerContainer.classList.toggle('bg-slate-900', !isOnline);
                    powerContainer.classList.toggle('shadow-slate-900/40', !isOnline);
                }

                if (statusDot) {
                    statusDot.classList.toggle('bg-emerald-500', isOnline);
                    statusDot.classList.toggle('animate-pulse', isOnline);
                    statusDot.classList.toggle('bg-rose-500', !isOnline);
                }

                if (tooltipDot) {
                    if (isOnline) {
                        tooltipDot.className = 'w-2 h-2 rounded-full bg-emerald-500 animate-pulse inline-block';
                    } else {
                        tooltipDot.className = 'hidden';
                    }
                }

                if (statusText) {
                    statusText.textContent = isOnline ? "You're online" : "You're offline";
                }

                if (tooltipText) {
                    tooltipText.textContent = isOnline ? "Take a break?" : "Go On Duty?";
                }

                // Toggle tricycle marker pulsing halo/glow (visible ONLY when online)
                const markerPing = document.getElementById('driver-marker-ping');
                const markerDot = document.getElementById('driver-marker-dot');
                if (markerPing) markerPing.style.display = isOnline ? 'block' : 'none';
                if (markerDot) markerDot.style.display = isOnline ? 'block' : 'none';

                const posNum = (position && position > 0) ? parseInt(position, 10) : 1;

                if (compactBadge) {
                    if (isOnline) {
                        compactBadge.className = "px-2.5 py-0.5 sm:px-3 sm:py-1 text-white rounded-full text-[10px] sm:text-xs font-black uppercase tracking-wider shadow-sm flex items-center gap-1";
                        compactBadge.style.background = (posNum === 1)
                            ? "linear-gradient(to right, #10b981, #0d9488, #2563eb)"
                            : "linear-gradient(to right, #60a5fa, #3b82f6, #1d4ed8)";
                        compactBadge.textContent = `#${posNum} IN QUEUE`;
                    } else {
                        compactBadge.className = "px-2.5 py-0.5 sm:px-3 sm:py-1 bg-slate-200 text-slate-600 rounded-full text-[10px] sm:text-xs font-black uppercase tracking-wider";
                        compactBadge.style.background = "";
                        compactBadge.textContent = "OFF DUTY";
                    }
                }

                /* 🎵 Tactile Web Audio API Duty Status Chime Sounds */
                window.playDutySound = function(isOnline) {
                    try {
                        const AudioCtx = window.AudioContext || window.webkitAudioContext;
                        if (!AudioCtx || !window._srhUserHasInteracted) return;
                        if (!window._srhAudioCtx) {
                            window._srhAudioCtx = new AudioCtx();
                        }
                        const ctx = window._srhAudioCtx;
                        if (ctx.state === 'suspended') {
                            ctx.resume();
                        }

                        const now = ctx.currentTime;

                        if (isOnline) {
                            // 🔔 Going Online: Uplifting Rising 3-Note Chime (C5 -> E5 -> G5)
                            const notes = [523.25, 659.25, 783.99];
                            notes.forEach((freq, idx) => {
                                const osc = ctx.createOscillator();
                                const gain = ctx.createGain();

                                osc.type = 'sine';
                                osc.frequency.setValueAtTime(freq, now + idx * 0.07);

                                gain.gain.setValueAtTime(0, now + idx * 0.07);
                                gain.gain.linearRampToValueAtTime(0.14, now + idx * 0.07 + 0.015);
                                gain.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.07 + 0.22);

                                osc.connect(gain);
                                gain.connect(ctx.destination);

                                osc.start(now + idx * 0.07);
                                osc.stop(now + idx * 0.07 + 0.23);
                            });
                        } else {
                            // 🌙 Going Offline: Soft Descending 2-Note Chime (E5 -> C5)
                            const notes = [659.25, 523.25];
                            notes.forEach((freq, idx) => {
                                const osc = ctx.createOscillator();
                                const gain = ctx.createGain();

                                osc.type = 'sine';
                                osc.frequency.setValueAtTime(freq, now + idx * 0.09);

                                gain.gain.setValueAtTime(0, now + idx * 0.09);
                                gain.gain.linearRampToValueAtTime(0.10, now + idx * 0.09 + 0.015);
                                gain.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.09 + 0.2);

                                osc.connect(gain);
                                gain.connect(ctx.destination);

                                osc.start(now + idx * 0.09);
                                osc.stop(now + idx * 0.09 + 0.21);
                            });
                        }
                    } catch(e) {}
                };

                if (queueCardContainer) {
                    // Re-query fresh: the sheet content is rebuilt in place by
                    // triggerDriverActionInstantUI, which may have mounted a brand
                    // new #queue-card-container node since this function's start.
                    queueCardContainer = document.getElementById('queue-card-container');
                    if (!queueCardContainer) return;
                    const curOnline = queueCardContainer.dataset.isOnline;
                    const curPos = queueCardContainer.dataset.position ? parseInt(queueCardContainer.dataset.position, 10) : null;
                    const sameOnline = curOnline === String(isOnline);
                    const samePos = !isOnline || curPos === posNum;

                    if (!sameOnline || !samePos) {
                        if (!sameOnline && window.playDutySound) {
                            window.playDutySound(isOnline);
                        }
                        queueCardContainer.dataset.isOnline = String(isOnline);
                        if (isOnline) {
                            queueCardContainer.dataset.position = String(posNum);
                        }

                        // Dual-stack duty cards: BOTH cards stay mounted inside
                        // #queue-card-states and a CSS crossfade flips visibility
                        // on [data-online]. Missing/older markup gets the full
                        // two-card stack built once; position-only changes just
                        // update the text nodes in place (no re-mount, no flicker).
                        let states = queueCardContainer.querySelector('#queue-card-states');
                        let onlineCard = states ? states.querySelector('#online-queue-card') : null;

                        const buildStackHtml = (pos, subtext) => `
                            <div id="online-queue-card" class="text-white rounded-2xl p-3.5 sm:p-4 text-center shadow-lg shadow-emerald-500/25 overflow-hidden flex flex-col items-center justify-center" data-position="${pos}" data-theme="${pos === 1 ? 'now' : 'wait'}">
                                <div class="queue-theme-layer queue-theme-now"></div>
                                <div class="queue-theme-layer queue-theme-wait"></div>
                                <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-white/10 rounded-full blur-lg pointer-events-none z-10"></div>
                                <div class="relative z-10 w-full">
                                <p class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-emerald-100 mb-1">Queue Position</p>
                                <h1 id="queue-position-heading" class="text-2xl xs:text-3xl sm:text-5xl font-black tracking-tight my-0.5 truncate">#${pos} IN QUEUE</h1>
                                <p id="queue-position-subtext" class="text-[10px] sm:text-xs font-bold text-emerald-50 truncate mt-1">${subtext}</p>
                                <button type="button" id="start-walkin-btn" onclick="handleTerminalWalkInClick(event)" class="w-full flex items-center justify-center gap-2 p-2.5 sm:p-3 rounded-2xl bg-white hover:bg-emerald-50 text-emerald-900 border border-emerald-200/80 active:scale-95 transition cursor-pointer mt-2.5 sm:mt-3 shadow-sm">
                                    <span class="text-[11px] sm:text-xs font-black text-emerald-900 tracking-tight truncate">Start Terminal Walk-In Ride</span>
                                </button>
                                </div>
                            </div>
<div id="offline-queue-card" style="background: linear-gradient(150deg, #334155 0%, #1e293b 45%, #0f172a 100%) !important; color: #ffffff !important;" class="w-full max-w-full rounded-2xl p-3.5 sm:p-4 text-center shadow-xl shadow-2xl overflow-hidden flex flex-col items-center justify-center border border-slate-700">
                                            <div class="absolute -right-4 -bottom-4 w-20 h-20 rounded-full blur-lg pointer-events-none z-10" style="background: rgba(148, 163, 184, 0.1) !important;"></div>
                                <div class="relative z-10 w-full">
                                <p style="color: #cbd5e1 !important;" class="w-full text-[9px] sm:text-[10px] font-black uppercase tracking-widest mb-1">Driver Status</p>
                                <h1 style="color: #ffffff !important;" class="w-full text-2xl xs:text-3xl sm:text-5xl font-black tracking-tight my-0.5 truncate">YOU'RE OFFLINE</h1>
                                <p style="color: #94a3b8 !important;" class="w-full text-[10px] sm:text-xs font-bold truncate mt-1">Go online to enter TODA queue & receive passenger requests!</p>
<div class="w-full flex items-center justify-center gap-2 p-2.5 sm:p-3 rounded-2xl bg-transparent text-white/50 mt-2.5 sm:mt-3">
                                    <span class="text-[11px] sm:text-xs font-black text-white/50 tracking-tight truncate">Toggle the power button to go on duty</span>
                                </div>
                            </div>
                            </div>`;

                        if (!onlineCard || !onlineCard.querySelector('#queue-position-heading')) {
                            const rebuildPos = (curPos && curPos > 0) ? curPos : posNum;
                            const subtext = window.getQueueSubtext ? window.getQueueSubtext(rebuildPos) : `#${rebuildPos} in queue`;
                            if (states) {
                                states.innerHTML = buildStackHtml(rebuildPos, subtext);
                            } else {
                                queueCardContainer.innerHTML = `<div id="queue-card-states" class="relative rounded-2xl" data-online="true" data-position="${rebuildPos}">${buildStackHtml(rebuildPos, subtext)}</div>`;
                                states = queueCardContainer.querySelector('#queue-card-states');
                            }
                            onlineCard = states ? states.querySelector('#online-queue-card') : null;
                        }

                        if (states) {
                            states.dataset.online = String(isOnline);
                            if (isOnline) {
                                states.dataset.position = String(posNum);
                            }
                        }

                        if (onlineCard && isOnline) {
                            const heading = onlineCard.querySelector('#queue-position-heading');
                            if (heading) {
                                heading.textContent = `#${posNum} IN QUEUE`;
                                const sub = onlineCard.querySelector('#queue-position-subtext');
                                if (sub) {
                                    sub.textContent = window.getQueueSubtext ? window.getQueueSubtext(posNum) : `#${posNum} in queue`;
                                }
                            }
                            onlineCard.dataset.position = String(posNum);
                            onlineCard.dataset.theme = (posNum === 1) ? 'now' : 'wait';
                        }

                        if (clipContainer) {
                            if (isOnline) {
                                clipContainer.classList.add('is-expanded');
                            } else {
                                clipContainer.classList.remove('is-expanded');
                            }
                        }

                        if (!window.driverSheetHasActiveTrip || !window.driverSheetHasActiveTrip()) {
                            const sheet = document.getElementById('driver-bottom-sheet');
                            if (sheet) {
                                const restingY = window.getDriverOnlinePeekY ? window.getDriverOnlinePeekY() : (window.innerHeight - 300);
                                if (window._driverSheetInst && typeof window._driverSheetInst.moveTo === 'function') {
                                    window._driverSheetInst.moveTo(restingY, 280);
                                } else {
                                    sheet.style.transform = 'translate3d(0, ' + restingY + 'px, 0)';
                                }
                            }
                        }
                    }
                }

                // ⚡ Instant 0ms Re-Sync: Re-align floating action buttons with zero bottom sheet container movement
                syncFloatingButtonsPosition();
                requestAnimationFrame(() => syncFloatingButtonsPosition());
            }

            // Hardened facade: a duty-card/UI re-render failure anywhere (poller,
            // Reverb queue events, duty toggle, position sync) must NEVER throw
            // into unrelated flows — notably the incoming-ride state machine,
            // which used to die here and strand the offer popup for the full 60s.
            function updateDutyUI(isOnline, position, totalCount) {
                try {
                    updateDutyUIImpl(isOnline, position, totalCount);
                } catch (e) {
                    try { window.__srhDbg && window.__srhDbg.push('DUTYUI THREW: ' + (e && e.message)); } catch (e2) {}
                }
            }
            window.updateDutyUI = updateDutyUI;

            // The driver's own queue row id, used to keep queue position texts
            // (#badge, queue card, "of N") perfectly in sync with the live queue.
            window.srhMyDriverId = {{ $driver ? $driver->id : 'null' }};

            // Reads the CURRENT driver's position straight from the live queue list
            // and pushes it into the compact badge + queue card via updateDutyUI
            // (which no-ops on identical values and animates the card on change).
            window.syncOwnQueuePosition = function() {
                if (window.__srhDutyToggleInFlight) return;
                // Never force the ONLINE card for an offline driver (this function
                // used to hardcode updateDutyUI(true, ...) and could resurrect the
                // online card after a vault park/restore round-trip).
                if (window.__srhIsOnline === false) return;
                // While the hub is parked in the keep-alive vault, stale tab-level
                // events must not rewrite its duty card; the restore resync corrects it.
                if (window.__srhVaultActive) return;
                const dutyCard = document.getElementById('queue-card-container');
                if (dutyCard && dutyCard.dataset.isOnline === 'false') return;
                if (!window.srhMyDriverId) return;
                const wrapper = document.getElementById('live-queue-wrapper');
                if (!wrapper) return;

                const queueItems = wrapper.querySelector('#queue-items-container');
                if (!queueItems) return;

                const myRow = Array.prototype.slice.call(queueItems.children)
                    .find(el => el.dataset && el.dataset.driverId && el.dataset.driverId === String(window.srhMyDriverId));
                if (!myRow) return;

                const badgeEl = myRow.querySelector('.queue-number-badge');
                const posNum = badgeEl ? parseInt((badgeEl.textContent || '').replace(/[^\d]/g, ''), 10) : (Array.prototype.indexOf.call(queueItems.children, myRow) + 1);
                if (!posNum || posNum < 1) return;

                const countEl = wrapper.querySelector('.flex.items-center.justify-between span.rounded-full');
                const totalCount = countEl ? parseInt((countEl.textContent || '').replace(/[^\d]/g, ''), 10) : (queueItems ? queueItems.children.length : 0);

                const oldPos = dutyCard ? parseInt(dutyCard.dataset.position, 10) : null;
                updateDutyUI(true, posNum, totalCount);

                // 1. If this driver is #1 in queue: check if an incoming ride is waiting!
                if (posNum === 1) {
                    if (window.srhExecuteIncomingFetch) {
                        window.srhExecuteIncomingFetch(true);
                    }
                } else if (posNum !== 1 && window.__srhInc && window.__srhInc.state && window.__srhInc.state !== 'hidden') {
                    // 2. If demoted from #1 while holding an overlay, dismiss it in place
                    if (window.srhHideIncomingOverlay) {
                        window.srhHideIncomingOverlay({}, { instant: true });
                    }
                }
            };

            function attachSheetResizeObserver() {
                const sheetContainer = document.getElementById('driver-bottom-sheet') || document.getElementById('incoming-ride-wrapper');
                if (!sheetContainer || sheetContainer.dataset.resizeAttached === 'true') return;

                if (window.ResizeObserver) {
                    const ro = new ResizeObserver(() => {
                        syncFloatingButtonsPosition();
                    });
                    ro.observe(sheetContainer);
                    sheetContainer.dataset.resizeAttached = 'true';
                }
            }

            // ---- Active-trip sheet helpers ----
            // The driver's ACTIVE RIDE view reuses the dashboard sheet element while a
            // ride is live (`data-ride-id` set). In that mode the sheet behaves like the
            // passenger's: only TWO snap states — the full view of the real trip content
            // (never pulled past it to the very top) and a semi-hidden sliver. The
            // dashboard 3-state behavior (full / card / semi-hidden) stays untouched.
            window.driverSheetHasActiveTrip = function() {
                const sheet = document.getElementById('driver-bottom-sheet') || document.getElementById('active-trip-wrapper');
                if (!sheet) return false;
                // A ride is live when the server rendered one (data-ride-id) or a
                // client flow mounted trip UI (data-active-status: accepted /
                // arrived / in_transit / returning — also covers walk-ins).
                if (sheet.dataset.rideId && sheet.dataset.rideId !== '' && sheet.dataset.rideId !== '0') return true;
                return !!(sheet.dataset.activeStatus && sheet.dataset.activeStatus !== '');
            };

            // translateY that shows exactly the trip content (+ handle/margins), clamped
            // so the sheet top edge never reaches the top of the screen.
            window.getDriverActiveTripFullY = function() {
                const contentEl = document.getElementById('sheet-details-content');
                let natural = 0;
                if (contentEl) {
                    const inner = contentEl.firstElementChild;
                    if (inner) natural = Math.max(0, Math.ceil(inner.getBoundingClientRect().height) || 0);
                    else natural = Math.max(0, contentEl.scrollHeight || 0);
                }
                const visible = Math.max(160, Math.min(window.innerHeight - 110, natural + 110));
                return Math.max(60, Math.min(window.innerHeight - 110, window.innerHeight - visible));
            };

            // Re-hug the trip sheet after a content swap (arrived / start transit /
            // drop off / wayside). Skips when the driver deliberately tucked the sheet
            // into the semi-hidden slit so it never yanks back up uninvited.
            window.hugDriverActiveTripSheet = function(duration) {
                const inst = window._driverSheetInst;
                const sheet = document.getElementById('driver-bottom-sheet');
                if (!inst || typeof inst.fitToContent !== 'function' || !sheet) return;
                if (!window.driverSheetHasActiveTrip()) return;
                if (sheet.classList.contains('is-dragging')) return;
                let y = 0;
                try {
                    const st = window.getComputedStyle(sheet);
                    const t = st.transform || st.webkitTransform;
                    if (t && t !== 'none') {
                        const m1 = t.match(/matrix\((.+)\)/);
                        if (m1) {
                            y = parseFloat(m1[1].split(',')[5]) || 0;
                        } else {
                            const m3 = t.match(/matrix3d\((.+)\)/);
                            if (m3) y = parseFloat(m3[1].split(',')[13]) || 0;
                        }
                    }
                } catch(e) {}
                // Leave a deliberately semi-hidden sheet alone (its slit is ~100px
                // from the bottom of the viewport).
                if (y >= (window.innerHeight - 140)) return;
                inst.fitToContent(typeof duration === 'number' ? duration : 460);
            };

            // Retracts and syncs sheet to dashboard peek height when trip finishes/returns to queue
            window.syncDriverDashboardSheet = function(duration) {
                const inst = window._driverSheetInst;
                const sheet = document.getElementById('driver-bottom-sheet');
                if (!inst || !sheet) return;
                if (sheet.classList.contains('is-dragging')) return;
                const dur = typeof duration === 'number' ? duration : 460;
                if (window.driverSheetHasActiveTrip()) {
                    if (window.hugDriverActiveTripSheet) window.hugDriverActiveTripSheet(dur);
                } else {
                    const defaultY = window.getDriverOnlinePeekY ? window.getDriverOnlinePeekY() : (window.innerHeight - 300);
                    if (typeof inst.moveTo === 'function') {
                        inst.moveTo(defaultY, dur);
                    } else if (typeof inst.snapToState === 'function') {
                        inst.snapToState(1, dur);
                    }
                }
            };

            function initSheetSwipeGesture() {
                const sheet = document.getElementById('driver-bottom-sheet');
                const hasActiveTrip = sheet && sheet.dataset.rideId && sheet.dataset.rideId !== '';

                // Pin sheet height to exact device pixel height (fixes 100vh ≠ window.innerHeight on mobile)
                function setSheetHeight() {
                    const h = window.innerHeight;
                    if (sheet) {
                        sheet.style.height = h + 'px';
                        sheet.style.minHeight = h + 'px';
                    }
                    document.documentElement.style.setProperty('--sheet-h', h + 'px');
                }
                setSheetHeight();
                window.addEventListener('resize', setSheetHeight);
                window.addEventListener('orientationchange', function() {
                    setTimeout(setSheetHeight, 200);
                });

                // Keep the sheet's snap position proportional to the viewport when
                // it resizes (DevTools/browser zoom 75%–125%, address bar show/hide,
                // rotation): once snapped, the translateY is a fixed px value that
                // goes stale. The sheet always rests EXACTLY on one of its anchors,
                // so we classify the current position against the anchors of the
                // PREVIOUS viewport height (captured before this resize) and then
                // re-apply that same state's anchor computed for the NEW height —
                // the visible peek stays identical at every zoom, no reload needed.
                let driverSheetPrevVh = window.innerHeight;
                let driverSheetNewVh = window.innerHeight;
                let resnapTimer = null;
                let resnapFollowupTimer = null;
                function resnapOnViewportChange() {
                    const newVh = window.innerHeight;
                    // Only a real viewport-HEIGHT change advances the classification
                    // pair; follow-up re-runs keep the original pre-zoom pair so the
                    // state is still classified correctly after a snap animation
                    // (which finishes at the engine's stale target) settles.
                    if (Math.abs(newVh - driverSheetNewVh) >= 4) {
                        driverSheetPrevVh = driverSheetNewVh;
                        driverSheetNewVh = newVh;
                    }
                    clearTimeout(resnapTimer);
                    resnapTimer = setTimeout(resnapDriverSheetToCurrentState, 120);
                    // Re-run once more after any in-flight snap animation ends
                    clearTimeout(resnapFollowupTimer);
                    resnapFollowupTimer = setTimeout(resnapDriverSheetToCurrentState, 1500);
                }
                window.addEventListener('resize', resnapOnViewportChange);
                window.addEventListener('orientationchange', resnapOnViewportChange);

                // Driver Online Status Helper
                window.isDriverOnline = function() {
                    const queueContainer = document.getElementById('queue-card-container');
                    if (queueContainer && queueContainer.dataset.isOnline !== undefined) {
                        return queueContainer.dataset.isOnline === 'true';
                    }
                    const powerBtn = document.getElementById('power-toggle-btn');
                    if (powerBtn) {
                        return powerBtn.classList.contains('bg-emerald-500');
                    }
                    const badge = document.getElementById('compact-queue-badge');
                    if (badge) {
                        return badge.textContent.indexOf('OFF DUTY') === -1;
                    }
                    return {{ ($driver && $driver->is_online) ? 'true' : 'false' }};
                };

                window.getDriverOnlinePeekY = function(vh) {
                    const viewportHeight = vh || window.innerHeight;
                    // Hugs cleanly right below the status card (300px) matching online and offline positions identically
                    return Math.max(0, viewportHeight - 300);
                };

                function driverSheetStateIndex(curY, vh, hasTrip) {
                    if (hasTrip) {
                        const full = window.getDriverActiveTripFullY ? window.getDriverActiveTripFullY() : 60;
                        return Math.abs(curY - full) <= Math.abs(curY - (vh - 100)) ? 0 : 1;
                    }
                    if (window.isDriverOnline && !window.isDriverOnline()) {
                        return Math.abs(curY - (vh - 300)) <= Math.abs(curY - (vh - 100)) ? 0 : 1;
                    }
                    const onlinePeek = window.getDriverOnlinePeekY(vh);
                    const anchors = [0, onlinePeek, vh - 100];
                    let best = 0;
                    for (let i = 1; i < anchors.length; i++) {
                        if (Math.abs(curY - anchors[i]) < Math.abs(curY - anchors[best])) best = i;
                    }
                    return best;
                }
                function driverSheetAnchorFor(stateIdx, vh, hasTrip) {
                    if (hasTrip) {
                        return stateIdx === 0
                            ? (window.getDriverActiveTripFullY ? window.getDriverActiveTripFullY() : 60)
                            : vh - 100;
                    }
                    if (window.isDriverOnline && !window.isDriverOnline()) {
                        return stateIdx === 0 ? (vh - 300) : (vh - 100);
                    }
                    const onlinePeek = window.getDriverOnlinePeekY(vh);
                    return [0, onlinePeek, vh - 100][stateIdx];
                }
                function resnapDriverSheetToCurrentState() {
                    const sh = document.getElementById('driver-bottom-sheet');
                    if (!sh || sh.classList.contains('is-dragging')) return;

                    const prevVh = driverSheetPrevVh;
                    const newVh = driverSheetNewVh;
                    if (Math.abs(newVh - prevVh) < 4) return;

                    let curY = 0;
                    try {
                        const t = window.getComputedStyle(sh).transform;
                        const m1 = t && t !== 'none' ? t.match(/matrix\((.+)\)/) : null;
                        const m3 = t && t !== 'none' ? t.match(/matrix3d\((.+)\)/) : null;
                        if (m1) curY = parseFloat(m1[1].split(',')[5]) || 0;
                        else if (m3) curY = parseFloat(m3[1].split(',')[13]) || 0;
                    } catch (e) {}

                    const hasTrip = window.driverSheetHasActiveTrip ? window.driverSheetHasActiveTrip() : false;
                    // State BEFORE this resize (classified on the old viewport anchors)…
                    const stateIdx = driverSheetStateIndex(curY, prevVh, hasTrip);
                    // …re-applied at the NEW viewport height.
                    const target = driverSheetAnchorFor(stateIdx, newVh, hasTrip);
                    if (Math.abs(curY - target) < 2) return;

                    sh.style.transition = 'none';
                    sh.style.transform = 'translate3d(0, ' + target + 'px, 0)';

                    if (window.syncFloatingButtonsPosition) window.syncFloatingButtonsPosition();

                    const detailsContent = document.getElementById('sheet-details-content');
                    if (detailsContent && window.isDriverOnline && window.isDriverOnline()) {
                        detailsContent.style.overflowY = 'auto';
                    }

                    const dimmer = document.getElementById('map-backdrop-dimmer');
                    if (dimmer) {
                        if (hasTrip || (window.isDriverOnline && !window.isDriverOnline())) {
                            dimmer.style.opacity = '0';
                            dimmer.style.pointerEvents = 'none';
                        } else {
                            const expansionProgress = 1 - Math.max(0, Math.min(1, target / (newVh - 100 || 1)));
                            dimmer.style.opacity = (expansionProgress * 0.75).toFixed(2);
                            dimmer.style.pointerEvents = expansionProgress > 0.4 ? 'auto' : 'none';
                        }
                    }

                    const mapHub = document.getElementById('grab-home-map')?.parentElement || document.body;
                    const mapHubRect = mapHub.getBoundingClientRect();
                    const sheetRect = sh.getBoundingClientRect();
                    const sheetTopFromMapBottom = sheetRect ? (mapHubRect.bottom - sheetRect.top) : 0;
                    const targetBottomNum = Math.round(sheetTopFromMapBottom + 15);
                    const fadeStartPos = Math.max(380, Math.round(newVh * 0.52));
                    let buttonOpacity = 1;
                    if (targetBottomNum > fadeStartPos) {
                        const fadeProgress = (targetBottomNum - fadeStartPos) / 90;
                        buttonOpacity = Math.max(0, Math.min(1, 1 - fadeProgress)).toFixed(2);
                    }
                    const powerBtn = document.getElementById('floating-power-container');
                    const mapCtrls = document.getElementById('floating-map-controls-container');
                    if (powerBtn) {
                        powerBtn.style.opacity = buttonOpacity;
                        powerBtn.style.pointerEvents = buttonOpacity > 0.3 ? 'auto' : 'none';
                    }
                    if (mapCtrls) {
                        mapCtrls.style.opacity = buttonOpacity;
                        mapCtrls.style.pointerEvents = buttonOpacity > 0.3 ? 'auto' : 'none';
                    }
                }
//draggablesheet
                if (window.NativeBottomSheet) {
                    const inst = window.NativeBottomSheet.attach({
                        handle: '#sheet-drag-handle',
                        content: '#sheet-details-content',
                        parentCard: '#driver-bottom-sheet',
                        freeDragMode: false,
                        disableAutoSnap: false,
                        disableKineticGlide: false,
                        // Live mode: 3-state behavior only when online with active queue; 2-state when on active trip OR offline.
                        enableThreeStates: function() { 
                            return !window.driverSheetHasActiveTrip() && window.isDriverOnline(); 
                        },
                        limitToTwoStates: function() { 
                            return window.driverSheetHasActiveTrip() || !window.isDriverOnline(); 
                        },
                        twoStateAnchors: function() {
                            if (window.driverSheetHasActiveTrip()) {
                                // [full view of trip content, semi-hidden sliver]
                                return [window.getDriverActiveTripFullY(), window.innerHeight - 100];
                            }
                            if (!window.isDriverOnline()) {
                                // When offline: Rest at the exact same standard peek height (window.innerHeight - 300), or semi-hidden slit (window.innerHeight - 100).
                                // Disables full-screen pull view since there are no additional contents while offline.
                                return [window.innerHeight - 300, window.innerHeight - 100];
                            }
                            return null;
                        },
                        fitContentPadding: 110,
                        getMinTranslateY: function() {
                            // During an active ride the sheet must never pull above the content-fit anchor — the full view IS the top limit.
                            if (window.driverSheetHasActiveTrip()) return window.getDriverActiveTripFullY();
                            // When offline: cannot pull above the resting peek height (window.innerHeight - 300)
                            if (!window.isDriverOnline()) return window.innerHeight - 300;
                            // Top edge of form stops at screen top (0px) when fully pulled up
                            return 0;
                        },
                        getDefaultTranslateY: function() {
                            // Active trip: rest hugging the real trip content (passenger-style).
                            // Dashboard: dynamic peek height ensuring online card and walk-in button are fully visible
                            if (window.driverSheetHasActiveTrip()) return window.getDriverActiveTripFullY();
                            return window.getDriverOnlinePeekY ? window.getDriverOnlinePeekY() : (window.innerHeight - 300);
                        },
                        
                        getMaxAllowed: function() {
                            // Maximum bottom drag limit (how far down user can drag form to tuck away)
                            return window.innerHeight - 100;
                        },
                        getExpandedMaxHeight: function() {
                            if (!window.isDriverOnline() && !window.driverSheetHasActiveTrip()) {
                                return 300;
                            }
                            return window.innerHeight;
                        },
                        contentMaxHeight: function() {
                            return (window.innerHeight - 70) + 'px';
                        },
                        marginTop: '6px',
                        onSync: function(visibleHeight, maxAllowed) {
                            if (window.syncFloatingButtonsPosition) window.syncFloatingButtonsPosition();
                            if (window.syncHomeMapPadding) window.syncHomeMapPadding();

                            const currentTranslateY = maxAllowed - visibleHeight;
                            const minTranslate = (typeof this.getMinTranslateY === 'function') ? this.getMinTranslateY() : 0;
                            const isAtTop = currentTranslateY <= (minTranslate + 5);

                            const detailsContent = document.getElementById('sheet-details-content');
                            if (detailsContent && window.isDriverOnline && window.isDriverOnline()) {
                                detailsContent.style.overflowY = isAtTop ? 'auto' : 'hidden';
                                if (!isAtTop) detailsContent.scrollTop = 0;
                            }

                            const sheet = document.getElementById('driver-bottom-sheet');
                            const hasActiveTrip = sheet && sheet.dataset.rideId && sheet.dataset.rideId !== '';
                            const isOffline = !window.isDriverOnline();

                            const dimmer = document.getElementById('map-backdrop-dimmer');
                            if (dimmer) {
                                if (hasActiveTrip || isOffline) {
                                    dimmer.style.opacity = '0';
                                    dimmer.style.pointerEvents = 'none';
                                } else {
                                    const expansionProgress = 1 - Math.max(0, Math.min(1, currentTranslateY / (maxAllowed || 1)));
                                    dimmer.style.opacity = (expansionProgress * 0.75).toFixed(2);
                                    dimmer.style.pointerEvents = expansionProgress > 0.4 ? 'auto' : 'none';
                                }
                            }

                            const mapHub = document.getElementById('grab-home-map')?.parentElement || document.body;
                            const mapHubRect = mapHub.getBoundingClientRect();
                            const sheetRect = sheet ? sheet.getBoundingClientRect() : null;
                            const sheetTopFromMapBottom = sheetRect ? (mapHubRect.bottom - sheetRect.top) : 0;
                            const targetBottomNum = Math.round(sheetTopFromMapBottom + 15);

                            const fadeStartPos = Math.max(380, Math.round(window.innerHeight * 0.52));
                            let buttonOpacity = 1;
                            if (targetBottomNum > fadeStartPos) {
                                const fadeProgress = (targetBottomNum - fadeStartPos) / 90;
                                buttonOpacity = Math.max(0, Math.min(1, 1 - fadeProgress)).toFixed(2);
                            }

                            const powerBtn = document.getElementById('floating-power-container');
                            const mapCtrls = document.getElementById('floating-map-controls-container');
                            if (powerBtn) {
                                powerBtn.style.opacity = buttonOpacity;
                                powerBtn.style.pointerEvents = buttonOpacity > 0.3 ? 'auto' : 'none';
                            }
                            if (mapCtrls) {
                                mapCtrls.style.opacity = buttonOpacity;
                                mapCtrls.style.pointerEvents = buttonOpacity > 0.3 ? 'auto' : 'none';
                            }
                        },
                        onExpand: function() {
                            const detailsContent = document.getElementById('sheet-details-content');
                            if (detailsContent && window.isDriverOnline()) {
                                detailsContent.style.overflowY = 'auto';
                            }
                            const sheet = document.getElementById('driver-bottom-sheet');
                            const hasActiveTrip = sheet && sheet.dataset.rideId && sheet.dataset.rideId !== '';
                            const isOffline = !window.isDriverOnline();
                            const dimmer = document.getElementById('map-backdrop-dimmer');
                            if (dimmer) { 
                                dimmer.style.opacity = (hasActiveTrip || isOffline) ? '0' : '0.75'; 
                                dimmer.style.pointerEvents = (hasActiveTrip || isOffline) ? 'none' : 'auto'; 
                            }
                            if (window.animateFloatingButtonsSync) window.animateFloatingButtonsSync(350);
                        },
                        onCollapse: function() {
                            const detailsContent = document.getElementById('sheet-details-content');
                            if (detailsContent) {
                                detailsContent.style.overflowY = 'hidden';
                                detailsContent.scrollTop = 0;
                            }
                            const dimmer = document.getElementById('map-backdrop-dimmer');
                            if (dimmer) { dimmer.style.opacity = '0'; dimmer.style.pointerEvents = 'none'; }
                            const powerBtn = document.getElementById('floating-power-container');
                            const mapCtrls = document.getElementById('floating-map-controls-container');
                            if (powerBtn) powerBtn.style.opacity = '1';
                            if (mapCtrls) mapCtrls.style.opacity = '1';
                            if (window.animateFloatingButtonsSync) window.animateFloatingButtonsSync(350);
                        },
                        onToggle: function() {
                            toggleDriverSheet();
                        }
                    });
                    if (inst) window._driverSheetInst = inst;

                }
            }

            function initActiveTripSwipeGesture() {
                if (window.NativeBottomSheet) {
                    window.NativeBottomSheet.attach({
                        handle: '#active-trip-drag-handle',
                        content: '#active-trip-details-content',
                        getMaxAllowed: function() { return 320; },
                        marginTop: '4px',
                        onSync: function() {
                            if (window.syncFloatingButtonsPosition) window.syncFloatingButtonsPosition();
                        },
                        onExpand: function() {
                            const chevron = document.getElementById('active-trip-chevron');
                            if (chevron) chevron.style.transform = 'rotate(180deg)';
                            if (window.animateFloatingButtonsSync) window.animateFloatingButtonsSync(350);
                        },
                        onCollapse: function() {
                            const chevron = document.getElementById('active-trip-chevron');
                            if (chevron) chevron.style.transform = 'rotate(0deg)';
                            if (window.animateFloatingButtonsSync) window.animateFloatingButtonsSync(350);
                        },
                        onToggle: function() {
                            toggleActiveTripSheet();
                        }
                    });
                }
            }

            window.initSheetSwipeGesture = initSheetSwipeGesture;
            window.initActiveTripSwipeGesture = initActiveTripSwipeGesture;
            initSheetSwipeGesture();
            initActiveTripSwipeGesture();
            window.addEventListener('spa:page-loaded', function() {
                initSheetSwipeGesture();
                initActiveTripSwipeGesture();
                if (typeof initGrabHomeMap === 'function') initGrabHomeMap();
                if (window.syncFloatingButtonsPosition) window.syncFloatingButtonsPosition();
                if (typeof window.syncHomeMapPadding === 'function') window.syncHomeMapPadding();
            });
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    initSheetSwipeGesture();
                    initActiveTripSwipeGesture();
                    if (window.syncFloatingButtonsPosition) window.syncFloatingButtonsPosition();
                });
            }
            window.addEventListener('pageshow', function() {
                initSheetSwipeGesture();
                initActiveTripSwipeGesture();
                if (window.syncFloatingButtonsPosition) window.syncFloatingButtonsPosition();
            });
            window.addEventListener('load', function() {
                if (window.syncFloatingButtonsPosition) window.syncFloatingButtonsPosition();
            });
            setTimeout(function() {
                if (window.syncFloatingButtonsPosition) window.syncFloatingButtonsPosition();
            }, 100);
            setTimeout(function() {
                if (window.syncFloatingButtonsPosition) window.syncFloatingButtonsPosition();
            }, 500);

            let deviceWatchId = null;
            let targetCompassHeading = 0;
            let smoothedCompassHeading = 0;
            let currentUnwrappedHeading = 0;
            let isCompassAnimating = false;

            function hasActiveDriverTrip() {
                const sheet = document.getElementById('driver-bottom-sheet') || document.getElementById('active-trip-wrapper');
                const status = sheet ? sheet.dataset.activeStatus : null;
                return !!(window._hasActivePathwayMode || (status && status !== '' && status !== 'none' && status !== 'completed' && status !== 'cancelled'));
            }

            // Shortest Angular Path Unwrapping Algorithm (Prevents 360° Spin Loops when crossing 0°/360° boundary)
            function unwrapAngle(prevAngle, targetAngle) {
                let diff = (targetAngle - prevAngle) % 360;
                if (diff < -180) diff += 360;
                if (diff > 180) diff -= 360;
                return prevAngle + diff;
            }

            let lastRenderedBearing = null;

            // 60FPS Adaptive Low-Pass Filter Exponential Smoothing Loop for Phone Compass Marker Orientation
            function animateCompassRotation() {
                if (!isCompassAnimating) return;

                // 🛑 Completely disable compass sensor when on an active trip (locked to road pathway)
                if (hasActiveDriverTrip()) {
                    isCompassAnimating = false;
                    return;
                }

                // When actively driving via GPS, GPS road bearing directs the marker
                if (window._driverGpsIsMoving && !window.srhCompassMode) {
                    isCompassAnimating = false;
                    return;
                }

                const delta = currentUnwrappedHeading - smoothedCompassHeading;
                const absDelta = Math.abs(delta);

                if (absDelta > 0.35) {
                    // Adaptive Smoothing:
                    // • Small sensor jitter (<2.5°): calm 0.10 factor for steady holding without twitching
                    // • Normal hand turns (2.5° - 15°): responsive 0.18 factor
                    // • Large turns (>15°): fast 0.28 factor to snap 1:1
                    let factor = 0.16;
                    if (absDelta < 2.5) {
                        factor = 0.10;
                    } else if (absDelta > 15.0) {
                        factor = 0.28;
                    }

                    smoothedCompassHeading += delta * factor;

                    const headingDeg = (smoothedCompassHeading % 360 + 360) % 360;
                    if (window.srhTricycle) window.srhTricycle.heading = headingDeg;

                    const markerImg = document.querySelector('#driver-tricycle-marker-img');
                    if (markerImg) {
                        markerImg.style.transform = 'rotate(' + headingDeg.toFixed(1) + 'deg)';
                    }
                    if (typeof window.syncCameraToLiveCompass === 'function' && typeof homeMap !== 'undefined') {
                        window.syncCameraToLiveCompass(homeMap, headingDeg);
                    }

                    requestAnimationFrame(animateCompassRotation);
                } else {
                    // Converged to deadband — apply exact target and sleep until next genuine turn
                    smoothedCompassHeading = currentUnwrappedHeading;
                    const headingDeg = (smoothedCompassHeading % 360 + 360) % 360;
                    if (window.srhTricycle) window.srhTricycle.heading = headingDeg;

                    const markerImg = document.querySelector('#driver-tricycle-marker-img');
                    if (markerImg) {
                        markerImg.style.transform = 'rotate(' + headingDeg.toFixed(1) + 'deg)';
                    }
                    if (typeof window.syncCameraToLiveCompass === 'function' && typeof homeMap !== 'undefined') {
                        window.syncCameraToLiveCompass(homeMap, headingDeg);
                    }
                    isCompassAnimating = false;
                }
            }

            function updateSmoothCompassHeading(newRawHeading) {
                if (hasActiveDriverTrip()) return;
                if (typeof newRawHeading !== 'number' || isNaN(newRawHeading)) return;
                // Always unwrap against current actual smoothedCompassHeading
                currentUnwrappedHeading = unwrapAngle(smoothedCompassHeading, newRawHeading);
                if (!isCompassAnimating) {
                    isCompassAnimating = true;
                    requestAnimationFrame(animateCompassRotation);
                }
            }

            // Sync helper so road-snapping or GPS heading updates keep compass state synchronized
            window._syncCompassStateToHeading = function(h) {
                if (typeof h === 'number' && !isNaN(h)) {
                    smoothedCompassHeading = h;
                    currentUnwrappedHeading = h;
                }
            };

            function initCompassAndRealtimeGps() {
                if (window._compassListenerRegistered) return;
                window._compassListenerRegistered = true;

                function handleCompassOrientation(e) {
                    if (hasActiveDriverTrip()) return;
                    let compassHeading = null;
                    if (typeof e.webkitCompassHeading === 'number' && !isNaN(e.webkitCompassHeading) && e.webkitCompassHeading >= 0) {
                        // iOS Safari: webkitCompassHeading is magnetic north directly in degrees (0 = North, 90 = East)
                        compassHeading = e.webkitCompassHeading;
                    } else if (e.alpha !== null && e.alpha !== undefined && !isNaN(e.alpha)) {
                        // Android Chrome: alpha is counter-clockwise (compass heading = 360 - alpha)
                        compassHeading = (360 - e.alpha) % 360;
                    }

                    if (compassHeading !== null && !isNaN(compassHeading) && isFinite(compassHeading)) {
                        // Screen orientation compensation (Portrait 0°, Landscape 90°/270°, Inverted 180°)
                        const screenOrientation = (window.screen && window.screen.orientation && typeof window.screen.orientation.angle === 'number')
                            ? window.screen.orientation.angle 
                            : (typeof window.orientation === 'number' ? window.orientation : 0);
                        
                        const adjustedHeading = (compassHeading + screenOrientation + 360) % 360;
                        updateSmoothCompassHeading(adjustedHeading);
                    }
                }

                window.handleCompassOrientation = handleCompassOrientation;

                if (typeof DeviceOrientationEvent !== 'undefined' && typeof DeviceOrientationEvent.requestPermission === 'function') {
                    const triggerIOSPermission = () => {
                        DeviceOrientationEvent.requestPermission()
                            .then(res => {
                                if (res === 'granted') {
                                    window.removeEventListener('deviceorientation', handleCompassOrientation, true);
                                    window.addEventListener('deviceorientation', handleCompassOrientation, true);
                                }
                            })
                            .catch(() => {});
                    };
                    window.addEventListener('click', triggerIOSPermission, { once: true });
                    window.addEventListener('touchend', triggerIOSPermission, { once: true });
                } else {
                    // Only attach ONE listener to prevent dual-stream angle oscillation
                    if ('ondeviceorientationabsolute' in window) {
                        window.addEventListener('deviceorientationabsolute', handleCompassOrientation, true);
                    } else if ('ondeviceorientation' in window) {
                        window.addEventListener('deviceorientation', handleCompassOrientation, true);
                    }
                }

                // Re-awaken sensors when tab is brought back into focus
                document.addEventListener('visibilitychange', function() {
                    if (!document.hidden && !hasActiveDriverTrip()) {
                        isCompassAnimating = false;
                    }
                });
            }

            function runInitializations() {
                initGrabHomeMap();
                if (window.drawActiveTripPins) window.drawActiveTripPins();
                syncFloatingButtonsPosition();
                attachSheetResizeObserver();
                initSheetSwipeGesture();
                initActiveTripSwipeGesture();
                initCompassAndRealtimeGps();

                // 🔄 Ride went live (accepted / arrived / in_transit / returning):
                // the SPA partial swap carries the sheet's old dashboard transform
                // across with data-sheet-position-locked, so the sheet would stay
                // parked at the dashboard position instead of hugging the trip card.
                // hugDriverActiveTripSheet self-guards (no active trip, mid-drag, or
                // the deliberately semi-hidden slit are all left untouched).
                if (window.hugDriverActiveTripSheet) {
                    setTimeout(function() { window.hugDriverActiveTripSheet(420); }, 130);
                }

                // 🚚 Seed the passenger-facing location cache with the driver's
                // CURRENT known position right away — not only after the next GPS /
                // simulated update — so the passenger's initial render (and first
                // status polls) already show the real driver location, never the
                // pickup-coords fallback. No-op when there is no active ride.
                if (window.sendDriverLocationToBackend) {
                    let seedLat = null, seedLng = null;
                    try {
                        const pos = driverMarker && driverMarker.getLngLat ? driverMarker.getLngLat() : null;
                        if (pos && typeof pos.lat === 'number') { seedLat = pos.lat; seedLng = pos.lng; }
                    } catch(e) {}
                    if (!(seedLat !== null && isFinite(seedLat))) {
                        const sLat = parseFloat(localStorage.getItem('srh_simulated_lat'));
                        const sLng = parseFloat(localStorage.getItem('srh_simulated_lng'));
                        if (isFinite(sLat) && isFinite(sLng)) { seedLat = sLat; seedLng = sLng; }
                    }
                    if (!(seedLat !== null && isFinite(seedLat))) {
                        const rLat = parseFloat(localStorage.getItem('srh_last_real_lat'));
                        const rLng = parseFloat(localStorage.getItem('srh_last_real_lng'));
                        if (isFinite(rLat) && isFinite(rLng)) { seedLat = rLat; seedLng = rLng; }
                    }
                    if (seedLat !== null && isFinite(seedLat) && isFinite(seedLng)) {
                        window.sendDriverLocationToBackend(seedLat, seedLng);
                    }
                }
            }

            // Global Event Delegation: Guarantees collapse/expand tap works instantly even when innerHTML changes
            document.addEventListener('click', function(e) {
                const handle = e.target ? e.target.closest('#active-trip-drag-handle') : null;
                if (handle && typeof window.toggleActiveTripSheet === 'function') {
                    window.toggleActiveTripSheet();
                }
            });

            let _lastInitRunTime = 0;
            function runInitializationsDebounced() {
                const now = Date.now();
                if (now - _lastInitRunTime < 500) return;
                _lastInitRunTime = now;
                runInitializations();
            }

            if (document.readyState === 'interactive' || document.readyState === 'complete') {
                runInitializationsDebounced();
            } else {
                document.addEventListener('DOMContentLoaded', runInitializationsDebounced, { once: true });
            }
            window.addEventListener('load', runInitializationsDebounced);
            window.addEventListener('resize', syncFloatingButtonsPosition);
            window.addEventListener('spa:page-loaded', runInitializationsDebounced);
            window.addEventListener('pageshow', runInitializationsDebounced);
            window.addEventListener('popstate', runInitializationsDebounced);
        })();
    </script>

    <script>
        // ============================================================
        // INCOMING RIDE TAKEOVER OVERLAY MANAGER
        // Renders & animates the Grab-style offer sheet fully on the
        // client (no reloads): offer -> sending -> sent (waiting for
        // passenger) -> hidden. Drives the 60s countdown, fare chips,
        // decline/retract actions and the WebAudio chimes.
        // ============================================================
        (function () {
            if (window.__srhIncManagerLoaded) return;
            window.__srhIncManagerLoaded = true;

            var SRH_RESPONSE_SECONDS = 60;
            var MY_DRIVER_ID = {{ auth()->id() }};

            window.__srhInc = {
                rideId: null,
                lastFare: null,
                state: 'hidden',      // hidden | offer | sending | sent | hiding
                timer: null,
                deadline: 0,
                hiddenByMe: false,
                autoDeclined: false,
                toastThrottle: 0,
                declineSuppressUntil: 0,
                lastRideId: null,
                lastRide: null,
                reOfferTimer: null
            };
            var st = window.__srhInc;

            function now() { return Date.now(); }

            function toast(msg, type) {
                if (window.createSlidingToast) window.createSlidingToast(msg, type || 'info');
            }

            function throttleToast() {
                var t = now();
                if (t - st.toastThrottle < 300) return false;
                st.toastThrottle = t;
                return true;
            }

            function fmtFare(n) {
                var x = Math.round((parseFloat(n) || 0) * 100) / 100;
                return 'P' + x.toLocaleString('en-PH');
            }

            // ---- chimes (WebAudio, no asset needed) ----
            function audioCtx() {
                if (!window._srhUserHasInteracted) return null; // Wait for user gesture to avoid autoplay warning
                if (!window.__srhAudioCtx) {
                    var AC = window.AudioContext || window.webkitAudioContext;
                    if (AC) window.__srhAudioCtx = new AC();
                }
                return window.__srhAudioCtx || null;
            }
            function tone(freq, dur, gain, when) {
                var ctx = audioCtx();
                if (!ctx) return;
                try {
                    if (ctx.state === 'suspended') ctx.resume();
                    var o = ctx.createOscillator();
                    var g = ctx.createGain();
                    o.type = 'sine';
                    o.frequency.value = freq;
                    var t = ctx.currentTime + (when || 0);
                    g.gain.setValueAtTime(0.0001, t);
                    g.gain.exponentialRampToValueAtTime(gain || 0.2, t + 0.02);
                    g.gain.exponentialRampToValueAtTime(0.0001, t + dur);
                    o.connect(g); g.connect(ctx.destination);
                    o.start(t); o.stop(t + dur + 0.05);
                } catch (e) {}
            }
            function vibrate() {
                try { if (navigator.vibrate && window._srhUserHasInteracted) navigator.vibrate([200, 120, 200]); } catch (e) {}
            }
            window.srhPlayRequestChime = function () {
                tone(880, 0.16, 0.22, 0);
                tone(1174.66, 0.28, 0.2, 0.16);
                vibrate();
            };
            window.srhPlaySuccessChime = function () {
                tone(659.25, 0.14, 0.2, 0);
                tone(880, 0.14, 0.2, 0.13);
                tone(1108.73, 0.26, 0.2, 0.26);
            };
            window.srhPlayEndChime = function () {
                tone(440, 0.2, 0.18, 0);
                tone(349.23, 0.3, 0.16, 0.18);
            };

            // ---- DOM helpers ----
            var __srhDbg = [];
            window.__srhDbg = __srhDbg;
            function srhDbg(where, extra) {
                try {
                    var line = '[' + new Date().toISOString().slice(11, 23) + '] ' + where + (extra ? ' | ' + extra : '');
                    __srhDbg.push(line);
                    if (__srhDbg.length > 60) __srhDbg.shift();
                    // Debug output silenced — inspect window.__srhDbg in DevTools if needed
                } catch (e) {}
            }
            function getWrapper() { return document.getElementById('incoming-ride-wrapper'); }

            function forceRemoveWrapper() {
                var w = getWrapper();
                if (w && w.parentNode) w.parentNode.removeChild(w);
                stopCountdown();
            }

            function clearIncomingMapPins() {
                // The ride is gone (cancelled/declined/timed-out): drop the passenger
                // pickup pin and the terminal→pickup route so they never linger on
                // the map after the overlay hides.
                if (window._incomingPickupMarker) {
                    try { window._incomingPickupMarker.remove(); } catch (e) {}
                    window._incomingPickupMarker = null;
                }
                var liveMap = window._grabHomeMapInstance || window.homeMap;
                if (liveMap && typeof window.srhClearLineSource === 'function') {
                    try { window.srhClearLineSource(liveMap, 'incoming-ride-route'); } catch (e) {}
                }
            }

            function ensureWrapper() {
                var w = getWrapper();
                if (w) return w;
                w = document.createElement('div');
                w.id = 'incoming-ride-wrapper';
                w.className = 'srh-inc-overlay';
                var b = document.createElement('div'); b.className = 'srh-inc-backdrop';
                var card = document.createElement('div'); card.id = 'incoming-ride-card'; card.className = 'srh-inc-card';
                var tw = document.createElement('div'); tw.id = 'incoming-ride-timer-wrap'; tw.className = 'srh-inc-timer-wrap';
                var tb = document.createElement('div'); tb.className = 'srh-timer-bar';
                tw.appendChild(tb);
                var content = document.createElement('div'); content.id = 'incoming-ride-content';
                card.appendChild(tw); card.appendChild(content);
                var marker = document.createElement('div'); marker.id = 'incoming-ride-marker'; marker.className = 'hidden';
                w.appendChild(b); w.appendChild(card); w.appendChild(marker);
                document.body.appendChild(w);
                return w;
            }

            function fillMarker(ride) {
                var m = document.getElementById('incoming-ride-marker');
                if (!m) return;
                var kv = {
                    status: ride.status || '',
                    fare: ride.fare != null ? ride.fare : '',
                    pickupLat: ride.pickup_lat != null ? ride.pickup_lat : 15.4265,
                    pickupLng: ride.pickup_lng != null ? ride.pickup_lng : 120.9405,
                    destLat: ride.destination_lat != null ? ride.destination_lat : 15.4215,
                    destLng: ride.destination_lng != null ? ride.destination_lng : 120.9350
                };
                Object.keys(kv).forEach(function (k) { m.dataset[k] = kv[k]; });
            }

            function esc(s) {
                return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
                    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
                });
            }

            var INCOMING_URL = "{{ route('driver.incoming-ride') }}";

            // After a decline/retract back-off window, re-check the server and
            // re-offer the SAME ride ONLY if it is still assigned to this driver.
            function scheduleReOffer(rideId) {
                if (!rideId) return;
                if (st.reOfferTimer) { clearTimeout(st.reOfferTimer); st.reOfferTimer = null; }
                var wait = Math.max(0, (st.declineSuppressUntil || now()) - now()) + 400;
                st.reOfferTimer = setTimeout(function () {
                    st.reOfferTimer = null;
                    if (!rideId || st.state !== 'hidden' || String(st.lastRideId) !== String(rideId)) {
                        srhDbg('REOFFER skip', 'rideId=' + rideId + ' state=' + st.state + ' lastRideId=' + st.lastRideId);
                        return;
                    }
                    fetch(INCOMING_URL, { headers: { 'Accept': 'application/json' } })
                        .then(function (r) { return r.json(); })
                        .then(function (data) {
                            srhDbg('REOFFER check', 'ride=' + (data && data.ride ? data.ride.id + '@' + data.ride.status : 'null'));
                            if (data && data.ride && String(data.ride.id) === String(rideId) && st.state === 'hidden') {
                                // The decline never reached the server — the offer
                                // is still live, so surface it again now that the
                                // back-off window has fully elapsed.
                                st.lastRideId = null;
                                st.declineSuppressUntil = 0;
                                window.srhShowIncomingOverlay(data.ride, {});
                            } else if (data && !data.ride && st.state === 'hidden') {
                                // Server confirms the ride is truly gone (cancelled
                                // elsewhere while our ack was lost) — restore the sheet.
                                restoreSheet();
                            }
                        })
                        .catch(function () {});
                }, wait);
            }

            function renderOffer(ride) {
                var content = document.getElementById('incoming-ride-content');
                if (!content) return;
                content.innerHTML =
                    '<div class="srh-inc-head">' +
                    '  <span class="srh-inc-badge"><span class="srh-inc-badge-dot"></span>New Booking</span>' +
                    '  <span class="srh-inc-pos">Position #1</span>' +
                    '</div>' +
                    '<h2 class="srh-inc-title">Accept this ride</h2>' +
                    '<div class="srh-inc-routes">' +
                    '  <div class="srh-inc-row">' +
                    '    <div class="srh-inc-line"><span class="srh-inc-dot"></span></div>' +
                    '    <div class="srh-inc-body"><span class="srh-inc-label">Pickup</span>' +
                    '      <span class="srh-inc-address">' + esc(ride.pickup_location) + '</span></div>' +
                    '  </div>' +
                    '  <div class="srh-inc-row">' +
                    '    <div class="srh-inc-line"><span class="srh-inc-dot srh-inc-dot-red"></span></div>' +
                    '    <div class="srh-inc-body"><span class="srh-inc-label">Destination</span>' +
                    '      <span class="srh-inc-address">' + esc(ride.destination) + '</span></div>' +
                    '  </div>' +
                    '</div>' +
                    '<div class="srh-inc-fare-wrap">' +
                    '  <div class="srh-inc-fare-title">Propose your fare</div>' +
                    '  <div class="srh-inc-fare-chips">' +
                    '    <button type="button" class="srh-fare-chip" data-fare="30" onclick="window.srhSelectFare(30, this)">P30</button>' +
                    '    <button type="button" class="srh-fare-chip" data-fare="40" onclick="window.srhSelectFare(40, this)">P40</button>' +
                    '    <button type="button" class="srh-fare-chip" data-fare="50" onclick="window.srhSelectFare(50, this)">P50</button>' +
                    '    <button type="button" class="srh-fare-chip" data-fare="60" onclick="window.srhSelectFare(60, this)">P60</button>' +
                    '  </div>' +
                    '  <div class="srh-inc-custom-fare">' +
                    '    <span class="srh-cf-peso">P</span>' +
                    '    <input id="srh-custom-fare" type="number" min="10" max="9999" step="1" inputmode="numeric" aria-label="Custom proposed fare amount" placeholder="Custom amount"' +
                    '      oninput="window.srhSyncChipFromCustom(this)"' +
                    '      onkeydown="if(event.key === String.fromCharCode(13)){event.preventDefault();window.srhSendFareProposal();}" />' +
                    '  </div>' +
                    '</div>' +
                    '<div class="srh-inc-actions">' +
                    '  <button type="button" id="srh-decline-btn" class="srh-inc-decline" onclick="window.srhDeclineIncomingRide(this, false)">Decline</button>' +
                    '  <button type="button" id="srh-send-fare-btn" class="srh-inc-send" onclick="window.srhSendFareProposal(this)">Send Fare Proposal</button>' +
                    '</div>';
                var suggested = parseFloat(ride.fare);
                if (isFinite(suggested) && suggested > 0) {
                    var chip = content.querySelector('.srh-fare-chip[data-fare="' + suggested + '"]');
                    if (chip) {
                        chip.classList.add('srh-fare-chip-active');
                    } else {
                        var el = document.getElementById('srh-custom-fare');
                        if (el) el.value = suggested;
                    }
                }
            }

            function renderSent(ride, fare) {
                var content = document.getElementById('incoming-ride-content');
                if (!content) return;
                content.innerHTML =
                    '<div class="srh-inc-head">' +
                    '  <span class="srh-inc-badge"><span class="srh-inc-badge-dot"></span>Offer Sent</span>' +
                    '</div>' +
                    '<div class="srh-inc-sent">' +
                    '  <span class="srh-inc-sent-label">Proposed Fare</span>' +
                    '  <div class="srh-inc-sent-fare">' + fmtFare(fare) + '</div>' +
                    '  <div class="srh-inc-wait-dots"><span></span><span></span><span></span></div>' +
                    '  <p class="srh-inc-sent-hint">Waiting for the passenger to confirm your fare proposal.' +
                    '    <button type="button" onclick="window.srhDeclineIncomingRide(this, false)">Retract offer</button></p>' +
                    '</div>';
            }

            // ---- countdown ----
            function startCountdown() {
                stopCountdown();
                var w = getWrapper();
                if (!w) return;
                w.classList.remove('srh-timer-paused');
                void w.offsetWidth;
                w.classList.add('srh-timer-run');
                st.deadline = now() + SRH_RESPONSE_SECONDS * 1000;
                st.timer = setInterval(function () {
                        if (now() >= st.deadline) {
                            stopCountdown();
                            st.autoDeclined = true;
                            st.hiddenByMe = true;
                            st.declineSuppressUntil = now() + 20000;
                            var expiredRideId = st.rideId;
                            st.lastRideId = String(expiredRideId);
                            scheduleReOffer(expiredRideId);
                            if (throttleToast()) {
                                toast(st.state === 'sent' ? 'Offer timed out.' : 'Request timed out.', 'info');
                            }
                            if (window.srhPlayEndChime) window.srhPlayEndChime();
                            hideOverlay(true);
                            window.srhCancelRide(null, expiredRideId);
                        }
                }, 1000);
            }

            function stopCountdown() {
                if (st.timer) { clearInterval(st.timer); st.timer = null; }
                if (window.srhStopRequestChime) window.srhStopRequestChime();
                var w = getWrapper();
                if (w) w.classList.remove('srh-timer-run');
                st.deadline = 0;
            }

            function pauseCountdown() {
                if (st.timer) { clearInterval(st.timer); st.timer = null; }
                var w = getWrapper();
                if (w) w.classList.add('srh-timer-paused');
            }

            function hideOverlay(instant, skipCheck) {
                var w = getWrapper();
                if (!w) {
                    st.state = 'hidden';
                    st.rideId = null;
                    clearIncomingMapPins();
                    return;
                }
                st.state = 'hiding';
                stopCountdown();
                clearIncomingMapPins();
                if (instant) {
                    forceRemoveWrapper();
                    st.state = 'hidden';
                    st.rideId = null;
                    if (!skipCheck) afterHideCheck();
                    return;
                }
                w.classList.add('srh-inc-hiding');
                w.classList.remove('srh-inc-visible');
                setTimeout(function () {
                    var ww = getWrapper();
                    if (ww && st.state === 'hiding') {
                        ww.parentNode.removeChild(ww);
                        st.state = 'hidden';
                        st.rideId = null;
                        if (!skipCheck) afterHideCheck();
                    }
                }, 340);
            }

            function afterHideCheck() {
                srhDbg('afterHideCheck', 'hiddenByMe=' + st.hiddenByMe + ' sheet=' + !!document.getElementById('driver-bottom-sheet') + ' activeTrip=' + !!document.getElementById('active-trip-wrapper'));
                // Never race the server: when we hid because the DRIVER declined,
                // retracted or timed out ("hiddenByMe"), the underlying sheet is
                // only restored after the cancel lands (see restoreSheet) or after
                // the poller confirms the ride is really gone — never before, or
                // the re-render re-hydrates the still-pending ride (decline loop).
                if (st.hiddenByMe) return;
                if (!document.getElementById('driver-bottom-sheet') && !document.getElementById('active-trip-wrapper')) {
                    if (window.navigateTo) {
                        try {
                            // Cache-bust the restore navigation so neither the SPA
                            // page cache nor the service worker can ever serve a
                            // stale page that still contains the cancelled ride.
                            var base = window.location.href;
                            var sep = base.indexOf('?') === -1 ? '?' : '&';
                            window.navigateTo(base + sep + 'srh_restore=' + Date.now(), false, false, true);
                        } catch (e) {}
                    }
                }
            }

            // Restore the underlying queue/trip sheet ONCE the server has
            // confirmed the offered ride is gone (cancel ack, poll result or
            // timed-out re-check). Safe to call from anywhere at any time:
            // the sheet-presence check makes it a no-op when nothing is owed.
            function restoreSheet() {
                srhDbg('restoreSheet()', 'hiddenByMe=' + st.hiddenByMe + ' lastRideId=' + st.lastRideId + ' state=' + st.state);
                if (!st.hiddenByMe && st.lastRideId == null) return;
                st.hiddenByMe = false;
                st.lastRideId = null;
                st.declineSuppressUntil = 0;
                if (st.reOfferTimer) { clearTimeout(st.reOfferTimer); st.reOfferTimer = null; }
                afterHideCheck();
            }

            // 🔔 Continuous Incoming Dispatch Chime (Repeating Call Tone & Pulsing Vibration)
            var __srhChimeTimer = null;
            var __srhAudioCtx = null;

            function getAudioContext() {
                try {
                    if (!__srhAudioCtx) {
                        var AudioCtxClass = window.AudioContext || window.webkitAudioContext;
                        if (AudioCtxClass) {
                            __srhAudioCtx = new AudioCtxClass();
                        }
                    }
                    if (__srhAudioCtx && __srhAudioCtx.state === 'suspended') {
                        __srhAudioCtx.resume().catch(function() {});
                    }
                } catch (e) {}
                return __srhAudioCtx;
            }

            function playSingleChimeTone() {
                try {
                    var ctx = getAudioContext();
                    if (!ctx) return;
                    var t = ctx.currentTime;

                    // Tone 1 (High bell ding)
                    var osc1 = ctx.createOscillator();
                    var gain1 = ctx.createGain();
                    osc1.type = 'sine';
                    osc1.frequency.setValueAtTime(880, t); // A5
                    osc1.frequency.exponentialRampToValueAtTime(1046.5, t + 0.12); // C6
                    gain1.gain.setValueAtTime(0.7, t);
                    gain1.gain.exponentialRampToValueAtTime(0.001, t + 0.35);
                    osc1.connect(gain1);
                    gain1.connect(ctx.destination);
                    osc1.start(t);
                    osc1.stop(t + 0.35);

                    // Tone 2 (Echo chime)
                    var osc2 = ctx.createOscillator();
                    var gain2 = ctx.createGain();
                    osc2.type = 'triangle';
                    osc2.frequency.setValueAtTime(1318.5, t + 0.14); // E6
                    gain2.gain.setValueAtTime(0.6, t + 0.14);
                    gain2.gain.exponentialRampToValueAtTime(0.001, t + 0.45);
                    osc2.connect(gain2);
                    gain2.connect(ctx.destination);
                    osc2.start(t + 0.14);
                    osc2.stop(t + 0.45);

                    // Continuous Phone Vibration pulse
                    if (typeof navigator !== 'undefined' && navigator.vibrate) {
                        try { navigator.vibrate([400, 150, 400, 150, 600]); } catch (e) {}
                    }
                } catch (e) {}
            }

            window.srhPlayRequestChime = function () {
                playSingleChimeTone();
            };

            window.srhStopRequestChime = function () {
                // No-op (continuous loop removed in favor of single chime tone + push notification phone vibration)
            };

            // Listen for push notifications posted from service worker to check incoming ride immediately
            if (typeof navigator !== 'undefined' && navigator.serviceWorker) {
                navigator.serviceWorker.addEventListener('message', function(event) {
                    if (event.data && event.data.type === 'srh-incoming-dispatch') {
                        if (window.srhForceIncomingCheck) {
                            window.srhForceIncomingCheck(true);
                        } else if (typeof checkIncoming === 'function') {
                            checkIncoming(true);
                        }
                    }
                });
            }

            // ---- public API ----
            window.srhShowIncomingOverlay = function (ride, opts) {
                opts = opts || {};
                if (!ride || !ride.id) return;
                var w = getWrapper();
                var rideKey = String(ride.id);
                var sameRide = st.rideId !== null && String(st.rideId) === rideKey;
                // This marker intentionally keys on lastRideId (not rideId): the
                // overlay nulls rideId when it hides, so a re-render of the SAME
                // ride while the decline PATCH is still in flight could otherwise
                // re-hydrate the popup forever (the decline loop).
                var declinedRide = st.lastRideId !== null && String(st.lastRideId) === rideKey;
                var sameFare = String(st.lastFare) === String(ride.fare == null ? '' : ride.fare);

                if (st.state === 'sent' && sameRide) return;
                if (st.state === 'sending' && sameRide) return;
                // Back-off re-showing a ride the driver just declined/retracted:
                // SPA re-renders may briefly re-serve the same server ride while
                // the decline request is still in flight.
                if (declinedRide && st.declineSuppressUntil && now() < st.declineSuppressUntil) {
                    srhDbg('SHOW-BLOCKED', 'ride=' + rideKey + ' declinedRide=true suppressLeft=' + Math.round(st.declineSuppressUntil - now()) + 'ms');
                    return;
                }
                if ((st.state === 'offer' || st.state === 'hiding') && sameRide && sameFare && w && w.classList.contains('srh-inc-visible')) return;

                if (w && !sameRide && st.state !== 'hidden' && st.state !== 'hiding') {
                    forceRemoveWrapper();
                }
                if (!declinedRide) {
                    st.declineSuppressUntil = 0;
                    st.lastRideId = null;
                    if (st.reOfferTimer) { clearTimeout(st.reOfferTimer); st.reOfferTimer = null; }
                }

                w = ensureWrapper();
                st.rideId = ride.id;
                st.lastFare = ride.fare == null ? '' : ride.fare;
                st.hiddenByMe = false;
                st.autoDeclined = false;
                st.lastRide = ride;
                fillMarker(ride);
                w.classList.remove('srh-inc-hiding');
                renderOffer(ride);

                if (opts.hydrate) {
                    w.classList.remove('srh-inc-visible');
                    void w.offsetWidth;
                }
                w.classList.add('srh-inc-visible');
                st.state = 'offer';
                startCountdown();
                srhDbg('SHOW', 'ride=' + rideKey + (opts && opts.hydrate ? ' src=hydrate' : ' src=stack') + ' :: ' + ((new Error().stack || '?').split('\n').slice(2, 5).join(' < ')));
                if (window.srhPlayRequestChime) window.srhPlayRequestChime();
                try { document.dispatchEvent(new CustomEvent('srh:incoming-ride-shown')); } catch (e) {}
            };

            window.srhHideIncomingOverlay = function (data, opts) {
                opts = opts || {};
                var w = getWrapper();
                if (!w || st.state === 'hidden') {
                    // The ride vanished while the overlay was already closed (e.g.
                    // the driver declined, the ride was cancelled in the meantime
                    // and the poll just confirmed it): restore the underlying
                    // sheet now that the server state is settled.
                    if (!opts.noRestore && st.lastRideId != null) {
                        srhDbg('HIDE(hidden)+restore', 'lastRideId=' + st.lastRideId);
                        restoreSheet();
                    } else {
                        srhDbg('HIDE(hidden) skip', 'noRestore=' + !!opts.noRestore + ' lastRideId=' + st.lastRideId);
                    }
                    return;
                }
                var ls = data ? data.last_ride_status : null;
                var lsDriverId = data ? data.last_ride_driver_id : null;
                var mine = lsDriverId != null && String(lsDriverId) === String(MY_DRIVER_ID);
                // The passenger already cancelled (or the ride was taken elsewhere):
                // kill the countdown and tear the popup down RIGHT NOW. Never let
                // the offer keep waiting out the 60s timer for a ride that is gone.
                if (ls === 'cancelled' || !(data && data.ride)) {
                    stopCountdown();
                    opts.instant = true;
                }
                if (st.state !== 'sending' && !opts.silent && !st.hiddenByMe && throttleToast()) {
                    var msg = null;
                    if (ls === 'cancelled') {
                        msg = 'Passenger cancelled the request.';
                    } else if (ls === 'accepted' || ls === 'arrived' || ls === 'in_transit' || ls === 'returning') {
                        msg = mine ? 'Passenger accepted your fare! Trip started.' : 'Another driver took this request.';
                    } else {
                        msg = 'This ride is no longer available.';
                    }
                    toast(msg, ls === 'cancelled' ? 'danger' : 'info');
                }
                hideOverlay(!!opts.instant, opts.silent);
            };

            window.srhOnRideActionSuccess = function (url, method, data) {
                srhDbg('ACK', url + ' state=' + st.state);
                if (window.srhStopRequestChime) window.srhStopRequestChime();
                if (String(url).indexOf('/propose-fare') !== -1) {
                    if (st.state === 'sent' || st.state === 'hiding') return;
                    // Keep countdown running continuously even after sending fare proposal
                    st.state = 'sent';
                    var fare = (data && data.fare != null) ? data.fare : st.lastFare;
                    renderSent({ id: st.rideId }, fare);
                    if (window.srhPlaySuccessChime) window.srhPlaySuccessChime();
                    return;
                }
                if (String(url).indexOf('/cancel') !== -1) {
                    // Decline landed server-side: the ride can no longer re-offer.
                    // Drop the suppression marker and restore the queue card —
                    // this runs AFTER the server committed, so the re-render can
                    // never race the cancel and re-show the same pending ride.
                    if (st.state === 'sent') {
                        st.hiddenByMe = true;
                        hideOverlay(true, true);
                    }
                    restoreSheet();
                    return;
                }
            };

            window.handleDriverRideActionSuccess = function (url, method, data) {
                if (typeof window.srhOnRideActionSuccess === 'function') {
                    try { window.srhOnRideActionSuccess(url, method, data); } catch(e) {}
                }
                const isCancel = String(url).indexOf('/cancel') !== -1;
                const isCompleteReturn = String(url).indexOf('/complete-return') !== -1 || String(url).indexOf('/complete') !== -1;
                if (isCancel || isCompleteReturn) {
                    if (window.syncDriverDashboardSheet) {
                        window.syncDriverDashboardSheet(460);
                    }
                    if (typeof fetchLiveQueue === 'function') {
                        setTimeout(() => { fetchLiveQueue(); }, 300);
                    }
                }
            };

            window.srhSelectFare = function (fare, chipEl) {
                var chips = document.querySelectorAll('.srh-fare-chip');
                Array.prototype.forEach.call(chips, function (c) { c.classList.remove('srh-fare-chip-active'); });
                if (chipEl) chipEl.classList.add('srh-fare-chip-active');
                var el = document.getElementById('srh-custom-fare');
                if (el) el.value = fare;
            };

            window.srhSyncChipFromCustom = function (input) {
                if (!input) return;
                var v = parseFloat(input.value);
                var chips = document.querySelectorAll('.srh-fare-chip');
                Array.prototype.forEach.call(chips, function (c) { c.classList.remove('srh-fare-chip-active'); });
                if (isFinite(v) && v > 0) {
                    var chip = document.querySelector('.srh-fare-chip[data-fare="' + v + '"]');
                    if (chip) chip.classList.add('srh-fare-chip-active');
                }
            };

            function selectedFare() {
                var active = document.querySelector('.srh-fare-chip.srh-fare-chip-active');
                if (active) return parseFloat(active.dataset.fare);
                var el = document.getElementById('srh-custom-fare');
                if (el && el.value !== '') {
                    var v = parseFloat(el.value);
                    if (isFinite(v) && v > 0) return v;
                }
                return null;
            }

            window.srhSendFareProposal = function (btn) {
                if (st.state !== 'offer') return;
                if (!btn) btn = document.getElementById('srh-send-fare-btn');
                var fare = selectedFare();
                if (fare == null || fare < 10 || fare > 9999) {
                    toast('Enter a fare between P10 and P9,999.', 'error');
                    return;
                }
                var rideId = st.rideId;
                if (!rideId || !window.submitRideAction) return;
                st.state = 'sending';
                if (btn) {
                    btn.classList.add('srh-inc-send-disabled');
                    btn.textContent = 'Sending...';
                }
                window.submitRideAction('/rides/' + rideId + '/propose-fare', 'PATCH', { fare: String(fare) }, btn);
            };

            window.srhDeclineIncomingRide = function (btn, auto) {
                auto = !!auto;
                var rideId = st.rideId;
                srhDbg('DECLINE-CLICK', 'rideId=' + rideId + ' auto=' + auto + ' state=' + st.state);
                if (!rideId || st.state === 'hidden' || st.state === 'hiding') return;
                var wasSent = st.state === 'sent';
                st.hiddenByMe = true;
                st.declineSuppressUntil = now() + 20000;
                st.lastRideId = String(rideId);
                stopCountdown();
                if (!auto) {
                    if (wasSent) {
                        if (throttleToast()) toast('Proposal retracted.', 'info');
                    } else {
                        if (throttleToast()) toast('Request declined.', 'info');
                    }
                }
                hideOverlay(true);
                scheduleReOffer(rideId);
                window.srhCancelRide(btn, rideId);
            };

            window.srhCancelRide = function (btn, rideId) {
                // rideId must be captured BEFORE hideOverlay() nulls st.rideId,
                // otherwise the cancel POST never fires and the same searching
                // ride re-offers 20s later (the decline loop).
                if (rideId == null) rideId = st.rideId;
                if (!rideId || !window.submitRideAction) return;
                window.submitRideAction('/rides/' + rideId + '/cancel', 'PATCH', {}, btn);
            };

            window.addEventListener('spa:page-loaded', function () {
                var w = getWrapper();
                var vis = w && w.classList.contains('srh-inc-visible');
                try { window.__srhDbg && window.__srhDbg.push('spa:page-loaded vis=' + !!vis + ' w=' + (w ? !!w.dataset.rideId : false)); } catch (e) {}
                if (w && !vis) {
                    // A freshly server-rendered incoming hook sits in the DOM but
                    // its inline hydration script was skipped (partial SPA swaps
                    // only re-run scripts nested INSIDE the swapped sheet). The
                    // manager persists across swaps, so activate it here. The
                    // decline back-off inside srhShowIncomingOverlay still guards
                    // against re-showing a ride the driver just declined.
                    var rid = w.dataset.rideId;
                    if (rid) {
                        if (st.state === 'sent' && st.rideId !== null && String(st.rideId) === String(rid)) {
                            // Offer already sent for this ride — just re-surface
                            // the waiting view (countdown stays paused).
                            srhDbg('SPA-RESURFACE-SENT', 'ride=' + rid);
                            w.classList.add('srh-inc-visible');
                            renderSent({ id: rid }, st.lastFare);
                            return;
                        }
                        var ride = {
                            id: rid,
                            status: w.dataset.rideStatus || '',
                            fare: w.dataset.rideFare || null,
                            pickup_location: w.dataset.pickupLocation || 'Pickup',
                            destination: w.dataset.destination || 'Destination',
                            pickup_lat: w.dataset.pickupLat ? parseFloat(w.dataset.pickupLat) : 15.4265,
                            pickup_lng: w.dataset.pickupLng ? parseFloat(w.dataset.pickupLng) : 120.9405,
                            destination_lat: w.dataset.destLat ? parseFloat(w.dataset.destLat) : 15.4215,
                            destination_lng: w.dataset.destLng ? parseFloat(w.dataset.destLng) : 120.9350
                        };
                        srhDbg('SPA-REHYDRATE', 'ride=' + rid + ' state=' + st.state + ' stRideId=' + st.rideId + ' lastRideId=' + st.lastRideId + ' suppress=' + Math.max(0, (st.declineSuppressUntil || 0) - now()));
                        window.srhShowIncomingOverlay(ride, { hydrate: true });
                        return;
                    }
                }
                if (!w || !vis) {
                    stopCountdown();
                    st.state = 'hidden';
                    st.rideId = null;
                    st.hiddenByMe = false;
                    st.autoDeclined = false;
                }
            });

            window.addEventListener('online', function () {
                if (window.srhForceIncomingCheck) window.srhForceIncomingCheck();
            });
        })();
    </script>

    <script>
        // 100% Pure Event-Driven Architecture:
        // Incoming bookings, cancellations, fare proposals, and queue changes are pushed
        // in real-time via WebSockets (<50ms). Zero background polling loops.
        (function() {
            if (!document.getElementById('grab-home-map')) return;

            const CHECK_MS = 3500;
            const MY_USER_ID = {{ auth()->id() }};
            const initSheet = document.getElementById('driver-bottom-sheet');
            const initActiveId = initSheet ? initSheet.dataset.rideId : '';
            const initActiveStatus = initSheet ? initSheet.dataset.activeStatus : '';
            const initIncWrap = document.getElementById('incoming-ride-wrapper');
            const initIncId = initIncWrap ? initIncWrap.dataset.rideId : '';
            const initIncStatus = initIncWrap ? initIncWrap.dataset.rideStatus : '';
            const initIncFare = initIncWrap ? initIncWrap.dataset.rideFare : '';
            const initKey = (initIncId ? (initIncId + '|' + initIncStatus + '|' + (initIncFare || '')) : '') + '||' + (initActiveId ? (initActiveId + '|' + initActiveStatus) : '');
            let lastState = { key: initKey, rideId: initIncId || null, booted: false };
            let pollTimer = null;
            let navigating = false;
            let checkInFlight = false;

            function isStale() {
                const mapEl = document.getElementById('grab-home-map');
                return !mapEl || !document.body.contains(mapEl);
            }

            function shouldPoll() {
                if (window.__srhDutyToggleInFlight) return false;
                if (navigating || isStale()) return false;
                if (document.querySelector('form[data-submitting="true"]')) return false;
                const inc = window.__srhInc;
                const offerUp = inc && inc.state && inc.state !== 'hidden';
                const active = document.activeElement;
                if (!offerUp && active && (active.tagName === 'INPUT' || active.tagName === 'TEXTAREA' || active.tagName === 'SELECT')) return false;
                return true;
            }

            function stateKey(ride, activeTrip) {
                const rKey = ride ? ride.id + '|' + ride.status + '|' + (ride.fare || '') : '';
                const aKey = activeTrip ? activeTrip.id + '|' + activeTrip.status : '';
                return rKey + '||' + aKey;
            }

            let lastCheckIncomingTs = 0;
            async function checkIncoming(force = false) {
                if (isStale()) return;
                if (checkInFlight) return;
                if (!force && Date.now() - lastCheckIncomingTs < 500) return;
                if (!force && !shouldPoll()) return;
                checkInFlight = true;
                lastCheckIncomingTs = Date.now();
                try {
                    const lastRideId = lastState ? lastState.rideId : 0;
                    const res = await fetch("{{ route('driver.incoming-ride') }}?last_ride_id=" + (lastRideId || ''), {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (!res.ok) { checkInFlight = false; return; }
                    const data = await res.json();
                    const ride = data && data.ride;
                    const activeTrip = data && data.active_trip;
                    // HARD FAULTLINE: if an offer popup is currently up and THIS
                    // check confirms the ride it is showing is gone (cancelled /
                    // declined / taken elsewhere) while no active trip exists,
                    // tear the popup down RIGHT NOW.
                    const hardInc = window.__srhInc;
                    if (hardInc && hardInc.rideId &&
                        (hardInc.state === 'offer' || hardInc.state === 'hiding' || hardInc.state === 'sent') &&
                        !ride && window.srhHideIncomingOverlay) {
                        try {
                            window.__srhDbg && window.__srhDbg.push('EVENT hard-hide ride=' + hardInc.rideId + '@' + (data && data.last_ride_status ? data.last_ride_status : 'null'));
                        } catch (e) {}
                        try { window.srhHideIncomingOverlay(data || {}, { instant: true, noRestore: !!(activeTrip && activeTrip.id) }); } catch (e) {}
                    }
                    const key = stateKey(ride, activeTrip);
                    const isActiveTrip = !!(activeTrip && activeTrip.id);
                    window.__srhActiveTrip = isActiveTrip ? activeTrip : null;
                    if (!lastState) {
                        lastState = { key: key, rideId: ride ? ride.id : null, booted: false };
                    }
                    if (!lastState.booted) {
                        lastState.booted = true;
                    }
                    if (ride && (!window.__srhInc || window.__srhInc.state === 'hidden') && !isActiveTrip && window.srhShowIncomingOverlay) {
                        try {
                            window.srhShowIncomingOverlay(ride, {});
                        } catch (showErr) {}
                    }
                    let incChanged = false;
                    let actChanged = false;
                    if (lastState && key !== lastState.key) {
                        const sep = lastState.key.indexOf('||');
                        const oldInc = sep === -1 ? lastState.key : lastState.key.slice(0, sep);
                        const oldAct = sep === -1 ? '' : lastState.key.slice(sep + 2);
                        const newInc = ride ? (ride.id + '|' + ride.status + '|' + (ride.fare || '')) : '';
                        const newAct = activeTrip ? (activeTrip.id + '|' + activeTrip.status) : '';
                        incChanged = oldInc !== newInc;
                        actChanged = oldAct !== newAct;
                    }
                    if (data && typeof data.is_online === 'boolean') {
                        window.__srhLastQueueTotal = data.total_queue_count;
                        if (!activeTrip && !ride && !window.__srhActiveTrip) {
                            try {
                                updateDutyUI(data.is_online, data.queue_position, data.total_queue_count);
                            } catch (dutyErr) {
                                try { window.__srhDbg && window.__srhDbg.push('EVENT dutyUI THREW: ' + (dutyErr && dutyErr.message)); } catch (e) {}
                            }
                        }
                    }

                    if (lastState && key !== lastState.key) {
                        const currentStatus = activeTrip ? activeTrip.status : null;
                        const justReturned = window._justCompletedReturn && (Date.now() - window._justCompletedReturn) < 5000;
                        if (currentStatus === 'returning' || checkIsReturning() || justReturned) {
                            try { window.__srhDbg && window.__srhDbg.push('EVENT skip (returning guard) key=' + key); } catch (e) {}
                            lastState = { key: key, rideId: ride ? ride.id : null };
                            checkInFlight = false;
                            return;
                        }
                        // Ride request arrived or disappeared with no active trip:
                        // handle 100% client-side with the takeover overlay (no reload).
                        if (incChanged && !actChanged && !isActiveTrip) {
                            lastState = { key: key, rideId: ride ? ride.id : null };
                            if (ride && window.srhShowIncomingOverlay) {
                                try { window.__srhDbg && window.__srhDbg.push('EVENT show ride=' + ride.id + '@' + ride.status); } catch (e) {}
                                try {
                                    window.srhShowIncomingOverlay(ride, {});
                                } catch (showErr) {
                                    try { window.__srhDbg && window.__srhDbg.push('EVENT show THREW: ' + (showErr && showErr.message)); } catch (e) {}
                                }
                            } else if (!ride && window.srhHideIncomingOverlay) {
                                try { window.__srhDbg && window.__srhDbg.push('EVENT no-ride'); } catch (e) {}
                                window.srhHideIncomingOverlay(data || {}, { instant: true });
                            }
                            checkInFlight = false;
                            return;
                        }
                        // If no active trip and no incoming ride: driver is in normal idle queue mode!
                        // In-place duty update handled above. No reload or navigation needed.
                        if (!isActiveTrip && !ride) {
                            lastState = { key: key, rideId: null, booted: true };
                            if (window.srhHideIncomingOverlay) {
                                try { window.srhHideIncomingOverlay(data || {}, { instant: true }); } catch (e) {}
                            }
                            checkInFlight = false;
                            return;
                        }

                        if (!navigating) {
                            navigating = true;
                            try { window.__srhDbg && window.__srhDbg.push('EVENT nav key=' + key); } catch (e) {}
                            if (window.srhHideIncomingOverlay) {
                                try { window.srhHideIncomingOverlay(data || {}, { silent: true, instant: true, noRestore: true }); } catch (e) {}
                            }
                            if (lastState.rideId && !ride && window.createSlidingToast && !(window.__srhInc && window.__srhInc.hiddenByMe)) {
                                const ls = data ? data.last_ride_status : null;
                                const lsDriverId = data ? data.last_ride_driver_id : null;
                                const mine = lsDriverId != null && String(lsDriverId) === String(MY_USER_ID);
                                if (ls === 'cancelled') {
                                    window.createSlidingToast('Passenger cancelled the request.', 'danger');
                                } else if (ls === 'accepted' || ls === 'arrived' || ls === 'in_transit' || ls === 'returning') {
                                    if (mine) {
                                        window.createSlidingToast('Passenger accepted your fare! Trip started.', 'success');
                                    } else {
                                        window.createSlidingToast('This ride is no longer available.', 'info');
                                    }
                                } else if (ls === 'fare_proposed' || ls === 'searching') {
                                    window.createSlidingToast('This ride is no longer available.', 'info');
                                }
                            }
                            lastState = { key: key, rideId: ride ? ride.id : null, booted: true };
                            navigating = false;
                            if (window.navigateTo) {
                                window.navigateTo(window.location.href, false, false, true);
                            }
                            checkInFlight = false;
                            return;
                        }
                    }

                    lastState = { key: key, rideId: ride ? ride.id : null, booted: true };
                } catch (e) {
                } finally {
                    checkInFlight = false;
                }
            }

            window.srhResetLastState = function() {
                lastState = { key: '||', rideId: null, booted: true };
            };

            window.srhExecuteIncomingFetch = checkIncoming;

            let _lastForceIncomingTs = 0;
            // Purely Event-Driven: called only when WebSocket events push a ride or queue update
            window.srhForceIncomingCheck = function(force) {
                const now = Date.now();
                if (!force && (now - _lastForceIncomingTs < 4000)) return;
                _lastForceIncomingTs = now;

                const isOnline = window.isDriverOnline ? window.isDriverOnline() : true;
                const card = document.getElementById('online-queue-card') || document.getElementById('queue-card-states');
                const pos = card ? parseInt(card.dataset.position, 10) : null;
                const badge = document.getElementById('compact-queue-badge');
                const isPos1 = pos === 1 || (badge && badge.textContent.includes('#1 '));
                const hasOverlayOpen = window.__srhInc && window.__srhInc.state && window.__srhInc.state !== 'hidden';

                // Non-#1 drivers with no open overlay have no need to query /driver/incoming-ride
                if (!force && !hasOverlayOpen && (!isOnline || (!isPos1 && pos !== null && pos > 1))) {
                    return;
                }

                checkIncoming(true);
            };

            // Trigger incoming check only on visibility restore after prolonged backgrounding
            let _lastBackgroundHideTs = 0;
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'hidden') {
                    _lastBackgroundHideTs = Date.now();
                } else if (document.visibilityState === 'visible') {
                    if (_lastBackgroundHideTs && (Date.now() - _lastBackgroundHideTs > 15000)) {
                        window.srhForceIncomingCheck(false);
                    }
                }
            });

            // Check if URL has ?srh_incoming=1 (opened from notification click)
            try {
                const curParams = new URLSearchParams(window.location.search);
                if (curParams.has('srh_incoming')) {
                    setTimeout(() => window.srhForceIncomingCheck(true), 100);
                    curParams.delete('srh_incoming');
                    const cleanUrl = window.location.pathname + (curParams.toString() ? ('?' + curParams.toString()) : '') + window.location.hash;
                    window.history.replaceState({}, document.title, cleanUrl);
                }
            } catch(e) {}

            // 🎯 When a passenger booking is on screen, animate the map to their
            // pickup: drop a pickup pin, draw the terminal→pickup route, and frame
            // both in view with a smooth fitBounds flight.
            const TERMINAL_LAT = 15.429550175641715;
            const TERMINAL_LNG = 120.92240292427664;
            let focusAttempts = 0;

            function focusOnIncomingPassenger() {
                const markerEl = document.getElementById('incoming-ride-marker');
                const map = window._grabHomeMapInstance || window.homeMap;
                if (!markerEl || typeof maplibregl === 'undefined') {
                    if (window._incomingPickupMarker) {
                        try { window._incomingPickupMarker.remove(); } catch(e){}
                        window._incomingPickupMarker = null;
                    }
                    if (map && typeof window.srhClearLineSource === 'function') {
                        window.srhClearLineSource(map, 'incoming-ride-route');
                    }
                    return;
                }
                if (!map) {
                    if (focusAttempts < 40) { focusAttempts++; setTimeout(focusOnIncomingPassenger, 300); }
                    return;
                }

                const pLat = parseFloat(markerEl.dataset.pickupLat);
                const pLng = parseFloat(markerEl.dataset.pickupLng);
                if (!isFinite(pLat) || !isFinite(pLng)) return;

                let dLat, dLng;
                if (typeof driverMarker !== 'undefined' && driverMarker) {
                    const pos = driverMarker.getLngLat();
                    dLat = pos.lat;
                    dLng = pos.lng;
                } else {
                    dLat = parseFloat(localStorage.getItem('srh_simulated_lat')) || TERMINAL_LAT;
                    dLng = parseFloat(localStorage.getItem('srh_simulated_lng')) || TERMINAL_LNG;
                }

                if (window._incomingPickupMarker) { try { window._incomingPickupMarker.remove(); } catch(e){} }
                const el = document.createElement('div');
                el.innerHTML = `<div style="display:flex;flex-direction:column;align-items:center;"><div style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;font-size:10px;font-weight:900;padding:3px 9px;border-radius:999px;border:2px solid #fff;box-shadow:0 4px 12px rgba(245,158,11,0.5);white-space:nowrap;margin-bottom:3px;">PASSENGER</div><div style="width:14px;height:14px;background:#f59e0b;border:3px solid #fff;border-radius:50%;box-shadow:0 0 0 3px rgba(245,158,11,0.35);"></div></div>`;
                window._incomingPickupMarker = new maplibregl.Marker({ element: el.firstElementChild })
                    .setLngLat([pLng, pLat])
                    .addTo(map);

                const url = `https://router.project-osrm.org/route/v1/driving/${dLng},${dLat};${pLng},${pLat}?overview=full&geometries=geojson`;
                fetch(url)
                    .then(r => r.json())
                    .then(d => {
                        if (isStale()) return;
                        const liveMap = window._grabHomeMapInstance || window.homeMap;
                        if (!liveMap) return;
                        if (d.routes && d.routes[0] && d.routes[0].geometry && window.srhSetLineSource) {
                            window.srhSetLineSource(liveMap, 'incoming-ride-route', d.routes[0].geometry.coordinates, [
                                { id: 'incoming-ride-route-glow', color: '#f59e0b', weight: 10, opacity: 0.3 },
                                { id: 'incoming-ride-route-core', color: '#f59e0b', weight: 6, opacity: 0.95 }
                            ]);
                        }
                    })
                    .catch(() => {});

                try {
                    map.fitBounds(
                        [[Math.min(dLng, pLng) - 0.001, Math.min(dLat, pLat) - 0.001],
                         [Math.max(dLng, pLng) + 0.001, Math.max(dLat, pLat) + 0.001]],
                        { padding: 80, duration: 1200, maxZoom: 17 }
                    );
                } catch (e) {}
            }

            // When the overlay manager surfaces a new incoming ride, pan the map to the passenger
            if (!window.__srhIncFocusBound) {
                window.__srhIncFocusBound = true;
                document.addEventListener('srh:incoming-ride-shown', function () {
                    if (!isStale()) focusOnIncomingPassenger();
                });
            }

            function onDriverHubLoaded() {
                if (isStale()) return;
                navigating = false;
                lastState = null;
                focusAttempts = 0;
                if (typeof updateActiveTripRoute === 'function') {
                    updateActiveTripRoute();
                }
                if (typeof initActiveTripSwipeGesture === 'function') {
                    initActiveTripSwipeGesture();
                }
                if (window.srhEnsureMaplibre && typeof window.srhEnsureMaplibre === 'function') {
                    window.srhEnsureMaplibre(focusOnIncomingPassenger);
                } else {
                    focusOnIncomingPassenger();
                }
            }

            if (window._onDriverHubLoaded) {
                window.removeEventListener('spa:page-loaded', window._onDriverHubLoaded);
            }
            window._onDriverHubLoaded = onDriverHubLoaded;
            window.addEventListener('spa:page-loaded', window._onDriverHubLoaded);
            if (document.readyState === 'interactive' || document.readyState === 'complete') {
                onDriverHubLoaded();
            } else {
                document.addEventListener('DOMContentLoaded', onDriverHubLoaded, { once: true });
            }
        })();
    </script>

    <!-- ⚡ QUEUE ACTION BAR & TOAST FOR ADMIN QUEUE REORDERING -->
    @if(auth()->check() && auth()->user()->role === 'admin')
        <div id="queue-action-bar" 
             class="fixed left-1/2 rounded-xl shadow-2xl transition-all duration-300 pointer-events-none flex items-center justify-between gap-2 z-[999999]"
             style="position: fixed !important; bottom: calc(4.75rem + env(safe-area-inset-bottom, 0px)) !important; left: 50% !important; transform: translateX(-50%) translateY(8rem); opacity: 0; pointer-events: none; z-index: 999999 !important; width: calc(100% - 2rem) !important; max-width: 23.5rem !important; background-color: #0f172a !important; border: 1px solid rgba(255, 255, 255, 0.18) !important; box-shadow: 0 12px 36px -4px rgba(0, 0, 0, 0.75) !important; padding: 0.5rem 0.75rem !important;">
            
            <div class="min-w-0 flex-1 pl-0.5">
                <p class="text-[11px] font-black tracking-tight leading-tight whitespace-nowrap" style="color: #ffffff !important; font-weight: 800;">Unsaved Changes</p>
                <p class="text-[9px] font-semibold truncate leading-tight" style="color: #94a3b8 !important;">Save to apply new order</p>
            </div>

            <div class="flex items-center gap-1.5 shrink-0">
                <button type="button" onclick="cancelQueueChanges()" 
                        class="px-2.5 py-1 rounded-lg font-bold text-[10px] transition active:scale-95 cursor-pointer whitespace-nowrap"
                        style="background-color: #1e293b !important; color: #cbd5e1 !important; border: 1px solid #334155 !important; padding: 0.25rem 0.6rem !important;">
                    Cancel
                </button>
                <button type="button" onclick="confirmSaveQueueOrder()" id="save-queue-btn"
                        class="px-3 py-1 rounded-lg font-black text-[10px] transition active:scale-95 shadow-xs flex items-center gap-1 cursor-pointer whitespace-nowrap"
                        style="background-color: #2563eb !important; color: #ffffff !important; border: 1px solid #3b82f6 !important; padding: 0.25rem 0.75rem !important;">
                    <span>Save</span>
                </button>
            </div>
        </div>
    @endif

    <div id="queueToast" 
         class="fixed left-1/2 -translate-x-1/2 px-4 py-2 rounded-full shadow-xl transition-all duration-300 opacity-0 -translate-y-12 pointer-events-none flex items-center justify-center whitespace-nowrap z-[999999]"
         style="position: fixed; top: calc(1rem + env(safe-area-inset-top, 0px)); left: 50%; transform: translateX(-50%); z-index: 999999; background-color: #0f172a !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.2) !important; box-shadow: 0 10px 30px -4px rgba(15, 23, 42, 0.6) !important;">
        <span id="queueToastMessage" class="text-xs font-extrabold tracking-wide whitespace-nowrap" style="color: #ffffff !important;">Queue order saved</span>
    </div>

    <!-- SortableJS Library (Local Bundle) -->
    <script src="{{ asset('vendor/sortable/Sortable.min.js') }}"></script>

    <script>
    (function() {
        window.isAdmin = {{ (auth()->check() && auth()->user()->role === 'admin') ? 'true' : 'false' }};
        window.isUserDragging = false;
        window.hasUnsavedChanges = false;
        window.originalDriverOrder = null;

        if (window.sortableInstance) {
            try { window.sortableInstance.destroy(); } catch(e){}
            window.sortableInstance = null;
        }

        function teleportElements() {
            let bar = document.getElementById('queue-action-bar');
            if (!bar && window.isAdmin) {
                bar = document.createElement('div');
                bar.id = 'queue-action-bar';
                bar.className = 'fixed left-1/2 rounded-xl shadow-2xl transition-all duration-300 pointer-events-none flex items-center justify-between gap-2 z-[999999]';
                bar.style.cssText = 'position: fixed !important; bottom: calc(4.75rem + env(safe-area-inset-bottom, 0px)) !important; left: 50% !important; transform: translateX(-50%) translateY(8rem); opacity: 0; pointer-events: none; z-index: 999999 !important; width: calc(100% - 2rem) !important; max-width: 23.5rem !important; background-color: #0f172a !important; border: 1px solid rgba(255, 255, 255, 0.18) !important; box-shadow: 0 12px 36px -4px rgba(0, 0, 0, 0.75) !important; padding: 0.5rem 0.75rem !important;';
                bar.innerHTML = `
                    <div class="min-w-0 flex-1 pl-0.5">
                        <p class="text-[11px] font-black tracking-tight leading-tight whitespace-nowrap" style="color: #ffffff !important; font-weight: 800;">Unsaved Changes</p>
                        <p class="text-[9px] font-semibold truncate leading-tight" style="color: #94a3b8 !important;">Save to apply new order</p>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <button type="button" onclick="cancelQueueChanges()" 
                                class="px-2.5 py-1 rounded-lg font-bold text-[10px] transition active:scale-95 cursor-pointer whitespace-nowrap"
                                style="background-color: #1e293b !important; color: #cbd5e1 !important; border: 1px solid #334155 !important; padding: 0.25rem 0.6rem !important;">
                            Cancel
                        </button>
                        <button type="button" onclick="confirmSaveQueueOrder()" id="save-queue-btn"
                                class="px-3 py-1 rounded-lg font-black text-[10px] transition active:scale-95 shadow-xs flex items-center gap-1 cursor-pointer whitespace-nowrap"
                                style="background-color: #2563eb !important; color: #ffffff !important; border: 1px solid #3b82f6 !important; padding: 0.25rem 0.75rem !important;">
                            <span>Save</span>
                        </button>
                    </div>
                `;
                document.body.appendChild(bar);
            } else if (bar && bar.parentElement !== document.body) {
                document.body.appendChild(bar);
            }

            let toast = document.getElementById('queueToast');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'queueToast';
                toast.className = 'fixed left-1/2 -translate-x-1/2 px-4 py-2 rounded-full shadow-xl transition-all duration-300 opacity-0 -translate-y-12 pointer-events-none flex items-center justify-center whitespace-nowrap z-[999999]';
                toast.style.cssText = 'position: fixed; top: calc(1rem + env(safe-area-inset-top, 0px)); left: 50%; transform: translateX(-50%); z-index: 999999; background-color: #0f172a !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.2) !important; box-shadow: 0 10px 30px -4px rgba(15, 23, 42, 0.6) !important;';
                toast.innerHTML = '<span id="queueToastMessage" class="text-xs font-extrabold tracking-wide whitespace-nowrap" style="color: #ffffff !important;">Queue order saved</span>';
                document.body.appendChild(toast);
            } else if (toast && toast.parentElement !== document.body) {
                document.body.appendChild(toast);
            }
        }
        window.teleportElements = teleportElements;
        teleportElements();

        function showQueueToast(message = 'Queue order saved') {
            teleportElements();
            const toast = document.getElementById('queueToast');
            if (!toast) return;
            const msgEl = document.getElementById('queueToastMessage');
            if (msgEl) msgEl.innerText = message;

            toast.classList.remove('opacity-0', '-translate-y-12');
            setTimeout(() => {
                toast.classList.add('opacity-0', '-translate-y-12');
            }, 2800);
        }
        window.showQueueToast = showQueueToast;

        function captureOriginalOrder(force = false) {
            if (!force && (window.hasUnsavedChanges || window.isUserDragging)) return;
            const container = document.getElementById('queue-items-container');
            if (!container) return;
            const items = container.querySelectorAll('.draggable-queue-item:not(.sortable-fallback)');
            const ids = Array.from(items).map(item => String(item.getAttribute('data-driver-id'))).filter(Boolean);
            if (ids.length > 0) {
                window.originalDriverOrder = ids;
            }
        }
        window.captureOriginalOrder = captureOriginalOrder;

        function checkUnsavedChanges() {
            if (!window.isAdmin) return;
            teleportElements();
            const container = document.getElementById('queue-items-container');
            const actionBar = document.getElementById('queue-action-bar');
            if (!container || !actionBar) return;

            const currentItems = container.querySelectorAll('.draggable-queue-item:not(.sortable-fallback)');
            const currentOrder = Array.from(currentItems).map(item => String(item.getAttribute('data-driver-id'))).filter(Boolean);

            if (!window.originalDriverOrder || window.originalDriverOrder.length === 0) {
                window.originalDriverOrder = currentOrder.slice();
                return;
            }

            const isDifferent = currentOrder.length > 0 && window.originalDriverOrder.length > 0 && (
                currentOrder.length !== window.originalDriverOrder.length ||
                currentOrder.some((id, idx) => String(id) !== String(window.originalDriverOrder[idx]))
            );

            window.hasUnsavedChanges = isDifferent;

            if (window.hasUnsavedChanges) {
                actionBar.style.setProperty('transform', 'translateX(-50%) translateY(0)', 'important');
                actionBar.style.setProperty('opacity', '1', 'important');
                actionBar.style.setProperty('pointer-events', 'auto', 'important');
                actionBar.classList.remove('opacity-0', 'pointer-events-none');
            } else {
                actionBar.style.setProperty('transform', 'translateX(-50%) translateY(8rem)', 'important');
                actionBar.style.setProperty('opacity', '0', 'important');
                actionBar.style.setProperty('pointer-events', 'none', 'important');
                actionBar.classList.add('opacity-0', 'pointer-events-none');
            }
        }
        window.checkUnsavedChanges = checkUnsavedChanges;

        function initSortableQueue() {
            teleportElements();
            const container = document.getElementById('queue-items-container');
            if (!container || !window.isAdmin) return;

            if (typeof Sortable === 'undefined') {
                if (!window.loadingSortableScript) {
                    window.loadingSortableScript = true;
                    const script = document.createElement('script');
                    script.src = "{{ asset('vendor/sortable/Sortable.min.js') }}";
                    script.onload = function() {
                        window.loadingSortableScript = false;
                        initSortableQueue();
                    };
                    document.head.appendChild(script);
                } else {
                    setTimeout(initSortableQueue, 100);
                }
                return;
            }

            if (window.sortableInstance) {
                try { window.sortableInstance.destroy(); } catch(e){}
                window.sortableInstance = null;
            }

            window.sortableInstance = new Sortable(container, {
                animation: 200,
                easing: "cubic-bezier(0.16, 1, 0.3, 1)",
                handle: '.drag-handle, .queue-number-badge',
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                fallbackClass: 'sortable-fallback',
                chosenClass: 'sortable-chosen',
                forceFallback: true,
                fallbackOnBody: true,
                fallbackTolerance: 0,
                touchStartThreshold: 0,
                swapThreshold: 0.5,
                onStart: function () {
                    window.isUserDragging = true;
                    if (!window.originalDriverOrder || window.originalDriverOrder.length === 0) {
                        captureOriginalOrder(true);
                    }
                    if (navigator.vibrate) {
                        try { navigator.vibrate(20); } catch(e){}
                    }
                },
                onChange: function() {
                    updateVisualQueueNumbers();
                },
                onEnd: function () {
                    window.isUserDragging = false;
                    if (navigator.vibrate) {
                        try { navigator.vibrate([15, 15]); } catch(e){}
                    }
                    updateVisualQueueNumbers();
                    checkUnsavedChanges();
                }
            });

            // Capture initial baseline
            captureOriginalOrder(true);
        }
        window.initSortableQueue = initSortableQueue;

        initSortableQueue();

        window.__srhQueueSpaInitHandler = function() {
            if (window.initSortableQueue) window.initSortableQueue();
            if (window.syncOwnQueuePosition) window.syncOwnQueuePosition();
        };
        window.removeEventListener('spa:page-loaded', window.__srhQueueSpaInitHandler);
        window.addEventListener('spa:page-loaded', window.__srhQueueSpaInitHandler);

        // Vault restore: while parked, duty/queue events were skipped (see
        // syncOwnQueuePosition). Re-pull live state so the restored card shows
        // the driver's REAL current online/offline status and queue position.
        window.addEventListener('spa:page-restored', function(e) {
            const p = (e && e.detail && e.detail.path) || '';
            if (p !== '/dashboard') return;
            if (window.srhRefreshQueueList) { try { window.srhRefreshQueueList(); } catch(err) {} }
            if (window.srhForceIncomingCheck) { try { window.srhForceIncomingCheck(); } catch(err) {} }
        });

        function updateVisualQueueNumbers() {
            const container = document.getElementById('queue-items-container');
            if (!container) return;

            const items = container.querySelectorAll('.draggable-queue-item:not(.sortable-fallback)');
            items.forEach((item, index) => {
                const numSpan = item.querySelector('.queue-number-badge');
                if (numSpan) {
                    const newText = '#' + (index + 1);
                    if (numSpan.innerText !== newText) {
                        numSpan.innerText = newText;
                        numSpan.classList.remove('badge-pop');
                        void numSpan.offsetWidth;
                        numSpan.classList.add('badge-pop');
                    }
                    if (index === 0) {
                        numSpan.className = 'queue-number-badge font-black text-sm sm:text-base px-2.5 py-1 rounded-xl shadow-xs transition-all bg-blue-600 text-white shrink-0';
                    } else {
                        numSpan.className = 'queue-number-badge font-black text-sm sm:text-base px-2.5 py-1 rounded-xl shadow-xs transition-all bg-blue-50 text-blue-700 border border-blue-200 shrink-0';
                    }
                }
            });

            const fallback = document.querySelector('.sortable-fallback');
            const ghost = container.querySelector('.sortable-ghost');
            if (fallback && ghost) {
                const realSiblings = Array.from(container.children).filter(child => !child.classList.contains('sortable-fallback'));
                const ghostIndex = realSiblings.indexOf(ghost);
                const fallbackBadge = fallback.querySelector('.queue-number-badge');
                if (fallbackBadge && ghostIndex !== -1) {
                    const newText = '#' + (ghostIndex + 1);
                    fallbackBadge.innerText = newText;
                    if (ghostIndex === 0) {
                        fallbackBadge.className = 'queue-number-badge font-black text-sm sm:text-base px-2.5 py-1 rounded-xl shadow-xs bg-blue-600 text-white shrink-0';
                    } else {
                        fallbackBadge.className = 'queue-number-badge font-black text-sm sm:text-base px-2.5 py-1 rounded-xl shadow-xs bg-blue-50 text-blue-700 border border-blue-200 shrink-0';
                    }
                }
            }
        }
        window.updateVisualQueueNumbers = updateVisualQueueNumbers;

        function confirmSaveQueueOrder() {
            const container = document.getElementById('queue-items-container');
            const saveBtn = document.getElementById('save-queue-btn');
            if (!container) return;

            const items = container.querySelectorAll('.draggable-queue-item:not(.sortable-fallback)');
            const driverIds = Array.from(items).map(item => item.getAttribute('data-driver-id')).filter(Boolean);

            if (driverIds.length === 0) return;

            if (saveBtn) {
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<span>Saving...</span>';
            }

            fetch("{{ route('admin.reorder-queue') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ driver_ids: driverIds })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    window.originalDriverOrder = [...driverIds];
                    window.hasUnsavedChanges = false;
                    // The saved order must not be served back stale from the SPA cache
                    // when leaving /dashboard and returning — drop only that entry.
                    if (window.srhInvalidatePageCache) window.srhInvalidatePageCache('{{ route('dashboard') }}');
                    checkUnsavedChanges();
                    showQueueToast('Queue order saved');
                    updateVisualQueueNumbers();
                    if (window.syncOwnQueuePosition) window.syncOwnQueuePosition();
                }
            })
            .catch(err => console.error('Error saving queue order:', err))
            .finally(() => {
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<span>Save</span>';
                }
            });
        }
        window.confirmSaveQueueOrder = confirmSaveQueueOrder;

        function cancelQueueChanges() {
            const container = document.getElementById('queue-items-container');
            if (!container || !window.originalDriverOrder || window.originalDriverOrder.length === 0) return;

            const itemMap = new Map();
            const items = container.querySelectorAll('.draggable-queue-item:not(.sortable-fallback)');
            items.forEach(item => {
                itemMap.set(item.getAttribute('data-driver-id'), item);
            });

            window.originalDriverOrder.forEach(id => {
                const item = itemMap.get(id);
                if (item) {
                    container.appendChild(item);
                }
            });

            window.hasUnsavedChanges = false;
            updateVisualQueueNumbers();
            checkUnsavedChanges();
        }
        window.cancelQueueChanges = cancelQueueChanges;

        // Save initial queue HTML as early as possible so we can restore instantly
        try {
            const initialEmb = document.getElementById('embedded-live-queue-wrapper');
            if (initialEmb && initialEmb.innerHTML.trim().length > 30) {
                window._srhSavedQueueHtml = initialEmb.innerHTML;
            }
        } catch(e) {}

        // If a ride-action instant-UI swap rebuilt #sheet-details-content it may
        // have dropped the embedded queue list. Re-create the skeleton so the
        // next fetch + merge restores it seamlessly (no page reload).
        function restoreEmbeddedQueueSkeleton() {
            const details = document.getElementById('sheet-details-content');
            if (!details) return null;
            const card = details.querySelector('#queue-card-container');
            if (!card) return null;

            window._srhQueueFetchInFlight = false;

            let clip = document.getElementById('live-queue-clip-container');
            if (!clip) {
                clip = document.createElement('div');
                clip.id = 'live-queue-clip-container';
                clip.className = 'is-expanded';
                const emb = document.createElement('div');
                emb.id = 'embedded-live-queue-wrapper';
                emb.className = 'w-full overflow-x-hidden border-t border-slate-100 text-left space-y-3 pt-3';
                if (window._srhSavedQueueHtml) {
                    emb.innerHTML = window._srhSavedQueueHtml;
                } else {
                    const tmp = document.createElement('div');
                    tmp.id = 'live-queue-wrapper';
                    tmp.className = 'space-y-6 pb-28';
                    emb.appendChild(tmp);
                }
                clip.appendChild(emb);
                card.insertAdjacentElement('afterend', clip);
            } else {
                clip.classList.add('is-expanded');
                const emb = document.getElementById('embedded-live-queue-wrapper');
                if (emb && (!emb.innerHTML || emb.innerHTML.trim().length < 20) && window._srhSavedQueueHtml) {
                    emb.innerHTML = window._srhSavedQueueHtml;
                }
            }

            const wrapper = document.getElementById('live-queue-wrapper');
            if (wrapper) wrapper.style.display = '';
            return wrapper;
        }
        window.restoreEmbeddedQueueSkeleton = restoreEmbeddedQueueSkeleton;

        function fetchLiveQueue() {
            if (window.isUserDragging || window.hasUnsavedChanges) return;
            if (window._srhQueueFetchInFlight) return;
            window._srhQueueFetchInFlight = true;

            let wrapper = document.getElementById('live-queue-wrapper');
            let restoredSkeleton = false;
            if (!wrapper) {
                wrapper = restoreEmbeddedQueueSkeleton();
                if (!wrapper) {
                    window._srhQueueFetchInFlight = false;
                    return;
                }
                restoredSkeleton = true;
            }

            const activeEl = document.activeElement;
            if (activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA' || activeEl.tagName === 'SELECT')) {
                window._srhQueueFetchInFlight = false;
                return;
            }

            fetch("{{ route('driver.fetch-queue') }}", {
                cache: 'no-store',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) return null;
                    return response.text();
                })
                .then(html => {
                    if (!html || window.isUserDragging || window.hasUnsavedChanges) return;
                    window._srhSavedQueueHtml = html;

                    // The empty skeleton holds no sections to diff — mount the
                    // fresh full partial directly, then continue as normal.
                    if (restoredSkeleton) {
                        if (wrapper.style) wrapper.style.display = '';
                        const doc = new DOMParser().parseFromString(html, 'text/html');
                        const fresh = doc.getElementById('live-queue-wrapper');
                        if (fresh) {
                            wrapper.replaceWith(fresh);
                            wrapper = fresh;
                        }
                    }

                    // Seamless DOM-diff merge: preserves rows, transitions and scroll
                    const structureChanged = window.srhMergeLiveQueue ? window.srhMergeLiveQueue(wrapper, html) : false;
                    const queueContainer = document.getElementById('queue-items-container');
                    const queueBound = !!(window.sortableInstance && window.sortableInstance.el === queueContainer);
                    if (structureChanged || !queueBound) {
                        initSortableQueue();
                    }

                    // Re-baseline the drag state only when idle
                    if (!window.hasUnsavedChanges && !window.isUserDragging) {
                        captureOriginalOrder();
                    }

                    // Keep the driver's own queue-position texts (#badge / card) in sync
                    if (window.syncOwnQueuePosition) window.syncOwnQueuePosition();
                })
                .catch(error => console.error('Error fetching queue:', error))
                .finally(() => {
                    window._srhQueueFetchInFlight = false;
                });
        }

        // Rerverb "queue.changed" events hook into this for instant updates.
        window.srhRefreshQueueList = function() {
            fetchLiveQueue();
        };

        // No interval polling — the list is refreshed by ride.status.updated /
        // queue.changed events only. Safety nets that never poll in the
        // Strictly Event-Driven: Queue only updates via Reverb WebSockets
        initSortableQueue();
        setTimeout(initSortableQueue, 100);
    })();
    </script>
    @endif
</x-app-layout>