import { Injectable, inject, signal, computed } from '@angular/core';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { environment } from '../../environments/environment';
import { AuthService } from './auth.service';
import { DriverService } from './driver.service';
import { NotificationService } from './notification.service';
import { SoundService } from './sound.service';
import { PushService } from './push.service';
import { MaintenanceService } from './maintenance.service';

declare global {
  interface Window {
    Pusher: typeof Pusher;
    Echo: Echo<any>;
  }
}

@Injectable({
  providedIn: 'root',
})
export class RealtimeService {
  private authService = inject(AuthService);
  private driverService = inject(DriverService);
  private notificationService = inject(NotificationService);
  private soundService = inject(SoundService);
  private pushService = inject(PushService);
  private maintenanceService = inject(MaintenanceService);

  private echoInstance: Echo<any> | null = null;
  private isConnectedSignal = signal<boolean>(false);
  readonly isConnected = computed(() => this.isConnectedSignal());

  /** Increments every time the socket comes back after a drop (consumers resync once). */
  private reconnectTick = signal<number>(0);
  readonly reconnected = this.reconnectTick.asReadonly();
  private hasConnectedOnce = false;

  private rideLocationRideId: number | null = null;
  private rideLocationRetry: ReturnType<typeof setTimeout> | null = null;
  private rideLocationAttempts = 0;

  get echo(): Echo<any> | null {
    return this.echoInstance;
  }

  constructor() {
    this.initEcho();
  }

  private initEcho(): void {
    if (typeof window === 'undefined') return;

    window.Pusher = Pusher;

    try {
      const isHttps = typeof window !== 'undefined' && window.location.protocol === 'https:';
      const token = this.authService.token();
      const host = environment.reverb?.host || (isHttps ? 'srh-link-toda.duckdns.org' : window.location.hostname);
      const port = isHttps ? 443 : (environment.reverb?.port || 8080);
      const scheme = isHttps ? 'https' : (environment.reverb?.scheme || 'http');

      this.echoInstance = new Echo({
        broadcaster: 'reverb',
        key: environment.reverb?.appKey || 'srhlinktodakey',
        wsHost: host,
        wsPort: port,
        wssPort: port,
        forceTLS: scheme === 'https',
        enabledTransports: ['ws', 'wss'],
        disableStats: true,
        authEndpoint: `${environment.apiUrl}/broadcasting/auth`,
        auth: {
          headers: {
            Authorization: token ? `Bearer ${token}` : '',
            Accept: 'application/json',
          },
        },
        // Private channels are authorised with the CURRENT login token each time (read at call
        // time, so it still works if the user logged in after the socket was created).
        channelAuthorization: {
          transport: 'ajax',
          endpoint: `${environment.apiUrl}/broadcasting/auth`,
          customHandler: (
            params: { socketId: string; channelName: string },
            callback: (error: Error | null, data: any) => void
          ) => {
            const t = this.authService.token();
            fetch(`${environment.apiUrl}/broadcasting/auth`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                Authorization: t ? `Bearer ${t}` : '',
              },
              body: JSON.stringify({ socket_id: params.socketId, channel_name: params.channelName }),
            })
              .then((r) => {
                if (!r.ok) throw new Error(`Channel auth failed (${r.status})`);
                return r.json();
              })
              .then((data) => callback(null, data))
              .catch((err) => callback(err, null));
          },
        },
      } as any);

      window.Echo = this.echoInstance;

      // Listen on public queue channel
      const handleQueueEvent = (e: any) => {
        if (e && Array.isArray(e.active_queue)) {
          this.driverService.syncFromDashboard({
            driver: {
              active_queue: e.active_queue,
              total_queue_count: e.total_queue_count ?? e.active_queue.length,
            },
            admin: {
              active_queue: e.active_queue,
              total_queue_count: e.total_queue_count ?? e.active_queue.length,
            },
          });
        }
        this.driverService.triggerLiveSync();
      };

      // Listen on public system status and maintenance events
      const handleMaintenanceEvent = (e: any) => {
        if (e) {
          const isDown =
            typeof e.active === 'boolean'
              ? e.active
              : typeof e.is_maintenance === 'boolean'
              ? e.is_maintenance
              : false;
          this.maintenanceService.setMaintenanceState(isDown, e.message);
        }
      };

      this.echoInstance
        .channel('srh-system-status')
        .listen('.maintenance.status', handleMaintenanceEvent)
        .listen('maintenance.status', handleMaintenanceEvent)
        .listen('.MaintenanceModeToggled', handleMaintenanceEvent)
        .listen('MaintenanceModeToggled', handleMaintenanceEvent);

      this.echoInstance
        .channel('srh-toda-queue')
        .listen('.queue.changed', handleQueueEvent)
        .listen('queue.changed', handleQueueEvent)
        .listen('QueueUpdated', handleQueueEvent)
        .listen('.maintenance.status', handleMaintenanceEvent)
        .listen('maintenance.status', handleMaintenanceEvent)
        .listen('.MaintenanceModeToggled', handleMaintenanceEvent)
        .listen('MaintenanceModeToggled', handleMaintenanceEvent);

      // Listen on public rides channel
      const handleRideEvent = (e: any) => {
        if (e && e.ride) {
          this.driverService.handleRideStatusBroadcast(e);
        }
        this.driverService.triggerLiveSync();
      };

