import { Injectable } from '@angular/core';

/**
 * Removes the iOS rubber-band stretch when a page is pulled DOWN while already at the top,
 * so it doesn't fight the pull-to-refresh gesture. Bouncing at the bottom (pulling up past
 * the end of the content) is left alone.
 *
 * Works by cancelling the native touchmove default only when the scroll container is at
 * scrollTop 0 and the finger is moving down. Ionic's refresher gesture still receives the
 * touch events, so pull-to-refresh keeps working.
 */
@Injectable({ providedIn: 'root' })
export class TopBounceGuardService {
  private started = false;
  private lastY = 0;
  private lastX = 0;

  /** Areas that manage their own touch gestures (map, draggable sheets, modals). */
  private readonly EXCLUDE = '.maplibregl-map, .grab-fullscreen-map, .srh-edge-bottom-sheet, ion-modal, ion-popover, ion-toast';

  init(): void {
    if (this.started || typeof document === 'undefined') return;
    this.started = true;

    document.addEventListener(
      'touchstart',
      (e: TouchEvent) => {
        const t = e.touches[0];
        if (!t) return;
        this.lastY = t.clientY;
        this.lastX = t.clientX;
      },
      { passive: true, capture: true }
    );

    document.addEventListener(
      'touchmove',
      (e: TouchEvent) => {
        const t = e.touches[0];
        if (!t) return;
        const dy = t.clientY - this.lastY;
        const dx = t.clientX - this.lastX;
        this.lastY = t.clientY;
        this.lastX = t.clientX;

        // Only a downward, mostly-vertical pull
        if (dy <= 0 || Math.abs(dy) < Math.abs(dx) || !e.cancelable) return;

        const path = e.composedPath() as HTMLElement[];
        for (const node of path) {
          if (!(node instanceof HTMLElement)) continue;
          if (node.matches?.(this.EXCLUDE)) return;
        }

        const scroller = this.findScroller(path);
        // At the very top of the scroller: stop the stretch (nothing above to reveal)
        if (!scroller || scroller.scrollTop <= 0) {
          e.preventDefault();
        }
      },
      { passive: false, capture: true }
    );
  }

  /** Nearest vertically-scrollable element under the finger (Ionic's inner scroll included). */
  private findScroller(path: HTMLElement[]): HTMLElement | null {
    for (const node of path) {
      if (!(node instanceof HTMLElement)) continue;
      if (node === document.body || node === document.documentElement) break;
      if (node.classList?.contains('inner-scroll')) return node;
      const oy = getComputedStyle(node).overflowY;
      if ((oy === 'auto' || oy === 'scroll') && node.scrollHeight > node.clientHeight + 1) {
        return node;
      }
    }
    return null;
  }
}
