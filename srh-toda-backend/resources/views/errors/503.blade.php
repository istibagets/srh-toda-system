<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a">
    <title>{{ \App\Support\SystemSettings::brandName() }} — Under Maintenance</title>
    <link rel="icon" type="image/png" href="{{ srh_logo_url() }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #0f172a;
            color: #f1f5f9;
            -webkit-font-smoothing: antialiased;
        }
    </style>
    <!-- Laravel Reverb & WebSockets (100% Local Bundle) -->
    <script src="{{ asset('vendor/echo/pusher.min.js') }}"></script>
    <script src="{{ asset('vendor/echo/echo.iife.js') }}"></script>
</head>
<body>
    {{-- Same markup & styles as the in-app overlay so a reload looks identical --}}
    <div id="srh-maintenance-overlay" style="display:flex; position:fixed; inset:0; z-index:2147483000; background:#0f172a; align-items:center; justify-content:center; padding:1.5rem; font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">
        @include('partials.maintenance-screen')
    </div>
    <script>
        (function () {
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
            }

            function handleMaintenanceEvent(data) {
                if (data && data.active === false) {
                    location.reload();
                }
            }

            function attach503Echo() {
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

            attach503Echo();

            if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
                window.Echo.connector.pusher.connection.bind('connected', attach503Echo);
            }
        })();
    </script>
</body>
</html>
