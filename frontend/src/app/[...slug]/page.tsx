import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import { fetchPage, fetchSettings } from '@/lib/api';
import { pageMetadata } from '@/lib/seo';
import { BlockRenderer } from '@/components/blocks/BlockRenderer';

/**
 * Catch-all CMS route. Pages are looked up by their leaf slug (the last URL
 * segment), so nesting a page under a parent doesn't change its lookup key.
 * Server-rendered for SEO.
 *
 * Next.js 16: `params` is a Promise and must be awaited.
 */
type Params = { slug: string[] };

function leafSlug(slug: string[]): string {
  return slug[slug.length - 1] ?? '';
}

export async function generateMetadata({ params }: { params: Promise<Params> }): Promise<Metadata> {
  const { slug } = await params;
  const page = await fetchPage(leafSlug(slug));
  if (!page) return {};

  return pageMetadata({ seo: page.seo, title: page.title });
}

export default async function CmsPage({ params }: { params: Promise<Params> }) {
  const { slug } = await params;
  const [page, settings] = await Promise.all([fetchPage(leafSlug(slug)), fetchSettings()]);

  if (!page) notFound();

  const path = '/' + slug.join('/');
  // Pages that don't open with a full-bleed hero need to clear the floating
  // menu bar, which is absolutely positioned over the content.
  const hasHero = page.blocks[0]?.type === 'hero';

  return (
    <div className={hasHero ? undefined : 'pt-[163px]'}>
      {page.blocks.map((block) => (
        <BlockRenderer key={block.id} block={block} settings={settings} source={path} />
      ))}
    </div>
  );
}
