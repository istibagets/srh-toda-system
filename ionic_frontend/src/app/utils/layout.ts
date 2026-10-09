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

function blockHeight(el: Element): number {
  const cs = getComputedStyle(el);
  if (cs.display === 'none') return 0;
  if (cs.position === 'absolute' || cs.position === 'fixed') return 0;
  // Custom elements (<app-drop-off-card>) are inline by default: measure their children instead
  if (cs.display === 'inline' || cs.display === 'contents') {
    return Array.from(el.children).reduce((h, c) => h + blockHeight(c), 0);
  }
  return (
    (el as HTMLElement).getBoundingClientRect().height +
    (parseFloat(cs.marginTop) || 0) +
    (parseFloat(cs.marginBottom) || 0)
  );
}

/**
 * Height a bottom sheet needs to show its drag handle plus ALL of its current content.
 * Measures the content blocks themselves (not the stretched sheet container), so it follows
 * whatever state is rendered on any device, font size or safe-area combination.
 */
export function measureNaturalHeight(sheet: HTMLElement | null | undefined): number {
  if (!sheet) return 0;
  const handle = sheet.querySelector('.sheet-drag-handle') as HTMLElement | null;
  const content = sheet.querySelector('.sheet-details-content') as HTMLElement | null;
  if (!content) return 0;
  const cs = getComputedStyle(content);
  const padding = (parseFloat(cs.paddingTop) || 0) + (parseFloat(cs.paddingBottom) || 0);
  const inner = Array.from(content.children).reduce((h, c) => h + blockHeight(c), 0);
  return Math.ceil((handle?.getBoundingClientRect().height || 0) + padding + inner);
}
