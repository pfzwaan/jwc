import type { Metadata } from 'next';

/** Stripped of tags and collapsed, then cut on a word boundary near 160 chars. */
export function toDescription(html: string | null | undefined, max = 160): string {
  const text = (html ?? '')
    .replace(/<[^>]+>/g, ' ')
    .replace(/\s+/g, ' ')
    .trim();

  if (text.length <= max) return text;

  const cut = text.slice(0, max);

  return `${cut.slice(0, cut.lastIndexOf(' '))}…`;
}

/**
 * Builds page metadata from a CMS `seo` object with sensible fallbacks.
 *
 * A staff-written seo.title is used verbatim (`absolute`) so the brand suffix
 * isn't appended twice — they control the whole string. Anything falling back
 * goes through the root layout's title template instead, which keeps the suffix
 * consistent without every route repeating it.
 */
export function pageMetadata({
  seo,
  title,
  description,
  image,
}: {
  seo?: { title?: string; description?: string } | null;
  title: string;
  description?: string | null;
  image?: string | null;
}): Metadata {
  const seoTitle = seo?.title?.trim();
  const seoDescription = seo?.description?.trim();
  const resolvedDescription = seoDescription || toDescription(description) || undefined;

  return {
    title: seoTitle ? { absolute: seoTitle } : title,
    description: resolvedDescription,
    openGraph: {
      title: seoTitle || title,
      description: resolvedDescription,
      images: image ? [image] : undefined,
    },
  };
}
