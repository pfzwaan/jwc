/**
 * Narrowing helpers for CMS block data, which arrives as `unknown` from the API.
 *
 * These live here rather than in BlockRenderer on purpose: every block imports
 * them, and BlockRenderer imports every block, so keeping them together made
 * the module graph circular — which Turbopack surfaced as
 * "ReferenceError: require is not defined" the moment a block was also imported
 * from a page.
 */
export function str(v: unknown): string {
  return typeof v === 'string' ? v : '';
}

export function arr(v: unknown): Record<string, unknown>[] {
  return Array.isArray(v) ? (v as Record<string, unknown>[]) : [];
}
