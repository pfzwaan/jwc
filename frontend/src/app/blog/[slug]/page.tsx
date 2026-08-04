import type { Metadata } from 'next';
import Link from 'next/link';
import { notFound } from 'next/navigation';
import { fetchBlogPost, fetchBlogPosts } from '@/lib/api';
import { AnimateIn } from '@/components/ui/AnimateIn';
import { ArrowChevron } from '@/components/ui/ArrowChevron';
import { SmartImage } from '@/components/ui/SmartImage';
import { BlogCard, formatDate } from '@/components/blog/BlogCard';
import { pageMetadata } from '@/lib/seo';

/**
 * Article detail. No Figma frame exists for it, so it is composed from the
 * house style already established elsewhere: the compact dark hero used on
 * every subpage, the image overhanging onto white exactly as the project
 * detail mockup does, a readable article column, related posts and the CTA
 * banner.
 *
 * Next.js 16: `params` is a Promise and must be awaited.
 */
type Params = { slug: string };

export async function generateMetadata({ params }: { params: Promise<Params> }): Promise<Metadata> {
  const { slug } = await params;
  const post = await fetchBlogPost(slug);
  if (!post) return {};

  const meta = pageMetadata({
    seo: post.seo,
    title: post.title,
    description: post.excerpt || post.body,
    image: post.image,
  });

  return {
    ...meta,
    openGraph: { ...meta.openGraph, type: 'article', publishedTime: post.publishedAt ?? undefined },
  };
}

export async function generateStaticParams() {
  const posts = await fetchBlogPosts();

  return posts.map((p) => ({ slug: p.slug }));
}

