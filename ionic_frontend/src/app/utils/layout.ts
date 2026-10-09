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
