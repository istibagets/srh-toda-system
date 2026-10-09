/**
 * Terminal walk-in and wayside destinations. Each one is set by the Superadministrator with a fixed
 * estimated fare for 1, 2, 3 and 4 passengers. There is no formula: the fare is only a base for the
 * earnings records, and the driver and passenger can still negotiate. The server looks up the same
 * table (laravel_backend SystemSettings::walkinFare), so it does not rely on the amount sent by the app.
 */

export interface WalkinZone {
  name: string;
  /** Fares for 1, 2, 3 and 4 passengers. */
  fares: number[];
}

/** Fare of a zone for a passenger count (1 to 4). */
export function walkinFare(zone: WalkinZone | null | undefined, pax: number): number {
  if (!zone) return 0;
  const i = Math.min(Math.max(pax, 1), 4) - 1;
  const v = Number(zone.fares?.[i]);
  return isFinite(v) ? v : 0;
}

/** Normalizes zones coming from the server. */
export function normalizeWalkinZones(raw: any[] | null | undefined): WalkinZone[] {
  if (!Array.isArray(raw)) return [];
  return raw
    .filter((z) => z && z.name && Array.isArray(z.fares) && z.fares.length === 4)
    .map((z) => ({ name: String(z.name), fares: z.fares.map((f: any) => Number(f) || 0) }));
}

const row = (name: string, base: number): WalkinZone => ({ name, fares: [base, base, base + 5, base + 10] });

/** Same defaults as laravel_backend SystemSettings::defaultWalkinZones(). */
export const DEFAULT_WALKIN_ZONES: WalkinZone[] = [
  row('Main Gate Guard House', 50), row('Clubhouse', 50), row('Santa Rosa Public Market', 60), row('SM Cabanatuan', 120),
  row('Santa Rosa Municipal Hall', 60), row('Brgy. La Fuente', 50), row('Brgy. Cojuangco', 50), row('Brgy. Mapalad', 75),
  row('Brgy. Rizal', 75), row('Robinsons Cabanatuan', 140), row('Cabanatuan City Hall', 165),
  row('Zaragoza Public Market', 250), row('San Leonardo Public Market', 190), row('Gapan City Public Market', 260),
];