export default async function BlogDetailPage({ params }: { params: Promise<Params> }) {
  const { slug } = await params;
  const [post, all] = await Promise.all([fetchBlogPost(slug), fetchBlogPosts()]);

  if (!post) notFound();

  const related = all.filter((p) => p.slug !== post.slug).slice(0, 3);
  const date = formatDate(post.publishedAt);

  return (
    <>
      {/* Hero. The image overhangs the dark panel, so the clipping wrapper is
          the panel itself — never the section. */}
      <section className="relative w-full bg-white">
        {/* The dark panel is sized from the content, not a fixed height: with an
            image it stops short so the image overhangs onto white, without one
            it covers the whole hero. A hardcoded height hid the opening
            paragraph of every article that has no image. */}
        <div
          className={[
            'absolute inset-x-0 top-0 overflow-hidden bg-[color:var(--color-overlay)]',
            post.image ? 'h-[calc(100%-120px)] xl:h-[calc(100%-220px)]' : 'h-full',
          ].join(' ')}
          aria-hidden="true"
        >
          {/* eslint-disable-next-line @next/next/no-img-element */}
          <img
            src="/images/design/hero-swoosh-right.svg"
            alt=""
            className="absolute left-[1150px] top-[-629px] hidden h-[1384px] w-[1709px] max-w-none xl:block"
          />
        </div>

        <div
          className={`relative mx-auto max-w-[1800px] px-6 pt-[190px] md:px-12 xl:px-[100px] xl:pt-[256px] ${
            post.image ? '' : 'pb-[64px] xl:pb-[96px]'
          }`}
        >
          <AnimateIn>
            <div className="max-w-[1100px]">
              {post.category && (
                <span className="inline-flex h-[38px] items-center rounded-[20px] bg-[color:var(--color-brand)] px-[18px] font-body text-[18px] font-semibold leading-5 text-[color:var(--color-ink)]">
                  {post.category}
                </span>
              )}

              <h1 className="mt-[27px] font-heading text-[clamp(30px,4vw,56px)] font-extrabold leading-[1.2] text-white">
                {post.title}
              </h1>

              <div className="mt-[24px] flex flex-wrap items-center gap-x-[24px] gap-y-2 font-body text-[16px] text-white/70">
                {date && (
                  <span className="inline-flex items-center gap-[7px]">
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img
                      src="/images/design/icon-date.svg"
                      alt=""
                      aria-hidden="true"
                      className="size-[18px] brightness-0 invert"
                    />
                    {date}
                  </span>
                )}
                {post.readMinutes && (
                  <span className="inline-flex items-center gap-[7px]">
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img
                      src="/images/design/icon-readtime.svg"
                      alt=""
                      aria-hidden="true"
                      className="size-[15px] brightness-0 invert"
                    />
                    {post.readMinutes} min leestijd
                  </span>
                )}
                {post.author && <span>door {post.author}</span>}
              </div>
            </div>
          </AnimateIn>

          {post.image && (
            <AnimateIn delay={0.08}>
              <div className="mt-[50px] xl:mt-[64px]">
                <SmartImage
                  src={post.image}
                  alt={post.title}
                  className="h-[240px] w-full rounded-[30px] object-cover md:h-[420px] xl:h-[560px]"
                />
              </div>
            </AnimateIn>
          )}
        </div>
      </section>

      {/* Article body — a single readable column. */}
      <section className="w-full bg-white">
        <div className="mx-auto max-w-[1800px] px-6 pb-[96px] pt-[64px] md:px-12 xl:px-[100px]">
          <AnimateIn>
            <article
              className={[
                'mx-auto max-w-[860px] font-body text-[clamp(17px,1.3vw,20px)] leading-[1.75] text-[color:var(--color-ink)]',
                '[&_p]:mb-[24px] [&_p:last-child]:mb-0',
                '[&_h2]:mt-[48px] [&_h2]:mb-[16px] [&_h2]:font-heading [&_h2]:text-[clamp(24px,2.2vw,32px)] [&_h2]:font-extrabold [&_h2]:leading-[1.25] [&_h2]:text-[color:var(--color-ink)]',
                '[&_h3]:mt-[40px] [&_h3]:mb-[12px] [&_h3]:font-heading [&_h3]:text-[24px] [&_h3]:font-bold [&_h3]:text-[color:var(--color-card-title)]',
                '[&_ul]:mb-[24px] [&_ul]:flex [&_ul]:flex-col [&_ul]:gap-[10px] [&_li]:ml-[22px] [&_li]:list-disc [&_li]:pl-[6px]',
                '[&_ol]:mb-[24px] [&_ol]:flex [&_ol]:flex-col [&_ol]:gap-[10px] [&_ol>li]:list-decimal',
                '[&_a]:font-semibold [&_a]:text-[color:var(--color-link-strong)] [&_a]:underline',
                '[&_strong]:font-bold',
                '[&_blockquote]:my-[32px] [&_blockquote]:border-l-4 [&_blockquote]:border-[color:var(--color-brand)] [&_blockquote]:pl-[24px] [&_blockquote]:italic',
                '[&_img]:my-[32px] [&_img]:rounded-[20px]',
              ].join(' ')}
              // Staff-authored rich text (Filament RichEditor).
              dangerouslySetInnerHTML={{ __html: post.body || post.excerpt || '' }}
            />
          </AnimateIn>

          <AnimateIn>
            <div className="mx-auto mt-[56px] flex max-w-[860px] justify-start">
              <Link
                href="/blog"
                className="inline-flex h-[65px] items-center justify-center gap-2 rounded-[10px] border-2 border-[color:var(--color-ink)] px-[28px] font-heading text-[18px] font-extrabold text-[color:var(--color-ink)] transition hover:bg-[color:var(--color-ink)] hover:text-white"
              >
                Terug naar alle artikelen
              </Link>
            </div>
          </AnimateIn>
        </div>
      </section>

      {/* Related articles */}
      {related.length > 0 && (
        <section className="w-full bg-[color:var(--color-surface-alt)]">
          <div className="mx-auto max-w-[1800px] px-6 py-[96px] md:px-12 xl:px-[100px]">
            <AnimateIn>
              <h2 className="font-heading text-[clamp(26px,2.8vw,40px)] font-extrabold leading-[1.2] text-[color:var(--color-ink)]">
                Meer artikelen
              </h2>
            </AnimateIn>

            <div className="mt-[60px] grid gap-[30px] sm:grid-cols-2 xl:grid-cols-3">
              {related.map((p) => (
                <AnimateIn key={p.id}>
                  <BlogCard post={p} />
                </AnimateIn>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* CTA banner — same block as every other page. */}
      <section className="w-full bg-white">
        <div className="mx-auto max-w-[1800px] px-6 py-[95px] md:px-12 xl:px-[100px]">
          <AnimateIn>
            <div className="relative flex min-h-[360px] flex-col items-center justify-center overflow-hidden rounded-[30px] bg-[color:var(--color-ink-deep)] px-6 py-[60px] text-center xl:min-h-[493px] xl:py-[114px]">
              <div className="pointer-events-none absolute inset-0" aria-hidden="true">
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img
                  src="/images/design/banner-swoosh.svg"
                  alt=""
                  className="absolute left-[-175px] top-[411px] h-[899px] w-[1321px] max-w-none"
                />
              </div>

              <div className="relative">
                <h2 className="mx-auto max-w-[606px] font-heading text-[clamp(32px,4.4vw,65px)] font-extrabold leading-[1.154] text-white">
                  Direct hulp nodig?
                </h2>
                <p className="mx-auto mt-[25px] max-w-[932px] font-body text-[clamp(17px,1.5vw,22px)] leading-[1.7] text-white 2xl:leading-[36.563px]">
                  Heeft u een technisch probleem? Met TeamViewer kunnen wij veilig op afstand verbinding maken met uw
                  computer en u direct helpen.
                </p>
                <div className="mt-[27px] flex justify-center">
                  <Link
                    href="/contact"
                    className="inline-flex h-[65px] w-[256px] items-center justify-center gap-2 rounded-[10px] bg-[color:var(--color-brand)] font-heading text-[18px] font-extrabold text-[color:var(--color-ink)] transition hover:brightness-105"
                  >
                    Start remote support
                    <ArrowChevron />
                  </Link>
                </div>
              </div>
            </div>
          </AnimateIn>
        </div>
      </section>
    </>
  );
}

