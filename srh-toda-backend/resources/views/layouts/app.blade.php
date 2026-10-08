<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="vapid-public-key" content="{{ config('services.vapid.public_key') }}">

        <!-- Web App & Mobile Meta Tags (Android & iOS) -->
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="{{ \App\Support\SystemSettings::brandName() }}">
        <meta name="theme-color" content="#2563eb">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon, Touch Icon & Web App Manifest -->
        <link rel="icon" type="image/png" href="{{ srh_logo_url() }}">
        <link rel="apple-touch-icon" href="{{ srh_logo_url() }}">
        <link rel="manifest" href="/manifest.json?v=20">

        <!-- DNS Pre-connect for Fonts, Map Tiles & CDNs (Faster initial icon & tile load) -->
        @if(Auth::check() && Auth::user()->profile_photo_url)
            <link rel="preload" as="image" fetchpriority="high" href="{{ route('user.avatar', [Auth::user(), 'v' => optional(Auth::user()->updated_at)->timestamp]) }}">
        @endif
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="preconnect" href="https://cdn.jsdelivr.net">
        <link rel="preconnect" href="https://unpkg.com" crossorigin>
        <link rel="dns-prefetch" href="https://unpkg.com">
        <link rel="preconnect" href="https://api.maptiler.com" crossorigin>
        <link rel="dns-prefetch" href="https://api.maptiler.com">
        <link rel="preconnect" href="https://tiles.maptiler.com" crossorigin>
        <link rel="dns-prefetch" href="https://tiles.maptiler.com">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <!-- Figtree (App UI font) - SELF-HOSTED locally to guarantee the correct
             text weight on every initial load (no CDN latency, no blocked
             connection falling back to a heavier system font). -->
        <style>
            @font-face {
                font-family: 'Figtree';
                font-style: normal;
                font-weight: 400;
                font-display: swap;
                src: url(/fonts/figtree-latin-400-normal.woff2) format('woff2');
                unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            }
            @font-face {
                font-family: 'Figtree';
                font-style: normal;
                font-weight: 500;
                font-display: swap;
                src: url(/fonts/figtree-latin-500-normal.woff2) format('woff2');
                unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            }
            @font-face {
                font-family: 'Figtree';
                font-style: normal;
                font-weight: 600;
                font-display: swap;
                src: url(/fonts/figtree-latin-600-normal.woff2) format('woff2');
                unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            }
        </style>
        <link rel="preload" as="font" type="font/woff2" crossorigin href="/fonts/figtree-latin-400-normal.woff2">
        <link rel="preload" as="font" type="font/woff2" crossorigin href="/fonts/figtree-latin-500-normal.woff2">
        <link rel="preload" as="font" type="font/woff2" crossorigin href="/fonts/figtree-latin-600-normal.woff2">



        <!-- Remix Icon & Lucide Icons (System & UI Navigation - 100% Local Bundle) -->
        <link rel="preload" as="font" type="font/woff2" crossorigin href="{{ asset('vendor/remixicon/remixicon.woff2') }}">
        <link href="{{ asset('vendor/remixicon/remixicon.css') }}" rel="stylesheet" />
        <script src="{{ asset('vendor/lucide/lucide.min.js') }}"></script>

        <!-- Global SortableJS for Drag & Drop Queue Reordering (Local Bundle) -->
        <script src="{{ asset('vendor/sortable/Sortable.min.js') }}"></script>

        <!-- Laravel Reverb & WebSockets (100% Local Bundle) -->
        <script src="{{ asset('vendor/echo/pusher.min.js') }}"></script>
        <script src="{{ asset('vendor/echo/echo.iife.js') }}"></script>
        <script>
            (function() {
                var host = "{{ env('VITE_REVERB_HOST') ?: (request()->getHost() ?: '127.0.0.1') }}";
                var isHttps = window.location.protocol === 'https:' || "{{ env('VITE_REVERB_SCHEME') }}" === 'https';
                var wsPort = parseInt("{{ env('VITE_REVERB_PORT', 8080) }}") || 8080;
                var wssPort = isHttps ? (window.location.port ? parseInt(window.location.port) : 443) : wsPort;

                if (typeof Pusher !== 'undefined' && typeof Echo !== 'undefined') {
                    window.Pusher = Pusher;
                    window.Echo = new Echo({
                        broadcaster: 'reverb',
                        key: "{{ config('broadcasting.connections.reverb.key') ?? env('REVERB_APP_KEY', 'srhlinktodakey') }}",
                        wsHost: host,
                        wsPort: wsPort,
                        wssPort: wssPort,
                        forceTLS: isHttps,
                        enabledTransports: ['ws', 'wss'],
                        disableStats: true,
                        activityTimeout: 30000,
                        pongTimeout: 10000,
                        unavailableTimeout: 10000,
                    });

                    if (window.Echo.connector && window.Echo.connector.pusher) {
                        window.Echo.connector.pusher.connection.bind('error', function(err) {
                            console.warn('[Reverb WebSocket Warning]', err);
                        });
                    }

                    try {
                        window.dispatchEvent(new CustomEvent('echo:ready', { detail: window.Echo }));
                    } catch (e) {}
                }

                window.srhOnEchoReady = function(cb) {
                    if (window.Echo) {
                        cb(window.Echo);
                        if (window.Echo.connector && window.Echo.connector.pusher) {
                            window.Echo.connector.pusher.connection.bind('connected', function() {
                                cb(window.Echo);
                            });
                        }
                    } else {
                        var done = false;
                        var run = function() {
                            if (done || !window.Echo) return;
                            done = true;
                            cb(window.Echo);
                            if (window.Echo.connector && window.Echo.connector.pusher) {
                                window.Echo.connector.pusher.connection.bind('connected', function() {
                                    cb(window.Echo);
                                });
                            }
                        };
                        window.addEventListener('echo:ready', run, { once: true });
                        var count = 0;
                        var t = setInterval(function() {
                            count++;
                            if (window.Echo) {
                                clearInterval(t);
                                run();
                            } else if (count > 50) {
                                clearInterval(t);
                            }
                        }, 100);
                    }
                };
            })();
        </script>

        <!-- MapLibre GL JS + MapTiler Vector Maps (100% Local Bundle) -->
        <link rel="stylesheet" href="{{ asset('vendor/maplibre/maplibre-gl.css') }}" />
        <script src="{{ asset('vendor/maplibre/maplibre-gl.js') }}"></script>
        <script>
            window.SRH_MAPTILER_KEY = "{{ config('services.maptiler.key') }}";
            window.SRH_MAP_STYLE = "{{ url('srh-map-style.json') }}";
            window._srhMapStyleJson = null;

            // In-Memory Fast Cache: Pre-fetches style JSON once so returning to dashboard uses memory directly (0ms network)
            (function() {
                try {
                    var styleUrl = window.SRH_MAP_STYLE + '?v=8';
                    fetch(styleUrl, { cache: 'no-cache' })
                        .then(r => r.ok ? r.json() : null)
                        .then(json => {
                            if (json) window._srhMapStyleJson = json;
                        })
                        .catch(() => {});
                } catch(e) {}
            })();

            // Robust MapLibre loader: if the local <script> above hasn't finished yet, wait for it.
            (function() {
                var injectedFallback = false;
                window.srhEnsureMaplibre = function(cb, attempts) {
                    attempts = attempts || 0;
                    if (typeof maplibregl !== 'undefined') { cb(); return; }
                    if (!injectedFallback && attempts >= 12) {
                        injectedFallback = true;
                        var s = document.createElement('script');
                        s.src = '{{ asset('vendor/maplibre/maplibre-gl.js') }}';
                        s.onload = function() { cb(); };
                        s.onerror = function() { window.srhEnsureMaplibre(cb, attempts + 1); };
                        document.head.appendChild(s);
                        return;
                    }
                    setTimeout(function() { window.srhEnsureMaplibre(cb, attempts + 1); }, 1000);
                };
            })();

            // Early-friendly status toasts. Defined here (before page content scripts)
            // so the unguarded calls in the driver hub never throw. The toast renderer
            // is defined later, so dispatch through setTimeout.
            window.showDynamicStatus = function(msg, type) {
                setTimeout(function() {
                    if (window.createSlidingToast) window.createSlidingToast(msg, type || 'success');
                }, 0);
            };
            window.showDynamicError = function(msg) {
                setTimeout(function() {
                    if (window.createSlidingToast) window.createSlidingToast(msg, 'danger');
                }, 0);
            };
        </script>
        <script>
            window.srhMetersToPixels = function(map, lat, meters) {
                if (!map || !map.getZoom) return 1;
                const metersPerPixel = 156543.03392 * Math.cos(lat * Math.PI / 180) / Math.pow(2, map.getZoom());
                return Math.max(1, meters / metersPerPixel);
            };

            window.srhCreateMap = function(container, opts) {
                if (typeof maplibregl === 'undefined') return null;
                const o = opts || {};
                const map = new maplibregl.Map({
                    container: container,
                    style: window._srhMapStyleJson ? JSON.parse(JSON.stringify(window._srhMapStyleJson)) : window.SRH_MAP_STYLE,
                    center: [o.lng, o.lat],
                    zoom: o.zoom !== undefined ? o.zoom : 16,
                    minZoom: o.minZoom !== undefined ? o.minZoom : 12,
                    maxZoom: o.maxZoom !== undefined ? o.maxZoom : 19.5,
                    maxBounds: o.maxBounds !== undefined ? o.maxBounds : [[120.0, 13.3], [122.5, 15.6]],
                    maxPitch: 65,
                    maxTileCacheSize: 120,
                    collectResourceTiming: false,
                    pitch: o.pitch !== undefined ? o.pitch : 0,
                    bearing: o.bearing !== undefined ? o.bearing : 0,
                    attributionControl: false,
                    dragRotate: o.dragRotate !== undefined ? o.dragRotate : true,
                    touchZoomRotate: o.touchZoomRotate !== undefined ? o.touchZoomRotate : true,
                    pitchWithRotate: o.pitchWithRotate !== undefined ? o.pitchWithRotate : true,
                    touchPitch: o.touchPitch !== undefined ? o.touchPitch : true,
                    fadeDuration: 0
                });
                map.resize();
                // Native viewport padding: keeps the map center & markers clear of the
                // collapsible bottom sheet. Re-centering (flyTo/easeTo/jumpTo/fitBounds)
                // automatically respects this active visible viewport.
                map.setPadding({
                    top: 0,
                    bottom: (o.paddingBottom !== undefined ? o.paddingBottom : 60),
                    left: 0,
                    right: 0
                });
                if (o.doubleClickZoom === false) { try { map.doubleClickZoom.disable(); } catch(e){} }
                if (o.boxZoom === false) { try { map.boxZoom.disable(); } catch(e){} }

                // Native MapLibre controls (disabled by default in favor of custom floating glass buttons)
                const navPos = o.navPosition || 'top-right';
                if (o.navigationControl === true) {
                    map.addControl(new maplibregl.NavigationControl({ showCompass: true, showZoom: true, visualizePitch: true }), navPos);
                }
                if (o.geolocateControl === true) {
                    map.addControl(new maplibregl.GeolocateControl({ trackUserLocation: true, showUserLocation: false }), navPos);
                }

                map.on('error', function(err) {
                    if (err && err.error && err.error.message && (err.error.message.includes('font') || err.error.message.includes('Noto') || err.error.message.includes('8192'))) {
                        return;
                    }
                });

                // Transparent placeholder for any missing icons (with sdf: true to match SDF sprite buffer)
                map.on('styleimagemissing', function(e) {
                    if (!e || !e.id || map.hasImage(e.id)) return;
                    try {
                        map.addImage(e.id, { width: 1, height: 1, data: new Uint8Array([0, 0, 0, 0]) }, { sdf: true });
                    } catch(err) {}
                });

                // Blank-map recovery: if the style ever fails to load, retry it once
                // so the map isn't stuck empty until a manual refresh.
                (function() {
                    let styleLoadedOnce = false;
                    map.on('load', function() { styleLoadedOnce = true; });
                    map.on('error', function(e) {
                        if (styleLoadedOnce) return;
                        setTimeout(function() {
                            try { map.setStyle(window.SRH_MAP_STYLE); } catch(err) {}
                        }, 2500);
                    });
                })();

                return map;
            };

            // Live tricycle tracking state shared by the driver & passenger (live-tracking)
            // maps. Both pages publish the tricycle's latest [lng, lat] and heading here so
            // the single centerOnTricycle() helper can re-center on it from anywhere.
            window.srhTricycle = { lng: null, lat: null, heading: null, source: null };

            // Helper to query the exact visual compass heading of the tricycle marker
            window.getTricycleHeading = function() {
                if (window.srhTricycle && typeof window.srhTricycle.heading === 'number' && !isNaN(window.srhTricycle.heading)) {
                    return ((window.srhTricycle.heading % 360) + 360) % 360;
                }
                const markerImg = document.querySelector('#driver-tricycle-marker-img') || document.querySelector('.driver-arrow-svg');
                if (markerImg && markerImg.style && markerImg.style.transform) {
                    const match = markerImg.style.transform.match(/rotate\(([-+]?\d*\.?\d+)deg\)/);
                    if (match && match[1]) {
                        const parsed = parseFloat(match[1]);
                        if (!isNaN(parsed)) {
                            return ((parsed % 360) + 360) % 360;
                        }
                    }
                }
                return 0;
            };

            // Google Maps-Style Single Unified Map Action Button State Machine
            // State 0: GPS Disabled / Denied / Unavailable (Gray button, Location Off icon, Tricycle marker hidden)
            // State 1: Unlocked / Manually Panned Away (Solid Google Maps Blue button, Recenter Crosshair icon)
            // State 2: Centered 2D Top View (Clean White/Glass button, Unfilled Neutral Compass Needle icon)
            // State 3: Centered 3D Live Compass Mode (Glowing Cyan button, Active 3D Navigation Compass needle icon)
            window.srhUnifiedMapState = 0; // Starts in State 0 (Gray disabled/locating) until active GPS lock is acquired
            window._userCustomMapBearing = null;

            window.setUnifiedMapButtonState = function(state) {
                window.srhUnifiedMapState = state;
                window.updateUnifiedMapButtonUI();
            };

            window.resetSrhCompassMode = function(map) {
                window.srhCompassMode = 0;
                window._userCustomMapBearing = null;
                window._isCompassEaseTransitioning = false;
                window.setUnifiedMapButtonState(2);
                const targetMap = map || window.homeMap || window.bookingMapInstance || window.paxMap || window.map;
                if (targetMap && typeof targetMap.easeTo === 'function') {
                    try {
                        targetMap.easeTo({
                            pitch: 0,
                            bearing: 0,
                            zoom: 16.3,
                            duration: 350
                        });
                    } catch(e) {}
                }
            };

            window.updateUnifiedMapButtonUI = function() {
                const state = window.srhUnifiedMapState !== undefined ? window.srhUnifiedMapState : 2;
                const btns = document.querySelectorAll('#srh-unified-map-btn, #srh-recenter-location-btn, #srh-compass-mode-btn, #pax-recenter-location-btn, #pax-compass-mode-btn, #pax-active-compass-btn, #pax-wait-recenter-btn');

                // Tricycle marker visibility: hidden if GPS is disabled (State 0)
                const marker = window.driverMarker;
                if (marker && marker.getElement) {
                    try {
                        marker.getElement().style.display = (state === 0) ? 'none' : '';
                    } catch(e) {}
                }

                btns.forEach(btn => {
                    if (state === 0) {
                        btn.className = 'w-11 h-11 rounded-2xl bg-slate-100 text-slate-400 shadow-sm border border-slate-200 flex items-center justify-center transition-all transform active:scale-90 cursor-pointer hover:bg-slate-200';
                        btn.innerHTML = `<svg class="w-6 h-6 text-slate-400 leading-none select-none" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>`;
                        btn.setAttribute('title', 'GPS Location Disabled (Tap to Enable Permissions)');
                    } else if (state === 1) {
                        const spinStyle = window._srhGpsFetching ? 'animation: spin 0.8s linear infinite; transform-origin: 50% 50%;' : '';
                        btn.className = 'w-11 h-11 rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-500/40 border-2 border-blue-400 flex items-center justify-center transition-all transform active:scale-90 cursor-pointer hover:bg-blue-700';
                        btn.innerHTML = `<svg class="w-6 h-6 text-white leading-none select-none" style="${spinStyle}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3c-.46-4.17-3.77-7.48-7.94-7.94V1h-2v2.06C6.83 3.52 3.52 6.83 3.06 11H1v2h2.06c.46 4.17 3.77 7.48 7.94 7.94V23h2v-2.06c4.17-.46 7.48-3.77 7.94-7.94H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/></svg>`;
                        btn.setAttribute('title', window._srhGpsFetching ? 'Fetching Live Location...' : 'Re-center on Live Location');
                    } else if (state === 2) {
                        btn.className = 'w-11 h-11 rounded-2xl bg-white/95 text-slate-700 shadow-md border border-slate-200 flex items-center justify-center transition-all transform active:scale-90 cursor-pointer hover:bg-slate-50';
                        btn.innerHTML = `<svg class="w-6 h-6 text-slate-700 leading-none select-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><polygon points="12,6 15,15 12,13 9,15" fill="currentColor" stroke="none"/></svg>`;
                        btn.setAttribute('title', '2D Centered View (Tap for 3D Live Compass Tracking)');
                    } else if (state === 3) {
                        btn.className = 'w-11 h-11 rounded-2xl bg-cyan-400 text-slate-950 shadow-lg shadow-cyan-400/50 border-2 border-cyan-200 flex items-center justify-center transition-all transform active:scale-90 cursor-pointer animate-pulse';
                        btn.innerHTML = `<svg class="w-6 h-6 text-slate-950 leading-none select-none" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm2.19 12.19L6 18l3.81-8.19L18 6l-3.81 8.19z"/></svg>`;
                        btn.setAttribute('title', '3D Live Compass Tracking Active (Tap for 2D Top View)');
                    }
                });
            };

            window.updateSrhCompassButtonUI = window.updateUnifiedMapButtonUI;

            window.handleUnifiedMapButtonClick = function(map, btnEl) {
                const now = Date.now();
                if (window._lastUnifiedMapTapTime && (now - window._lastUnifiedMapTapTime < 280)) {
                    return;
                }
                window._lastUnifiedMapTapTime = now;

                const currentState = window.srhUnifiedMapState !== undefined ? window.srhUnifiedMapState : 2;
                const liveMap = map || window.homeMap || window._grabHomeMapInstance || window.bookingMapInstance || window.paxMap;

                if (currentState === 0) {
                    if (navigator.geolocation) {
                        window._srhGpsFetching = true;
                        window.updateUnifiedMapButtonUI();
                        navigator.geolocation.getCurrentPosition(
                            pos => {
                                window._srhGpsFetching = false;
                                window.setUnifiedMapButtonState(2);
                                window.recenterOnRealLocation(liveMap, btnEl);
                            },
                            err => {
                                window._srhGpsFetching = false;
                                window.setUnifiedMapButtonState(0);
                                if (window.createSlidingToast) window.createSlidingToast("GPS access required. Please allow location permissions in your browser.", "warning");
                            },
                            { enableHighAccuracy: true, timeout: 8000 }
                        );
                    }
                } else if (currentState === 1) {
                    window.recenterOnRealLocation(liveMap, btnEl);
                } else if (currentState === 2) {
                    window.toggleSrhCompassMode(liveMap);
                    window.setUnifiedMapButtonState(3);
                } else if (currentState === 3) {
                    window.toggleSrhCompassMode(liveMap);
                    window.setUnifiedMapButtonState(2);
                }
            };

            window.toggleSrhCompassMode = function(map) {
                if (!map || typeof map.easeTo !== 'function') {
                    map = window.bookingMapInstance || window.bookingMap || window.homeMap || window.activeTripMap || window.paxMap || window._paxMap || window.map;
                }
                window.srhCompassMode = (window.srhCompassMode === 1) ? 0 : 1;
                window._userCustomMapBearing = null;
                
                const targetState = (window.srhCompassMode === 1) ? 3 : 2;
                window.setUnifiedMapButtonState(targetState);

                // Re-engage live centered follow-lock
                window._driverMapManualPan = false;
                window._paxMapManualPan = false;

                const currentHeading = typeof window.getTricycleHeading === 'function' ? window.getTricycleHeading() : (window.srhTricycle?.heading || 0);
                const targetPitch = (window.srhCompassMode === 1) ? 60 : 0;
                const rawTargetBearing = (window.srhCompassMode === 1) ? ((currentHeading % 360) + 360) % 360 : 0;
                const targetZoom = (window.srhCompassMode === 1) ? 17.3 : 16.3;

                const curMapBearing = map.getBearing ? map.getBearing() : 0;
                let diff = ((rawTargetBearing - curMapBearing) % 360);
                if (diff > 180) diff -= 360;
                if (diff < -180) diff += 360;
                const targetBearing = curMapBearing + diff;

                const currentCenter = map.getCenter ? map.getCenter() : null;
                const targetLng = (window.srhTricycle?.lng) || (currentCenter ? currentCenter.lng : 121.11105);
                const targetLat = (window.srhTricycle?.lat) || (currentCenter ? currentCenter.lat : 14.30105);

                window._isMapCameraAnimating = true;
                map.easeTo({
                    center: [targetLng, targetLat],
                    bearing: targetBearing,
                    pitch: targetPitch,
                    zoom: targetZoom,
                    duration: 450,
                    easing: function(t) { return t * (2 - t); },
                    essential: true
                });
                setTimeout(() => { window._isMapCameraAnimating = false; }, 480);
            };

            window.syncCameraToLiveCompass = function(map, headingDeg) {
                if (window.srhCompassMode !== 1) return;
                if (!map || typeof headingDeg !== 'number' || isNaN(headingDeg)) return;

                const rawTargetBearing = ((headingDeg % 360) + 360) % 360;
                const curMapBearing = map.getBearing ? map.getBearing() : 0;
                let diff = ((rawTargetBearing - curMapBearing) % 360);
                if (diff > 180) diff -= 360;
                if (diff < -180) diff += 360;

                if (Math.abs(diff) < 0.25) return;

                const targetBearing = curMapBearing + diff;
                try {
                    map.jumpTo({
                        bearing: targetBearing,
                        pitch: 60
                    });
                } catch(e) {}
            };

            window._srhLastFetchedGps = null;
            window._srhGpsFetching = false;

            window.recenterOnRealLocation = function(map, btnEl) {
                const liveMap = map || window.homeMap || window._grabHomeMapInstance || (typeof window.paxActiveMapForButtons === 'function' ? window.paxActiveMapForButtons() : null) || window.bookingMapInstance;
                if (!liveMap) return;

                // Re-engage live centered follow-lock
                window._driverMapManualPan = false;
                window._paxMapManualPan = false;

                // Clear any simulated override so we get authentic live location
                localStorage.removeItem('srh_simulated_lat');
                localStorage.removeItem('srh_simulated_lng');
                window.dispatchEvent(new CustomEvent('srh-use-real-gps', {}));

                // Ensure button is in State 1 (Blue Target) and actively spinning
                window._srhGpsFetching = true;
                window.srhUnifiedMapState = 1;
                if (window.updateUnifiedMapButtonUI) window.updateUnifiedMapButtonUI();

                function smoothRecenterAndDone(lat, lng) {
                    const currentPitch = (liveMap.getPitch) ? liveMap.getPitch() : 0;
                    const curMapBearing = (liveMap.getBearing) ? liveMap.getBearing() : 0;
                    const is3D = (window.srhCompassMode === 1) || (currentPitch > 15);
                    const targetPitch = is3D ? 60 : currentPitch;
                    const targetZoom = is3D ? 17.3 : 16.3;

                    window._isMapCameraAnimating = true;
                    liveMap.easeTo({
                        center: [lng, lat],
                        zoom: targetZoom,
                        bearing: curMapBearing,
                        pitch: targetPitch,
                        duration: 450,
                        easing: function(t) { return t * (2 - t); },
                        essential: true
                    });

                    // Keep spinning until the camera has fully centered onto the location
                    setTimeout(() => {
                        window._isMapCameraAnimating = false;
                        window._srhGpsFetching = false;
                        window.setUnifiedMapButtonState(2);
                    }, 500);
                }

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(pos => {
                        const realLat = pos.coords.latitude;
                        const realLng = pos.coords.longitude;

                        window._srhLastFetchedGps = {
                            lat: realLat,
                            lng: realLng,
                            timestamp: Date.now()
                        };

                        localStorage.setItem('srh_last_real_lat', realLat.toString());
                        localStorage.setItem('srh_last_real_lng', realLng.toString());

                        if (window.srhTricycle) {
                            window.srhTricycle.lat = realLat;
                            window.srhTricycle.lng = realLng;
                        }

                        if (window.driverMarker) {
                            window.driverMarker.setLngLat([realLng, realLat]);
                            if (window.setDriverMarkerOpacity) window.setDriverMarkerOpacity(window.driverMarker, 1);
                        }

                        if (typeof handleDriverLocationChanged === 'function') {
                            handleDriverLocationChanged(realLat, realLng);
                        }

                        smoothRecenterAndDone(realLat, realLng);
                    }, (err) => {
                        const fallbackPos = window._srhLastFetchedGps || (window.srhTricycle?.lat && window.srhTricycle?.lng ? { lat: window.srhTricycle.lat, lng: window.srhTricycle.lng } : null);
                        if (fallbackPos && fallbackPos.lat && fallbackPos.lng) {
                            smoothRecenterAndDone(fallbackPos.lat, fallbackPos.lng);
                        } else {
                            window._srhGpsFetching = false;
                            window.setUnifiedMapButtonState(0);
                            if (window.createSlidingToast) window.createSlidingToast("Could not fetch live GPS position", "warning");
                        }
                    }, { enableHighAccuracy: true, maximumAge: 0, timeout: 10000 });
                } else {
                    const fallbackPos = window._srhLastFetchedGps || (window.srhTricycle?.lat && window.srhTricycle?.lng ? { lat: window.srhTricycle.lat, lng: window.srhTricycle.lng } : null);
                    if (fallbackPos && fallbackPos.lat && fallbackPos.lng) {
                        smoothRecenterAndDone(fallbackPos.lat, fallbackPos.lng);
                    } else {
                        window._srhGpsFetching = false;
                        window.setUnifiedMapButtonState(0);
                    }
                }
            };

            // Shared 3D "over-the-shoulder" camera helper used by the driver map and the
            // passenger live-tracking map. Centers the camera on the tricycle, pitches it
            // to 60 degrees and rotates the bearing to match the tricycle's driving
            // direction. Accepts an optional explicit { lng, lat, heading } via opts, or
            // falls back to the shared window.srhTricycle state / a MapLibre marker.
            window.centerOnTricycle = function(map, opts) {
                if (!map || typeof map.easeTo !== 'function') return false;

                const o = opts || {};
                const state = window.srhTricycle || {};
                const paxWrapper = document.getElementById('passenger-status-wrapper');
                const isPassengerIdle = paxWrapper ? (paxWrapper.dataset.status === 'none') : false;

                let lng = (typeof o.lng === 'number' && !isNaN(o.lng)) ? o.lng : null;
                let lat = (typeof o.lat === 'number' && !isNaN(o.lat)) ? o.lat : null;
                let heading = (typeof o.heading === 'number' && !isNaN(o.heading)) ? o.heading : (typeof window.getTricycleHeading === 'function' ? window.getTricycleHeading() : (state.heading || 0));

                if (lng === null || lat === null) {
                    if ((typeof o.lng !== 'number' || typeof o.lat !== 'number') && o.marker && typeof o.marker.getLngLat === 'function') {
                        try {
                            const pos = o.marker.getLngLat();
                            lng = pos.lng;
                            lat = pos.lat;
                        } catch(e) {}
                    }
                }

                if (lng === null || lat === null) {
                    if (!isPassengerIdle && typeof state.lng === 'number' && typeof state.lat === 'number' && !isNaN(state.lng) && !isNaN(state.lat)) {
                        lng = state.lng;
                        lat = state.lat;
                    } else if (window.pickupMarker && typeof window.pickupMarker.getLngLat === 'function') {
                        try { const p = window.pickupMarker.getLngLat(); lng = p.lng; lat = p.lat; } catch(e){}
                    } else if (map.getCenter) {
                        try { const c = map.getCenter(); lng = c.lng; lat = c.lat; } catch(e){}
                    }
                }

                if (typeof lng !== 'number' || typeof lat !== 'number' || isNaN(lng) || isNaN(lat)) return false;

                const mapPitch = (map && map.getPitch) ? map.getPitch() : 0;
                const defaultPitch = (window.srhCompassMode === 1) ? 60 : mapPitch;
                const targetPitch = (o.pitch !== undefined && !isNaN(o.pitch)) ? o.pitch : defaultPitch;
                const targetBearing = (targetPitch === 0) ? 0 : (((heading % 360) + 360) % 360);
                const targetZoom = (o.zoom !== undefined && !isNaN(o.zoom)) ? o.zoom : Math.min(18, Math.max(16.5, map.getZoom() || 17));

                const m = window.driverMarker || o.marker;
                if (m && typeof m.setRotation === 'function') {
                    try { m.setRotation(0); } catch(e){}
                }

                const options = {
                    center: [lng, lat],
                    zoom: targetZoom,
                    pitch: targetPitch,
                    bearing: targetBearing,
                    duration: (o.duration !== undefined && !isNaN(o.duration)) ? o.duration : 650,
                    essential: true
                };
                if (o.padding !== undefined) options.padding = o.padding;

                try {
                    if ((o.flyTo === true || o.method === 'flyTo') && typeof map.flyTo === 'function') {
                        map.flyTo(options);
                    } else {
                        map.easeTo(options);
                    }
                } catch(e) {}
                return true;
            };

            // Dynamically adjust a map's bottom padding (used when the bottom form sheet is
            // pulled up/collapsed) so re-centering commands always respect the visible viewport.
            // ✨ 1:1 sync: applied instantly (duration:0) on every real change so the map push
            // tracks the finger frame-for-frame with zero lag — no lerp catch-up rush on the
            // first pull. Re-renders are skipped when the rounded value already matches, and
            // sub-pixel touch jitter is filtered one level up (sync*MapPadding source guard).
            window.srhSetMapPadding = function(map, bottom) {
                if (!map || typeof map.setPadding !== 'function') return;
                const target = Math.max(0, Math.round(bottom || 0));
                const st = map.__srhPush || (map.__srhPush = { applied: null });
                if (st.applied === target) return;
                st.applied = target;
                try {
                    map.setPadding({
                        top: 0,
                        bottom: target,
                        left: 0,
                        right: 0
                    }, { duration: 0 });
                } catch(e) {}
            };

            // True when the map has a style initialized AND finished loading. Calling
            // map.isStyleLoaded() earlier triggers maplibre's "There is no style added
            // to the map" warning and getSource/getLayer throw on an unstyled map.
            window.srhStyleIsReady = function(map) {
                if (!map || !map.style) return false;
                try { return !!map.isStyleLoaded(); } catch(e) { return false; }
            };

            window.srhAddGeofenceCircle = function(map, sourceId, lng, lat, radiusMeters, paint) {
                if (!map) return;
                const add = function() {
                    if (map.getSource(sourceId)) return;
                    const p = paint || {};
                    map.addSource(sourceId, {
                        type: 'geojson',
                        data: { type: 'Feature', geometry: { type: 'Point', coordinates: [lng, lat] }, properties: {} }
                    });
                    map.addLayer({ id: sourceId + '-fill', type: 'circle', source: sourceId, paint: {
                        'circle-radius': window.srhMetersToPixels(map, lat, radiusMeters),
                        'circle-color': p.color || '#3b82f6',
                        'circle-opacity': p.fillOpacity !== undefined ? p.fillOpacity : 0.15,
                        'circle-stroke-width': p.weight || 2,
                        'circle-stroke-color': p.strokeColor || '#2563eb',
                        'circle-stroke-opacity': p.strokeOpacity !== undefined ? p.strokeOpacity : 0.9
                    }});
                    const updateRadius = function() {
                        if (!map.getSource(sourceId)) return;
                        try {
                            map.setPaintProperty(sourceId + '-fill', 'circle-radius', window.srhMetersToPixels(map, lat, radiusMeters));
                        } catch(e) {}
                    };
                    map.on('zoom', updateRadius);
                    map.on('move', updateRadius);
                };
                if (window.srhStyleIsReady(map)) { add(); } else { map.once('load', add); }
            };

            window.isRealCoordinate = function(lat, lng) {
                return typeof lat === 'number' && typeof lng === 'number' &&
                       !isNaN(lat) && !isNaN(lng) &&
                       lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180 &&
                       (lat !== 0 || lng !== 0);
            };

            window.srhSetLineSource = function(map, sourceId, coordsLngLat, layers) {
                if (!map) return;
                const add = function() {
                    const geo = { type: 'Feature', geometry: { type: 'LineString', coordinates: coordsLngLat }, properties: {} };
                    if (map.getSource(sourceId)) {
                        map.getSource(sourceId).setData(geo);
                        const defs = layers || [];
                        defs.forEach(function(l) {
                            if (!map.getLayer(l.id)) return;
                            const paintObj = {
                                'line-color': l.color || '#2563eb',
                                'line-width': l.weight || 5,
                                'line-opacity': l.opacity !== undefined ? l.opacity : 0.9,
                                'line-opacity-transition': { duration: 300 }
                            };
                            try {
                                map.setPaintProperty(l.id, 'line-color', paintObj['line-color']);
                            } catch(e) {}
                            try {
                                map.setPaintProperty(l.id, 'line-color-transition', { duration: 350 });
                            } catch(e) {}
                            try {
                                map.setPaintProperty(l.id, 'line-width', paintObj['line-width']);
                            } catch(e) {}
                            try {
                                map.setPaintProperty(l.id, 'line-opacity', paintObj['line-opacity']);
                            } catch(e) {}
                        });
                        return;
                    }
                    map.addSource(sourceId, { type: 'geojson', data: geo });
                    const defs = layers || [];
                    defs.forEach(function(l) {
                        if (!map.getLayer(l.id)) {
                            const paintObj = {
                                'line-color': l.color || '#2563eb',
                                'line-width': l.weight || 5,
                                'line-opacity': l.opacity !== undefined ? l.opacity : 0.9,
                                'line-opacity-transition': { duration: 300 }
                            };
                            if (l.dashArray && Array.isArray(l.dashArray)) {
                                paintObj['line-dasharray'] = l.dashArray;
                            }
                            map.addLayer({
                                id: l.id,
                                type: 'line',
                                source: sourceId,
                                layout: {
                                    'line-cap': l.lineCap || 'round',
                                    'line-join': l.lineJoin || 'round'
                                },
                                paint: paintObj
                            });
                        }
                    });
                };
                if (window.srhStyleIsReady(map)) { add(); } else { map.once('load', add); }
            };

            window.srhClearLineSource = function(map, sourceId) {
                if (!map || typeof map.getSource !== 'function') return;
                const clear = function() {
                    try {
                        if (map.getSource(sourceId)) {
                            map.getSource(sourceId).setData({ type: 'Feature', geometry: { type: 'LineString', coordinates: [] }, properties: {} });
                        }
                        if (sourceId === 'home-route') {
                            ['home-route-glow', 'home-route-core'].forEach(id => {
                                if (map.getLayer(id)) map.removeLayer(id);
                            });
                            if (map.getSource('home-route')) map.removeSource('home-route');
                        }
                    } catch(e) {}
                };
                if (window.srhStyleIsReady(map)) { clear(); } else { map.once('load', clear); }
            };
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Native App Touch & Scroll Engine */
            html, body {
                overscroll-behavior-y: contain;
                -webkit-tap-highlight-color: transparent;
                -webkit-touch-callout: none;
            }

            button, a, .nav-item-link, .btn-animate, .select-none {
                user-select: none;
                -webkit-user-select: none;
            }

            /* Global MapLibre Attribution Hide (clean full-bleed map UI) */
            .maplibregl-ctrl-attrib, .maplibregl-ctrl-attrib-button, .maplibregl-ctrl-logo {
                display: none !important;
            }
            .maplibregl-canvas { outline: none; }

            /* Position MapLibre top-right controls below floating header bars on full maps */
            #grab-home-map .maplibregl-ctrl-top-right,
            .srh-fullscreen-map .maplibregl-ctrl-top-right {
                top: 64px !important;
                right: 12px !important;
            }

            /* Glassmorphism & High-Visibility Styling for Map Controls */
            .maplibregl-ctrl-group {
                border-radius: 1rem !important;
                box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25) !important;
                border: 1px solid rgba(226, 232, 240, 0.9) !important;
                overflow: hidden !important;
                background: rgba(255, 255, 255, 0.95) !important;
                backdrop-filter: blur(12px) !important;
            }

            /* Top Instant SPA Progress Loader */
            #spa-progress-bar {
                display: none !important;
                position: fixed;
                top: 0;
                left: 0;
                height: 3px;
                background: linear-gradient(90deg, #2563eb, #3b82f6, #60a5fa);
                z-index: 999999;
                width: 0%;
                transition: width 0.2s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.25s ease;
                box-shadow: 0 0 10px rgba(37, 99, 235, 0.8);
                pointer-events: none;
            }

            /* ✨ Life360-Style Content-Aware Skeleton Shimmer (Zero Layout Shift, Pure GPU Composited) */
            @keyframes srhShimmerWave {
                0% { background-position: -200% 0; }
                100% { background-position: 200% 0; }
            }
            .srh-shimmer {
                background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
                background-size: 200% 100%;
                animation: srhShimmerWave 1.4s ease-in-out infinite;
                will-change: background-position;
            }

            @keyframes srhFadeIn {
                from { opacity: 0; transform: translateY(4px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in {
                animation: srhFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }

            /* Instant Native Tab Switching (Zero lag / Zero GPU transition overhead like Grab & Foodpanda) */
            .page-transition-wrapper {
                opacity: 1;
                transform: none;
            }

            /* High-Performance GPU Compositing & Hardware Acceleration (60fps/90fps/120fps on Mobile & Desktop) */
            .maplibregl-map,
            .maplibregl-canvas,
            #mobile-bottom-nav,
            #driver-bottom-sheet,
            #passenger-bottom-sheet,
            .srh-draggable-sheet,
            .srh-hud-glass,
            .grab-glass-panel,
            #saved-location-modal > div,
            .hw-accelerate {
                transform: translateZ(0);
                -webkit-transform: translateZ(0);
                backface-visibility: hidden;
                -webkit-backface-visibility: hidden;
                perspective: 1000px;
                -webkit-perspective: 1000px;
            }

            /* Content Visibility & DOM Optimization for Fast Scroll & Layout */
            .content-auto {
                content-visibility: auto;
                contain-intrinsic-size: 1px 120px;
            }

            /* Modern Interactive Micro Press Scale Animations */
            button, .btn-animate, .nav-item-link, .desktop-nav-link {
                transition: transform 0.15s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.15s ease, opacity 0.15s ease, color 0.15s ease, background-color 0.15s ease;
            }
            button:not(:disabled):active, .btn-animate:active, .nav-item-link:active {
                transform: scale(0.95);
            }

            /* Floating HUD Glass Card */
            .srh-hud-glass {
                background: rgba(15, 23, 42, 0.88);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1px solid rgba(255,255,255,0.12);
            }

            .grab-glass-panel {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.5);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            }

            /* Hardware-Accelerated 90Hz/120Hz High-FPS Drag Engine Styles */
            .srh-draggable-sheet {
                touch-action: none !important;
                -webkit-touch-callout: none;
                user-select: none;
                -webkit-user-select: none;
            }

            .srh-edge-bottom-sheet {
                border-top-left-radius: 32px !important;
                border-top-right-radius: 32px !important;
                border-bottom-left-radius: 0px !important;
                border-bottom-right-radius: 0px !important;
                overflow-x: hidden !important;
            }

            #driver-bottom-sheet {
                box-sizing: border-box;
                max-width: 100vw;
            }

            /* 🎯 Invisible scrollbars inside bottom sheets — the queue list
               stays fully scrollable (touch/wheel/keyboard), just no visible bar */
            #driver-bottom-sheet,
            #active-trip-wrapper,
            #incoming-ride-wrapper,
            #driver-bottom-sheet *,
            #active-trip-wrapper *,
            #incoming-ride-wrapper * {
                scrollbar-width: none;
                -ms-overflow-style: none;
            }
            #driver-bottom-sheet ::-webkit-scrollbar,
            #active-trip-wrapper ::-webkit-scrollbar,
            #incoming-ride-wrapper ::-webkit-scrollbar {
                display: none;
                width: 0;
                height: 0;
                background: transparent;
            }

            /* 🎨 Queue card theme layers — #1 = green "next" theme, 2nd+ = violet
               "waiting" theme (harmonizes with the emerald/teal/blue gradient).
               Gradients can't be transitioned directly, so two absolutely-
               positioned gradient layers cross-fade (1.2s). */
            #queue-card-container > #online-queue-card,
            #queue-card-container > #offline-queue-card {
                width: 100%;
                max-width: 100%;
            }
            #online-queue-card {
                transition: box-shadow 1.2s cubic-bezier(0.4, 0, 0.2, 1);
            }
            #online-queue-card .queue-theme-layer {
                position: absolute;
                inset: 0;
                z-index: 0;
                border-radius: inherit;
                pointer-events: none;
                transition: opacity 1.2s cubic-bezier(0.4, 0, 0.2, 1);
                will-change: opacity;
            }
            #online-queue-card .queue-theme-now {
                background: linear-gradient(to right, #10b981, #0d9488, #2563eb);
                opacity: 1;
            }
            #online-queue-card .queue-theme-wait {
                background: linear-gradient(to right, #60a5fa, #3b82f6, #1d4ed8);
                opacity: 0;
            }
            #online-queue-card[data-theme="wait"] .queue-theme-now {
                opacity: 0;
            }
            #online-queue-card[data-theme="wait"] .queue-theme-wait {
                opacity: 1;
            }
            #online-queue-card[data-theme="wait"] {
                box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.28), 0 4px 6px -2px rgba(37, 99, 235, 0.12);
            }

            /* 🎨 Compact "#N IN QUEUE" badge — smooth background fade when
               switching between the emerald (#1) and violet (2nd+) themes */
            #compact-queue-badge {
                transition: background-color 1.2s cubic-bezier(0.4, 0, 0.2, 1),
                            box-shadow 1.2s cubic-bezier(0.4, 0, 0.2, 1);
            }

            #sheet-details-content,
            #embedded-live-queue-wrapper,
            #live-queue-wrapper,
            #queue-items-container {
                max-width: 100%;
                box-sizing: border-box;
                overflow-x: hidden;
            }

            .srh-drag-handle {
                touch-action: none !important;
                user-select: none !important;
                -webkit-user-select: none !important;
                cursor: grab;
            }

            /* 🚀 Active Dragged Fallback Card (Transition: NONE for Instant 1:1 Cursor/Touch Tracking) */
            .sortable-drag, .sortable-fallback {
                transition: none !important;
                opacity: 0.98 !important;
                background: #ffffff !important;
                border: 2px solid #2563eb !important;
                border-radius: 1rem !important;
                box-shadow: 0 16px 36px -6px rgba(37, 99, 235, 0.35) !important;
                z-index: 999999 !important;
                pointer-events: none !important;
            }

            .sortable-ghost {
                opacity: 0.25 !important;
                background: #eff6ff !important;
                border: 2px dashed #2563eb !important;
                border-radius: 1rem !important;
                box-shadow: inset 0 2px 8px rgba(37, 99, 235, 0.1) !important;
            }

            .sortable-ghost * {
                opacity: 0 !important;
                visibility: hidden !important;
            }

            .sortable-chosen {
                background-color: #f0f9ff !important;
            }

            /* ☁️ Soft Feather-Light Smooth Fade Entrance */
            @@keyframes formOnlineFade {
                0% {
                    opacity: 0;
                    transform: translate3d(0, 10px, 0);
                }
                100% {
                    opacity: 1;
                    transform: translate3d(0, 0, 0);
                }
            }

            .animate-online-pop {
                animation: formOnlineFade 0.35s ease-out forwards;
            }

            .animate-online-pop-delayed {
                animation: formOnlineFade 0.35s ease-out 0.05s forwards;
            }

            /* ☁️ High-Framerate 120Hz/144Hz Hardware GPU Composited Expansion */
            #queue-card-container {
                transition: height 0.65s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.45s ease-out;
            }

            .card-content-fade-in {
                animation: cardSoftFadeIn 0.45s ease-out forwards;
            }

            /* ⚡ Spring Pop Animation for Queue Number Badges (shared driver pages) */
            .queue-number-badge.badge-pop {
                animation: badgePopAnim 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
            @@keyframes badgePopAnim {
                0% { transform: scale(0.85); }
                50% { transform: scale(1.12); }
                100% { transform: scale(1); }
            }

            @@keyframes cardSoftFadeIn {
                0% { opacity: 0; transform: scale(0.98); }
                100% { opacity: 1; transform: scale(1); }
            }

            /* 🛡️ Driver Hub Sticky Duty Status Card (Queue scrolls cleanly underneath/behind) */
            #queue-card-container {
                position: sticky !important;
                top: 0px !important;
                z-index: 30 !important;
                background-color: #ffffff !important;
                padding-top: 4px !important;
                padding-bottom: 6px !important;
            }

            /* 🛡️ Strict Hardware Clip Container (Guarantees Zero Overlap Above Status Card) */
            #live-queue-clip-container {
                display: grid;
                grid-template-rows: 0fr;
                opacity: 0;
                margin-top: 0px;
                overflow: hidden;
                position: relative;
                z-index: 10;
                transition: opacity 0.35s ease-out;
                will-change: opacity;
                backface-visibility: hidden;
                -webkit-backface-visibility: hidden;
                transform: translateZ(0);
            }

            #live-queue-clip-container.is-expanded {
                grid-template-rows: 1fr;
                opacity: 1;
                margin-top: 0.75rem;
            }

            #embedded-live-queue-wrapper {
                min-height: 0;
                overflow: hidden;
                will-change: opacity;
                backface-visibility: hidden;
                -webkit-backface-visibility: hidden;
            }

            /* GPU Optimization during Active Drag (Strips heavy blur/shadows on high refresh rate displays) */
            .is-dragging {
                will-change: transform !important;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12) !important;
                backdrop-filter: none !important;
                -webkit-backdrop-filter: none !important;
            }

            /* Prevent Alpine x-cloak elements from flashing during initial page load */
            [x-cloak] {
                display: none !important;
            }

            /* 🌊 Modern In-Place Pull-To-Refresh Indicator (Rock-Solid Center & 120Hz Composited) */
            #srh-pull-refresh {
                position: fixed;
                top: calc(env(safe-area-inset-top, 0px) + 12px);
                left: 0;
                right: 0;
                margin-left: auto;
                margin-right: auto;
                width: max-content;
                z-index: 999999;
                pointer-events: none;
                opacity: 0;
                transform: translate3d(0, -90px, 0);
                will-change: transform, opacity;
                backface-visibility: hidden;
                -webkit-backface-visibility: hidden;
            }

            #srh-pull-refresh.is-animating {
                transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.22s ease;
            }

            #srh-pull-refresh.is-active {
                opacity: 1;
            }

            .srh-pull-svg {
                transform-origin: center center;
                will-change: transform;
            }

            #srh-pull-circle {
                transform-origin: 18px 18px;
                stroke-dasharray: 100;
                stroke-dashoffset: 100;
            }

            /* 🌀 Infinite Smooth Material Spinner while Refreshing */
            #srh-pull-refresh.is-refreshing .srh-pull-svg {
                animation: srhPullSpin 0.75s linear infinite !important;
            }

            #srh-pull-refresh.is-refreshing #srh-pull-circle {
                animation: srhPullArc 1.4s ease-in-out infinite !important;
            }

            @@keyframes srhPullSpin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            @@keyframes srhPullArc {
                0% {
                    stroke-dasharray: 1 100;
                    stroke-dashoffset: 0;
                }
                50% {
                    stroke-dasharray: 75 100;
                    stroke-dashoffset: -20;
                }
                100% {
                    stroke-dasharray: 75 100;
                    stroke-dashoffset: -100;
                }
            }
        </style>

        <script>
            /* Native Bottom Sheet Engine (Life360 Dual-Phase Gesture & Nested Scroll Architecture) */
            window.NativeBottomSheet = {
                attach: function(options) {
                    const handle = typeof options.handle === 'string' ? document.querySelector(options.handle) : options.handle;
                    const content = typeof options.content === 'string' ? document.querySelector(options.content) : options.content;
                    if (!handle || !content) return;
                    if (handle._nativeSheetAttached) return;
                    handle._nativeSheetAttached = true;

                    const parentCard = (options.parentCard ? (typeof options.parentCard === 'string' ? document.querySelector(options.parentCard) : options.parentCard) : null) || content.closest('.grab-glass-panel, .srh-draggable-sheet, .bg-white, .srh-wait-glass, .srh-hud-glass') || content.parentElement;
                    if (parentCard) {
                        parentCard.classList.add('srh-draggable-sheet');
                        parentCard.style.touchAction = 'none';
                    }

                    handle.classList.add('srh-drag-handle');
                    handle.style.touchAction = 'none';

                    let startTouchY = 0, startTouchX = 0, lastTouchY = 0, lastTouchTime = 0, velocity = 0;
                    let isDragging = false, hasMoved = false, gestureMode = 'idle'; // 'idle' | 'sheet_drag' | 'content_scroll' | 'ignored'
                    let baseTranslateY = 0, activeTranslateY = 0, dragAnchorY = 0;
                    let rafId = null, glideRafId = null, snapSyncRafId = null;

                    function getClientY(e) {
                        if (e.touches && e.touches.length > 0) return e.touches[0].clientY;
                        if (e.changedTouches && e.changedTouches.length > 0) return e.changedTouches[0].clientY;
                        return e.clientY;
                    }

                    function getClientX(e) {
                        if (e.touches && e.touches.length > 0) return e.touches[0].clientX;
                        if (e.changedTouches && e.changedTouches.length > 0) return e.changedTouches[0].clientX;
                        return e.clientX;
                    }

                    function getMinTranslateY() {
                        if (typeof options.getMinTranslateY === 'function') {
                            return options.getMinTranslateY();
                        }
                        return options.minTranslateY || 0;
                    }

                    function getMaxAllowed() {
                        if (typeof options.getMaxAllowed === 'function') {
                            return options.getMaxAllowed();
                        }
                        return options.maxAllowed || 280;
                    }

                    function getDefaultTranslateY() {
                        if (typeof options.getDefaultTranslateY === 'function') {
                            return options.getDefaultTranslateY();
                        }
                        if (options.defaultTranslateY !== undefined) {
                            return typeof options.defaultTranslateY === 'function' ? options.defaultTranslateY() : options.defaultTranslateY;
                        }
                        return getMaxAllowed();
                    }

                    function getExpandedMaxHeight() {
                        if (typeof options.getExpandedMaxHeight === 'function') {
                            return options.getExpandedMaxHeight();
                        }
                        return Math.min(window.innerHeight - 100, 540);
                    }

                    function getTwoStateAnchors() {
                        let def = options.twoStateAnchors;
                        if (typeof def === 'function') def = def();
                        if (Array.isArray(def) && def.length >= 2) {
                            const a = typeof def[0] === 'function' ? def[0]() : def[0];
                            const b = typeof def[1] === 'function' ? def[1]() : def[1];
                            if (typeof a === 'number' && typeof b === 'number' && isFinite(a) && isFinite(b)) {
                                return [Math.min(a, b), Math.max(a, b)];
                            }
                        }
                        return null;
                    }

                    function getIsTwoStates() {
                        let l = options.limitToTwoStates;
                        let e = options.enableThreeStates;
                        if (typeof l === 'function') l = l();
                        if (typeof e === 'function') e = e();
                        return !!l || e === false;
                    }

                    function getContentMaxHeight() {
                        if (typeof options.contentMaxHeight === 'function') return options.contentMaxHeight();
                        if (typeof options.contentMaxHeight === 'string') return options.contentMaxHeight;
                        return 'calc(100vh - 120px)';
                    }

                    function getMidDistance() {
                        if (typeof options.getDefaultTranslateY === 'function') {
                            return options.getDefaultTranslateY();
                        }
                        if (options.defaultTranslateY !== undefined) {
                            return typeof options.defaultTranslateY === 'function' ? options.defaultTranslateY() : options.defaultTranslateY;
                        }
                        if (typeof options.getMidDistance === 'function') {
                            return options.getMidDistance();
                        }
                        return options.midDistance || 110;
                    }

                    function setupFixedContent() {
                        if (options.lockContentStyles) return;
                        content.style.maxHeight = getContentMaxHeight();
                        content.style.opacity = '1';
                        const minTranslate = getMinTranslateY();
                        const isAtTop = (activeTranslateY <= minTranslate + 5);
                        if (options.overflowY !== false) {
                            content.style.overflowY = isAtTop ? (options.overflowY || 'auto') : 'hidden';
                        }
                        if (!isAtTop) {
                            content.scrollTop = 0;
                        }
                        content.style.overflowX = 'hidden';
                        content.style.overscrollBehaviorY = 'contain';
                        content.style.webkitOverflowScrolling = 'touch';
                        if (options.marginTop !== false) {
                            content.style.marginTop = options.marginTop || '12px';
                        }
                        content.style.pointerEvents = 'auto';
                    }
                    setupFixedContent();

                    const initMax = getMaxAllowed();
                    const positionLocked = parentCard && parentCard.dataset.sheetPositionLocked === '1';
                    const initialTranslateY = (options.startCollapsed !== false && !positionLocked) ? getDefaultTranslateY() : getCurrentTranslateY();
                    activeTranslateY = initialTranslateY;
                    if (parentCard) {
                        parentCard.style.transform = 'translate3d(0, ' + initialTranslateY + 'px, 0)';
                    }
                    if (options.onSync) {
                        options.onSync(Math.max(0, initMax - initialTranslateY), initMax, false);
                    }

                    function getCurrentTranslateY() {
                        const liveParent = (typeof options.parentCard === 'string' ? document.querySelector(options.parentCard) : null) || parentCard;
                        if (!liveParent) return 0;
                        const style = window.getComputedStyle(liveParent);
                        const transform = style.transform || style.webkitTransform;
                        if (transform && transform !== 'none') {
                            const matrix = transform.match(/^matrix\((.+)\)$/);
                            if (matrix) return parseFloat(matrix[1].split(',')[5]) || 0;
                            const matrix3d = transform.match(/^matrix3d\((.+)\)$/);
                            if (matrix3d) return parseFloat(matrix3d[1].split(',')[13]) || 0;
                        }
                        return activeTranslateY || 0;
                    }

                    function stopAllAnimations() {
                        if (glideRafId) { cancelAnimationFrame(glideRafId); glideRafId = null; }
                        if (snapSyncRafId) { cancelAnimationFrame(snapSyncRafId); snapSyncRafId = null; }
                        if (rafId) { cancelAnimationFrame(rafId); rafId = null; }
                    }

                    function applySheetTransform(y) {
                        const liveParent = (typeof options.parentCard === 'string' ? document.querySelector(options.parentCard) : null) || parentCard;
                        if (!liveParent) return;
                        activeTranslateY = y;
                        liveParent.style.transform = 'translate3d(0, ' + y.toFixed(2) + 'px, 0)';
                        const maxAllowed = getMaxAllowed();
                        const visibleHeight = Math.max(0, maxAllowed - y);
                        if (options.onSync) options.onSync(visibleHeight, maxAllowed, isDragging);
                    }

                    function startSheetDragging(liveParent) {
                        if (!isDragging) {
                            isDragging = true;
                            if (content) {
                                content.style.overflowY = 'hidden';
                            }
                            if (liveParent) {
                                liveParent.classList.add('is-dragging');
                                liveParent.style.willChange = 'transform';
                                liveParent.style.transition = 'none';
                            }
                        }
                    }

                    let currentDragEventType = null;
                    let startScrollTop = 0;
                    let suppressClickUntil = 0;

                    function onDragStart(e) {
                        if (e.target && e.target.closest) {
                            if (e.target.closest('input, select, textarea, [contenteditable="true"], [data-no-sheet-drag]')) {
                                return;
                            }
                        }
                        if (window.closeAnnouncementModal) window.closeAnnouncementModal();
                        window.dispatchEvent(new CustomEvent('srh-close-notifications'));

                        stopAllAnimations();

                        const liveParent = (typeof options.parentCard === 'string' ? document.querySelector(options.parentCard) : null) || parentCard;
                        if (liveParent) {
                            liveParent.style.transition = 'none';
                        }

                        isDragging = false;
                        hasMoved = false;
                        gestureMode = 'idle';
                        startScrollTop = content ? content.scrollTop : 0;
                        currentDragEventType = (e.type && e.type.indexOf('touch') !== -1) ? 'touch' : 'mouse';

                        startTouchY = getClientY(e);
                        startTouchX = getClientX(e);
                        lastTouchY = startTouchY;
                        lastTouchTime = performance.now();
                        velocity = 0;

                        baseTranslateY = getCurrentTranslateY();
                        activeTranslateY = baseTranslateY;
                        dragAnchorY = startTouchY;

                        const minTranslate = getMinTranslateY();
                        const isSheetAtTop = (baseTranslateY <= minTranslate + 5);

                        const isTouchOnHandle = !!(e.target && (
                            e.target === handle || 
                            handle.contains(e.target) || 
                            e.target.closest('.srh-drag-handle, #sheet-drag-handle, .srh-sheet-header, [data-sheet-handle]')
                        ));

                        if (!isSheetAtTop) {
                            // Sheet not at top: Lock content scroll immediately, any touch starts sheet drag
                            if (content) {
                                content.style.overflowY = 'hidden';
                                content.scrollTop = 0;
                            }
                            gestureMode = 'sheet_drag';
                            startSheetDragging(liveParent);
                        } else if (isTouchOnHandle) {
                            gestureMode = 'sheet_drag';
                            startSheetDragging(liveParent);
                        }

                        if (currentDragEventType === 'touch') {
                            window.addEventListener('touchmove', onDragMove, { passive: false });
                            window.addEventListener('touchend', onDragEnd, { capture: true });
                            window.addEventListener('touchcancel', onDragEnd, { capture: true });
                        } else {
                            window.addEventListener('mousemove', onDragMove);
                            window.addEventListener('mouseup', onDragEnd, { capture: true });
                        }
                    }

                    function onDragMove(e) {
                        const curY = getClientY(e);
                        const curX = getClientX(e);
                        const deltaY = curY - startTouchY;
                        const deltaX = curX - startTouchX;
                        const now = performance.now();
                        const dt = now - lastTouchTime;
                        if (dt > 0) {
                            const instVel = (curY - lastTouchY) / dt;
                            velocity = velocity === 0 ? instVel : (velocity * 0.65 + instVel * 0.35);
                        }
                        lastTouchY = curY;
                        lastTouchTime = now;

                        const liveParent = (typeof options.parentCard === 'string' ? document.querySelector(options.parentCard) : null) || parentCard;
                        const minTranslate = getMinTranslateY();
                        const maxAllowed = getMaxAllowed();
                        const isSheetAtTop = (baseTranslateY <= minTranslate + 5) && (activeTranslateY <= minTranslate + 5);

                        // Phase 1: Determine gesture intent
                        if (gestureMode === 'idle') {
                            if (Math.abs(deltaX) > Math.abs(deltaY) * 1.5 && Math.abs(deltaX) > 8) {
                                gestureMode = 'ignored';
                                return;
                            }

                            if (Math.abs(deltaY) > 3) {
                                hasMoved = true;
                                if (!isSheetAtTop) {
                                    // Sheet not at top: Content scroll disabled, drag sheet directly
                                    if (content) {
                                        content.style.overflowY = 'hidden';
                                        content.scrollTop = 0;
                                    }
                                    gestureMode = 'sheet_drag';
                                    dragAnchorY = startTouchY;
                                    startSheetDragging(liveParent);
                                    if (e.cancelable) e.preventDefault();
                                } else {
                                    // Sheet IS at top: Full expanded state
                                    if (deltaY < 0 || startScrollTop > 0) {
                                        // Scrolling UP into list or already inside list: Pure elastic list scrolling
                                        gestureMode = 'content_scroll';
                                    } else {
                                        // Started touch at top of list (scrollTop === 0) AND pulling DOWN: Drag the sheet!
                                        if (content) {
                                            content.style.overflowY = 'hidden';
                                        }
                                        gestureMode = 'sheet_drag';
                                        dragAnchorY = startTouchY;
                                        baseTranslateY = minTranslate;
                                        startSheetDragging(liveParent);
                                        if (e.cancelable) e.preventDefault();
                                    }
                                }
                            }
                        }

                        // Phase 2: Native Content Scroll (pure elastic bounce, only when fully expanded at top)
                        if (gestureMode === 'content_scroll') {
                            return;
                        }

                        // Phase 3: Handle active Sheet Dragging (1:1 Real-time Touch Sync)
                        if (gestureMode === 'sheet_drag') {
                            if (e.cancelable) e.preventDefault();
                            if (content) {
                                content.style.overflowY = 'hidden';
                            }

                            const dragDelta = curY - dragAnchorY;
                            let rawTranslateY = baseTranslateY + dragDelta;

                            // Resistance rubberbanding when dragging past boundaries
                            if (rawTranslateY < minTranslate) {
                                rawTranslateY = minTranslate + (rawTranslateY - minTranslate) * 0.22;
                            } else if (rawTranslateY > maxAllowed) {
                                const overflow = rawTranslateY - maxAllowed;
                                rawTranslateY = maxAllowed + overflow * 0.22;
                            }

                            applySheetTransform(rawTranslateY);
                        }
                    }

                    function onDragEnd(e) {
                        if (currentDragEventType === 'touch') {
                            window.removeEventListener('touchmove', onDragMove);
                            window.removeEventListener('touchend', onDragEnd, { capture: true });
                            window.removeEventListener('touchcancel', onDragEnd, { capture: true });
                        } else {
                            window.removeEventListener('mousemove', onDragMove);
                            window.removeEventListener('mouseup', onDragEnd, { capture: true });
                        }
                        currentDragEventType = null;

                        const liveParent = (typeof options.parentCard === 'string' ? document.querySelector(options.parentCard) : null) || parentCard;
                        if (liveParent) liveParent.classList.remove('is-dragging');

                        if (hasMoved) {
                            suppressClickUntil = performance.now() + 400;
                        }

                        if (gestureMode !== 'sheet_drag') {
                            isDragging = false;
                            gestureMode = 'idle';
                            if (content && (activeTranslateY <= getMinTranslateY() + 5) && options.overflowY !== false) {
                                content.style.overflowY = options.overflowY || 'auto';
                            }
                            return;
                        }

                        isDragging = false;
                        gestureMode = 'idle';

                        const maxAllowed = getMaxAllowed();
                        const midDist = getMidDistance();
                        const minTranslate = getMinTranslateY();
                        const isTwoStates = getIsTwoStates();
                        const finalPos = activeTranslateY;

                        if (options.freeDragMode || options.disableAutoSnap) {
                            activeTranslateY = Math.max(minTranslate, Math.min(maxAllowed, activeTranslateY));
                            if (liveParent) {
                                liveParent.style.transform = 'translate3d(0, ' + activeTranslateY.toFixed(2) + 'px, 0)';
                            }
                            const visibleHeight = Math.max(0, maxAllowed - activeTranslateY);
                            if (options.onSync) options.onSync(visibleHeight, maxAllowed, false);
                            return;
                        }

                        // ⚡ Ultra Low-Sensitivity Flick Detection (Requires intentional high velocity >= 1.6 px/ms)
                        let nextState = 2;
                        const isFlickUp = velocity < -1.6;
                        const isFlickDown = velocity > 1.6;
                        const isStrongFlickUp = velocity < -2.4;
                        const isStrongFlickDown = velocity > 2.4;

                        if (isTwoStates) {
                            if (isFlickUp) {
                                nextState = 0; // Default Open View
                            } else if (isFlickDown) {
                                nextState = 2; // Semi Hidden View
                            } else {
                                const twoAnchors = getTwoStateAnchors();
                                const topAnchor = twoAnchors ? twoAnchors[0] : minTranslate;
                                const botAnchor = twoAnchors ? twoAnchors[1] : maxAllowed;
                                if (baseTranslateY <= topAnchor + 15) {
                                    nextState = (finalPos >= topAnchor + 50) ? 2 : 0;
                                } else {
                                    nextState = (finalPos <= botAnchor - 50) ? 0 : 2;
                                }
                            }
                        } else {
                            if (isFlickUp) {
                                if (isStrongFlickUp || baseTranslateY <= (midDist + 30)) {
                                    nextState = 0; // Top Expanded View
                                } else {
                                    nextState = 1; // Mid View
                                }
                            } else if (isFlickDown) {
                                if (isStrongFlickDown || baseTranslateY >= (midDist - 30)) {
                                    nextState = 2; // Bottom Collapsed View
                                } else {
                                    nextState = 1; // Mid View
                                }
                            } else {
                                // Threshold-aware snap from starting position (solid, non-trigger-happy thresholds)
                                if (baseTranslateY <= minTranslate + 15) {
                                    // Started at TOP: Pull down by >= 50px transitions to MID
                                    if (finalPos >= (minTranslate + 50)) {
                                        nextState = (finalPos >= midDist + 50) ? 2 : 1;
                                    } else {
                                        nextState = 0;
                                    }
                                } else if (baseTranslateY >= maxAllowed - 30) {
                                    // Started at BOTTOM: Pull up by >= 50px transitions to MID
                                    if (finalPos <= (maxAllowed - 50)) {
                                        nextState = (finalPos <= midDist - 50) ? 0 : 1;
                                    } else {
                                        nextState = 2;
                                    }
                                } else {
                                    // Started at MID:
                                    if (finalPos <= (midDist - 50)) {
                                        nextState = 0; // Top
                                    } else if (finalPos >= (midDist + 50)) {
                                        nextState = 2; // Bottom
                                    } else {
                                        nextState = 1; // Mid
                                    }
                                }
                            }
                        }

                        animateToSnapState(nextState, 800, velocity);
                    }

                    function animateToSnapState(targetState, duration = 800, releaseVelocity = 0) {
                        stopAllAnimations();
                        const liveParent = (typeof options.parentCard === 'string' ? document.querySelector(options.parentCard) : null) || parentCard;
                        const maxAllowed = getMaxAllowed();
                        const midDist = getMidDistance();
                        const minTranslate = getMinTranslateY();
                        const isTwoStates = getIsTwoStates();
                        const twoAnchors = getTwoStateAnchors();

                        let targetTranslateY = 0;
                        if (targetState === 2) targetTranslateY = twoAnchors ? twoAnchors[1] : maxAllowed;
                        else if (targetState === 1) targetTranslateY = twoAnchors ? twoAnchors[0] : (isTwoStates ? maxAllowed : midDist);
                        else targetTranslateY = twoAnchors ? twoAnchors[0] : minTranslate;

                        // ☁️ Gentle, Silky-Soft Deceleration Easing (Zero Aggression, Smooth Landing)
                        const springEasing = 'transform ' + duration + 'ms cubic-bezier(0.16, 1, 0.3, 1)';
                        setupFixedContent();

                        if (liveParent) {
                            liveParent.style.willChange = 'transform';
                            liveParent.style.transition = 'none';
                            liveParent.style.transform = 'translate3d(0, ' + activeTranslateY.toFixed(2) + 'px, 0)';
                            void liveParent.offsetHeight;

                            liveParent.style.transition = springEasing;
                            liveParent.style.transform = 'translate3d(0, ' + targetTranslateY + 'px, 0)';
                        }

                        activeTranslateY = targetTranslateY;
                        const isExpanded = targetState === 0;
                        if (typeof options.onSnapStart === 'function') options.onSnapStart(isExpanded);

                        const startSnapTime = performance.now();
                        function snapSyncLoop() {
                            const elapsed = performance.now() - startSnapTime;
                            const currentTranslate = getCurrentTranslateY();
                            const visibleHeight = Math.max(0, maxAllowed - currentTranslate);

                            if (options.onSync) options.onSync(visibleHeight, maxAllowed, true);

                            if (elapsed < duration) {
                                snapSyncRafId = requestAnimationFrame(snapSyncLoop);
                            } else {
                                snapSyncRafId = null;
                                if (liveParent) {
                                    liveParent.style.willChange = 'auto';
                                }
                                if (targetState === 0) {
                                    if (content && options.overflowY !== false) {
                                        content.style.overflowY = options.overflowY || 'auto';
                                    }
                                    if (options.onExpand) options.onExpand();
                                } else {
                                    if (content) {
                                        content.style.overflowY = 'hidden';
                                        content.scrollTop = 0;
                                    }
                                    if (targetState === 2 && options.onCollapse) {
                                        options.onCollapse();
                                    }
                                }
                            }
                        }
                        snapSyncRafId = requestAnimationFrame(snapSyncLoop);
                    }

                    function animateToState(shouldExpand, duration = 800, releaseVelocity = 0) {
                        animateToSnapState(shouldExpand ? 0 : 2, duration, releaseVelocity);
                    }

                    function moveToTranslate(targetY, duration = 750) {
                        stopAllAnimations();
                        const liveParent = (typeof options.parentCard === 'string' ? document.querySelector(options.parentCard) : null) || parentCard;
                        const maxAllowed = getMaxAllowed();
                        const minTranslate = getMinTranslateY();
                        const clampedY = Math.max(minTranslate, Math.min(maxAllowed, targetY));

                        setupFixedContent();

                        if (liveParent) {
                            liveParent.style.willChange = 'transform';
                            liveParent.style.transition = 'none';
                            liveParent.style.transform = 'translate3d(0, ' + activeTranslateY.toFixed(2) + 'px, 0)';
                            void liveParent.offsetHeight;

                            const springEasing = 'transform ' + duration + 'ms cubic-bezier(0.16, 1, 0.3, 1)';
                            liveParent.style.transition = springEasing;
                            liveParent.style.transform = 'translate3d(0, ' + clampedY + 'px, 0)';
                        }

                        activeTranslateY = clampedY;

                        const startTime = performance.now();
                        function fitSyncLoop() {
                            const elapsed = performance.now() - startTime;
                            const currentTranslate = liveParent ? getCurrentTranslateY() : clampedY;
                            const visibleHeight = Math.max(0, maxAllowed - currentTranslate);
                            if (options.onSync) options.onSync(visibleHeight, maxAllowed, true);
                            if (elapsed < duration) {
                                snapSyncRafId = requestAnimationFrame(fitSyncLoop);
                            } else {
                                snapSyncRafId = null;
                                if (liveParent) liveParent.style.willChange = 'auto';
                            }
                        }
                        snapSyncRafId = requestAnimationFrame(fitSyncLoop);
                    }

                    function computeContentFitY() {
                        const contentEl = document.querySelector(options.content);
                        let natural = 0;
                        if (contentEl) {
                            const inner = contentEl.firstElementChild;
                            if (inner) {
                                natural = Math.max(0, Math.ceil(inner.getBoundingClientRect().height) || 0);
                            } else {
                                natural = Math.max(0, contentEl.scrollHeight || 0);
                            }
                        }
                        const padding = options.fitContentPadding || 110;
                        const visible = Math.max(160, Math.min(window.innerHeight - 110, natural + padding));
                        return Math.max(getMinTranslateY(), Math.min(getMaxAllowed(), window.innerHeight - visible));
                    }

                    const dragTargets = [handle, content];
                    if (parentCard && !dragTargets.includes(parentCard)) {
                        dragTargets.push(parentCard);
                    }
                    if (parentCard && !parentCard.dataset.nativeClickBound) {
                        parentCard.dataset.nativeClickBound = 'true';
                        parentCard.addEventListener('click', function(e) {
                            if (hasMoved || performance.now() < suppressClickUntil) {
                                e.preventDefault();
                                e.stopPropagation();
                                e.stopImmediatePropagation();
                                hasMoved = false;
                                return false;
                            }
                        }, true);
                    }

                    dragTargets.forEach(target => {
                        target.addEventListener('touchstart', onDragStart, { passive: false });
                        target.addEventListener('mousedown', onDragStart);
                    });

                    handle.srhToggleSheet = function(targetState) {
                        if (options.freeDragMode) return;
                        if (!getIsTwoStates()) {
                            if (typeof targetState === 'number') {
                                animateToSnapState(targetState, 280);
                            } else {
                                const currentTranslate = getCurrentTranslateY();
                                const midDist = getMidDistance();
                                const maxAllowed = getMaxAllowed();

                                if (currentTranslate > maxAllowed * 0.6) {
                                    animateToSnapState(1, 280);
                                } else if (currentTranslate > midDist * 0.4) {
                                    animateToSnapState(0, 280);
                                } else {
                                    animateToSnapState(2, 280);
                                }
                            }
                        } else {
                            const maxAllowed = getMaxAllowed();
                            const currentTranslate = getCurrentTranslateY();
                            const twoAnchors = getTwoStateAnchors();
                            let shouldExpand;
                            if (twoAnchors) {
                                shouldExpand = currentTranslate > (twoAnchors[0] + twoAnchors[1]) / 2;
                            } else {
                                shouldExpand = currentTranslate > maxAllowed * 0.4;
                            }
                            if (targetState !== undefined) shouldExpand = targetState;
                            animateToState(shouldExpand, 280);
                        }
                    };

                    return {
                        snapToState: function(targetState, duration = 1250) {
                            animateToSnapState(targetState, duration);
                        },
                        expand: function(duration = 1250) {
                            animateToSnapState(0, duration);
                        },
                        collapse: function(duration = 1250) {
                            animateToSnapState(2, duration);
                        },
                        sync: function(targetState = 2, duration = 200) {
                            animateToSnapState(targetState, duration);
                        },
                        moveTo: function(y, duration = 500) {
                            moveToTranslate(y, duration);
                        },
                        fitToContent: function(duration = 500) {
                            moveToTranslate(computeContentFitY(), duration);
                            return computeContentFitY();
                        }
                    };
                }
            };

            // 🚀 Auto-hide notification dropdown whenever user touches, taps, or glides the bottom form sheet
            // Also marks the first user gesture so AudioContext can be created safely (no autoplay warning).
            ['pointerdown', 'touchstart', 'mousedown', 'dragstart'].forEach(evtType => {
                document.addEventListener(evtType, function(e) {
                    window._srhUserHasInteracted = true;
                    if (e.target && e.target.closest && e.target.closest('#driver-bottom-sheet, #active-trip-wrapper, #incoming-ride-wrapper, .srh-draggable-sheet, .srh-drag-handle, #sheet-drag-handle, #active-trip-drag-handle, #sheet-details-content, #active-trip-details-content')) {
                        if (window.closeAnnouncementModal) window.closeAnnouncementModal();
                        window.dispatchEvent(new CustomEvent('srh-close-notifications'));
                    }
                }, { passive: true });
            });
            // Keyboard interaction also counts as a user gesture for AudioContext
            document.addEventListener('keydown', function() { window._srhUserHasInteracted = true; }, { once: true, passive: true });

            window.srhSafeVibrate = function(pattern) {
                try {
                    if (typeof navigator !== 'undefined' && 'vibrate' in navigator) {
                        if (navigator.userActivation && !navigator.userActivation.hasBeenActive) return;
                        navigator.vibrate(pattern);
                    }
                } catch(e) {}
            };
        </script>
    </head>
    <body class="font-sans antialiased">
        <!-- Modern In-Place Pull-To-Refresh Indicator (Pure Vector SVG Capsule, Zero Favicon Requests) -->
        <div id="srh-pull-refresh" aria-hidden="true">
            <div class="w-10 h-10 rounded-full bg-white shadow-[0_8px_24px_rgba(0,0,0,0.15)] border border-slate-200/90 flex items-center justify-center relative">
                <svg id="srh-pull-svg" class="srh-pull-svg w-6 h-6" viewBox="0 0 36 36">
                    <circle cx="18" cy="18" r="13" fill="none" stroke="#e2e8f0" stroke-width="3" />
                    <circle id="srh-pull-circle" cx="18" cy="18" r="13" fill="none" stroke="#2563eb" stroke-width="3" stroke-linecap="round" pathLength="100" stroke-dasharray="100" stroke-dashoffset="100" transform="rotate(-90 18 18)" />
                </svg>
                <div id="srh-pull-arrow" class="absolute inset-0 flex items-center justify-center text-blue-600 transition-transform duration-100">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                </div>
            </div>
        </div>

        <!-- Subtle Top Progress Indicator on Slow Connections -->
        <div id="spa-progress-bar" style="position: fixed; top: 0; left: 0; height: 3px; background: linear-gradient(90deg, #2563eb, #38bdf8); width: 0%; z-index: 999999; transition: width 0.2s ease, opacity 0.3s ease; opacity: 0; pointer-events: none;"></div>

        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Dynamic Content Container -->
            <div id="spa-content-area" class="page-transition-wrapper">
                @isset($header)
                    <header class="bg-white" style="background-color: #ffffff; border: none; box-shadow: none;">
                        <div class="max-w-7xl mx-auto pt-3 pb-1 sm:pt-6 sm:pb-3 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main class="{{ request()->routeIs('dashboard') ? 'pb-0' : 'pb-20 sm:pb-8' }}" id="app-main-content">
                    {{ $slot }}
                </main>
            </div>

            <!-- SPA Keep-Alive Vault: parked pages (driver map hub) survive tab switches
                 fully loaded. The translateX() creates a containing block so position:fixed
                 children (bottom sheets, overlays) can never leak onto the visible page,
                 and visibility:hidden + pointer-events:none double-guard rendering/input. -->
            <div id="srh-spa-vault" aria-hidden="true" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; visibility: hidden; overflow: hidden; pointer-events: none; z-index: -1; transform: translateX(-200vw);"></div>
        </div>

        <!-- ONE UNIFIED MOBILE NAVIGATION BAR (Fully opaque solid white, touching bottom of screen) -->
        <nav id="mobile-bottom-nav" class="fixed bottom-0 left-0 right-0 w-full bg-white border-t border-slate-200/90 shadow-[0_-4px_25px_rgba(0,0,0,0.08)] z-[9999] sm:hidden" style="background-color: #ffffff !important; opacity: 1 !important; bottom: 0px !important; margin: 0px !important; padding-bottom: max(0px, env(safe-area-inset-bottom, 0px));">
            <div class="flex justify-around items-center h-14 px-2 max-w-md mx-auto bg-white" style="background-color: #ffffff !important;">
                
                <!-- HOME -->
                <a href="{{ route('dashboard') }}" class="nav-item-link flex flex-col items-center justify-center w-full h-full transition-all duration-150 active:scale-90 {{ request()->routeIs('dashboard') ? 'text-blue-600 font-extrabold' : 'text-gray-400' }}">
                    <i class="ri-home-5-line text-xl leading-none mb-0.5"></i>
                    <span class="text-[9px] font-bold uppercase tracking-wider">Home</span>
                </a>

                <!-- SAVED PLACES (Passengers & General Users) -->
                @if(!Auth::check() || Auth::user()->role === 'passenger' || Auth::user()->role === 'user')
                <a href="{{ route('saved-locations.index') }}" class="nav-item-link flex flex-col items-center justify-center w-full h-full transition-all duration-150 active:scale-90 {{ request()->routeIs('saved-locations.*') ? 'text-blue-600 font-extrabold' : 'text-gray-400' }}">
                    <i class="ri-bookmark-3-line text-xl leading-none mb-0.5"></i>
                    <span class="text-[9px] font-bold uppercase tracking-wider">Saved</span>
                </a>
                @endif

                <!-- HISTORY -->
                <a href="{{ route('history') }}" class="nav-item-link flex flex-col items-center justify-center w-full h-full transition-all duration-150 active:scale-90 {{ request()->routeIs('history') ? 'text-blue-600 font-extrabold' : 'text-gray-400' }}">
                    <i class="ri-history-line text-xl leading-none mb-0.5"></i>
                    <span class="text-[9px] font-bold uppercase tracking-wider">History</span>
                </a>

                <!-- EARNINGS (Drivers & Admins Only) -->
                @if(Auth::check() && (Auth::user()->role === 'driver' || Auth::user()->role === 'admin'))
                <a href="{{ route('earnings') }}" class="nav-item-link flex flex-col items-center justify-center w-full h-full transition-all duration-150 active:scale-90 {{ request()->routeIs('earnings') ? 'text-blue-600 font-extrabold' : 'text-gray-400' }}">
                    <i class="ri-wallet-3-line text-xl leading-none mb-0.5"></i>
                    <span class="text-[9px] font-bold uppercase tracking-wider">Earnings</span>
                </a>
                @endif

                <!-- ADMIN (Admins Only) -->
                @if(Auth::check() && Auth::user()->role === 'admin')
                <a href="{{ route('drivers.index') }}" class="nav-item-link flex flex-col items-center justify-center w-full h-full transition-all duration-150 active:scale-90 {{ request()->routeIs('drivers.index') ? 'text-blue-600 font-extrabold' : 'text-gray-400' }}">
                    <i class="ri-shield-keyhole-line text-xl leading-none mb-0.5"></i>
                    <span class="text-[9px] font-bold uppercase tracking-wider">Admin</span>
                </a>
                @endif

                <!-- PROFILE -->
                <a href="{{ route('profile.edit') }}" class="nav-item-link flex flex-col items-center justify-center w-full h-full transition-all duration-150 active:scale-90 {{ request()->routeIs('profile.edit') ? 'text-blue-600 font-extrabold' : 'text-gray-400' }}">
                    <i class="ri-user-3-line text-xl leading-none mb-0.5"></i>
                    <span class="text-[9px] font-bold uppercase tracking-wider">Profile</span>
                </a>

            </div>
        </nav>

        <!-- Instant Native SPA Navigation Engine -->
        <script>
            // ---- Global Device Web Push & Native System Notification Engine (Full Emojis + Audio Chime + Vibration) ----
            window.srhNotificationChimeUri = "data:audio/wav;base64,UklGRoQJAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YWAJAACAu+b36L6ESRwKFj53suD06cOMUSMNFTlwqtnw6siTWSoRFTVoodLs6syaYjEVFTFhmcro6tChajgaFi5bkcPj6dOncT8eFytVirze59WteUckGSlPgrXZ5deygE4pGydKe63T49m3h1UvHiZGdabN4Nm7jlw1ISVCbp/H3dq/lGM7JCU+aJjB2dnCmmpBKCY7Y5G71dnFn3FHLCc5XYu10djHpHdNMCg3WYSvzdbJqX5TNSo1VH6pyNTKrYRZOSw0UHmjw9LLsYlfPi40TXOdvs/MtI9lQzEzSm6Xuc3Mt5RrSDQ0R2mRtMnLuZhxTjc0RWSLr8bKu512Uzs1Q2CGqsLJvaF8WD82QVyBpL7IvqSBXUM4QFl8n7rGv6iGYkc6QFZ3mrbEwKuKZ0s8P1NylbLCwK2PbE8/P1FukK6/wLCTcVRBQE9qi6m8v7GXdlhEQE1nh6W5vrOae1xHQUtjgqC2vbSdf2FLQ0pgfpyzvLWgg2VOREpdepivurajh2lSRklbdpOsuLali25VSElZco+otranjnJZSklXb4uktLWpknZdTEpVbIehsbWqlXpgT0tUaYOdr7SsmH5kUkxTZoCZrLOsmoFoVU1SZHyVqbKtnYVrV05SYXmSprCtn4hvWlBSX3aOo66toItzXlJSXnOLoKytoo52YVRSXHCHnaqto5F5ZFZSW22EmqispJN9Z1hTWmuBlqarpZWAalpUWWl+k6SqppeDbV1VWWd7kKGpppmFcF9XWWV4jZ+oppuIc2JYWWR2ipymppyLdmRaWWJzh5mkpp2NeWdbWWFxhJejpZ6PfGpdWmBvgpShpZ+Rf2xfW2Btf5GfpKCTgW9hW19rfY+do6CVhHJjXF9qeoyboqCWhnRlXl9oeIqYoaCXiHdoX19ndoeWn6CYinlqYF9mdIWUnqCZjHxsYl9lcoKSnJ+ajn5uZGBlcICPm5+bj4BxZWFkb36NmZ6bkYJzZ2JkbnyLl52bkoR1aWJkbHqJlZybk4Z3a2Rka3iHk5ublIh5bGVkanaFkpqblYl7bmZkanWDkJiblot9cGdlaYDM9eirWxsKL3jF8eqxYiELK3G+7eu3aicNJ2q26eu8cS0PJWSv5ezBeTQSIl2o4OvFfzoVIFih2+rJhkAYH1Ka1enNjUcbHk2T0OjQk04fHUiMyubSmVQjHUSGxeTUn1soHUCAv+HWpGEsHjx5ud7YqWcxHzlzs9vYrW42ITZurdfZsnQ7IjRop9PZtnpBJDJjoc/ZuX9GJzFem8vYvYVLKi9alcfXv4pRLS9WkMLWwpBWMC5Sir7UxJVcMy5OhbnSxplhNy5LgLTQx55mOy9Ieq/OyKJsPzBFdqrLyaZxQzFDcaXIyql2RzJBbKDFyqx7SzQ/aJvCyq9/UDY+ZJa+ybKEVDg9YJK7yLWJWTs8XY23x7eNXT08WoizxriRYkA8V4SvxLqVZkM8VICrwruYa0Y9UXunwLycb0o9T3ejvr2fc00+TXOfvL2id1E/THCbub2lfFRBSmyXtr2nf1hDSWmTs7ypg1xESGaPsLyrh19GSGOLrbuti2NJSGCHqrqvjmdLR16Dp7iwkWpOSFt/pLexlG5QSFl8oLWyl3JTSVd5nbOymnVWSVZ1mbGynHlZSlVylq+znnxcTFNvk62yoIBfTVNtj6uyooNiT1JqjKixpIZlUFFoiaaxpYloUlFlhqOwp4trVFFjg6CvqI5uVlFhgJ2tqJFxWFFgfZusqZN0W1JeepirqpV3XVNdd5Wpqpd6X1NcdZKnqpl9YlRbco+lqpt/ZFZacI2jqZyCZ1daboqhqZ6FaVhZbIefqJ+HbFpZaoWdqKCJb1tZaIKbp6GMcV1ZZ3+YpqGOdF9ZZX2WpKKQdmFaZHuUo6KSeWNaY3mRoqOTe2VbYnaPoKOVfWdcYXSNn6KWgGldYXOKnaKXgmteYHGIm6KZhG1fYG+GmqGahm9gYG6EmKGaiHFiYGyClqCbinRjYGuAlJ+ci3ZlYGp+kp6cjXhmYGl8kJ2cjnpoYWh6jpydkHxpYWd4jJudkX5rYmZ2ipmckn9tY2Z1iZick4FvZGVzh5eclINwZWVyhZWclYVyZmVxg5SbloZ0Z2VvgZKaloh2aGVugJGal4l3aWVtfo+Zl4t5amZtfI2Yl4x7bGZse4yXmI18bWZreYqWmI5+b2dreImVmI9/cGhqd4eUl5CBcWhqdYWTl5GCc2lqdISRl5GEdGpqc4KQlpKFdmtqcoGPlpKGd2xqcYCOlZOIeW1qcX6MlZOJem5qcH2LlJOKfG9qb3yKk5OLfXBrb3qIkpOMfnFrbnmHkZOMf3NsbniGkJONgXRsbneEj5OOgnVtbXaDjpOOg3ZubXWCjZKPhHdubXSBjJKPhXlvbXSAi5KQhnpwbXN+ipGQh3txbnN9iZCQiHxybnJ8iJCQiX1zbnJ7h4+Qin50b3F6ho6Qin91b3F5hY2Qi4F2cHF5hI2Qi4J3cHF4g4yPjIJ4cXB3gYuPjIN5cXB2gIqPjYR6cnB2gImOjYV7c3F1f4iOjYZ8c3F1foeNjYd9dHF0fYaNjYd+dXF0fIaMjYh/dnJ0e4WMjYh/d3JzeoSLjYmAd3JzeoOKjYmBeHNzeYKKjYqCeXNzeIGJjYqDenRzeICIjIqDe3Rzd4CIjIuEfHVzd3+HjIuFfHZzd36Gi4uFfXZzdn2Fi4uGfnd0dn2FiouGf3d0dnyEiouHgHh0dXuDiYuHgHl1dXuCiYuIgXp1dXqCiIqIgnp1dXqBh4qIgnt2dXmAh4qIg3x2dXmAhoqJg3x3dXh/homJhH13dXh+hYmJhH54dXh+hImJhX54dnh9hIiJhX95dnd9g4iJhn95dnd8goeJhoB6dnd8goeJhoF7d3d7gYeJh4F7d3d7gYaIh4J8d3d6gIaIh4J8eHd6gIWIh4N9eHd6f4WIh4N9eXd5foSHh4R+eXd5foOHh4R+end5fYOHh4R/enh5fYKGh4V/enh5fYKGh4WAe3h4fIGGh4WAe3h4fIGFh4WBfHl4e4CFh4WBfHl4e4CFh4aCfXl4e4CEh4aCfXl4e3+EhoaCfnp5en+DhoaDfnp5en6DhoaDf3t5en6ChoaDf3t5en2ChYaEgHt5en2ChYaEgHx5en2BhYaEgHx5enyBhIaEgX16enyAhIaEgX16enyAhIaFgX16enw=";

            window.srhNotificationChimeFallbackUri = window.srhNotificationChimeUri;
            // Custom sound files are NOT used: the OS notification plays the
            // phone/Chrome default sound instead. Keep the built-in chime only
            // as the iOS fallback (iOS cannot show system banners in-app).
            window.srhNotificationChimeUri = window.srhNotificationChimeFallbackUri;

            window.srhStripEmojis = function(str) {
                if (!str) return '';
                return str.replace(/[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{1F700}-\u{1F77F}\u{1F780}-\u{1F7FF}\u{1F800}-\u{1F8FF}\u{1F900}-\u{1F9FF}\u{1FA00}-\u{1FA6F}\u{1FA70}-\u{1FAFF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}]/gu, '').trim();
            };

            window.srhPlayNotificationSound = function() {
                // Built-in chime — only used on iOS, where the OS cannot play a
                // notification sound while the app is open. Everywhere else the
                // phone/Chrome DEFAULT notification sound is used instead.

                try {
                    const soundEnabled = localStorage.getItem('srh_sound_enabled') !== 'false';
                    if (!soundEnabled) return;

                    // On Chrome/Android a "hidden" page may still be the only way
                    // to ring a sound (see srhTriggerSystemNotification) — so the
                    // hidden-suppression decision belongs to the caller, not here.

                    // 1. Web Audio API High-Precision Synthesizer
                    // Only create the AudioContext after a user gesture — browsers
                    // block it before interaction and log a console warning otherwise.
                    const AudioCtx = window.AudioContext || window.webkitAudioContext;
                    if (AudioCtx && window._srhUserHasInteracted) {
                        const ctx = new AudioCtx();
                        if (ctx.state === 'suspended') ctx.resume();
                        const now = ctx.currentTime;

                        // Tone 1: E5 (659.25 Hz)
                        const osc1 = ctx.createOscillator();
                        const gain1 = ctx.createGain();
                        osc1.type = 'sine';
                        osc1.frequency.setValueAtTime(659.25, now);
                        gain1.gain.setValueAtTime(0, now);
                        gain1.gain.linearRampToValueAtTime(0.35, now + 0.02);
                        gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
                        osc1.connect(gain1);
                        gain1.connect(ctx.destination);
                        osc1.start(now);
                        osc1.stop(now + 0.35);

                        // Tone 2: A5 (880.00 Hz)
                        const osc2 = ctx.createOscillator();
                        const gain2 = ctx.createGain();
                        osc2.type = 'sine';
                        osc2.frequency.setValueAtTime(880.00, now + 0.1);
                        gain2.gain.setValueAtTime(0, now + 0.1);
                        gain2.gain.linearRampToValueAtTime(0.4, now + 0.13);
                        gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.55);
                        osc2.connect(gain2);
                        gain2.connect(ctx.destination);
                        osc2.start(now + 0.1);
                        osc2.stop(now + 0.55);

                        setTimeout(() => { try { ctx.close(); } catch(e){} }, 800);
                        return;
                    }
                } catch(e) {}

                // 2. HTML5 Audio Data URI Fallback
                try {
                    const audio = new Audio(window.srhNotificationChimeUri);
                    audio.volume = 0.8;
                    const p = audio.play();
                    if (p && typeof p.catch === 'function') p.catch(() => {});
                } catch(e) {}
            };

            window.srhTriggerSystemNotification = function(title, body, options) {
                options = options || {};
                
                // Keep emojis intact for display in app & outside phone notifications
                const displayTitle = (title || 'SRH LINK-TODA').trim();
                const displayBody = (body || 'You have a new update.').trim();

                // Remember what we showed, so the service-worker push handler can
                // suppress its duplicate banner (same title+body) via postMessage.
                const sig = displayTitle + '|' + displayBody;
                window.__srhNotifSeen = window.__srhNotifSeen || new Map();
                window.__srhNotifSeen.set(sig, Date.now());
                if (window.__srhNotifSeen.size > 50) {
                    const now = Date.now();
                    for (const [key, ts] of window.__srhNotifSeen) {
                        if (now - ts > 120000) window.__srhNotifSeen.delete(key);
                    }
                }

                const isHidden = typeof document !== 'undefined' && document.visibilityState === 'hidden';

                // 1. In-App Alert Sound — plays whether the app is open or
                //    backgrounded, so Sound mode always rings. Android web
                //    notifications are silent by default (the site's channel is
                //    muted until enabled in phone settings), so this chime
                //    guarantees an audible alert while the browser is running.
                //    Toggle it off in Profile → Permissions → In-App Alert Sound.
                window.srhPlayNotificationSound();

                window.srhSafeVibrate = function(pattern) {
                    try {
                        if (typeof navigator !== 'undefined' && 'vibrate' in navigator) {
                            if (navigator.userActivation && !navigator.userActivation.hasBeenActive) return;
                            navigator.vibrate(pattern);
                        }
                    } catch(e) {}
                };

                // 2. In-App Haptic Vibration — foreground only (the OS banner
                //    also carries its own vibrate pattern for hidden apps).
                const vibrationEnabled = localStorage.getItem('srh_vibration_enabled') !== 'false';
                if (!isHidden && vibrationEnabled) {
                    window.srhSafeVibrate([200, 100, 200]);
                }

                // iOS Safari has no page-level Notification API — nothing more
                // to show there.
                if (!('Notification' in window)) return;

                // 3. System Push Notification — open, minimized, or closed.
                //    Always non-silent so the phone/Chrome DEFAULT notification
                //    sound rings wherever the channel allows it.
                const notifEnabled = localStorage.getItem('srh_notif_enabled') !== 'false';
                if (!notifEnabled) return;

                const notifTag = options.tag || ('srh-notif-' + Date.now());
                const notifOptions = {
                    body: displayBody,
                    icon: '{{ srh_logo_url() }}',
                    badge: '{{ srh_logo_url() }}',
                    silent: false,
                    tag: notifTag,
                    requireInteraction: false,
                    vibrate: [200, 100, 200]
                };
                // Same-tag replacements replay the default sound + vibration on
                // every update instead of only on the first one.
                if (options.tag) {
                    notifOptions.renotify = true;
                }
                // Note: the `sound` Notification option is unsupported in Chrome
                // (removed on Android 8+) — the Android notification channel
                // governs the sound, so long-press a notification → Settings →
                // enable Sound for this site once to ring it even when closed.

                function showNativeNotification() {
                    try {
                        const notif = new Notification(displayTitle, notifOptions);
                        notif.onclick = function() {
                            window.focus();
                            if (options.url) {
                                if (window.navigateTo) window.navigateTo(options.url);
                                else window.location.href = options.url;
                            }
                        };
                    } catch(err) {}
                }

                // Service-worker notifications are required on Android Chrome and
                // iOS home-screen apps, and they survive tab switches. Fall back to
                // the page-level Notification API on desktop browsers.
                if (Notification.permission === 'granted') {
                    if (navigator.serviceWorker && navigator.serviceWorker.ready) {
                        navigator.serviceWorker.ready.then(reg => {
                            if (reg && reg.showNotification) {
                                reg.showNotification(displayTitle, Object.assign({}, notifOptions, {
                                    data: { url: options.url || window.location.href }
                                }));
                            } else {
                                showNativeNotification();
                            }
                        }).catch(() => showNativeNotification());
                    } else {
                        showNativeNotification();
                    }
                } else if (Notification.permission !== 'denied') {
                    try {
                        const reqPerm = Notification.requestPermission();
                        if (reqPerm && typeof reqPerm.then === 'function') {
                            reqPerm.then(permission => {
                                if (permission === 'granted') {
                                    window.srhTriggerSystemNotification(title, body, options);
                                }
                            }).catch(() => {});
                        }
                    } catch(e) {}
                }
            };

            // ---- Seamless Live Queue DOM Merger (no full innerHTML rewrites) ----
            // Diff-merges a freshly polled queue-list fragment into the live wrapper,
            // preserving existing row elements, transitions, scroll and Alpine state.
            // Returns true when the row structure changed (caller re-inits sortable).
            window.srhMergeLiveQueue = function(wrapper, newHtml) {
                if (!wrapper || !newHtml) return false;
                try {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(newHtml, 'text/html');
                    const newWrapper = doc.getElementById('live-queue-wrapper');
                    if (!newWrapper) return false;

                    let structureChanged = false;

                    // --- Section 1: Active Terminal Queue ---
                    const oldSection = wrapper.firstElementChild;
                    const newSection = newWrapper.firstElementChild;
                    if (oldSection && newSection) {
                        // Header queue-count badge (in-place text swap)
                        const oldCount = oldSection.querySelector('.flex.items-center.justify-between span.rounded-full');
                        const newCount = newSection.querySelector('.flex.items-center.justify-between span.rounded-full');
                        if (oldCount && newCount && oldCount.textContent.trim() !== newCount.textContent.trim()) {
                            oldCount.textContent = newCount.textContent;
                        }

                        const oldItems = oldSection.querySelector('#queue-items-container');
                        const newItems = newSection.querySelector('#queue-items-container');
                        if (oldItems && newItems) {
                            const oldRows = new Map();
                            oldItems.querySelectorAll(':scope > [data-driver-id]').forEach(el => oldRows.set(el.dataset.driverId, el));

                            const newHasRows = Array.from(newItems.children).some(el => el.dataset && el.dataset.driverId);

                            if (!newHasRows) {
                                // Queue became empty: clear rows, show the empty-state block
                                if (oldRows.size > 0) {
                                    oldItems.innerHTML = '';
                                    const emptyClone = newItems.firstElementChild ? document.importNode(newItems.firstElementChild, true) : null;
                                    if (emptyClone) oldItems.appendChild(emptyClone);
                                    structureChanged = true;
                                }
                            } else {
                                // Drop any stale empty-state block before adding rows:
                                // an "No drivers waiting" note left behind while the
                                // queue gained drivers would sit above the new rows.
                                Array.from(oldItems.children).forEach(el => {
                                    if (!el.dataset || !el.dataset.driverId) el.remove();
                                });

                                const seen = new Set();
                                Array.from(newItems.children).forEach(newRow => {
                                    if (!newRow.dataset || !newRow.dataset.driverId) return;
                                    const id = newRow.dataset.driverId;
                                    seen.add(id);
                                    let row = oldRows.get(id);
                                    if (!row) {
                                        // New driver joined the queue — clone the fresh row
                                        row = document.importNode(newRow, true);
                                        oldItems.appendChild(row);
                                        structureChanged = true;
                                    } else {
                                        // Seamless in-place queue-number badge update
                                        const oldBadge = row.querySelector('.queue-number-badge');
                                        const newBadge = newRow.querySelector('.queue-number-badge');
                                        if (oldBadge && newBadge) {
                                            const oldText = oldBadge.textContent.trim();
                                            const newText = newBadge.textContent.trim();
                                            if (oldText !== newText || oldBadge.className !== newBadge.className) {
                                                oldBadge.textContent = newText;
                                                oldBadge.className = newBadge.className;
                                                oldBadge.classList.remove('badge-pop');
                                                void oldBadge.offsetWidth;
                                                oldBadge.classList.add('badge-pop');
                                            }
                                        }
                                        // appendChild re-orders the kept element to match server order
                                        oldItems.appendChild(row);
                                    }
                                });
                                // Remove drivers that left the queue
                                oldItems.querySelectorAll(':scope > [data-driver-id]').forEach(el => {
                                    if (!seen.has(el.dataset.driverId)) {
                                        el.remove();
                                        structureChanged = true;
                                    }
                                });
                            }
                        }
                    }

                    // --- Section 2: Drivers On Trip (row-keyed diff merge: in-place status updates) ---
                    const oldSecond = wrapper.children[1];
                    const newSecond = newWrapper.children[1];
                    if (oldSecond && newSecond) {
                        const oldTripCount = oldSecond.querySelector('.flex.items-center.justify-between span.rounded-full');
                        const newTripCount = newSecond.querySelector('.flex.items-center.justify-between span.rounded-full');
                        if (oldTripCount && newTripCount && oldTripCount.textContent.trim() !== newTripCount.textContent.trim()) {
                            oldTripCount.textContent = newTripCount.textContent;
                        }

                        const oldTripItems = oldSecond.querySelector('#on-trip-items-container');
                        const newTripItems = newSecond.querySelector('#on-trip-items-container');
                        if (oldTripItems && newTripItems) {
                            const oldRides = new Map();
                            oldTripItems.querySelectorAll(':scope > [data-ride-id]').forEach(el => oldRides.set(el.dataset.rideId, el));

                            const newHasTrips = Array.from(newTripItems.children).some(el => el.dataset && el.dataset.rideId);

                            if (!newHasTrips) {
                                // No active trips: if rows existed, swap in the fresh empty-state block
                                if (oldRides.size > 0) {
                                    oldTripItems.innerHTML = '';
                                    const emptyClone = newTripItems.firstElementChild ? document.importNode(newTripItems.firstElementChild, true) : null;
                                    if (emptyClone) oldTripItems.appendChild(emptyClone);
                                    structureChanged = true;
                                }
                            } else {
                                // Drop any stale empty-state block before adding rows
                                Array.from(oldTripItems.children).forEach(el => {
                                    if (!el.dataset || !el.dataset.rideId) el.remove();
                                });

                                const seenRides = new Set();
                                Array.from(newTripItems.children).forEach(newRow => {
                                    if (!newRow.dataset || !newRow.dataset.rideId) return;
                                    const rideId = newRow.dataset.rideId;
                                    seenRides.add(rideId);
                                    let row = oldRides.get(rideId);
                                    if (!row) {
                                        row = document.importNode(newRow, true);
                                        oldTripItems.appendChild(row);
                                        structureChanged = true;
                                    } else {
                                        const oldBadge = row.querySelector('.queue-trip-badge');
                                        const newBadge = newRow.querySelector('.queue-trip-badge');
                                        if (oldBadge && newBadge) {
                                            const textChanged = oldBadge.textContent.trim() !== newBadge.textContent.trim();
                                            const classChanged = oldBadge.className !== newBadge.className;
                                            if (textChanged || classChanged) {
                                                oldBadge.textContent = newBadge.textContent;
                                                oldBadge.className = newBadge.className;
                                            }
                                        }
                                        oldTripItems.appendChild(row);
                                    }
                                });

                                // Remove trips that finished
                                oldTripItems.querySelectorAll(':scope > [data-ride-id]').forEach(el => {
                                    if (!seenRides.has(el.dataset.rideId)) {
                                        el.remove();
                                        structureChanged = true;
                                    }
                                });
                            }
                        }
                    }

                    return structureChanged;
                } catch (e) {
                    return false;
                }
            };

            (function() {
                const progressBar = document.getElementById('spa-progress-bar');
                const contentArea = document.getElementById('spa-content-area');

                let currentFetchController = null;
                const pageCache = new Map();
                const inFlightFetches = new Map(); // Shared Promise Map to guarantee strictly ONE fetch per URL
                const CACHE_TTL_MS = 600000; // 10 minutes Event-Driven SPA Cache (Refreshed via WebSockets & pull-to-refresh)

                // ---- Keep-Alive Vault: zero-refetch, zero-flicker map persistence ----
                // Pages listed here park their ENTIRE DOM subtree (including the live
                // MapLibre WebGL canvas, markers & sheets) into #srh-spa-vault instead of
                // being destroyed on tab switch. Scripts are NEVER re-executed on return,
                // so no marker (tricycle icon) can ever be duplicated. GPS watch, Echo
                // subscriptions and pollers keep running into the parked DOM, so the
                // restored page is always fully up-to-date.
                const KEEPALIVE_PATHS = ['/dashboard'];
                const spaVault = document.getElementById('srh-spa-vault');
                let vaultedPath = null;
                let vaultedTitle = '';
                let vaultedScrollY = 0;

                // <style> tags keep applying document-wide even while their page sits
                // parked inside the vault (e.g. the driver hub's
                // html,body{overflow:hidden!important}). Disable them on park so other
                // tabs scroll normally, and re-enable on restore.
                function srhSetVaultStylesDisabled(disabled) {
                    if (disabled) {
                        if (!spaVault) return;
                        spaVault.querySelectorAll('style').forEach(s => {
                            if (!s.hasAttribute('data-srh-media')) s.setAttribute('data-srh-media', s.media || '');
                            s.media = 'not all';
                        });
                    } else {
                        document.querySelectorAll('style[data-srh-media]').forEach(s => {
                            const origMedia = s.getAttribute('data-srh-media');
                            if (origMedia) {
                                s.media = origMedia;
                            } else {
                                s.removeAttribute('media');
                            }
                            s.removeAttribute('data-srh-media');
                        });
                    }
                }

                function srhNormPath(url) {
                    try {
                        let p = new URL(url, window.location.origin).pathname;
                        return (p.length > 1 && p.endsWith('/')) ? p.slice(0, -1) : p;
                    } catch (e) { return null; }
                }

                function srhVaultHas(url) {
                    const p = srhNormPath(url);
                    return !!p && vaultedPath === p && !!spaVault && spaVault.childNodes.length > 0;
                }
                window.srhVaultHas = srhVaultHas;

                function srhParkCurrentPage() {
                    if (!spaVault || vaultedPath || !contentArea) return false;
                    const fromPath = srhNormPath(window.location.href);
                    if (!fromPath || KEEPALIVE_PATHS.indexOf(fromPath) === -1) return false;
                    // Park the driver map hub or the passenger map hub (both keep their
                    // live WebGL canvas, markers and sheets alive across tab switches)
                    if (!contentArea.querySelector('#grab-home-map') && !contentArea.querySelector('#pax-home-map')) return false;
                    vaultedTitle = document.title;
                    vaultedScrollY = window.scrollY || 0;
                    while (contentArea.firstChild) spaVault.appendChild(contentArea.firstChild);
                    vaultedPath = fromPath;
                    window.__srhVaultActive = true;
                    srhSetVaultStylesDisabled(true);
                    window.dispatchEvent(new CustomEvent('srh:page-parked', { detail: { path: fromPath } }));
                    return true;
                }

                function srhDiscardVault() {
                    if (!vaultedPath) return;
                    if (window._grabHomeMapInstance) {
                        try { window._grabHomeMapInstance.remove(); } catch (e) {}
                        window._grabHomeMapInstance = null;
                    }
                    if (window.paxHomeMapInstance) {
                        try { window.paxHomeMapInstance.remove(); } catch (e) {}
                        window.paxHomeMapInstance = null;
                    }
                    if (window.bookingMapInstance) {
                        try { window.bookingMapInstance.remove(); } catch (e) {}
                        window.bookingMapInstance = null;
                    }
                    if (window._paxDriverMarker) {
                        try { window._paxDriverMarker.remove(); } catch (e) {}
                        window._paxDriverMarker = null;
                    }
                    if (window._driverWatchPositionId && navigator.geolocation) {
                        try { navigator.geolocation.clearWatch(window._driverWatchPositionId); } catch (e) {}
                        window._driverWatchPositionId = null;
                    }
                    if (window._geofencePollerInterval) {
                        clearInterval(window._geofencePollerInterval);
                        window._geofencePollerInterval = null;
                    }
                    if (window._driverActiveTripLocationTimer) {
                        clearInterval(window._driverActiveTripLocationTimer);
                        clearTimeout(window._driverActiveTripLocationTimer);
                        window._driverActiveTripLocationTimer = null;
                    }
                    srhSetVaultStylesDisabled(false);
                    if (spaVault) spaVault.innerHTML = '';
                    vaultedPath = null;
                    window.__srhVaultActive = false;
                }

                function srhRestoreVault(targetFullUrl, pushState) {
                    if (!srhVaultHas(targetFullUrl) || !contentArea) return false;
                    if (currentFetchController) {
                        try { currentFetchController.abort(); } catch (e) {}
                        currentFetchController = null;
                    }
                    // Tear down whatever the OUTGOING page started (timers, pax maps…).
                    // Vault-owned driver resources are protected while __srhVaultActive.
                    clearPageIntervals();
                    updateActiveNavHighlights(targetFullUrl);
                    contentArea.innerHTML = '';
                    while (spaVault.firstChild) contentArea.appendChild(spaVault.firstChild);
                    srhSetVaultStylesDisabled(false);
                    const restoredPath = vaultedPath;
                    vaultedPath = null;
                    window.__srhVaultActive = false;
                    document.title = vaultedTitle || document.title;
                    if (pushState) window.history.pushState({ spa: true, restored: true }, '', targetFullUrl);
                    window.scrollTo({ top: vaultedScrollY, behavior: 'instant' });
                    // Re-measure the re-attached canvas WITHOUT rebuilding it (no tile
                    // refetch, no WebGL context churn, no flicker).
                    requestAnimationFrame(() => {
                        try { if (window._grabHomeMapInstance) window._grabHomeMapInstance.resize(); } catch (e) {}
                        try { if (window.paxHomeMapInstance) window.paxHomeMapInstance.resize(); } catch (e) {}
                        // Safety net: if a map never finished booting before the page
                        // was parked, boot it now that its container is live again.
                        if (!window._grabHomeMapInstance && typeof window.initGrabHomeMap === 'function' && document.getElementById('grab-home-map')) {
                            try { window.initGrabHomeMap(); } catch (e) {}
                        }
                        if (!window.paxHomeMapInstance && typeof window.initPaxHomeMapGlobal === 'function' && document.getElementById('pax-home-map')) {
                            try { window.initPaxHomeMapGlobal(); } catch (e) {}
                        }
                        try { if (typeof window.syncHomeMapPadding === 'function') window.syncHomeMapPadding(); } catch (e) {}
                        try { if (typeof window.syncPaxMapPadding === 'function') window.syncPaxMapPadding(); } catch (e) {}
                        try { if (typeof window.syncFloatingButtonsPosition === 'function') window.syncFloatingButtonsPosition(); } catch (e) {}
                        try { if (typeof window.syncPaxFloatingButtons === 'function') window.syncPaxFloatingButtons(); } catch (e) {}
                    });
                    setTimeout(() => {
                        try { if (window._grabHomeMapInstance) window._grabHomeMapInstance.resize(); } catch (e) {}
                        try { if (window.paxHomeMapInstance) window.paxHomeMapInstance.resize(); } catch (e) {}
                    }, 150);
                    window.dispatchEvent(new CustomEvent('spa:page-restored', { detail: { path: restoredPath, url: targetFullUrl } }));
                    try {
                        const __u = new URL(targetFullUrl, window.location.origin);
                        if (__u.searchParams.get('dest_lat') && typeof window.checkAndApplyUrlDestinationParams === 'function') {
                            setTimeout(window.checkAndApplyUrlDestinationParams, 60);
                            setTimeout(window.checkAndApplyUrlDestinationParams, 250);
                            setTimeout(window.checkAndApplyUrlDestinationParams, 600);
                        }
                    } catch(e) {}
                    finishProgressBar();
                    return true;
                }
                window.srhDiscardVault = srhDiscardVault;
                // ---- End Keep-Alive Vault ----

                function normalizeSpaUrl(url) {
                    try {
                        const u = new URL(url, window.location.origin);
                        let p = u.pathname;
                        if (p.length > 1 && p.endsWith('/')) p = p.slice(0, -1);
                        return u.origin + p + (u.search || '');
                    } catch(e) {
                        return url;
                    }
                }

                function isDynamicPath(url) {
                    try {
                        const path = new URL(url, window.location.origin).pathname;
                        return path.startsWith('/rides/active') || path.startsWith('/chat');
                    } catch(e) {
                        return false;
                    }
                }

                function fetchUrlHtml(fullUrl, signal) {
                    const normUrl = normalizeSpaUrl(fullUrl);
                    if (!isDynamicPath(normUrl)) {
                        const cached = pageCache.get(normUrl);
                        if (cached && (Date.now() - cached.timestamp < CACHE_TTL_MS)) {
                            return Promise.resolve(cached.html);
                        }
                    }

                    if (inFlightFetches.has(normUrl)) {
                        return inFlightFetches.get(normUrl);
                    }

                    const p = fetch(normUrl, {
                        signal: signal,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-SPA-Request': 'true'
                        }
                    })
                    .then(async res => {
                        if (!res.ok || res.url.includes('/login') || res.url.includes('/register')) {
                            pageCache.clear();
                            window.location.href = res.url || normUrl;
                            return null;
                        }
                        const text = await res.text();
                        if (text && !isDynamicPath(normUrl)) {
                            pageCache.set(normUrl, { html: text, timestamp: Date.now() });
                        }
                        return text;
                    })
                    .catch(err => {
                        return null;
                    })
                    .finally(() => {
                        inFlightFetches.delete(normUrl);
                    });

                    inFlightFetches.set(normUrl, p);
                    return p;
                }

                function prefetchUrl(url) {
                    if (!url || url.startsWith('#') || url.startsWith('javascript:')) return;
                    try {
                        const fullUrl = new URL(url, window.location.origin).href;
                        const prefetchUrlObj = new URL(fullUrl);
                        if (prefetchUrlObj.origin !== window.location.origin) return;
                        if (isDynamicPath(fullUrl)) return;
                        if (/\.(png|jpe?g|gif|webp|svg|bmp|avif|ico|pdf|docx?|xlsx?|pptx?|zip|rar|7z|tar|gz|mp4|mp3|webm)$/i.test(prefetchUrlObj.pathname)) return;

                        fetchUrlHtml(fullUrl);
                    } catch (e) {}
                }

                // Instant Speculative Prefetching only on Pointerdown / Touchstart
                document.addEventListener('pointerdown', function(e) {
                    const link = e.target.closest('a.nav-item-link, a.desktop-nav-link, a[data-prefetch]');
                    if (link && link.href) prefetchUrl(link.href);
                }, { passive: true });

                document.addEventListener('touchstart', function(e) {
                    const link = e.target.closest('a.nav-item-link, a.desktop-nav-link, a[data-prefetch]');
                    if (link && link.href) prefetchUrl(link.href);
                }, { passive: true });

                let isSpaNavigating = false;
                let progressBarTimer = null;

                function getSkeletonHtml(url) {
                    let path = '';
                    try { 
                        path = new URL(url, window.location.origin).pathname;
                        if (path.length > 1 && path.endsWith('/')) path = path.slice(0, -1);
                    } catch(e){}

                    // Dashboard / Home (Clean Colorless Neutral Shimmer Containers)
                    if (path === '/dashboard' || path === '/' || path === '') {
                        return `
                            <div class="relative w-full h-[100dvh] bg-[#f4f3f0] overflow-hidden select-none animate-fade-in" style="height: 100dvh; min-height: 100dvh;">
                                <!-- Top Floating Header (Exact 1:1 markup) -->
                                <div class="absolute z-[100] pointer-events-none flex items-center justify-between gap-3 pt-safe" style="top: max(0.75rem, env(safe-area-inset-top, 0px)); left: 14px; right: 14px; width: calc(100% - 28px);">
                                    <div class="pointer-events-auto relative block w-11 h-11 rounded-full bg-white/95 backdrop-blur-md p-0.5 border-2 border-white shadow-xl srh-shimmer"></div>
                                    <div class="pointer-events-auto flex items-center gap-2">
                                        <div class="flex items-center gap-1.5 px-3 py-2 rounded-full bg-white/95 backdrop-blur-md border border-slate-200/90 shadow-xl srh-shimmer" style="width: 62px; height: 36px;"></div>
                                        <div class="flex items-center justify-center w-11 h-11 rounded-full bg-white/95 backdrop-blur-md border border-slate-200/90 shadow-xl srh-shimmer"></div>
                                    </div>
                                </div>

                                <!-- Floating Power Toggle (Exact 1:1 markup & elevation) -->
                                <div id="floating-power-container" class="absolute z-30 flex flex-col items-start gap-2 floating-action-btn" style="bottom: 315px; left: 16px;">
                                    <div class="relative bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-2xl border border-slate-200 shadow-md grab-tooltip-arrow flex items-center gap-1.5 grab-callout-float">
                                        <div class="h-3 w-20 rounded-full bg-slate-200 srh-shimmer"></div>
                                    </div>
                                    <div class="relative flex items-center justify-center w-16 h-16 rounded-full border-4 border-white shadow-xl bg-slate-200 srh-shimmer"></div>
                                </div>

                                <!-- Floating Map Controls Stack (Exact 1:1 markup & elevation) -->
                                <div id="floating-map-controls-container" class="absolute z-30 flex flex-col gap-2.5 floating-action-btn" style="bottom: 315px; right: 16px;">
                                    <div class="w-10 h-10 rounded-2xl bg-white/95 shadow-md border border-slate-200 flex items-center justify-center srh-shimmer"></div>
                                    <div class="w-10 h-10 rounded-2xl bg-white/95 shadow-md border border-slate-200 flex items-center justify-center srh-shimmer"></div>
                                </div>

                                <!-- Unified Life360 / Grab Bottom Sheet (Exact 1:1 markup & transform) -->
                                <div id="driver-bottom-sheet" class="fixed inset-x-0 top-0 z-[9999] srh-edge-bottom-sheet px-3 sm:px-6 pt-1.5 pb-16 shadow-2xl pointer-events-auto border-t border-slate-200/90 border-x-0 border-b-0 bg-white flex flex-col relative after:content-[''] after:absolute after:top-full after:inset-x-0 after:h-[1000px] after:bg-white after:pointer-events-none"
                                     style="height: 100dvh; min-height: 100dvh; transform: translate3d(0, calc(100dvh - 300px), 0);">
                                    <div id="sheet-drag-handle" class="w-full pt-0.5 pb-1 flex flex-col items-center justify-center cursor-pointer touch-none select-none group pointer-events-auto">
                                        <div class="w-12 h-1 bg-slate-300 rounded-full mb-1"></div>
                                        <div class="w-full flex items-center justify-between px-1 gap-1 min-w-0">
                                            <div class="flex items-center gap-1.5 min-w-0 flex-1">
                                                <div class="w-2.5 h-2.5 rounded-full bg-slate-300 srh-shimmer shrink-0"></div>
                                                <div class="h-3.5 w-28 rounded-full bg-slate-200 srh-shimmer"></div>
                                            </div>
                                            <div class="flex items-center gap-1 shrink-0">
                                                <div class="h-6 w-24 rounded-full bg-slate-200 srh-shimmer"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="sheet-details-content" class="flex-1 pb-36 overscroll-contain w-full" style="opacity: 1; margin-top: 1px; pointer-events: auto; overflow-y: hidden; overflow-x: hidden;">
                                        <div id="queue-card-container" class="sticky top-0 z-30 bg-white pt-1 pb-2 shadow-xs rounded-2xl">
                                            <div class="rounded-2xl p-4 text-center shadow-md relative overflow-hidden bg-slate-100/90 border border-slate-200/80 srh-shimmer" style="height: 165px; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box;">
                                               
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                    }

                    // Profile
                    if (path.includes('profile')) {
                        return `
                            <div class="max-w-md mx-auto px-4 pt-4 pb-24 space-y-3.5 animate-fade-in">
                                <div class="p-5 rounded-3xl bg-white border border-slate-100 shadow-sm flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-full srh-shimmer shrink-0 border-2 border-slate-100"></div>
                                    <div class="flex-1 space-y-2">
                                        <div class="h-5 w-36 rounded-xl srh-shimmer"></div>
                                        <div class="h-3.5 w-44 rounded-lg srh-shimmer"></div>
                                    </div>
                                </div>
                                <div class="p-4 rounded-2xl bg-white border border-slate-100 shadow-sm space-y-3">
                                    <div class="h-4 w-32 rounded-lg srh-shimmer"></div>
                                    <div class="h-11 w-full rounded-xl srh-shimmer"></div>
                                    <div class="h-11 w-full rounded-xl srh-shimmer"></div>
                                </div>
                                <div class="p-4 rounded-2xl bg-white border border-slate-100 shadow-sm space-y-3">
                                    <div class="h-11 w-full rounded-xl srh-shimmer"></div>
                                </div>
                            </div>`;
                    }

                    // Earnings
                    if (path.includes('earnings')) {
                        return `
                            <div class="max-w-md mx-auto px-4 pt-4 pb-24 space-y-3.5 animate-fade-in">
                                <div class="p-6 rounded-3xl srh-shimmer shadow-md space-y-3">
                                    <div class="h-3.5 w-28 rounded-full bg-white/50"></div>
                                    <div class="h-9 w-44 rounded-xl bg-white/70"></div>
                                    <div class="h-3.5 w-36 rounded-full bg-white/40"></div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="p-4 rounded-2xl bg-white border border-slate-100 shadow-sm space-y-2">
                                        <div class="h-3 w-20 rounded-md srh-shimmer"></div>
                                        <div class="h-6 w-24 rounded-lg srh-shimmer"></div>
                                    </div>
                                    <div class="p-4 rounded-2xl bg-white border border-slate-100 shadow-sm space-y-2">
                                        <div class="h-3 w-20 rounded-md srh-shimmer"></div>
                                        <div class="h-6 w-24 rounded-lg srh-shimmer"></div>
                                    </div>
                                </div>
                                <div class="p-4 rounded-2xl bg-white border border-slate-100 shadow-sm space-y-3">
                                    <div class="h-4 w-32 rounded-lg srh-shimmer"></div>
                                    <div class="h-10 w-full rounded-xl srh-shimmer"></div>
                                    <div class="h-10 w-full rounded-xl srh-shimmer"></div>
                                </div>
                            </div>`;
                    }

                    // History
                    if (path.includes('history')) {
                        return `
                            <div class="max-w-md mx-auto px-4 pt-4 pb-24 space-y-3.5 animate-fade-in">
                                <div class="h-5 w-24 rounded-full srh-shimmer mb-1"></div>
                                <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-4 space-y-3">
                                    <div class="flex justify-between items-center pb-2 border-b border-slate-50">
                                        <div class="h-5 w-24 rounded-full srh-shimmer"></div>
                                        <div class="h-5 w-16 rounded-md srh-shimmer"></div>
                                    </div>
                                    <div class="space-y-2.5 pl-2">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-400 shrink-0"></div>
                                            <div class="h-3.5 w-48 rounded-md srh-shimmer"></div>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-2.5 h-2.5 rounded-full bg-rose-400 shrink-0"></div>
                                            <div class="h-3.5 w-40 rounded-md srh-shimmer"></div>
                                        </div>
                                    </div>
                                    <div class="pt-2 border-t border-slate-50 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full srh-shimmer shrink-0"></div>
                                            <div class="h-3 w-24 rounded-md srh-shimmer"></div>
                                        </div>
                                        <div class="h-3 w-16 rounded-md srh-shimmer"></div>
                                    </div>
                                </div>
                                <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-4 space-y-3">
                                    <div class="flex justify-between items-center pb-2 border-b border-slate-50">
                                        <div class="h-5 w-24 rounded-full srh-shimmer"></div>
                                        <div class="h-5 w-16 rounded-md srh-shimmer"></div>
                                    </div>
                                    <div class="space-y-2.5 pl-2">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-400 shrink-0"></div>
                                            <div class="h-3.5 w-48 rounded-md srh-shimmer"></div>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-2.5 h-2.5 rounded-full bg-rose-400 shrink-0"></div>
                                            <div class="h-3.5 w-40 rounded-md srh-shimmer"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                    }

                    // Saved Places
                    if (path.includes('saved-locations')) {
                        return `
                            <div class="max-w-md mx-auto px-4 pt-4 pb-24 space-y-3.5 animate-fade-in">
                                <div class="p-4 rounded-3xl bg-white border border-slate-100 shadow-sm space-y-3">
                                    <div class="h-4 w-32 rounded-lg srh-shimmer"></div>
                                    <div class="flex gap-2">
                                        <div class="h-10 flex-1 rounded-xl srh-shimmer"></div>
                                        <div class="h-10 flex-1 rounded-xl srh-shimmer"></div>
                                        <div class="h-10 flex-1 rounded-xl srh-shimmer"></div>
                                    </div>
                                </div>
                                <div class="p-4 rounded-2xl bg-white border border-slate-100 shadow-sm space-y-3">
                                    <div class="h-12 w-full rounded-xl srh-shimmer"></div>
                                    <div class="h-12 w-full rounded-xl srh-shimmer"></div>
                                    <div class="h-12 w-full rounded-xl srh-shimmer"></div>
                                </div>
                            </div>`;
                    }

                    // Generic Fallback
                    return `
                        <div class="max-w-md mx-auto px-4 pt-4 pb-24 space-y-3.5 animate-fade-in">
                            <div class="p-5 rounded-3xl bg-white border border-slate-100 shadow-sm space-y-2.5">
                                <div class="h-4 w-36 rounded-lg srh-shimmer"></div>
                                <div class="h-3 w-56 rounded-md srh-shimmer"></div>
                            </div>
                            <div class="p-4 rounded-2xl bg-white border border-slate-100 shadow-sm space-y-3">
                                <div class="h-12 w-full rounded-xl srh-shimmer"></div>
                                <div class="h-12 w-full rounded-xl srh-shimmer"></div>
                            </div>
                        </div>`;
                }

                function startProgressBar() {
                    isSpaNavigating = true;
                    const progressBar = document.getElementById('spa-progress-bar');
                    if (progressBar) {
                        progressBar.style.opacity = '1';
                        progressBar.style.width = '35%';
                        if (progressBarTimer) clearTimeout(progressBarTimer);
                        progressBarTimer = setTimeout(() => {
                            if (isSpaNavigating && progressBar.style.width === '35%') {
                                progressBar.style.width = '70%';
                            }
                        }, 150);
                    }
                }

                function finishProgressBar() {
                    isSpaNavigating = false;
                    if (progressBarTimer) { clearTimeout(progressBarTimer); progressBarTimer = null; }
                    const progressBar = document.getElementById('spa-progress-bar');
                    if (progressBar) {
                        progressBar.style.width = '100%';
                        setTimeout(() => {
                            progressBar.style.opacity = '0';
                            setTimeout(() => { progressBar.style.width = '0%'; }, 200);
                        }, 100);
                    }
                }
                window.srhHidePageLoader = finishProgressBar;

                // Auto-dismiss safety hooks on page lifecycle events
                window.addEventListener('pageshow', finishProgressBar);
                window.addEventListener('DOMContentLoaded', finishProgressBar);
                window.addEventListener('spa:page-loaded', finishProgressBar);

                function updateActiveNavHighlights(targetUrl) {
                    const rawPath = new URL(targetUrl || window.location.href, window.location.origin).pathname;
                    const currentPath = rawPath.endsWith('/') && rawPath.length > 1 ? rawPath.slice(0, -1) : rawPath;

                    // 1. Mobile Bottom Navbar Links
                    document.querySelectorAll('#mobile-bottom-nav .nav-item-link').forEach(link => {
                        const linkRawPath = new URL(link.href, window.location.origin).pathname;
                        const linkPath = linkRawPath.endsWith('/') && linkRawPath.length > 1 ? linkRawPath.slice(0, -1) : linkRawPath;

                        let isMatch = false;
                        if (currentPath === '/dashboard' || currentPath === '/' || currentPath === '') {
                            isMatch = (linkPath === '/dashboard' || linkPath === '/' || linkPath === '');
                        } else {
                            isMatch = (linkPath === currentPath) || (linkPath !== '/dashboard' && linkPath !== '/' && currentPath.startsWith(linkPath));
                        }

                        if (isMatch) {
                            link.classList.remove('text-gray-400', 'text-slate-400');
                            link.classList.add('text-blue-600', 'font-extrabold');
                            link.setAttribute('aria-current', 'page');
                        } else {
                            link.classList.remove('text-blue-600', 'font-extrabold');
                            link.classList.add('text-gray-400');
                            link.removeAttribute('aria-current');
                        }
                    });

                    // 2. Desktop Navigation Links
                    document.querySelectorAll('.desktop-nav-tabs a, header#navbar .desktop-nav-link').forEach(link => {
                        const linkRawPath = new URL(link.href, window.location.origin).pathname;
                        const linkPath = linkRawPath.endsWith('/') && linkRawPath.length > 1 ? linkRawPath.slice(0, -1) : linkRawPath;

                        let isMatch = false;
                        if (currentPath === '/dashboard' || currentPath === '/' || currentPath === '') {
                            isMatch = (linkPath === '/dashboard' || linkPath === '/' || linkPath === '');
                        } else {
                            isMatch = (linkPath === currentPath) || (linkPath !== '/dashboard' && linkPath !== '/' && currentPath.startsWith(linkPath));
                        }

                        if (isMatch) {
                            link.classList.remove('text-gray-600', 'hover:text-blue-600', 'hover:bg-gray-100');
                            link.classList.add('bg-blue-600', 'text-white', 'shadow-md', 'shadow-blue-500/20');
                            link.style.backgroundColor = '#2563eb';
                            link.style.color = '#ffffff';
                            link.style.boxShadow = '0 4px 12px rgba(37, 99, 235, 0.25)';
                        } else {
                            link.classList.remove('bg-blue-600', 'text-white', 'shadow-md', 'shadow-blue-500/20');
                            link.classList.add('text-gray-600', 'hover:text-blue-600', 'hover:bg-gray-100');
                            link.style.backgroundColor = 'transparent';
                            link.style.color = '#475569';
                            link.style.boxShadow = 'none';
                        }
                    });
                }

                document.addEventListener('DOMContentLoaded', () => {
                    updateActiveNavHighlights(window.location.href);
                });
                window.addEventListener('pageshow', () => {
                    updateActiveNavHighlights(window.location.href);
                });



                function clearPageIntervals() {
                    // Invalidate poller versions for async loops
                    window._srhDriverPollerVersion = (window._srhDriverPollerVersion || 0) + 1;
                    window._srhPaxPollerVersion = (window._srhPaxPollerVersion || 0) + 1;

                    const intervals = [
                        'queueStatusInterval', 
                        'driverStatusInterval', 
                        'passengerStatusInterval', 
                        'liveQueueInterval', 
                        'driverAppInterval', 
                        'activeRideInterval', 
                        'adminTableInterval', 
                        'passengerLiveLocationInterval',
                        '__srhQueueHeartbeat',
                        '_reportsPollInterval',
                        '_geofencePollerInterval',
                        '_driverActiveTripLocationTimer',
                        'chatPollingTimer'
                    ];
                    // While the driver hub is parked in the keep-alive vault, its own
                    // timers must keep running (live GPS broadcast, geofence checks).
                    const __srhVaultOwned = window.__srhVaultActive
                        ? ['_geofencePollerInterval', '_driverActiveTripLocationTimer']
                        : [];
                    intervals.forEach(key => {
                        if (__srhVaultOwned.indexOf(key) !== -1) return;
                        if (window[key]) {
                            clearInterval(window[key]);
                            clearTimeout(window[key]);
                            window[key] = null;
                        }
                    });

                    // Clean Teardown: Remove MapLibre WebGL instances & workers on SPA tab switch.
                    // While a page is parked in the keep-alive vault its live map, GPS watch
                    // and geofence pollers must survive — they are only destroyed on a real
                    // force-refresh discard (srhDiscardVault).
                    const __srhVaultActive = !!window.__srhVaultActive;
                    if (window._grabHomeMapInstance && !__srhVaultActive) {
                        try { window._grabHomeMapInstance.remove(); } catch(e){}
                        window._grabHomeMapInstance = null;
                    }
                    if (window.paxHomeMapInstance && !__srhVaultActive) {
                        try { window.paxHomeMapInstance.remove(); } catch(e){}
                        window.paxHomeMapInstance = null;
                    }
                    if (window.bookingMapInstance && !__srhVaultActive) {
                        try { window.bookingMapInstance.remove(); } catch(e){}
                        window.bookingMapInstance = null;
                    }
                    if (window.bookingMap && !__srhVaultActive) {
                        try { window.bookingMap.remove(); } catch(e){}
                        window.bookingMap = null;
                    }
                    if (window._driverWatchPositionId && navigator.geolocation && !__srhVaultActive) {
                        try { navigator.geolocation.clearWatch(window._driverWatchPositionId); } catch(e){}
                        window._driverWatchPositionId = null;
                    }
                    if (window._paxWatchPositionId && navigator.geolocation && !__srhVaultActive) {
                        try { navigator.geolocation.clearWatch(window._paxWatchPositionId); } catch(e){}
                        window._paxWatchPositionId = null;
                    }

                    try {
                        window.dispatchEvent(new CustomEvent('srh:page-unloaded'));
                    } catch(e) {}

                    const bar = document.getElementById('queue-action-bar');
                    if (bar) bar.remove();
                    const queueToast = document.getElementById('queueToast');
                    if (queueToast) queueToast.remove();
                    const ratingModal = document.getElementById('trip-completed-modal');
                    if (ratingModal) ratingModal.remove();
                }
                window.clearPageIntervals = clearPageIntervals;

                function clearPageCache() {
                    pageCache.clear();
                }
                window.clearPageCache = clearPageCache;

                // Targeted invalidation: drop ONLY the cached entries whose pathname
                // matches (e.g. /dashboard after a queue change) so live pages re-fetch
                // fresh on next nav while history/earnings/profile keep their cache.
                function invalidatePageCache(matchUrl) {
                    let matchPath = null;
                    try {
                        matchPath = new URL(matchUrl, window.location.origin).pathname;
                        matchPath = (matchPath.length > 1 && matchPath.endsWith('/')) ? matchPath.slice(0, -1) : matchPath;
                    } catch (e) { return; }
                    Array.from(pageCache.keys()).forEach(key => {
                        try {
                            let keyPath = new URL(key, window.location.origin).pathname;
                            keyPath = (keyPath.length > 1 && keyPath.endsWith('/')) ? keyPath.slice(0, -1) : keyPath;
                            if (keyPath === matchPath) {
                                pageCache.delete(key);
                            }
                        } catch (e) {}
                    });
                }
                window.srhInvalidatePageCache = invalidatePageCache;

                document.addEventListener('submit', () => {
                    clearPageCache();
                });

                let currentNavId = 0;

                async function navigateTo(url, pushState = true, showLoader = true, forceRefresh = false) {
                    currentNavId++;
                    const thisNavId = currentNavId;

                    const targetFullUrl = new URL(url, window.location.origin).href;
                    const isDifferentPath = new URL(targetFullUrl).pathname !== window.location.pathname;

                    // If user tapped the tab they are currently viewing, smoothly scroll to top without network re-fetch
                    if (!isDifferentPath && !forceRefresh) {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        return;
                    }

                    updateActiveNavHighlights(targetFullUrl);

                    // ---- Keep-Alive Vault: instant restore / discard before any fetch ----
                    const __toPath = srhNormPath(targetFullUrl);
                    if (vaultedPath && __toPath === vaultedPath) {
                        if (forceRefresh || !srhRestoreVault(targetFullUrl, pushState)) {
                            srhDiscardVault();
                        } else {
                            return;
                        }
                    }
                    // -------------------------

                    if (currentFetchController) {
                        try { currentFetchController.abort(); } catch (e) {}
                    }

                    if (forceRefresh || isDynamicPath(targetFullUrl)) {
                        pageCache.delete(targetFullUrl);
                    }

                    let htmlText = null;
                    if (!forceRefresh && !isDynamicPath(targetFullUrl)) {
                        const cached = pageCache.get(targetFullUrl);
                        if (cached && (Date.now() - cached.timestamp < CACHE_TTL_MS)) {
                            htmlText = cached.html;
                        }
                    }

                    const isCachedInstant = !!htmlText;

                    // Park the map hub DOM (incl. live WebGL map) BEFORE anything can
                    // clear the content area, so returning to it later is instant.
                    let parkedNow = false;
                    if (isDifferentPath) {
                        parkedNow = srhParkCurrentPage();
                    }

                    const controller = new AbortController();
                    if (!isCachedInstant) {
                        currentFetchController = controller;
                        if (showLoader) startProgressBar();
                    }

                    let skeletonTimer = null;
                    if (isDifferentPath && contentArea) {
                        clearPageIntervals();
                        if (!isCachedInstant) {
                            // Only show skeleton if network load takes longer than 150ms
                            skeletonTimer = setTimeout(() => {
                                if (thisNavId === currentNavId && contentArea) {
                                    contentArea.innerHTML = getSkeletonHtml(targetFullUrl);
                                }
                            }, 150);
                        }
                        window.scrollTo({ top: 0, behavior: 'instant' });
                    }

                    try {
                        if (!htmlText) {
                            htmlText = await fetchUrlHtml(targetFullUrl, controller.signal);
                        }

                        if (skeletonTimer) {
                            clearTimeout(skeletonTimer);
                        }

                        if (!htmlText || thisNavId !== currentNavId) {
                            // Navigation failed/superseded: if we had just parked the map
                            // hub for THIS navigation, put it right back so the user never
                            // sees a blank screen.
                            if (parkedNow && thisNavId === currentNavId && srhVaultHas(window.location.href)) {
                                srhRestoreVault(window.location.href, false);
                            }
                            return;
                        }

                        const cleanHtml = htmlText.replace(/<link[^>]*rel=["'](?:icon|apple-touch-icon|shortcut icon)["'][^>]*>/gi, '');
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(cleanHtml, 'text/html');
                        const newContent = doc.getElementById('spa-content-area');

                        if (newContent && contentArea) {
                            if (isDifferentPath) {
                                clearPageIntervals();
                            }

                            contentArea.innerHTML = '';
                            while (newContent.firstChild) {
                                contentArea.appendChild(newContent.firstChild);
                            }

                            document.title = doc.title || document.title;
                            if (pushState) window.history.pushState({ spa: true }, '', targetFullUrl);
                            updateActiveNavHighlights(targetFullUrl);

                            contentArea.querySelectorAll('script').forEach(oldScript => {
                                if (oldScript.src) {
                                    const newScript = document.createElement('script');
                                    Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                                    if (oldScript.parentNode) oldScript.parentNode.replaceChild(newScript, oldScript);
                                    return;
                                }
                                try {
                                    const code = oldScript.textContent;
                                    if (!code || !code.trim()) return;
                                    (1, eval)(code);
                                } catch(scriptErr) {
                                    console.warn('SPA script execution warning:', scriptErr);
                                }
                            });

                            window.dispatchEvent(new CustomEvent('spa:page-loaded'));
                            if (isDifferentPath && isCachedInstant) window.scrollTo({ top: 0, behavior: 'instant' });
                        }
                    } catch (err) {
                        if (err.name !== 'AbortError') {
                            console.warn('SPA Navigation handled:', err);
                        }
                    } finally {
                        if (showLoader) {
                            finishProgressBar();
                        }
                    }
                }
                window.navigateTo = navigateTo;
                // Background-fetch a URL into the SPA page cache (used e.g. to warm the
                // dashboard while the rating modal overlays it after drop-off).
                window.srhPrefetchUrl = prefetchUrl;

                // ---- Modern In-Place Pull-To-Refresh Controller (Pure Vector SVG Capsule) ----
                (function() {
                    const indicator = document.getElementById('srh-pull-refresh');
                    const circle = document.getElementById('srh-pull-circle');
                    const arrowEl = document.getElementById('srh-pull-arrow');
                    if (!indicator || !circle) return;

                    let startY = 0;
                    let startX = 0;
                    let isTracking = false;
                    let isPulling = false;
                    let isRefreshing = false;
                    let pendingRaf = null;
                    const PULL_MAX_PX = 65;

                    function isExcluded(target) {
                        if (document.getElementById('pax-booking-sheet') || document.getElementById('passenger-map-container') || document.getElementById('pax-wait-sheet')) {
                            return true;
                        }
                        if (!target || !target.closest) return false;
                        return !!target.closest(
                            '#pax-booking-sheet, #pax-wait-sheet, #pax-sheet-drag-handle, #pax-sheet-details-content, ' +
                            '#driver-bottom-sheet, #active-trip-wrapper, #incoming-ride-wrapper, ' +
                            '.srh-draggable-sheet, .srh-drag-handle, #sheet-drag-handle, #active-trip-drag-handle, ' +
                            '.maplibregl-canvas, #grab-home-map, #driver-map-canvas, #passenger-map-canvas, ' +
                            '#documentViewerModal, #updateReportModal, #announcementModal, #rejectApplicantModal, ' +
                            '#removeApplicantModal, #directSuspendModal, #attachmentPreviewModal, ' +
                            '#previewViewportOuter, #reportHubPreviewView, #previewSheetWrapper, #printableReportSheet, .report-sheet, ' +
                            'input, textarea, select, .leaflet-container'
                        );
                    }

                    function isAtTop() {
                        return (window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0) <= 2;
                    }

                    document.addEventListener('touchstart', function(e) {
                        // Pull-to-refresh is disabled on the map-hub dashboards: those
                        // pages never scroll (always "at top"), so scrolling inside
                        // overlays like the notifications panel falsely triggered the
                        // refresh gesture.
                        const __pullPath = window.location.pathname;
                        if (__pullPath === '/dashboard' || __pullPath === '/') return;
                        if (isRefreshing || e.touches.length !== 1) return;
                        if (!isAtTop()) return;
                        if (isExcluded(e.target)) return;

                        startY = e.touches[0].clientY;
                        startX = e.touches[0].clientX;
                        isTracking = true;
                        isPulling = false;
                    }, { passive: true });

                    document.addEventListener('touchmove', function(e) {
                        if (!isTracking || isRefreshing || e.touches.length !== 1) return;
                        
                        const clientY = e.touches[0].clientY;
                        const clientX = e.touches[0].clientX;
                        const deltaY = clientY - startY;
                        const deltaX = clientX - startX;

                        if (deltaY <= 0 || (Math.abs(deltaX) > deltaY * 1.1 && !isPulling)) {
                            if (!isPulling) isTracking = false;
                            return;
                        }

                        if (!isAtTop()) {
                            isTracking = false;
                            if (isPulling) resetIndicator();
                            return;
                        }

                        if (!isPulling && deltaY > 5) {
                            isPulling = true;
                            indicator.classList.remove('is-animating');
                        }

                        if (isPulling) {
                            if (e.cancelable) e.preventDefault();

                            const progress = Math.min(1, Math.max(0, (deltaY - 5) / PULL_MAX_PX));
                            const translateY = -55 + (progress * 62); // Smooth slide from -55px to +7px

                            if (pendingRaf) cancelAnimationFrame(pendingRaf);
                            pendingRaf = requestAnimationFrame(() => {
                                indicator.classList.add('is-active');
                                indicator.style.transform = `translate3d(0, ${translateY.toFixed(1)}px, 0) scale(${Math.min(1, 0.7 + progress * 0.3).toFixed(2)})`;
                                indicator.style.opacity = Math.min(1, 0.3 + progress * 0.7).toFixed(2);

                                // Blue circle fills progressively from 0% (100) to 100% (0)
                                const offset = (100 - (progress * 100)).toFixed(1);
                                circle.setAttribute('stroke-dashoffset', offset);
                                circle.style.strokeDashoffset = offset;

                                if (arrowEl) {
                                    arrowEl.style.opacity = '1';
                                    arrowEl.style.transform = `rotate(${(progress * 180).toFixed(0)}deg)`;
                                }

                                if (progress >= 1) {
                                    if (!indicator.dataset.reached) {
                                        indicator.dataset.reached = 'true';
                                        if (window.srhSafeVibrate) {
                                            window.srhSafeVibrate(12);
                                        }
                                    }
                                } else {
                                    indicator.removeAttribute('data-reached');
                                }
                            });
                        }
                    }, { passive: false });

                    document.addEventListener('touchend', function() {
                        if (!isTracking && !isPulling) return;
                        isTracking = false;

                        if (isPulling) {
                            isPulling = false;
                            if (indicator.dataset.reached === 'true') {
                                triggerInPlaceRefresh();
                            } else {
                                resetIndicator();
                            }
                        }
                    });

                    document.addEventListener('touchcancel', function() {
                        isTracking = false;
                        isPulling = false;
                        resetIndicator();
                    });

                    async function triggerInPlaceRefresh() {
                        if (isRefreshing) return;
                        isRefreshing = true;
                        indicator.removeAttribute('data-reached');
                        indicator.classList.add('is-animating', 'is-active', 'is-refreshing');
                        indicator.style.transform = 'translate3d(0, 8px, 0) scale(1)';
                        indicator.style.opacity = '1';
                        if (arrowEl) arrowEl.style.opacity = '0';
                        circle.removeAttribute('stroke-dashoffset');
                        circle.style.strokeDashoffset = '';

                        try {
                            const refreshPromise = (async () => {
                                if (typeof window.refreshApplicantsArea === 'function') {
                                    try { window.refreshApplicantsArea(); } catch(e){}
                                }
                                if (typeof window.refreshReportsArea === 'function') {
                                    try { window.refreshReportsArea(); } catch(e){}
                                }
                                if (typeof window.fetchLiveQueue === 'function') {
                                    try { window.fetchLiveQueue(); } catch(e){}
                                }
                                if (typeof window.navigateTo === 'function') {
                                    await window.navigateTo(window.location.href, false, false, true);
                                }
                                window.dispatchEvent(new CustomEvent('spa:page-refreshed'));
                            })();

                            const timeoutPromise = new Promise(resolve => setTimeout(resolve, 3500));
                            await Promise.race([refreshPromise, timeoutPromise]);
                            await new Promise(r => setTimeout(r, 200));
                        } catch(err) {
                            console.warn('In-place refresh error:', err);
                        } finally {
                            isRefreshing = false;
                            resetIndicator();
                        }
                    }

                    function resetIndicator() {
                        if (pendingRaf) {
                            cancelAnimationFrame(pendingRaf);
                            pendingRaf = null;
                        }
                        indicator.removeAttribute('data-reached');
                        indicator.classList.remove('is-refreshing');
                        indicator.classList.add('is-animating');
                        indicator.style.transform = 'translate3d(0, -60px, 0) scale(0.6)';
                        indicator.style.opacity = '0';
                        if (arrowEl) {
                            arrowEl.style.opacity = '1';
                            arrowEl.style.transform = 'rotate(0deg)';
                        }
                        circle.setAttribute('stroke-dashoffset', '100');
                        circle.style.strokeDashoffset = '100';

                        setTimeout(() => {
                            if (!isPulling && !isRefreshing) {
                                indicator.classList.remove('is-animating', 'is-active');
                            }
                        }, 300);
                    }

                    window.srhTriggerPullRefresh = triggerInPlaceRefresh;
                })();

                window.createSlidingToast = function(message, type) {
                    if (!message) return;
                    const now = Date.now();
                    // Deduplicate identical rapid successive toasts within 1.5 seconds
                    if (window._lastToastMsg === message && window._lastToastTime && (now - window._lastToastTime < 1500) && type !== 'danger' && type !== 'error') {
                        return;
                    }
                    window._lastToastMsg = message;
                    window._lastToastTime = now;

                    let existing = document.getElementById('global-toast');
                    if (existing) existing.remove();

                    let toast = document.createElement('div');
                    toast.id = 'global-toast';
                    
                    let bgStyle = '#0f172a';
                    let borderStyle = 'rgba(255, 255, 255, 0.2)';
                    
                    if (type === 'error' || type === 'danger') {
                        bgStyle = '#881337';
                        borderStyle = 'rgba(255, 255, 255, 0.25)';
                    } else if (type === 'warning' || type === 'queue_warning') {
                        bgStyle = '#7c2d12';
                        borderStyle = 'rgba(255, 255, 255, 0.25)';
                    } else if (type === 'success' || type === 'completed') {
                        bgStyle = '#065f46';
                        borderStyle = 'rgba(255, 255, 255, 0.25)';
                    }
                    
                    toast.className = 'fixed left-1/2 transform -translate-x-1/2 -translate-y-12 opacity-0 z-[999999] px-4 py-2.5 rounded-2xl shadow-2xl transition-all duration-300 ease-out flex items-center justify-center pointer-events-none max-w-[90vw] text-center';
                    toast.style.cssText = `position: fixed; top: calc(1rem + env(safe-area-inset-top, 0px)); left: 50%; transform: translateX(-50%); z-index: 999999; background-color: ${bgStyle} !important; color: #ffffff !important; border: 1px solid ${borderStyle} !important; box-shadow: 0 10px 30px -4px rgba(15, 23, 42, 0.6) !important; max-width: 90vw !important;`;
                    toast.innerHTML = `<span class="text-xs font-black tracking-wide leading-snug text-center" style="color: #ffffff !important; word-break: break-word;">${message}</span>`;
                    
                    document.body.appendChild(toast);

                    setTimeout(() => {
                        toast.classList.remove('-translate-y-12', 'opacity-0');
                    }, 50);

                    setTimeout(() => {
                        toast.classList.add('-translate-y-12', 'opacity-0');
                        setTimeout(() => toast.remove(), 400);
                    }, 3500);
                };

                // Inline Web Worker for High-FPS Drag Math Offloading
                (function initDragWorker() {
                    if (window.srhDragWorker) return;
                    try {
                        const blob = new Blob([`
                            self.onmessage = function(e) {
                                const d = e.data;
                                if (d.type === 'calcDrag') {
                                    const deltaY = d.startY - d.currentY;
                                    const maxAllowed = d.maxAllowed || 320;
                                    const targetHeight = Math.max(0, Math.min(maxAllowed, d.initialHeight + deltaY));
                                    const opacity = Math.min(1, targetHeight / 60);
                                    
                                    const dt = d.currentTime - d.lastTime;
                                    let velocity = 0;
                                    if (dt > 0) {
                                        velocity = (d.currentY - d.lastY) / dt;
                                    }
                                    
                                    self.postMessage({
                                        id: d.id,
                                        deltaY: deltaY,
                                        targetHeight: targetHeight,
                                        opacity: opacity,
                                        velocity: velocity,
                                        ratio: targetHeight / maxAllowed
                                    });
                                }
                            };
                        `], { type: 'application/javascript' });
                        window.srhDragWorker = new Worker(URL.createObjectURL(blob));
                    } catch (e) {
                        window.srhDragWorker = null;
                    }
                })();

                window.submitRideAction = function(url, method = 'POST', data = {}, btn = null, event = null) {
                    if (event && event.preventDefault) event.preventDefault();

                    if (!btn && event) {
                        btn = (event.target && event.target.tagName === 'BUTTON') ? event.target : event.target?.closest?.('button');
                    }

                    if (btn && (btn._submitting || btn.disabled || btn.getAttribute('data-srh-submitting') === 'true')) return;

                    let origHtml = '';
                    if (btn) {
                        btn._submitting = true;
                        btn.disabled = true;
                        btn.style.pointerEvents = 'none';
                        btn.style.opacity = '0.85';
                        btn.setAttribute('data-srh-submitting', 'true');
                        origHtml = btn.innerHTML;
                        const spinner = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
                        btn.innerHTML = `<span class="inline-flex items-center justify-center gap-1.5">${spinner} Processing...</span>`;
                    }

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    const formData = new FormData();
                    formData.append('_token', csrfToken);

                    let reqMethod = (method || 'POST').toUpperCase();
                    if (reqMethod === 'PATCH' || reqMethod === 'PUT' || reqMethod === 'DELETE') {
                        formData.append('_method', reqMethod);
                        reqMethod = 'POST';
                    }

                    if (data && typeof data === 'object') {
                        Object.keys(data).forEach(key => {
                            formData.append(key, data[key]);
                        });
                    }

                    if (!formData.has('lat')) {
                        let curLat = (window.srhTricycle && typeof window.srhTricycle.lat === 'number') ? window.srhTricycle.lat : null;
                        let curLng = (window.srhTricycle && typeof window.srhTricycle.lng === 'number') ? window.srhTricycle.lng : null;
                        let curHeading = (window.srhTricycle && typeof window.srhTricycle.heading === 'number') ? window.srhTricycle.heading : 0;
                        if (curLat === null) {
                            const sLat = parseFloat(localStorage.getItem('srh_simulated_lat'));
                            const sLng = parseFloat(localStorage.getItem('srh_simulated_lng'));
                            if (!isNaN(sLat) && !isNaN(sLng)) { curLat = sLat; curLng = sLng; }
                        }
                        if (curLat === null) {
                            const rLat = parseFloat(localStorage.getItem('srh_last_real_lat'));
                            const rLng = parseFloat(localStorage.getItem('srh_last_real_lng'));
                            if (!isNaN(rLat) && !isNaN(rLng)) { curLat = rLat; curLng = rLng; }
                        }
                        if (curLat !== null && curLng !== null) {
                            formData.append('lat', curLat);
                            formData.append('lng', curLng);
                            formData.append('heading', curHeading);
                        }
                    }

                    const isStartReturning = url.includes('/start-returning');
                    let returnRoutePromise = Promise.resolve(null);
                    if (isStartReturning) {
                        const curLat = (window.srhTricycle && typeof window.srhTricycle.lat === 'number') ? window.srhTricycle.lat : (parseFloat(localStorage.getItem('srh_simulated_lat')) || 15.429550175641715);
                        const curLng = (window.srhTricycle && typeof window.srhTricycle.lng === 'number') ? window.srhTricycle.lng : (parseFloat(localStorage.getItem('srh_simulated_lng')) || 120.92240292427664);
                        if (window._cachedReturnRouteCoords) {
                            returnRoutePromise = Promise.resolve(window._cachedReturnRouteCoords);
                        } else if (typeof window.fetchRouteAsync === 'function') {
                            returnRoutePromise = window.fetchRouteAsync(curLat, curLng, 15.429550175641715, 120.92240292427664);
                        }
                    }

                    fetch(url, {
                        method: reqMethod,
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(res => {
                        if (res.ok) {
                            const contentType = res.headers.get('content-type');
                            if (contentType && contentType.includes('application/json')) {
                                return res.json();
                            }
                        }
                        return { status: 'success', redirect: window.location.href };
                    })
                    .then(data => {
                        if (data && (data.status === 'error' || data.error)) {
                            if (btn && document.body.contains(btn)) {
                                btn._submitting = false;
                                btn.disabled = false;
                                btn.style.pointerEvents = '';
                                btn.style.opacity = '1';
                                btn.removeAttribute('data-srh-submitting');
                                if (origHtml) btn.innerHTML = origHtml;
                            }
                            if (window.createSlidingToast) {
                                window.createSlidingToast(data.message || data.error || 'Action could not be completed.', 'error');
                            }
                            return;
                        }

                        return returnRoutePromise.then(routeCoords => {
                            // State transition executes ONLY upon confirmed backend response & ready route
                            if (typeof window.triggerDriverActionInstantUI === 'function') {
                                window.triggerDriverActionInstantUI(url);
                            }

                            if (routeCoords && typeof window.drawHomeRouteLine === 'function') {
                                window.drawHomeRouteLine(routeCoords, '#3b82f6');
                            }
                            return data;
                        });
                    })
                    .then(data => {
                        if (!data) return;

                        const isStartReturning = url.includes('/start-returning');
                        const isArrived       = url.includes('/arrived');
                        const isStartTransit  = url.includes('/start-transit');

                        if (!isStartReturning && !isArrived && !isStartTransit) {
                            // Immediately zero out the active status and ride ID on existing sheet DOM
                            window.__srhActiveTrip = null;
                            window._hasActivePathwayMode = false;
                            window._srhActiveRouteCoordinates = null;
                            window.srhCompassMode = 0;
                            if (typeof window.srhResetLastState === 'function') window.srhResetLastState();
                            const existingSheet = document.getElementById('active-trip-wrapper') || document.getElementById('driver-bottom-sheet');
                            if (existingSheet) {
                                existingSheet.dataset.activeStatus = '';
                                existingSheet.dataset.rideId = '';
                                existingSheet.dataset.isWalkin = 'false';
                            }

                            // Wiping attached flag ensures NativeBottomSheet cleanly re-attaches to the newly loaded view
                            const handle = document.getElementById('sheet-drag-handle');
                            if (handle) {
                                handle._nativeSheetAttached = false;
                                delete handle.dataset.nativeSheetAttached;
                            }

                            // Clear map route lines & reset floating button to 2D White Compass
                            if (typeof window.clearHomeRouteLines === 'function') window.clearHomeRouteLines();
                            if (typeof window.setUnifiedMapButtonState === 'function') window.setUnifiedMapButtonState(2);
                            window._lastRouteOrigin = null;
                            window._lastRouteTarget = null;
                        } else if (isArrived || isStartTransit) {
                            const newSt = isStartTransit ? 'in_transit' : 'arrived';
                            const existingSheet = document.getElementById('active-trip-wrapper') || document.getElementById('driver-bottom-sheet');
                            if (existingSheet) existingSheet.dataset.activeStatus = newSt;
                            // Redraw the route for the new status
                            if (typeof window.updateActiveTripRoute === 'function') {
                                setTimeout(() => window.updateActiveTripRoute(), 150);
                            }
                        }

                        if (typeof window.closeAnnouncementModal === 'function') {
                            window.closeAnnouncementModal();
                        }
                        if (window.clearPageCache) window.clearPageCache();
                        if (data && data.message && window.createSlidingToast) {
                            window.createSlidingToast(data.message, data.status === 'error' ? 'error' : 'success');
                        }

                        // In-place reactive DOM update for driver dashboard
                        if (typeof window.handleDriverRideActionSuccess === 'function') {
                            try {
                                window.handleDriverRideActionSuccess(url, method, data);
                            } catch(e) {}
                            if (btn && document.body.contains(btn)) {
                                btn._submitting = false;
                                btn.disabled = false;
                                btn.style.pointerEvents = '';
                                btn.style.opacity = '1';
                                btn.removeAttribute('data-srh-submitting');
                                if (origHtml) btn.innerHTML = origHtml;
                            }
                            return;
                        }
                    })
                    .catch(err => {
                        if (btn && document.body.contains(btn)) {
                            btn._submitting = false;
                            btn.disabled = false;
                            btn.style.pointerEvents = '';
                            btn.style.opacity = '1';
                            btn.removeAttribute('data-srh-submitting');
                            if (origHtml) btn.innerHTML = origHtml;
                        }
                        if (window.createSlidingToast) {
                            window.createSlidingToast('Network slow or disconnected. Please try again.', 'error');
                        }
                    });

                        const targetUrl = (data && data.redirect) ? data.redirect : window.location.href;
                        if (window.navigateTo) {
                            window.navigateTo(targetUrl, false, false, true);
                        } else {
                            window.location.reload();
                        }
                    })
                    .catch(err => {
                        if (btn) {
                            btn._submitting = false;
                            if (document.body.contains(btn)) {
                                btn.disabled = false;
                                btn.style.opacity = '1';
                                btn.removeAttribute('data-srh-submitting');
                                if (origHtml) btn.innerHTML = origHtml;
                            }
                        }
                        if (typeof window.createSlidingToast === 'function') {
                            window.createSlidingToast('Connection error. Please try again.', 'error');
                        }
                        if (window.clearPageCache) window.clearPageCache();
                        if (window.navigateTo) {
                            window.navigateTo(window.location.href, false, false, true);
                        } else {
                            window.location.reload();
                        }
                    });
                };

                window.handleRideActionSubmit = function(event, form) {
                    if (event && event.preventDefault) event.preventDefault();
                    if (!form) return;
                    
                    const btn = form.querySelector('button[type="submit"]') || form.querySelector('button');
                    const fileInputs = form.querySelectorAll('input[type="file"]');
                    let hasFiles = false;
                    fileInputs.forEach(input => {
                        if (input.files && input.files.length > 0) hasFiles = true;
                    });

                    if (hasFiles) {
                        const origHtml = btn ? btn.innerHTML : '';
                        if (btn) {
                            btn.disabled = true;
                            btn.style.opacity = '0.7';
                            btn.innerHTML = 'Submitting...';
                        }
                        const formData = new FormData(form);
                        fetch(form.action, {
                            method: (form.querySelector('input[name="_method"]')?.value || form.method || 'POST').toUpperCase(),
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                            }
                        })
                        .then(async response => {
                            const data = await response.json().catch(() => ({}));
                            if (!response.ok) {
                                let errorMsg = data.message || 'Validation error. Please check your entries.';
                                if (data.errors) {
                                    errorMsg = Object.values(data.errors).flat().join(' ');
                                }
                                if (window.createSlidingToast) window.createSlidingToast(errorMsg, 'error');
                                if (btn) {
                                    btn.disabled = false;
                                    btn.style.opacity = '1';
                                    btn.innerHTML = origHtml;
                                }
                                return;
                            }
                            if (data.message && window.createSlidingToast) {
                                window.createSlidingToast(data.message, 'success');
                            }
                            if (window.navigateTo) {
                                window.navigateTo(data.redirect || window.location.href, false, false, true);
                            } else {
                                window.location.reload();
                            }
                        })
                        .catch(err => {
                            if (window.createSlidingToast) window.createSlidingToast('Connection error. Please try again.', 'error');
                            if (btn) {
                                btn.disabled = false;
                                btn.style.opacity = '1';
                                btn.innerHTML = origHtml;
                            }
                        });
                        return;
                    }

                    const method = (form.querySelector('input[name="_method"]')?.value || form.method || 'POST').toUpperCase();
                    const formDataObj = {};
                    new FormData(form).forEach((value, key) => {
                        if (key !== '_token' && key !== '_method') {
                            formDataObj[key] = value;
                        }
                    });
                    window.submitRideAction(form.action, method, formDataObj, btn, event);
                };

                document.addEventListener('submit', (e) => {
                    // Skip forms already handled by their own inline handlers
                    // (e.g. report update modal) and confirmed-cancel confirm() forms.
                    if (e.defaultPrevented) return;

                    const form = e.target;
                    if (!form) return;
                    const action = form.getAttribute('action') || '';
                    if (form.getAttribute('target') === '_blank' || 
                        form.classList.contains('no-spa') || 
                        form.classList.contains('no-spa-submit') ||
                        action.includes('/profile') ||
                        action.includes('/password') ||
                        action.includes('/logout') ||
                        action.includes('/verification')) {
                        return;
                    }

                    e.preventDefault();
                    window.handleRideActionSubmit(e, form);
                });

                document.addEventListener('click', (e) => {
                    const link = e.target.closest('a');
                    if (!link) return;

                    const href = link.getAttribute('href');
                    if (!href || href.startsWith('#') || href.startsWith('javascript:') || link.hasAttribute('download') || link.getAttribute('target') === '_blank') {
                        return;
                    }

                    if (link.origin === window.location.origin) {
                        if (link.closest('form') || link.classList.contains('no-spa')) return;

                        e.preventDefault();
                        updateActiveNavHighlights(link.href);
                        navigateTo(link.href);
                    }
                });

                // Register the push-notification service worker once (idempotent).
                // Android Chrome rejects page-level notifications without an active
                // service worker, so this must stay registered.
                const isSecureContext = location.protocol === 'https:' || ['localhost', '127.0.0.1'].includes(location.hostname);
                let srhSwReg = null;
                if ('serviceWorker' in navigator && isSecureContext) {
                    navigator.serviceWorker.register('{{ asset("sw.js") }}')
                        .then(reg => {
                            srhSwReg = reg;
                            window.srhSubscribeToPush();
                        })
                        .catch(() => {});
                }

                // ---- Web Push (Push API): notifies even when the app is closed ----
                // Subscribes this device to the browser push service so the server
                // (Laravel) can send native phone-banner notifications at any time,
                // even with the tab / browser closed.
                function urlBase64ToUint8Array(base64String) {
                    const padding = '='.repeat((4 - base64String.length % 4) % 4);
                    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
                    const rawData = window.atob(base64);
                    const outputArray = new Uint8Array(rawData.length);
                    for (let i = 0; i < rawData.length; ++i) {
                        outputArray[i] = rawData.charCodeAt(i);
                    }
                    return outputArray;
                }

                window.srhSavePushSubscription = function(sub) {
                    if (!sub) return;
                    const subJson = sub.toJSON();
                    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                    fetch('{{ route("push.subscribe") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfMeta ? csrfMeta.content : '',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            endpoint: subJson.endpoint,
                            public_key: subJson.keys ? subJson.keys.p256dh : null,
                            auth_token: subJson.keys ? subJson.keys.auth : null
                        })
                    }).catch(() => {});
                };

                window.srhRequestNotificationPermission = function() {
                    if (!('Notification' in window)) return Promise.resolve('unsupported');
                    if (Notification.permission === 'granted') {
                        if (window.srhSubscribeToPush) window.srhSubscribeToPush();
                        return Promise.resolve('granted');
                    }
                    if (Notification.permission === 'denied') {
                        return Promise.resolve('denied');
                    }
                    return Notification.requestPermission().then(function(perm) {
                        if (perm === 'granted' && window.srhSubscribeToPush) {
                            window.srhSubscribeToPush();
                        }
                        return perm;
                    }).catch(function() { return 'denied'; });
                };

                window.srhSubscribeToPush = function() {
                    if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;
                    if (!('Notification' in window) || Notification.permission !== 'granted') return;
                    const vapidMeta = document.querySelector('meta[name="vapid-public-key"]');
                    if (!vapidMeta || !vapidMeta.content) return;

                    const registrationPromise = srhSwReg ? Promise.resolve(srhSwReg) : navigator.serviceWorker.ready;

                    registrationPromise.then(reg => {
                        reg.pushManager.getSubscription().then(existing => {
                            const vapidKeyBytes = urlBase64ToUint8Array(vapidMeta.content);

                            // If the stored subscription was created with a different
                            // VAPID key, the push service rejects it (401) and no
                            // notification is ever delivered. Replace it silently.
                            const currentKey = existing && existing.options && existing.options.applicationServerKey;
                            let keyMatches = true;
                            if (existing && currentKey && currentKey instanceof ArrayBuffer) {
                                const bytes = new Uint8Array(currentKey);
                                keyMatches = bytes.length === vapidKeyBytes.length
                                    && bytes.every((b, i) => b === vapidKeyBytes[i]);
                            }

                            if (existing && !keyMatches) {
                                existing.unsubscribe().then(() => {
                                    return reg.pushManager.subscribe({
                                        userVisibleOnly: true,
                                        applicationServerKey: vapidKeyBytes
                                    });
                                }).then(sub => {
                                    window.srhSavePushSubscription(sub);
                                }).catch(() => {});
                                return;
                            }

                            if (existing) {
                                window.srhSavePushSubscription(existing);
                                return;
                            }
                            reg.pushManager.subscribe({
                                userVisibleOnly: true,
                                applicationServerKey: vapidKeyBytes
                            }).then(sub => {
                                window.srhSavePushSubscription(sub);
                            }).catch(() => {});
                        }).catch(() => {});
                    }).catch(() => {});

                    // Device key rotation / expired subscription → re-subscribe automatically
                    navigator.serviceWorker.addEventListener('pushsubscriptionchange', (e) => {
                        e.waitUntil(
                            Promise.resolve(srhSwReg || navigator.serviceWorker.ready).then(reg => {
                                return reg.pushManager.subscribe({
                                    userVisibleOnly: true,
                                    applicationServerKey: urlBase64ToUint8Array(vapidMeta.content)
                                });
                            }).then(sub => {
                                window.srhSavePushSubscription(sub);
                            }).catch(() => {})
                        );
                    });
                };

                // Record pushes the SW showed, so the in-app poller can skip
                // its own redundant banner for the same message.
                window.__srhPushShownSigs = window.__srhPushShownSigs || new Map();

                window.srhShouldSuppressSystemNotification = function(title, body) {
                    const sig = (title || '') + '|' + (body || '');
                    const ts = window.__srhPushShownSigs.get(sig);
                    if (ts && (Date.now() - ts) < 10000) {
                        window.__srhPushShownSigs.delete(sig);
                        return true;
                    }
                    return false;
                };

                if ('serviceWorker' in navigator) {
                    navigator.serviceWorker.addEventListener('message', (e) => {
                        if (!e.data) return;
                        if (e.data.type === 'srh-push-shown') {
                            // Passenger cancelled a fare proposal that is still on
                            // screen: kill the driver offer popup instantly (stop
                            // countdown, no animation) — never wait for the timer.
                            const pushTitle = e.data.title || '';
                            if (pushTitle.indexOf('Ride Cancelled') !== -1 && window.srhHideIncomingOverlay) {
                                try {
                                    window.srhHideIncomingOverlay(null, { instant: true, silent: true });
                                } catch (err) {}
                            }
                            const sig = (pushTitle) + '|' + (e.data.body || '');
                            window.__srhPushShownSigs.set(sig, Date.now());
                        } else if (e.data.type === 'srh-sw-updated') {
                            // New app version activated — drop the old (possibly
                            // stale cached) page so it re-fetches fresh HTML.
                            if (!window.__srhSwReloading) {
                                window.__srhSwReloading = true;
                                setTimeout(() => location.reload(), 300);
                            }
                        }
                    });
                }

                window.addEventListener('offline', () => {
                    if (window.createSlidingToast) {
                        window.createSlidingToast('No Internet Connection', 'danger');
                    }
                });

                window.addEventListener('online', () => {
                    if (window.createSlidingToast) {
                        window.createSlidingToast('Internet Restored', 'success');
                    }
                });

                window.addEventListener('popstate', (e) => {
                    updateActiveNavHighlights(window.location.href);
                    // Back/forward into a parked keep-alive page restores it instantly
                    // (no refetch); everything else keeps the hard-refresh behavior.
                    const skipForce = typeof window.srhVaultHas === 'function' && window.srhVaultHas(window.location.href);
                    navigateTo(window.location.href, false, true, !skipForce);
                });

                // Real-Time Reverb WebSocket Listener for Instant SPA Page & Map Updates
                const CURRENT_USER_ID = {{ auth()->check() ? auth()->id() : 0 }};

                function initReverbRideListeners() {
                    if (!window.Echo || !CURRENT_USER_ID) return;
                    if (window._reverbSubscribed) return;
                    window._reverbSubscribed = true;

                    window.Echo.channel('srh-toda-rides')
                        .listen('.ride.status.updated', (e) => {
                            if (!e) return;

                            // 1. Current Passenger status updates (always processed first!)
                            if (e.passengerId === CURRENT_USER_ID) {
                                if (window._isNavigating) return;

                                const paxWrapper = document.getElementById('passenger-status-wrapper');
                                const currentPaxStatus = paxWrapper ? paxWrapper.dataset.status : null;

                                if (e.status === 'cancelled') {
                                    if (window.resetSrhCompassMode) window.resetSrhCompassMode();
                                    if (window.createSlidingToast) {
                                        window.createSlidingToast('Your ride request was cancelled.', 'danger');
                                    }
                                    if (window.exitWaitBackToBooking && window._paxBookingSheetHtml) {
                                        window._paxExitedToBookingDone = false;
                                        window.exitWaitBackToBooking();
                                        return;
                                    }
                                    if (window.clearPageCache) window.clearPageCache();
                                    if (window.navigateTo) {
                                        window.navigateTo('/dashboard', false, false, true);
                                    } else {
                                        window.location.href = '/dashboard';
                                    }
                                    return;
                                }

                                if (e.status === 'fare_proposed') {
                                    if (window.fetchPassengerStatus) {
                                        window.fetchPassengerStatus();
                                        return;
                                    }
                                }

                                if (['accepted', 'arrived', 'in_transit'].includes(e.status)) {
                                    if (currentPaxStatus !== e.status) {
                                        const hasActiveDriverCard = document.getElementById('pax-active-driver-card');
                                        if (hasActiveDriverCard && typeof window.handlePaxRideStatusChange === 'function') {
                                            window.handlePaxRideStatusChange(e.status, e.ride);
                                            return;
                                        }
                                        if (window.clearPageCache) window.clearPageCache();
                                        if (window.navigateTo) {
                                            window.navigateTo(window.location.href, false, false, true);
                                            return;
                                        } else {
                                            window.location.reload();
                                            return;
                                        }
                                    }
                                }

                                if (e.status === 'completed' || e.status === 'none' || e.status === 'returning') {
                                    if (window.completePaxTripInPlace) {
                                        window.completePaxTripInPlace(e.ride || { id: e.rideId, fare: e.fare });
                                        return;
                                    }
                                    if (window.clearPageCache) window.clearPageCache();
                                    if (window.navigateTo) {
                                        window.navigateTo('/dashboard', false, false, true);
                                    } else {
                                        window.location.href = '/dashboard';
                                    }
                                    return;
                                }
                                return;
                            }

                            // 2. Driver / Unassigned ride notifications (when driverId is null)
                            if (e.driverId === null) {
                                if (e.status === 'cancelled') {
                                    const hardInc = window.__srhInc;
                                    if (hardInc && hardInc.rideId && String(hardInc.rideId) === String(e.rideId)) {
                                        if (window.srhHideIncomingOverlay) {
                                            try { window.srhHideIncomingOverlay({ last_ride_status: 'cancelled', last_ride_driver_id: null }, { instant: true }); } catch (err) {}
                                        }
                                        if (window.createSlidingToast && !hardInc.hiddenByMe) {
                                            window.createSlidingToast('Passenger cancelled the request.', 'danger');
                                        }
                                        if (window.srhRefreshQueueList) window.srhRefreshQueueList();
                                        return;
                                    }
                                }
                                if (window.srhForceIncomingCheck) window.srhForceIncomingCheck();
                                if (window.srhRefreshQueueList) window.srhRefreshQueueList();
                                return;
                            }

                            // 3. Current Driver status updates
                            if (e.driverId === CURRENT_USER_ID) {
                                if (window._isNavigating) return;

                                if (e.status === 'completed' || e.status === 'none' || e.status === 'cancelled') {
                                    window.__srhActiveTrip = false;
                                    if (window.resetSrhCompassMode) window.resetSrhCompassMode();
                                }

                                if (window.clearPageCache) window.clearPageCache();

                                if (e.status === 'returning' || e.status === 'completed' || e.status === 'none' || e.status === 'cancelled') {
                                    if (window.srhRefreshQueueList) window.srhRefreshQueueList();
                                    return;
                                }

                                if (e.status === 'fare_proposed') {
                                    if (window.srhForceIncomingCheck) window.srhForceIncomingCheck();
                                    return;
                                }

                                if (e.status === 'accepted' || e.status === 'arrived' || e.status === 'in_transit') {
                                    window.__srhActiveTrip = true;
                                    if (window.srhHideIncomingOverlay) {
                                        try { window.srhHideIncomingOverlay({ last_ride_status: e.status, last_ride_driver_id: e.driverId }, { instant: true, silent: true, noRestore: true }); } catch (e) {}
                                    }
                                    const sheet = document.getElementById('driver-bottom-sheet') || document.getElementById('active-trip-wrapper');
                                    if (!e.passengerId || (sheet && sheet.dataset.activeStatus)) {
                                        if (window.srhRefreshQueueList) window.srhRefreshQueueList();
                                        return;
                                    }
                                }

                                const targetUrl = (e.status === 'accepted' || e.status === 'arrived' || e.status === 'in_transit')
                                    ? (e.redirectUrl || window.location.href)
                                    : window.location.href;

                                if (window.navigateTo) {
                                    window.navigateTo(targetUrl, false, false, true);
                                }
                                return;
                            }

                            // Another driver tapped a ride action (dropped off, started
                            // returning, completed) → refresh the visible queue/trip lists
                            // seamlessly through the merge, no full page reload.
                            if (!(e.driverId === CURRENT_USER_ID || e.passengerId === CURRENT_USER_ID)) {
                                if (window.srhRefreshQueueList) window.srhRefreshQueueList();
                            }
                        });

                    // Queue shifts (driver went online/offline, accepted/declined a ride,
                    // admin reordered) → instantly refresh any visible queue list.
                    window.Echo.channel('srh-toda-queue')
                        .listen('.queue.changed', () => {
                            // The queue changed → any cached dashboard HTML is stale.
                            // Drop only that entry (keep every other page cached) so a
                            // back-nav to /dashboard is instant AND shows the new order.
                            if (window.srhInvalidatePageCache) window.srhInvalidatePageCache('/dashboard');
                            if (window.srhRefreshQueueList) window.srhRefreshQueueList();
                            if (window.syncOwnQueuePosition) window.syncOwnQueuePosition();
                        });
                }

                if (document.readyState === 'complete') {
                    setTimeout(initReverbRideListeners, 100);
                } else {
                    window.addEventListener('load', () => setTimeout(initReverbRideListeners, 100));
                }
                window.addEventListener('spa:page-loaded', initReverbRideListeners);
            })();

            // Global Attachment Preview Engine (Touch Pinch-to-Zoom & Pan)
            let currentAttachmentZoom = 1;
            let attachmentPanX = 0;
            let attachmentPanY = 0;
            let isPanningAttachment = false;
            let isPinchingAttachment = false;
            let startPanX = 0, startPanY = 0;
            let initialPanX = 0, initialPanY = 0;
            let initialPinchDistance = 0;
            let initialPinchZoom = 1;
            let initialPinchCenter = { x: 0, y: 0 };
            let lastAttachmentTapTime = 0;

            window.applyAttachmentTransform = function(withTransition = false) {
                const wrapper = document.getElementById('attachment_zoom_wrapper');
                const label = document.getElementById('attachment_zoom_level');
                const img = document.getElementById('attachment_modal_img');
                if (wrapper) {
                    wrapper.style.transition = withTransition ? 'transform 0.22s cubic-bezier(0.25, 1, 0.5, 1)' : 'none';
                    wrapper.style.transform = `translate3d(${attachmentPanX.toFixed(2)}px, ${attachmentPanY.toFixed(2)}px, 0px) scale(${currentAttachmentZoom.toFixed(3)})`;
                }
                if (img) {
                    img.style.cursor = currentAttachmentZoom > 1 ? (isPanningAttachment ? 'grabbing' : 'grab') : 'zoom-in';
                }
                if (label) {
                    label.textContent = Math.round(currentAttachmentZoom * 100) + '%';
                }
            };

            window.zoomAttachmentPreview = function(delta) {
                window.setAttachmentZoom(currentAttachmentZoom + delta, true);
            };

            window.resetAttachmentZoom = function(withTransition = false) {
                currentAttachmentZoom = 1;
                attachmentPanX = 0;
                attachmentPanY = 0;
                isPanningAttachment = false;
                isPinchingAttachment = false;
                initialPinchDistance = 0;
                window.applyAttachmentTransform(withTransition);
            };

            window.setAttachmentZoom = function(newZoom, withTransition = false) {
                currentAttachmentZoom = Math.max(0.5, Math.min(5, newZoom));
                if (currentAttachmentZoom <= 1) {
                    attachmentPanX = 0;
                    attachmentPanY = 0;
                }
                window.applyAttachmentTransform(withTransition);
            };

            window.openAttachmentPreviewModal = function(url, name, event) {
                if (event) {
                    if (event.preventDefault) event.preventDefault();
                    if (event.stopPropagation) event.stopPropagation();
                }
                const modal = document.getElementById('attachmentPreviewModal');
                const title = document.getElementById('attachment_modal_title');
                const downloadBtn = document.getElementById('attachment_modal_download');
                const img = document.getElementById('attachment_modal_img');
                const iframe = document.getElementById('attachment_modal_iframe');
                const spinner = document.getElementById('attachment_modal_spinner');
                const errorFallback = document.getElementById('attachment_modal_error');
                const zoomControls = document.getElementById('attachment_zoom_controls');

                if (!modal || !url) return;

                if (window._attachmentIframeTimeout) {
                    clearTimeout(window._attachmentIframeTimeout);
                    window._attachmentIframeTimeout = null;
                }
                if (window._attachmentUnloadTimer) {
                    clearTimeout(window._attachmentUnloadTimer);
                    window._attachmentUnloadTimer = null;
                }

                if (modal.parentNode !== document.body) {
                    document.body.appendChild(modal);
                }

                // Resolve relative URLs to absolute origin URLs
                let absoluteUrl = url;
                try {
                    absoluteUrl = new URL(url, window.location.origin).href;
                } catch(e) {}

                // Route appeal attachments through the streaming endpoint so they render inline
                let previewUrl = absoluteUrl;
                const appealMatch = absoluteUrl.match(/\/storage\/appeals\/([A-Za-z0-9._-]+)/);
                if (appealMatch) {
                    previewUrl = window.location.origin + '/attachments/appeals/' + appealMatch[1];
                }

                const downloadUrl = previewUrl + (previewUrl.indexOf('?') === -1 ? '?download=1' : '&download=1');

                if (title) title.textContent = name || 'Document Preview';

                const urlPath = previewUrl.split('?')[0].toLowerCase();
                const extMatch = urlPath.match(/\.([a-z0-9]+)$/i);
                const ext = extMatch ? extMatch[1].toLowerCase() : '';

                const KNOWN_IMAGES = ['jpg','jpeg','png','gif','webp','svg','bmp','avif'];
                const NON_RENDERABLE = ['doc','docx','xls','xlsx','ppt','pptx','odt','ods','zip','rar','7z','tgz','gz','tar','exe','msi','apk','iso'];

                window.resetAttachmentZoom(false);

                if (spinner) spinner.classList.remove('hidden');
                if (errorFallback) errorFallback.classList.add('hidden');
                if (img) {
                    img.classList.add('hidden');
                    if (img.getAttribute('src')) img.removeAttribute('src');
                }
                if (iframe) {
                    iframe.classList.add('hidden');
                    if (iframe.getAttribute('src')) iframe.removeAttribute('src');
                }
                if (downloadBtn) {
                    downloadBtn.href = downloadUrl;
                    downloadBtn.setAttribute('download', name || 'attachment');
                }

                function showErrorFallback() {
                    if (spinner) spinner.classList.add('hidden');
                    if (img) {
                        img.classList.add('hidden');
                        if (img.getAttribute('src')) img.removeAttribute('src');
                    }
                    if (iframe) {
                        iframe.classList.add('hidden');
                        if (iframe.getAttribute('src')) iframe.removeAttribute('src');
                    }
                    if (zoomControls) zoomControls.style.display = 'none';
                    const errorLink = document.getElementById('attachment_modal_error_link');
                    if (errorLink) {
                        errorLink.href = downloadUrl;
                        errorLink.setAttribute('download', name || 'attachment');
                    }
                    if (errorFallback) errorFallback.classList.remove('hidden');
                }

                function showAsImage(src) {
                    if (spinner) spinner.classList.add('hidden');
                    if (errorFallback) errorFallback.classList.add('hidden');
                    if (iframe) {
                        iframe.classList.add('hidden');
                        if (iframe.getAttribute('src')) iframe.removeAttribute('src');
                    }
                    if (img) {
                        img.onload = function() {
                            if (spinner) spinner.classList.add('hidden');
                            img.classList.remove('hidden');
                        };
                        img.onerror = function() {
                            showErrorFallback();
                        };
                        img.src = src;
                        img.classList.remove('hidden');
                    }
                    if (zoomControls) zoomControls.style.display = 'flex';
                }

                function showAsIframe(src) {
                    if (zoomControls) zoomControls.style.display = 'none';
                    if (img) {
                        img.classList.add('hidden');
                        if (img.getAttribute('src')) img.removeAttribute('src');
                    }
                    if (iframe) {
                        iframe.onload = function() {
                            if (spinner) spinner.classList.add('hidden');
                        };
                        iframe.src = src;
                        iframe.classList.remove('hidden');
                        window._attachmentIframeTimeout = setTimeout(function() {
                            if (spinner && !spinner.classList.contains('hidden')) {
                                showErrorFallback();
                            }
                        }, 10000);
                    }
                }

                if (NON_RENDERABLE.includes(ext)) {
                    showErrorFallback();
                } else if (KNOWN_IMAGES.includes(ext) || previewUrl.startsWith('data:image/')) {
                    showAsImage(previewUrl);
                } else if (ext === 'pdf') {
                    showAsIframe(previewUrl);
                } else {
                    // Dynamic route (e.g. /drivers/{id}/documents/mtop, /license, or extensionless appeal attachments)
                    // Inspect Content-Type via HEAD request so images render with full pinch-to-zoom
                    fetch(previewUrl, { method: 'HEAD', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(res => {
                            if (!res.ok) {
                                showErrorFallback();
                                return;
                            }
                            const contentType = (res.headers.get('content-type') || '').toLowerCase();
                            if (contentType.includes('image/') || contentType.includes('image')) {
                                showAsImage(previewUrl);
                            } else if (contentType.includes('pdf') || contentType.includes('application/pdf')) {
                                showAsIframe(previewUrl);
                            } else {
                                // Fallback: probe as image
                                const testImg = new Image();
                                testImg.onload = () => showAsImage(previewUrl);
                                testImg.onerror = () => showAsIframe(previewUrl);
                                testImg.src = previewUrl;
                            }
                        })
                        .catch(() => {
                            const testImg = new Image();
                            testImg.onload = () => showAsImage(previewUrl);
                            testImg.onerror = () => showAsIframe(previewUrl);
                            testImg.src = previewUrl;
                        });
                }

                document.body.style.overflow = 'hidden';
                modal.style.display = 'flex';
                modal.classList.remove('hidden');
            };

            window.closeAttachmentPreviewModal = function() {
                document.body.style.overflow = '';
                if (window._attachmentIframeTimeout) {
                    clearTimeout(window._attachmentIframeTimeout);
                    window._attachmentIframeTimeout = null;
                }
                const modal = document.getElementById('attachmentPreviewModal');
                const img = document.getElementById('attachment_modal_img');
                const iframe = document.getElementById('attachment_modal_iframe');
                const spinner = document.getElementById('attachment_modal_spinner');
                const errorFallback = document.getElementById('attachment_modal_error');
                if (modal) {
                    modal.style.display = 'none';
                    modal.classList.add('hidden');
                }
                if (img && img.getAttribute('src')) {
                    img.removeAttribute('src');
                }
                if (iframe && iframe.getAttribute('src')) {
                    iframe.removeAttribute('src');
                }
                if (spinner) spinner.classList.add('hidden');
                if (errorFallback) errorFallback.classList.add('hidden');
                window.resetAttachmentZoom(false);
            };

            // Global Touch Pinch-to-Zoom & Pan Gesture Engine Initialization
            (function initAttachmentGestureEngine() {
                function setupGestures() {
                    const body = document.getElementById('attachment_modal_body');
                    if (!body || body._srhGesturesBound) return;
                    body._srhGesturesBound = true;

                    function getPinchDist(e) {
                        if (!e.touches || e.touches.length < 2) return 0;
                        const dx = e.touches[0].clientX - e.touches[1].clientX;
                        const dy = e.touches[0].clientY - e.touches[1].clientY;
                        return Math.hypot(dx, dy);
                    }

                    function getPinchMid(e) {
                        if (!e.touches || e.touches.length < 2) {
                            return { x: e.touches ? e.touches[0].clientX : 0, y: e.touches ? e.touches[0].clientY : 0 };
                        }
                        return {
                            x: (e.touches[0].clientX + e.touches[1].clientX) / 2,
                            y: (e.touches[0].clientY + e.touches[1].clientY) / 2
                        };
                    }

                    body.addEventListener('touchstart', function(e) {
                        const activeImg = document.getElementById('attachment_modal_img');
                        if (!activeImg || activeImg.classList.contains('hidden') || !activeImg.src) return;

                        if (e.touches.length === 2) {
                            isPinchingAttachment = true;
                            isPanningAttachment = false;
                            initialPinchDistance = getPinchDist(e);
                            initialPinchZoom = currentAttachmentZoom;
                            initialPanX = attachmentPanX;
                            initialPanY = attachmentPanY;
                            initialPinchCenter = getPinchMid(e);
                        } else if (e.touches.length === 1) {
                            const now = Date.now();
                            if (now - lastAttachmentTapTime < 300) {
                                // Double Tap Gesture: Toggle between 1x and 2.5x
                                if (e.cancelable) e.preventDefault();
                                if (currentAttachmentZoom > 1) {
                                    window.resetAttachmentZoom(true);
                                } else {
                                    const rect = body.getBoundingClientRect();
                                    const tapX = e.touches[0].clientX - (rect.left + rect.width / 2);
                                    const tapY = e.touches[0].clientY - (rect.top + rect.height / 2);
                                    currentAttachmentZoom = 2.5;
                                    attachmentPanX = -tapX * 0.7;
                                    attachmentPanY = -tapY * 0.7;
                                    window.applyAttachmentTransform(true);
                                }
                                lastAttachmentTapTime = 0;
                                return;
                            }
                            lastAttachmentTapTime = now;

                            if (currentAttachmentZoom > 1) {
                                isPanningAttachment = true;
                                isPinchingAttachment = false;
                                startPanX = e.touches[0].clientX;
                                startPanY = e.touches[0].clientY;
                                initialPanX = attachmentPanX;
                                initialPanY = attachmentPanY;
                            }
                        }
                    }, { passive: false });

                    window.addEventListener('touchmove', function(e) {
                        const modal = document.getElementById('attachmentPreviewModal');
                        if (!modal || modal.style.display === 'none' || modal.classList.contains('hidden')) return;

                        if (isPinchingAttachment && e.touches.length >= 2 && initialPinchDistance > 0) {
                            if (e.cancelable) e.preventDefault();
                            const curDist = getPinchDist(e);
                            const curMid = getPinchMid(e);
                            const scale = curDist / initialPinchDistance;
                            currentAttachmentZoom = Math.max(0.6, Math.min(5, initialPinchZoom * scale));
                            attachmentPanX = initialPanX + (curMid.x - initialPinchCenter.x);
                            attachmentPanY = initialPanY + (curMid.y - initialPinchCenter.y);
                            window.applyAttachmentTransform(false);
                        } else if (isPanningAttachment && e.touches.length === 1 && currentAttachmentZoom > 1) {
                            if (e.cancelable) e.preventDefault();
                            attachmentPanX = initialPanX + (e.touches[0].clientX - startPanX);
                            attachmentPanY = initialPanY + (e.touches[0].clientY - startPanY);
                            window.applyAttachmentTransform(false);
                        }
                    }, { passive: false });

                    window.addEventListener('touchend', function(e) {
                        if (!e.touches || e.touches.length === 0) {
                            if (currentAttachmentZoom < 1) {
                                window.resetAttachmentZoom(true);
                            } else if (currentAttachmentZoom === 1) {
                                attachmentPanX = 0;
                                attachmentPanY = 0;
                                window.applyAttachmentTransform(true);
                            }
                            isPinchingAttachment = false;
                            isPanningAttachment = false;
                            initialPinchDistance = 0;
                        } else if (e.touches.length === 1 && isPinchingAttachment) {
                            // Seamless transition from 2 fingers to 1 finger
                            isPinchingAttachment = false;
                            initialPinchDistance = 0;
                            if (currentAttachmentZoom > 1) {
                                isPanningAttachment = true;
                                startPanX = e.touches[0].clientX;
                                startPanY = e.touches[0].clientY;
                                initialPanX = attachmentPanX;
                                initialPanY = attachmentPanY;
                            }
                        }
                    });

                    window.addEventListener('touchcancel', function() {
                        isPinchingAttachment = false;
                        isPanningAttachment = false;
                        initialPinchDistance = 0;
                        if (currentAttachmentZoom <= 1) {
                            window.resetAttachmentZoom(true);
                        }
                    });

                    // Desktop Mouse Drag & Wheel
                    body.addEventListener('mousedown', function(e) {
                        if (e.button !== 0) return;
                        if (currentAttachmentZoom > 1) {
                            isPanningAttachment = true;
                            startPanX = e.clientX;
                            startPanY = e.clientY;
                            initialPanX = attachmentPanX;
                            initialPanY = attachmentPanY;
                        }
                    });

                    window.addEventListener('mousemove', function(e) {
                        if (isPanningAttachment && currentAttachmentZoom > 1) {
                            e.preventDefault();
                            attachmentPanX = initialPanX + (e.clientX - startPanX);
                            attachmentPanY = initialPanY + (e.clientY - startPanY);
                            window.applyAttachmentTransform(false);
                        }
                    });

                    window.addEventListener('mouseup', function() {
                        isPanningAttachment = false;
                    });

                    body.addEventListener('wheel', function(e) {
                        const activeImg = document.getElementById('attachment_modal_img');
                        if (activeImg && !activeImg.classList.contains('hidden') && activeImg.src) {
                            e.preventDefault();
                            const delta = e.deltaY < 0 ? 0.25 : -0.25;
                            window.zoomAttachmentPreview(delta);
                        }
                    }, { passive: false });
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', setupGestures);
                } else {
                    setupGestures();
                }
                window.addEventListener('spa:page-loaded', setupGestures);
            })();
        </script>

        <!-- GLOBAL ATTACHMENT PREVIEW MODAL CONTAINER -->
        <div id="attachmentPreviewModal" class="fixed inset-0 z-[100000] hidden items-center justify-center p-2 sm:p-4 bg-slate-950/85 backdrop-blur-md transition-opacity duration-300" style="display: none;" onclick="if(event.target === this) closeAttachmentPreviewModal();">
            <div class="relative w-full max-w-4xl h-[90vh] bg-slate-900 border border-slate-700/80 rounded-3xl shadow-2xl flex flex-col overflow-hidden text-left" onclick="event.stopPropagation();">
                
                <!-- Modal Header -->
                <div class="px-4 py-3 bg-slate-900/90 border-b border-slate-800 flex items-center justify-between gap-3 shrink-0">
                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                        <div class="w-8 h-8 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0 border border-blue-500/30">
                            <span class="text-sm">📎</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Attachment Preview</p>
                            <h4 id="attachment_modal_title" class="text-xs sm:text-sm font-black text-white truncate">Document Preview</h4>
                        </div>
                    </div>

                    <!-- Action Controls -->
                    <div class="flex items-center gap-2 shrink-0">
                        <!-- Zoom Controls -->
                        <div id="attachment_zoom_controls" class="flex items-center gap-1 bg-slate-800/80 p-1 rounded-xl border border-slate-700">
                            <button type="button" onclick="zoomAttachmentPreview(-0.25)" class="w-7 h-7 rounded-lg bg-slate-700 hover:bg-slate-600 active:scale-95 text-white font-black text-xs flex items-center justify-center transition cursor-pointer border-none" title="Zoom Out">-</button>
                            <span id="attachment_zoom_level" class="text-[10px] font-black text-slate-300 px-1.5 min-w-[36px] text-center select-none">100%</span>
                            <button type="button" onclick="zoomAttachmentPreview(0.25)" class="w-7 h-7 rounded-lg bg-slate-700 hover:bg-slate-600 active:scale-95 text-white font-black text-xs flex items-center justify-center transition cursor-pointer border-none" title="Zoom In">+</button>
                            <button type="button" onclick="resetAttachmentZoom(true)" class="px-2 py-1 rounded-lg bg-slate-700 hover:bg-slate-600 active:scale-95 text-slate-300 font-bold text-[10px] transition cursor-pointer border-none" title="Reset Zoom">Reset</button>
                        </div>

                        <!-- Download Link -->
                        <a id="attachment_modal_download" href="#" target="_blank" download class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-black transition text-decoration-none shadow-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Download</span>
                        </a>

                        <!-- Close Button -->
                        <button type="button" onclick="closeAttachmentPreviewModal()" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-black text-sm flex items-center justify-center transition cursor-pointer border border-slate-700">✕</button>
                    </div>
                </div>

                <!-- Modal Body (Zoomable Container) -->
                <div id="attachment_modal_body" class="flex-1 w-full bg-slate-950 flex items-center justify-center relative overflow-hidden select-none touch-none p-2" style="touch-action: none; -webkit-user-select: none; user-select: none;">
                    <!-- Loading Spinner -->
                    <div id="attachment_modal_spinner" class="absolute z-10 flex flex-col items-center gap-2 text-slate-400">
                        <div class="w-8 h-8 border-3 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                        <span class="text-xs font-bold">Loading attachment...</span>
                    </div>

                    <!-- Error Fallback -->
                    <div id="attachment_modal_error" class="hidden absolute z-10 flex flex-col items-center gap-3 text-slate-300 p-4 text-center">
                        <div class="w-12 h-12 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center text-xl">⚠️</div>
                        <p class="text-xs font-bold max-w-sm">Unable to render file directly in browser preview.</p>
                        <a id="attachment_modal_error_link" href="#" target="_blank" download class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-black rounded-xl text-decoration-none shadow-md">Download Attachment File</a>
                    </div>

                    <div id="attachment_zoom_wrapper" class="relative w-full h-full flex items-center justify-center origin-center" style="transform: translate3d(0px, 0px, 0px) scale(1); touch-action: none; will-change: transform;">
                        <img id="attachment_modal_img" src="" alt="Attachment Preview" class="max-w-full max-h-full object-contain rounded-xl shadow-2xl hidden cursor-grab active:cursor-grabbing" draggable="false" style="touch-action: none; -webkit-user-drag: none; user-select: none; -webkit-user-select: none;">
                        <iframe id="attachment_modal_iframe" src="" class="w-full h-full border-none rounded-xl bg-white hidden"></iframe>
                    </div>
                </div>
            </div>
        </div>

        <!-- GLOBAL RIDE CHAT MODAL CONTAINER -->
        @include('partials.ride-chat-modal')

        <!-- MAINTENANCE MODE: GLOBAL OVERLAY + LIVE POLLER (detects admin toggles instantly, no reload needed) -->
        <div id="srh-maintenance-overlay" style="display:none; position:fixed; inset:0; z-index:2147483000; background:#0f172a; align-items:center; justify-content:center; padding:1.5rem; font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">
            @include('partials.maintenance-screen')
        </div>
        <script>
            (function () {
                var overlay = document.getElementById('srh-maintenance-overlay');
                if (!overlay) return;
                var down = false;

                if (window.__srhMaintenanceTimer) {
                    clearInterval(window.__srhMaintenanceTimer);
                    window.__srhMaintenanceTimer = null;
                }

                function handleMaintenanceEvent(data) {
                    var active = !!(data && data.active);
                    if (active && !down) {
                        down = true;
                        overlay.style.display = 'flex';
                        var msg = document.getElementById('srh-maint-message');
                        if (msg && data && data.message) msg.textContent = data.message;
                    } else if (!active && down) {
                        down = false;
                        overlay.style.display = 'none';
                        if (window.createSlidingToast) window.createSlidingToast('The system is back online. Welcome back!', 'success');
                    }
                }

                function attachMaintenanceEcho() {
                    if (typeof window.Echo !== 'undefined' && window.Echo) {
                        try {
                            window.Echo.channel('srh-system-status')
                                .stopListening('.maintenance.status')
                                .listen('.maintenance.status', handleMaintenanceEvent);
                            window.Echo.channel('srh-toda-queue')
                                .stopListening('.maintenance.status')
                                .listen('.maintenance.status', handleMaintenanceEvent);
                        } catch (e) {}
                    }
                }

                if (window.srhOnEchoReady) {
                    window.srhOnEchoReady(attachMaintenanceEcho);
                } else {
                    attachMaintenanceEcho();
                }
            })();
        </script>
        @if(auth()->check() && auth()->user()->role === 'driver')
        <script>
            (function() {
                var currentDriverStatus = "{{ optional(auth()->user()->driverProfile)->compliance_status ?? 'Approved' }}";
                var currentDriverId = {{ optional(auth()->user()->driverProfile)->id ?? 'null' }};
                var isHandlingTransition = false;

                if (window.__srhComplianceTimer) {
                    clearInterval(window.__srhComplianceTimer);
                    window.__srhComplianceTimer = null;
                }

                function handleComplianceEvent(data) {
                    if (!data || isHandlingTransition) return;
                    if (currentDriverId && data.driverId && String(data.driverId) !== String(currentDriverId)) return;

                    var newStatus = data.status;
                    if (!newStatus) return;

                    if (currentDriverStatus && newStatus.toLowerCase() !== currentDriverStatus.toLowerCase()) {
                        isHandlingTransition = true;
                        currentDriverStatus = newStatus;
                        if (window.clearPageCache) window.clearPageCache();

                        if (newStatus.toLowerCase() === 'suspended') {
                            if (window.createSlidingToast) {
                                window.createSlidingToast('Your driver account has been suspended by TODA Admin.', 'danger');
                            }
                            setTimeout(function() {
                                window.location.href = "{{ route('dashboard') }}";
                            }, 800);
                        } else if (newStatus.toLowerCase() === 'approved') {
                            if (window.createSlidingToast) {
                                window.createSlidingToast('Your driver account has been unsuspended and reactivated!', 'success');
                            }
                            setTimeout(function() {
                                window.location.href = "{{ route('dashboard') }}";
                            }, 800);
                        } else {
                            window.location.reload();
                        }
                    }
                }

                function attachComplianceEcho() {
                    if (typeof window.Echo !== 'undefined' && window.Echo) {
                        try {
                            window.Echo.channel('srh-toda-admin')
                                .stopListening('.driver.applicant.updated')
                                .listen('.driver.applicant.updated', handleComplianceEvent);
                        } catch (e) {}
                    }
                }

                if (window.srhOnEchoReady) {
                    window.srhOnEchoReady(attachComplianceEcho);
                } else {
                    attachComplianceEcho();
                }
            })();
        </script>
        @endif
    </body>
</html>