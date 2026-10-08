{{-- FLOATING TESTING DOCK BUTTON & OVERRIDE SIMULATOR MODAL --}}
<div id="srh-simulator-dock" style="position: fixed !important; bottom: 9.75rem !important; right: 1.25rem !important; z-index: 999999 !important; touch-action: none; user-select: none; -webkit-user-select: none;">
    {{-- Floating Action Button --}}
    <button type="button" 
            onclick="toggleSimulatorModal(true)" 
            class="group flex items-center gap-2 px-3.5 py-2.5 rounded-2xl shadow-2xl border-2 border-blue-500/60 cursor-grab active:cursor-grabbing active:scale-95 transition-transform"
            style="background-color: #0f172a !important; color: #ffffff !important; box-shadow: 0 12px 35px rgba(15,23,42,0.7) !important;">
        {{-- Drag Handle Icon --}}
        <div class="text-slate-400 font-bold text-xs cursor-grab px-0.5 select-none" title="Drag me anywhere">
            ⠿
        </div>
        <div class="w-7 h-7 rounded-xl bg-blue-600/40 border border-blue-400/60 flex items-center justify-center text-blue-400">
            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div class="text-left">
            <span class="block text-[9px] uppercase font-black tracking-wider" style="color: #60a5fa !important;">Dev GPS</span>
            <span class="block text-[11px] font-black" style="color: #ffffff !important;">Simulator</span>
        </div>
    </button>
</div>

