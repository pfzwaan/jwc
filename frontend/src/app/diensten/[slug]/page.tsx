import type { Metadata } from 'next';
import Link from 'next/link';
import { notFound } from 'next/navigation';
import { fetchDienst, fetchDiensten, fetchPage, fetchSettings } from '@/lib/api';
import { BlockRenderer } from '@/components/blocks/BlockRenderer';
import { AnimateIn } from '@/components/ui/AnimateIn';
import { ArrowChevron } from '@/components/ui/ArrowChevron';
import { CheckIcon } from '@/components/ui/CheckIcon';
import { SmartImage } from '@/components/ui/SmartImage';
import { dienstImageFor, mediaOr } from '@/content/designDefaults';
import { safeUrl } from '@/lib/safeUrl';
import { pageMetadata } from '@/lib/seo';

/**
 * A dienst page. Staff can build one as a full CMS page (like Website &
 * Hosting) — those widgets win. Any dienst without its own page still gets a
 * real page from this template rather than a 404.
 *
 * Next.js 16: `params` is a Promise and must be awaited.
 */
type Params = { slug: string };

export async function generateMetadata({ params }: { params: Promise<Params> }): Promise<Metadata> {
  const { slug } = await params;
  const page = await fetchPage(slug);
  if (page) return pageMetadata({ seo: page.seo, title: page.title });

  const dienst = await fetchDienst(slug);
  if (!dienst) return {};

  return pageMetadata({ title: dienst.title, description: dienst.excerpt, image: dienst.image });
}

export async function generateStaticParams() {
  const diensten = await fetchDiensten();

  return diensten.map((d) => ({ slug: d.slug }));
}

export default async function DienstPage({ params }: { params: Promise<Params> }) {
  const { slug } = await params;
  const [page, settings] = await Promise.all([fetchPage(slug), fetchSettings()]);

  // A hand-built CMS page takes precedence over the generated template.
  if (page) {
    const hasHero = page.blocks[0]?.type === 'hero';

    return (
      <div className={hasHero ? undefined : 'pt-[163px]'}>
        {page.blocks.map((block) => (
          <BlockRenderer key={block.id} block={block} settings={settings} source={`/diensten/${slug}`} />
        ))}
      </div>
    );
  }

  const dienst = await fetchDienst(slug);
  if (!dienst) notFound();

  const accent = dienst.accent || '#fcb040';
  const image = mediaOr(dienst.image, dienstImageFor(dienst.slug));

  return (
    <>
      {/* Hero — same compact/left treatment as the other subpages. */}
      <section className="relative w-full overflow-hidden bg-[color:var(--color-overlay)]">
        <div className="pointer-events-none absolute inset-0 hidden xl:block" aria-hidden="true">
          {/* eslint-disable-next-line @next/next/no-img-element */}
          <img
            src="/images/design/hero-swoosh-right.svg"
            alt=""
            className="absolute left-[1150px] top-[-629px] h-[1384px] w-[1709px] max-w-none"
          />
        </div>

        <div className="relative mx-auto flex min-h-[420px] max-w-[1800px] flex-col justify-start px-6 pb-[58px] pt-[200px] md:px-12 xl:min-h-[683px] xl:px-[100px] xl:pt-[259px]">
          {dienst.label && (
            <AnimateIn>
              <span
                className="inline-flex h-[42px] items-center rounded-[10px] px-[13px] font-heading text-[16px] font-extrabold uppercase leading-5 tracking-[0.8px]"
                style={{ backgroundColor: `${accent}33`, color: accent }}
              >
                {dienst.label}
              </span>
            </AnimateIn>
          )}
          <AnimateIn>
            <h1 className="mt-[27px] max-w-[956px] font-heading text-[clamp(34px,4.6vw,65px)] font-extrabold leading-[1.26] text-white">
              {dienst.title}
            </h1>
          </AnimateIn>
        </div>
      </section>

      {/* Body + checklist beside the photo. */}
      <section className="w-full bg-white">
        <div className="mx-auto max-w-[1800px] px-6 py-[100px] md:px-12 xl:px-[100px]">
          <div className="grid items-center gap-[32px] xl:grid-cols-[738fr_817fr] xl:gap-[45px]">
            <AnimateIn>
              <div>
                {dienst.excerpt && (
                  <div
                    className="max-w-[738px] font-body text-[clamp(17px,1.3vw,20px)] font-medium leading-[1.7] text-[color:var(--color-ink)] [&_p]:mb-[20px] [&_p:last-child]:mb-0 2xl:leading-[36.563px]"
                    // Staff-authored rich text (Filament RichEditor).
                    dangerouslySetInnerHTML={{ __html: dienst.excerpt }}
                  />
                )}

                {dienst.body && (
                  <div
                    className="mt-[24px] max-w-[738px] font-body text-[clamp(17px,1.3vw,20px)] leading-[1.7] text-[color:var(--color-ink)] [&_h3]:mt-8 [&_h3]:font-heading [&_h3]:text-[24px] [&_h3]:font-bold [&_li]:ml-5 [&_li]:list-disc [&_p]:mb-4 [&_ul]:my-4 2xl:leading-[36.563px]"
                    dangerouslySetInnerHTML={{ __html: dienst.body }}
                  />
                )}

                {dienst.features?.length > 0 && (
                  <ul className="mt-[37px] flex flex-col gap-[32px]">
                    {dienst.features.map((feature, i) => (
                      <li key={i} className="flex items-center gap-[15px]">
                        <CheckIcon accent={accent} />
                        <span className="font-body text-[18px] font-medium leading-[32px] text-[color:var(--color-ink)]">
                          {feature}
                        </span>
                      </li>
                    ))}
                  </ul>
                )}

                <Link
                  href={safeUrl('/contact')}
                  className="mt-[29px] inline-flex h-[65px] w-[256px] items-center justify-center gap-2 rounded-[10px] font-heading text-[18px] font-extrabold text-white transition hover:brightness-110"
                  style={{ backgroundColor: accent }}
                >
                  Neem contact op
                  <ArrowChevron />
                </Link>
              </div>
            </AnimateIn>

            <AnimateIn>
              <SmartImage
                src={image}
                alt={dienst.title}
                className="h-[280px] w-full rounded-[30px] object-cover md:h-[420px] xl:h-[646px]"
              />
            </AnimateIn>
          </div>
        </div>
      </section>
    </>
  );
}

