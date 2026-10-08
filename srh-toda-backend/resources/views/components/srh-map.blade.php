@props([
    'mapId' => 'srh-maplibre-map',
    'height' => '320px',
    'targetInputId' => null,
    'interactive' => true,
    'showGeofence' => true,
    'statusBadgeId' => 'srh-geofence-status',
])

<div class="space-y-2 relative z-0">
    <!-- Inline MapLibre Core Positioning & Hardware Acceleration Styles -->
    <style>
        .maplibregl-map { width: 100% !important; height: 100% !important; z-index: 1 !important; }
        .maplibregl-canvas { outline: none; }
        .srh-terminal-svg-marker {
            display: flex; align-items: center; justify-content: center;
            background: #2563eb; border: 2.5px solid #ffffff; border-radius: 9999px;
            box-shadow: 0 4px 14px rgba(37,99,235,0.5); color: #ffffff;
        }
        .srh-tricycle-svg-marker {
            display: flex; align-items: center; justify-content: center;
            background: #0f172a; border: 2.5px solid #38bdf8; border-radius: 9999px;
            box-shadow: 0 4px 16px rgba(15,23,42,0.6); color: #38bdf8;
        }
        .srh-user-svg-marker {
            display: flex; align-items: center; justify-content: center;
            background: #10b981; border: 2.5px solid #ffffff; border-radius: 9999px;
            box-shadow: 0 4px 14px rgba(16,185,129,0.5); color: #ffffff;
        }
    </style>

    <!-- Map Container -->
    <div id="{{ $mapId }}" class="w-full rounded-2xl border-2 border-slate-200 shadow-lg overflow-hidden relative" style="height: {{ $height }}; min-height: 240px; background-color: #e2e8f0;">
        <!-- Locate Me Action Button Floating Overlay -->
        <button type="button" 
                onclick="window.srhMapInstances && window.srhMapInstances['{{ $mapId }}'] ? window.srhMapInstances['{{ $mapId }}'].locateUser() : null"
                class="absolute top-3 left-3 z-[999] bg-white hover:bg-blue-50 text-blue-700 font-extrabold text-xs px-3.5 py-2 rounded-xl border border-blue-200 shadow-md flex items-center gap-1.5 active:scale-95 transition-all cursor-pointer">
            <svg class="w-4 h-4 text-blue-600 leading-none select-none" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3c-.46-4.17-3.77-7.48-7.94-7.94V1h-2v2.06C6.83 3.52 3.52 6.83 3.06 11H1v2h2.06c.46 4.17 3.77 7.48 7.94 7.94V23h2v-2.06c4.17-.46 7.48-3.77 7.94-7.94H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/></svg>
            <span>Pinpoint GPS</span>
        </button>
    </div>

    <!-- Geofence Live Status Banner -->
    <div id="{{ $statusBadgeId }}" class="p-3 rounded-xl bg-slate-100 border border-slate-200 text-xs font-extrabold text-slate-700 flex items-center justify-between shadow-sm transition-all">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse shrink-0"></span>
            <span id="{{ $statusBadgeId }}-text">Santa Rosa Homes TODA Coverage Area</span>
        </div>
        <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider shrink-0">Santa Rosa Homes</span>
    </div>
</div>