{{-- MODAL BACKDROP & DIALOG --}}
<div id="srh-simulator-modal" class="fixed inset-0 z-[10000] hidden bg-slate-950/70 backdrop-blur-md flex items-center justify-center p-2 sm:p-6 animate-in fade-in duration-200">
    <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[92vh]">
        
        {{-- Modal Header --}}
        <div class="px-4 py-3 sm:px-6 sm:py-4 bg-slate-900 text-white flex items-center justify-between border-b border-slate-800 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white font-black text-sm shadow-md">
                    🎯
                </div>
                <div>
                    <h3 class="font-extrabold text-sm sm:text-base leading-tight">Driver Location Simulator</h3>
                    <p class="text-[11px] sm:text-xs text-slate-400 font-medium">Test TODA Terminal 35m Geofencing &amp; Auto-Queue</p>
                </div>
            </div>
            <button type="button" onclick="toggleSimulatorModal(false)" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-colors cursor-pointer">
                ✕
            </button>
        </div>

        {{-- Modal Body: Map & Controls --}}
        <div class="p-3 sm:p-6 space-y-3 sm:space-y-4 overflow-y-auto flex-1">
            
            {{-- Quick Presets --}}
            <div class="space-y-1.5">
                <label class="text-xs font-black uppercase text-slate-500 tracking-wider block">Quick Presets</label>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" id="sim-preset-terminal" onclick="applySimulatorPreset('terminal')" class="sim-preset-btn py-3 px-3 rounded-xl border-2 border-emerald-400 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-black transition-all flex flex-col items-center justify-center gap-1 shadow-sm active:scale-95 cursor-pointer">
                        <span class="truncate text-xs font-black">🎯 TODA Terminal Preset</span>
                        <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-200 text-emerald-900 font-extrabold">Inside 35m Geofence</span>
                    </button>
                    <button type="button" id="sim-preset-real" onclick="applySimulatorPreset('real')" class="sim-preset-btn py-3 px-3 rounded-xl border-2 border-blue-400 bg-blue-50 hover:bg-blue-100 text-blue-800 text-xs font-black transition-all flex flex-col items-center justify-center gap-1 shadow-sm active:scale-95 cursor-pointer">
                        <span class="truncate text-xs font-black">📡 Real Live GPS (60fps)</span>
                        <span class="text-[9px] px-2 py-0.5 rounded-full bg-blue-200 text-blue-900 font-extrabold">Hardware GPS</span>
                    </button>
                </div>
            </div>

            {{-- MapLibre Interactive Override Map Container --}}
            <div class="relative rounded-2xl border-2 border-slate-200 overflow-hidden shadow-inner bg-slate-100 h-44 sm:h-64">
                <div id="srh-simulator-map-canvas" class="w-full h-full"></div>
                <div class="absolute bottom-2 left-2 z-[1000] bg-white/90 backdrop-blur px-2.5 py-1 rounded-xl border border-slate-200 shadow-md text-[10px] sm:text-[11px] font-bold text-slate-700">
                    💡 Click map or drag pin to set location
                </div>
            </div>

            {{-- Coordinates & Live Distance Display --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3 bg-slate-50 p-2.5 sm:p-3 rounded-2xl border border-slate-200">
                <div>
                    <span class="text-[10px] font-black uppercase text-slate-400 block">Simulated Lat / Lng</span>
                    <span id="sim-coords-text" class="text-xs font-black text-slate-800 font-mono">15.429550175641715, 120.92240292427664</span>
                </div>
                <div class="text-left sm:text-right">
                    <span class="text-[10px] font-black uppercase text-slate-400 block">Distance to TODA Terminal</span>
                    <span id="sim-dist-badge" class="inline-block px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                        0 meters (INSIDE 35m GEOFENCE)
                    </span>
                </div>
            </div>

        </div>

        {{-- Modal Footer: Locked visible at bottom --}}
        <div class="px-4 py-3 sm:px-6 sm:py-4 bg-slate-100 border-t border-slate-200 flex items-center justify-between gap-2 shrink-0">
            <div class="text-[11px] font-extrabold text-slate-500">
                ⚡ Instant Location Override
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="toggleSimulatorModal(false)" class="px-4 py-2.5 rounded-xl text-xs font-black text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 transition-colors">
                    Close
                </button>
                <button type="button" onclick="saveSimulatorLocation()" class="px-4 py-2.5 rounded-xl text-xs font-black text-white bg-blue-600 hover:bg-blue-700 shadow-md active:scale-95 transition-all text-center">
                    Apply Location Override
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    window.SRH_TERMINAL_LAT = 15.429550175641715;
    window.SRH_TERMINAL_LNG = 120.92240292427664;
    window.SRH_TERMINAL_RADIUS = 35;

    let simMap = null;
    let simMarker = null;
    let currentSimLat = window.SRH_TERMINAL_LAT;
    let currentSimLng = window.SRH_TERMINAL_LNG;

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3;
        const φ1 = lat1 * Math.PI / 180;
        const φ2 = lat2 * Math.PI / 180;
        const Δφ = (lat2 - lat1) * Math.PI / 180;
        const Δλ = (lon2 - lon1) * Math.PI / 180;

        const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                  Math.cos(φ1) * Math.cos(φ2) *
                  Math.sin(Δλ/2) * Math.sin(Δλ/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

        return R * c;
    }

    function getSimulatedLocation() {
        const savedLat = localStorage.getItem('srh_simulated_lat');
        const savedLng = localStorage.getItem('srh_simulated_lng');
        if (savedLat && savedLng) {
            return { lat: parseFloat(savedLat), lng: parseFloat(savedLng), isSimulated: true };
        }
        return { lat: window.SRH_TERMINAL_LAT, lng: window.SRH_TERMINAL_LNG, isSimulated: false };
    }

    function updateSimDisplay(lat, lng) {
        currentSimLat = lat;
        currentSimLng = lng;
        
        const coordsEl = document.getElementById('sim-coords-text');
        const distBadge = document.getElementById('sim-dist-badge');
        
        if (coordsEl) coordsEl.innerText = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;

        const dist = calculateDistance(window.SRH_TERMINAL_LAT, window.SRH_TERMINAL_LNG, lat, lng);
        if (distBadge) {
            if (dist <= window.SRH_TERMINAL_RADIUS) {
                distBadge.className = 'inline-block px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300';
                distBadge.innerText = `${Math.round(dist)}m (INSIDE 35m GEOFENCE)`;
            } else {
                distBadge.className = 'inline-block px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-red-100 text-red-800 border border-red-300';
                distBadge.innerText = `${Math.round(dist)}m (OUTSIDE 35m GEOFENCE)`;
            }
        }
    }

    function toggleSimulatorModal(show) {
        const modal = document.getElementById('srh-simulator-modal');
        if (!modal) return;

        if (show) {
            modal.classList.remove('hidden');
            initSimMap();
        } else {
            modal.classList.add('hidden');
        }
    }

    function initSimMap() {
        const loc = getSimulatedLocation();
        currentSimLat = loc.lat;
        currentSimLng = loc.lng;

        if (typeof maplibregl === 'undefined' || typeof window.srhCreateMap !== 'function') {
            if (window.srhEnsureMaplibre && typeof window.srhEnsureMaplibre === 'function') {
                window.srhEnsureMaplibre(initSimMap);
            } else {
                setTimeout(initSimMap, 300);
            }
            return;
        }

        const el = document.getElementById('srh-simulator-map-canvas');
        if (!el) return;

        if (simMap) {
            if (simMap.getContainer() !== el) {
                try { simMap.remove(); } catch(e) {}
                simMap = null;
                simMarker = null;
                return initSimMap();
            }
            simMap.resize();
            if (simMarker) simMarker.setLngLat([currentSimLng, currentSimLat]);
            updateSimDisplay(currentSimLat, currentSimLng);
            return;
        }

        simMap = window.srhCreateMap(el, {
            lat: window.SRH_TERMINAL_LAT,
            lng: window.SRH_TERMINAL_LNG,
            zoom: 17,
            touchZoomRotate: true,
            doubleClickZoom: true
        });
        if (!simMap) return;

        // Draw 35m Terminal Geofence (Green circle)
        window.srhAddGeofenceCircle(simMap, 'sim-geofence-35m', window.SRH_TERMINAL_LNG, window.SRH_TERMINAL_LAT, window.SRH_TERMINAL_RADIUS, {
            color: '#10b981',
            strokeColor: '#10b981',
            fillOpacity: 0.25,
            weight: 3
        });

        // Draw Terminal Pin
        new maplibregl.Marker().setLngLat([window.SRH_TERMINAL_LNG, window.SRH_TERMINAL_LAT]).addTo(simMap)
            .setPopup(new maplibregl.Popup({ offset: 25 }).setHTML('<b>TODA Main Terminal</b>'));

        // Draw Simulated Driver Pin
        const simMarkerEl = document.createElement('div');
        simMarkerEl.style.width = '28px';
        simMarkerEl.style.height = '28px';
        simMarkerEl.style.background = 'linear-gradient(135deg,#2563eb,#1d4ed8)';
        simMarkerEl.style.border = '3px solid #ffffff';
        simMarkerEl.style.borderRadius = '50%';
        simMarkerEl.style.boxShadow = '0 6px 16px rgba(37,99,235,0.5)';
        simMarkerEl.style.display = 'flex';
        simMarkerEl.style.alignItems = 'center';
        simMarkerEl.style.justifyContent = 'center';
        simMarkerEl.innerHTML = `<svg style="width:14px;height:14px;fill:#fff;display:block;" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>`;

        simMarker = new maplibregl.Marker({ element: simMarkerEl, draggable: true, pitchAlignment: 'map', rotationAlignment: 'map' })
            .setLngLat([currentSimLng, currentSimLat])
            .addTo(simMap);
        simMarker.setPopup(new maplibregl.Popup({ offset: 25 }).setHTML('<b>Simulated Driver Position</b>'));

        simMarker.on('drag', function() {
            const pos = simMarker.getLngLat();
            updateSimDisplay(pos.lat, pos.lng);
        });

        simMap.on('click', function(e) {
            currentSimLat = e.lngLat.lat;
            currentSimLng = e.lngLat.lng;
            simMarker.setLngLat([currentSimLng, currentSimLat]);
            updateSimDisplay(currentSimLat, currentSimLng);
        });

        updateSimDisplay(currentSimLat, currentSimLng);

        const hasSim = localStorage.getItem('srh_simulated_lat');
        highlightPresetButton(hasSim ? 'terminal' : 'real');

        setTimeout(() => { try { simMap.resize(); } catch(e){} }, 100);
    }

    function highlightPresetButton(type) {
        const btnTerminal = document.getElementById('sim-preset-terminal');
        const btnReal = document.getElementById('sim-preset-real');

        if (btnTerminal) {
            btnTerminal.className = (type === 'terminal') 
                ? 'sim-preset-btn py-3 px-3 rounded-xl border-2 border-emerald-500 bg-emerald-600 text-white text-xs font-black transition-all flex flex-col items-center justify-center gap-1 shadow-lg scale-[1.02] cursor-pointer'
                : 'sim-preset-btn py-3 px-3 rounded-xl border-2 border-emerald-400/50 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-black transition-all flex flex-col items-center justify-center gap-1 shadow-sm active:scale-95 cursor-pointer';
        }

        if (btnReal) {
            btnReal.className = (type === 'real' || type === 'outside')
                ? 'sim-preset-btn py-3 px-3 rounded-xl border-2 border-blue-500 bg-blue-600 text-white text-xs font-black transition-all flex flex-col items-center justify-center gap-1 shadow-lg scale-[1.02] cursor-pointer'
                : 'sim-preset-btn py-3 px-3 rounded-xl border-2 border-blue-400/50 bg-blue-50 hover:bg-blue-100 text-blue-800 text-xs font-black transition-all flex flex-col items-center justify-center gap-1 shadow-sm active:scale-95 cursor-pointer';
        }
    }

    function applySimulatorPreset(type) {
        highlightPresetButton(type);

        if (type === 'terminal') {
            currentSimLat = window.SRH_TERMINAL_LAT;
            currentSimLng = window.SRH_TERMINAL_LNG;
            if (simMarker && simMap) {
                simMarker.setLngLat([currentSimLng, currentSimLat]);
                simMap.easeTo({ center: [currentSimLng, currentSimLat], zoom: 17, duration: 500 });
            }
            updateSimDisplay(currentSimLat, currentSimLng);
            saveSimulatorLocation();
        } else if (type === 'outside' || type === 'real') {
            resetSimulatorLocation();
        }
    }

    function saveSimulatorLocation() {
        localStorage.setItem('srh_simulated_lat', currentSimLat.toString());
        localStorage.setItem('srh_simulated_lng', currentSimLng.toString());
        
        window.dispatchEvent(new CustomEvent('srh-location-updated', {
            detail: { lat: currentSimLat, lng: currentSimLng, isSimulated: true }
        }));

        if (window.showDynamicStatus) {
            const dist = calculateDistance(window.SRH_TERMINAL_LAT, window.SRH_TERMINAL_LNG, currentSimLat, currentSimLng);
            window.showDynamicStatus(`📍 GPS Overridden to TODA Terminal (${Math.round(dist)}m)`);
        }

        toggleSimulatorModal(false);
    }

    function resetSimulatorLocation() {
        localStorage.removeItem('srh_simulated_lat');
        localStorage.removeItem('srh_simulated_lng');
        
        window.dispatchEvent(new CustomEvent('srh-use-real-gps', {}));
        window.dispatchEvent(new CustomEvent('srh-location-updated', {
            detail: { isSimulated: false }
        }));

        if (window.showDynamicStatus) {
            window.showDynamicStatus("📡 Switched to Real Hardware Live GPS (60fps)");
        }

        toggleSimulatorModal(false);
    }

    (function initDraggableDock() {
        const dock = document.getElementById('srh-simulator-dock');
        if (!dock) return;

        dock.style.touchAction = 'none';
        dock.style.contain = 'layout paint';

        let isDragging = false;
        let startX = 0, startY = 0;
        let initialLeft = 0, initialTop = 0;
        let deltaX = 0, deltaY = 0;
        let hasDragged = false;
        let rafId = null;
        let activePointerId = null;

        const savedPos = localStorage.getItem('srh_sim_dock_pos');
        if (savedPos) {
            try {
                const { left, top } = JSON.parse(savedPos);
                dock.style.left = left + 'px';
                dock.style.top = top + 'px';
                dock.style.right = 'auto';
                dock.style.bottom = 'auto';
            } catch(e) {}
        }

        function clampDockToScreen() {
            if (!dock.style.left || dock.style.left === 'auto') return;
            let currentLeft = parseFloat(dock.style.left);
            let currentTop = parseFloat(dock.style.top);

            const maxLeft = window.innerWidth - dock.offsetWidth - 8;
            const maxTop = window.innerHeight - dock.offsetHeight - 8;

            currentLeft = Math.max(8, Math.min(currentLeft, maxLeft));
            currentTop = Math.max(8, Math.min(currentTop, maxTop));

            dock.style.left = currentLeft + 'px';
            dock.style.top = currentTop + 'px';
        }

        window.addEventListener('resize', clampDockToScreen);
        setTimeout(clampDockToScreen, 500);

        function updateDockRender() {
            if (!isDragging) return;
            // Hardware GPU translate3d rendering during active drag
            dock.style.transform = `translate3d(${deltaX}px, ${deltaY}px, 0)`;
            rafId = null;
        }

        function onStart(e) {
            if (e.type === 'mousedown' && e.button !== 0) return;
            
            isDragging = true;
            hasDragged = false;

            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;

            const rect = dock.getBoundingClientRect();
            startX = clientX;
            startY = clientY;
            initialLeft = rect.left;
            initialTop = rect.top;
            deltaX = 0;
            deltaY = 0;

            if (e.pointerId !== undefined && dock.setPointerCapture) {
                try {
                    dock.setPointerCapture(e.pointerId);
                    activePointerId = e.pointerId;
                } catch(err) {}
            }

            dock.classList.add('is-dragging');
            dock.style.willChange = 'transform';
            dock.style.transition = 'none';

            window.addEventListener('pointermove', onMove, { passive: false });
            window.addEventListener('pointerup', onEnd);
            window.addEventListener('pointercancel', onEnd);
            window.addEventListener('mousemove', onMove);
            window.addEventListener('mouseup', onEnd);
            window.addEventListener('touchmove', onMove, { passive: false });
            window.addEventListener('touchend', onEnd);
        }

        function onMove(e) {
            if (!isDragging) return;

            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;

            deltaX = clientX - startX;
            deltaY = clientY - startY;

            if (Math.abs(deltaX) > 4 || Math.abs(deltaY) > 4) {
                hasDragged = true;
                if (e.cancelable) e.preventDefault();
            }

            if (!rafId) {
                rafId = requestAnimationFrame(updateDockRender);
            }
        }

        function onEnd() {
            if (!isDragging) return;
            isDragging = false;
            if (rafId) { cancelAnimationFrame(rafId); rafId = null; }

            if (activePointerId !== null && dock.releasePointerCapture) {
                try {
                    dock.releasePointerCapture(activePointerId);
                    activePointerId = null;
                } catch(err) {}
            }

            window.removeEventListener('pointermove', onMove);
            window.removeEventListener('pointerup', onEnd);
            window.removeEventListener('pointercancel', onEnd);
            window.removeEventListener('mousemove', onMove);
            window.removeEventListener('mouseup', onEnd);
            window.removeEventListener('touchmove', onMove);
            window.removeEventListener('touchend', onEnd);

            dock.classList.remove('is-dragging');
            dock.style.willChange = 'auto';
            dock.style.transform = 'translate3d(0,0,0)';

            if (hasDragged) {
                let newLeft = initialLeft + deltaX;
                let newTop = initialTop + deltaY;

                const maxLeft = window.innerWidth - dock.offsetWidth - 8;
                const maxTop = window.innerHeight - dock.offsetHeight - 8;

                newLeft = Math.max(8, Math.min(newLeft, maxLeft));
                newTop = Math.max(8, Math.min(newTop, maxTop));

                dock.style.left = newLeft + 'px';
                dock.style.top = newTop + 'px';
                dock.style.right = 'auto';
                dock.style.bottom = 'auto';

                localStorage.setItem('srh_sim_dock_pos', JSON.stringify({ left: newLeft, top: newTop }));
            }
        }

        const btn = dock.querySelector('button');
        if (btn) {
            btn.addEventListener('click', function(e) {
                if (hasDragged) {
                    e.stopImmediatePropagation();
                    e.preventDefault();
                    hasDragged = false;
                }
            }, true);
        }

        dock.addEventListener('pointerdown', onStart);
        dock.addEventListener('mousedown', onStart);
        dock.addEventListener('touchstart', onStart, { passive: true });
    })();
</script>
