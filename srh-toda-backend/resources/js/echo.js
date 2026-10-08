import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Determine environment host dynamically at runtime on the client side.
// If VITE_REVERB_HOST is missing, 127.0.0.1, localhost, or 0.0.0.0, fallback to window.location.hostname.
const envHost = import.meta.env.VITE_REVERB_HOST;
const host = (envHost && envHost !== '127.0.0.1' && envHost !== 'localhost' && envHost !== '0.0.0.0')
    ? envHost
    : (window.location.hostname || '127.0.0.1');

// Determine TLS based on runtime window.location.protocol or env scheme
const isHttps = window.location.protocol === 'https:' || import.meta.env.VITE_REVERB_SCHEME === 'https';

// Port configuration:
// For HTTPS, Nginx proxies WSS on port 443 -> 127.0.0.1:8080.
// For HTTP local dev, direct port 8080 is used.
const envPort = import.meta.env.VITE_REVERB_PORT;
const wsPort = envPort ? parseInt(envPort) : 8080;
const wssPort = isHttps ? (window.location.port ? parseInt(window.location.port) : 443) : (envPort ? parseInt(envPort) : 443);

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY || 'srhlinktodakey',
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

// Graceful connection error handler to prevent unhandled console noise if Reverb is offline
if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
    window.Echo.connector.pusher.connection.bind('error', (err) => {
        if (import.meta.env.DEV) {
            console.warn('[Reverb WebSocket Warning]', err);
        }
    });
}

try {
    window.dispatchEvent(new CustomEvent('echo:ready', { detail: window.Echo }));
} catch (e) {}