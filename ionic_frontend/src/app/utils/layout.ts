/**
 * Real rendered height of the bottom tab bar, including the iPhone home-indicator
 * inset. Falls back to the nominal 56px bar when it is not in the DOM.
 */
export function getTabBarHeight(): number {
  if (typeof document === 'undefined') return 56;
  const bar = document.querySelector('ion-tab-bar') as HTMLElement | null;
  const h = bar?.getBoundingClientRect().height ?? 0;
  return h >= 40 ? Math.round(h) : 56;
}

/**
 * Natural (content-sized) height of a bottom sheet. The sheet normally has a fixed tall
 * height; briefly letting it size to its content tells us exactly how much of it must be
 * visible so nothing is clipped on any device, font size or safe-area combination.
 */
export function measureNaturalHeight(el: HTMLElement | null | undefined): number {
  if (!el) return 0;
  const prev = el.style.height;
  el.style.height = 'auto';
  const h = el.offsetHeight;
  el.style.height = prev;
  return Math.ceil(h);
}
