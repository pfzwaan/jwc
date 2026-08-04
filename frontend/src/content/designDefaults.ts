/**
 * Design fallbacks for empty CMS media.
 *
 * Filament's FileUpload can't represent the seeded "/images/design/…" paths (they
 * don't live on the uploads disk), so an untouched image field can come back as
 * `""` or `[]`. Rather than blanking a section, the frontend falls back to the
 * image the design ships with — the layout is never broken by an empty field.
 *
 * The backend guards the same case on save (App\Support\MediaState).
 */

/** Empty means: null, undefined, "", "[]", or an empty array. */
export function isEmptyMedia(value: unknown): boolean {
  if (value === null || value === undefined) return true;
  if (Array.isArray(value)) return value.length === 0;
  if (typeof value === 'string') {
    const v = value.trim();
    return v === '' || v === '[]';
  }
  return true;
}

/** The stored value if it holds anything, else the design default. */
export function mediaOr(value: unknown, fallback: string): string {
  if (isEmptyMedia(value)) return fallback;
  // A FileUpload can also hand back a single-entry array.
  if (Array.isArray(value)) return String(value[0] ?? fallback);
  return String(value);
}

export const designDefaults = {
  // Section 3 — Hero. The Figma layer is a video placeholder, so there is no
  // still to fall back to yet; an empty value renders the dark overlay only.
  hero: {
    image: '',
    video: '',
  },

  // Section 4 — Logos. The design uses "logoipsum" placeholders, so there is
  // nothing meaningful to fall back to; empty simply hides the strip.
  logos: [] as string[],

  // Section 5 — the four ICT solution cards, in design order.
  solutionIcons: [
    '/images/design/icon-support.svg',
    '/images/design/icon-cloud.svg',
    '/images/design/icon-network.svg',
    '/images/design/icon-web.svg',
  ],

  // Section 8 — project screenshots, in design order.
  projectImages: [
    '/images/design/project-ov-opheusden.png',
    '/images/design/project-vanmanenkleinfruit.png',
    '/images/design/project-vandoorn.png',
    '/images/design/project-willemsen-bouw.png',
  ],

  // Section 6 — one photo per dienst, keyed by slug.
  dienstImages: {
    'ict-support': '/images/design/dienst-ict-support.png',
    'microsoft-365': '/images/design/dienst-microsoft-365.png',
    'website-hosting': '/images/design/dienst-website-hosting.png',
    'cloud-diensten': '/images/design/dienst-cloud-diensten.png',
    'netwerk-telefonie': '/images/design/dienst-netwerk-telefonie.png',
  } as Record<string, string>,
};

/** Design photo for a dienst, by slug. Empty when the slug isn't a designed one. */
export function dienstImageFor(slug: string): string {
  return designDefaults.dienstImages[slug] ?? '';
}