      this.echoInstance
        .channel('srh-toda-rides')
        .listen('.ride.status.updated', handleRideEvent)
        .listen('ride.status.updated', handleRideEvent)
        .listen('RideStatusUpdated', handleRideEvent)
        .listen('.queue.changed', handleQueueEvent)
        .listen('queue.changed', handleQueueEvent)
        .listen('QueueUpdated', handleQueueEvent)
        .listen('.maintenance.status', handleMaintenanceEvent)
        .listen('maintenance.status', handleMaintenanceEvent)
        .listen('.MaintenanceModeToggled', handleMaintenanceEvent)
        .listen('MaintenanceModeToggled', handleMaintenanceEvent);

      // Driver GPS is NOT on a public channel any more: each active ride has its own private
      // channel (see watchRideLocation) that only that ride's passenger and driver can join.

      // Connection health: Pusher/Reverb reconnects by itself, but any event sent while the
      // socket was down is gone. Tell the app when the socket is back so it can resync once.
      try {
        const pusher: any = (this.echoInstance as any).connector?.pusher;
        pusher?.connection?.bind('state_change', (states: { previous: string; current: string }) => {
          if (states.current === 'connected') {
            if (this.hasConnectedOnce && states.previous !== 'connected') {
              this.reconnectTick.update((n) => n + 1);
            }
            this.hasConnectedOnce = true;
            this.isConnectedSignal.set(true);
          } else if (states.current === 'unavailable' || states.current === 'disconnected' || states.current === 'failed') {
            this.isConnectedSignal.set(false);
          }
        });
      } catch { }

      // Listen on public announcements channel
      const handleAnnouncementEvent = (e: any) => {
        if (e && e.announcement) {
          const ann = e.announcement;
          const role = this.authService.userRole() || 'passenger';
          const target = (ann.target_audience || 'ALL').toUpperCase();
          const matches =
            target === 'ALL' ||
            (target === 'DRIVERS' && (role === 'driver' || role === 'admin' || role === 'superadmin')) ||
            (target === 'PASSENGERS' && (role === 'passenger' || role === 'admin' || role === 'superadmin')) ||
            (target === 'ADMIN' && (role === 'admin' || role === 'superadmin'));

          if (matches) {
            this.notificationService.addAnnouncement({
              id: ann.id,
              title: ann.title,
              message: ann.message,
              targetAudience: ann.target_audience || 'ALL',
              createdAt: 'Just now',
              isRead: false,
              actionUrl: ann.action_url,
            });

            try {
              this.soundService.playBookingAlert();
            } catch {}
          }
        }
      };

      this.echoInstance
        .channel('srh-toda-announcements')
        .listen('.AnnouncementCreated', handleAnnouncementEvent)
        .listen('AnnouncementCreated', handleAnnouncementEvent)
        .listen('.maintenance.status', handleMaintenanceEvent)
        .listen('maintenance.status', handleMaintenanceEvent);

      // Listen on admin channel
      this.echoInstance
        .channel('srh-toda-admin')
        .listen('.driver.applicant.updated', () => this.driverService.triggerLiveSync())
        .listen('driver.applicant.updated', () => this.driverService.triggerLiveSync())
        .listen('.report.updated', () => this.driverService.triggerLiveSync())
        .listen('report.updated', () => this.driverService.triggerLiveSync())
        .listen('.AnnouncementCreated', handleAnnouncementEvent)
        .listen('AnnouncementCreated', handleAnnouncementEvent);

      this.isConnectedSignal.set(true);
    } catch (err) {
      console.warn('[Realtime] Echo initialization notice:', err);
    }
  }

  /**
   * Follows the driver's live position for ONE ride on its private channel. Pass null to stop.
   * Safe to call repeatedly: it only re-subscribes when the ride changes.
   */
  watchRideLocation(rideId: number | null): void {
    if (!this.echoInstance) return;
    if (this.rideLocationRideId === rideId) return;

    if (this.rideLocationRetry) { clearTimeout(this.rideLocationRetry); this.rideLocationRetry = null; }
    if (this.rideLocationRideId !== null) {
      try { this.echoInstance.leave(`srh-ride-location.${this.rideLocationRideId}`); } catch { }
    }
    this.rideLocationRideId = rideId;
    if (rideId === null) return;

    const token = this.authService.token();
    if (token) this.updateAuthToken(token);

    const handle = (e: any) => {
      if (e && (e.driverId || e.driver_id)) this.driverService.handleLocationBroadcast(e);
    };
    try {
      const channel: any = this.echoInstance.private(`srh-ride-location.${rideId}`);
      channel.listen('.tricycle.location', handle).listen('tricycle.location', handle);
      // A failed subscription (slow network, expired auth) is retried instead of silently lost
      channel.subscribed?.(() => { this.rideLocationAttempts = 0; });
      channel.error?.(() => this.retryRideLocation(rideId));
    } catch {
      this.retryRideLocation(rideId);
    }
  }

  /** Backoff retry (3s, 6s, 12s … max 30s) for a private-channel subscription that failed. */
  private retryRideLocation(rideId: number): void {
    if (this.rideLocationRideId !== rideId) return;
    this.rideLocationRideId = null;
    try { this.echoInstance?.leave(`srh-ride-location.${rideId}`); } catch { }
    const delay = Math.min(3000 * Math.pow(2, this.rideLocationAttempts++), 30000);
    this.rideLocationRetry = setTimeout(() => this.watchRideLocation(rideId), delay);
  }

  updateAuthToken(token: string): void {
    if (this.echoInstance) {
      this.echoInstance.options.auth = {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: 'application/json',
        },
      };
    }
  }
}