<script>
    (function() {
        window.srhMapInstances = window.srhMapInstances || {};

        const SRH_CENTER_LAT = 15.429550175641715;
        const SRH_CENTER_LNG = 120.92240292427664;
        const GEOFENCE_RADIUS_METERS = 1200;

        function calculateDistanceMeters(lat1, lon1, lat2, lon2) {
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

        function updateGeofenceStatus(lat, lng) {
            const statusEl = document.getElementById('{{ $statusBadgeId }}');
            const textEl = document.getElementById('{{ $statusBadgeId }}-text');
            if (!statusEl || !textEl) return;

            const distance = calculateDistanceMeters(SRH_CENTER_LAT, SRH_CENTER_LNG, lat, lng);

            if (distance <= GEOFENCE_RADIUS_METERS) {
                statusEl.className = 'p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-extrabold text-emerald-800 flex items-center justify-between shadow-sm transition-all';
                textEl.innerHTML = '🟢 Within SRH TODA Service Area (' + Math.round(distance) + 'm from Terminal)';
            } else {
                statusEl.className = 'p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs font-extrabold text-amber-900 flex items-center justify-between shadow-sm transition-all';
                textEl.innerHTML = '🟡 Outside Primary TODA Boundary (' + (distance / 1000).toFixed(1) + ' km away - Out-of-bounds rate apply)';
            }
        }

        function buildMap() {
            const mapContainer = document.getElementById('{{ $mapId }}');
            if (!mapContainer) return;
            if (typeof maplibregl === 'undefined') {
                if (window.srhEnsureMaplibre && typeof window.srhEnsureMaplibre === 'function') {
                    window.srhEnsureMaplibre(buildMap);
                } else {
                    setTimeout(buildMap, 300);
                }
                return;
            }

            if (mapContainer._srhMaplibre && window.srhMapInstances['{{ $mapId }}']) {
                try { window.srhMapInstances['{{ $mapId }}'].map.resize(); } catch(e) {}
                return;
            }

            try {
                const map = window.srhCreateMap(mapContainer, {
                    lat: SRH_CENTER_LAT,
                    lng: SRH_CENTER_LNG,
                    zoom: 15,
                    touchZoomRotate: true,
                    doubleClickZoom: true
                });
                if (!map) return;
                mapContainer._srhMaplibre = true;

                @if($showGeofence)
                window.srhAddGeofenceCircle(map, 'srh-geofence-{{ $mapId }}', SRH_CENTER_LNG, SRH_CENTER_LAT, GEOFENCE_RADIUS_METERS, {
                    color: '#3b82f6',
                    strokeColor: '#2563eb',
                    fillOpacity: 0.15,
                    weight: 2
                });
                @endif

                // Fast SVG TODA Terminal Icon
                const terminalEl = document.createElement('div');
                terminalEl.className = 'srh-terminal-svg-marker';
                terminalEl.style.width = '36px';
                terminalEl.style.height = '36px';
                terminalEl.innerHTML = `<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0a2 2 0 002-2V7a2 2 0 00-2-2H9a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>`;

                const terminalMarker = new maplibregl.Marker({ element: terminalEl, pitchAlignment: 'map', rotationAlignment: 'map' })
                    .setLngLat([SRH_CENTER_LNG, SRH_CENTER_LAT])
                    .addTo(map);
                terminalMarker.setPopup(new maplibregl.Popup({ offset: 25 }).setHTML('<b>SRH TODA Main Terminal</b><br>Santa Rosa Homes, Nueva Ecija'));

                // Fast SVG User GPS Marker
                let userMarker = null;

                function setUserLocation(lat, lng, popupText = 'Selected Location') {
                    if (userMarker) {
                        userMarker.setLngLat([lng, lat]);
                    } else {
                        const userEl = document.createElement('div');
                        userEl.className = 'srh-user-svg-marker';
                        userEl.style.width = '34px';
                        userEl.style.height = '34px';
                        userEl.innerHTML = `<svg class="w-5 h-5 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`;
                        userMarker = new maplibregl.Marker({ element: userEl, draggable: {{ $interactive ? 'true' : 'false' }}, pitchAlignment: 'map', rotationAlignment: 'map' })
                            .setLngLat([lng, lat])
                            .addTo(map);
                        if ({{ $interactive ? 'true' : 'false' }}) {
                            userMarker.on('dragend', function() {
                                const pos = userMarker.getLngLat();
                                updateGeofenceStatus(pos.lat, pos.lng);
                                updateTargetInput(pos.lat, pos.lng);
                            });
                        }
                    }
                    const popup = new maplibregl.Popup({ offset: 25 }).setHTML('<b>' + popupText + '</b><br>Lat: ' + lat.toFixed(5) + ', Lng: ' + lng.toFixed(5));
                    userMarker.setPopup(popup);
                    if (!popup.isOpen()) popup.open(map);
                    map.flyTo({ center: [lng, lat], zoom: 16, duration: 600 });
                    updateGeofenceStatus(lat, lng);
                    updateTargetInput(lat, lng);
                }

                function updateTargetInput(lat, lng) {
                    const targetInputId = '{{ $targetInputId }}';
                    if (!targetInputId) return;
                    const input = document.getElementById(targetInputId);
                    if (input) {
                        input.value = `Santa Rosa Homes (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
                    }
                }

                @if($interactive)
                map.on('click', function(e) {
                    setUserLocation(e.lngLat.lat, e.lngLat.lng, 'Pinned Location');
                });
                @endif

                window.srhMapInstances['{{ $mapId }}'] = {
                    map: map,
                    locateUser: function() {
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(
                                (position) => {
                                    setUserLocation(position.coords.latitude, position.coords.longitude, 'Your GPS Location');
                                },
                                (error) => {
                                    alert('Could not retrieve your GPS location. Please ensure Location services are enabled.');
                                },
                                { enableHighAccuracy: true, timeout: 10000 }
                            );
                        } else {
                            alert('Geolocation is not supported by your browser.');
                        }
                    },
                    setUserLocation: setUserLocation
                };

                updateGeofenceStatus(SRH_CENTER_LAT, SRH_CENTER_LNG);

                [100, 300, 600, 1000].forEach(delay => {
                    setTimeout(() => {
                        try { map.resize(); } catch(e) {}
                    }, delay);
                });

                window.addEventListener('spa:page-loaded', () => setTimeout(() => { try { map.resize(); } catch(e) {} }, 30));

                if (window.ResizeObserver) {
                    new ResizeObserver(() => {
                        try { map.resize(); } catch(e) {}
                    }).observe(mapContainer);
                }

            } catch(e) {
                console.error('MapLibre map error:', e);
            }
        }

        if (typeof maplibregl !== 'undefined') {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', buildMap);
            } else {
                buildMap();
            }
        } else {
            buildMap();
        }
    })();
</script>
