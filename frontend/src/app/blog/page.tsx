import type { Metadata } from 'next';
import { fetchBlogPosts, fetchPage } from '@/lib/api';
import { pageMetadata } from '@/lib/seo';
import { AnimateIn } from '@/components/ui/AnimateIn';
import { BlogCard } from '@/components/blog/BlogCard';
import { Pagination } from '@/components/ui/Pagination';

/**
 * Blog overview (Figma 31854:1496): compact hero, one featured article across
 * the full width, then a 3-column grid and pagination.
 *
 * Next.js 16: `searchParams` is a Promise and must be awaited.
 */
const PER_PAGE = 9;

export async function generateMetadata(): Promise<Metadata> {
  const page = await fetchPage('blog');

  return pageMetadata({
    seo: page?.seo,
    title: page?.title || 'Blog',
    description: 'IT-kennis voor iedereen: praktische tips over hardware, software, cloud, netwerk en beveiliging.',
  });
}

export default async function BlogPage({
  searchParams,
}: {
  searchParams: Promise<{ page?: string }>;
}) {
  const [{ page: pageParam }, posts] = await Promise.all([searchParams, fetchBlogPosts()]);

  const page = Math.max(1, Number(pageParam) || 1);

  // The newest article is featured, but only on page 1 — on later pages it
  // would just repeat an article the visitor has already scrolled past.
  const featured = page === 1 ? posts[0] : undefined;
  const rest = page === 1 ? posts.slice(1) : posts;

  const totalPages = Math.max(1, Math.ceil(rest.length / PER_PAGE));
  const start = (page - 1) * PER_PAGE;
  const visible = rest.slice(start, start + PER_PAGE);

  return (
    <>
      {/* Hero — the compact/left treatment used across the subpages. */}
      <section className="relative w-full overflow-hidden bg-[color:var(--color-overlay)]">
        <div className="pointer-events-none absolute inset-0 hidden xl:block" aria-hidden="true">
          {/* eslint-disable-next-line @next/next/no-img-element */}
          <img
            src="/images/design/hero-swoosh-right.svg"
            alt=""
            className="absolute left-[1150px] top-[-629px] h-[1384px] w-[1709px] max-w-none"
          />
        </div>

        <div className="relative mx-auto flex min-h-[380px] max-w-[1800px] flex-col justify-start px-6 pb-[58px] pt-[190px] md:px-12 xl:min-h-[601px] xl:px-[100px] xl:pt-[256px]">
          <AnimateIn>
            <h1 className="max-w-[956px] font-heading text-[clamp(34px,4.6vw,65px)] font-extrabold leading-[1.26] text-white">
              IT-kennis voor <span className="text-[color:var(--color-brand)]">iedereen</span>
            </h1>
          </AnimateIn>
          <AnimateIn delay={0.08}>
            <p className="mt-[39px] max-w-[819px] font-body text-[clamp(17px,1.4vw,22px)] font-semibold leading-[1.66] text-white">
              Praktische tips en heldere uitleg over hardware, software, cloud, netwerk en beveiliging — zonder
              onnodig jargon.
            </p>
          </AnimateIn>
        </div>
      </section>

      {/* Articles */}
      <section className="w-full bg-white">
        <div className="mx-auto max-w-[1800px] px-6 py-[96px] md:px-12 xl:px-[100px]">
          {posts.length === 0 ? (
            <p className="text-center font-body text-[18px] text-[color:var(--color-muted)]">
              Er zijn nog geen artikelen gepubliceerd.
            </p>
          ) : (
            <>
              {featured && (
                <AnimateIn>
                  <div className="mb-[89px]">
                    <BlogCard post={featured} featured />
                  </div>
                </AnimateIn>
              )}

              <div className="grid gap-[30px] sm:grid-cols-2 xl:grid-cols-3">
                {visible.map((post) => (
                  <AnimateIn key={post.id}>
                    <BlogCard post={post} />
                  </AnimateIn>
                ))}
              </div>

              <div className="mt-[89px]">
                <Pagination page={page} totalPages={totalPages} basePath="/blog" />
              </div>
            </>
          )}
        </div>
      </section>
    </>
  );
}
