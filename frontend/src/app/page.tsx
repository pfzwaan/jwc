import type { Metadata } from 'next';
import { fetchPage, fetchSettings } from '@/lib/api';
import { pageMetadata } from '@/lib/seo';
import { BlockRenderer } from '@/components/blocks/BlockRenderer';

/**
 * Homepage — the CMS page with slug "home". Fully server-rendered so every
 * section is crawlable.
 */
export async function generateMetadata(): Promise<Metadata> {
  const page = await fetchPage('home');
  if (!page) return {};

  return pageMetadata({ seo: page.seo, title: page.title });
}

export default async function HomePage() {
  const [page, settings] = await Promise.all([fetchPage('home'), fetchSettings()]);

  if (!page) {
    return (
      <section className="mx-auto max-w-[900px] px-6 py-24">
        <h1 className="font-heading text-3xl font-extrabold">Nog geen homepage</h1>
        <p className="mt-4 text-[color:var(--color-ink)]/70">
          Maak in de admin een pagina aan met slug <code>home</code> en voeg er widgets aan toe.
        </p>
      </section>
    );
  }

  return (
    <>
      {page.blocks.map((block) => (
        <BlockRenderer key={block.id} block={block} settings={settings} source="/" />
      ))}
    </>
  );
}
