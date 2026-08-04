import Link from 'next/link';
import { SmartImage } from '@/components/ui/SmartImage';
import type { BlogCard as BlogCardData } from '@/lib/api';

/**
 * Article card (Figma 31854:1894) — 513×536: image with a brand category pill,
 * then date + reading time, title, excerpt and a "Lees meer" link.
 *
 * `featured` is the wide 1600×424 variant (31854:1824): the same shell with the
 * image on the left and a larger 30px title.
 */
export function BlogCard({ post, featured = false }: { post: BlogCardData; featured?: boolean }) {
  return (
    <Link
      href={post.href}
      className={[
        'group block overflow-hidden rounded-[20px] border-[1.249px] border-[#f3f4f6] bg-white',
        'shadow-[0px_1.249px_3.747px_0px_rgba(0,0,0,0.1),0px_1.249px_2.498px_-1.249px_rgba(0,0,0,0.1)]',
        'transition duration-300 hover:shadow-[0_18px_40px_-18px_rgba(0,0,0,0.2)]',
        featured ? 'xl:grid xl:grid-cols-[746fr_854fr]' : 'flex h-full flex-col',
      ].join(' ')}
    >
      {/* Image + category pill */}
      <div className={`relative shrink-0 ${featured ? 'h-[240px] xl:h-[422px]' : 'h-[240px]'}`}>
        <SmartImage src={post.image} alt={post.title} className="h-full w-full object-cover" />

        {post.category && (
          <span
            className={[
              'absolute left-[15px] top-[15px] inline-flex items-center bg-[color:var(--color-brand)]',
              'font-body font-semibold leading-5 text-[color:var(--color-ink)]',
              featured ? 'h-[38px] rounded-[20px] px-[18px] text-[18px]' : 'h-[30px] rounded-[12.49px] px-[13px] text-[16px]',
            ].join(' ')}
          >
            {post.category}
          </span>
        )}
      </div>

      {/* Body */}
      <div
        className={
          featured
            ? 'flex flex-col justify-center px-[25px] py-[40px] xl:px-[54px]'
            : 'flex flex-1 flex-col px-[25px] pb-[25px] pt-[25px]'
        }
      >
        <Meta post={post} withIcons={featured} />

        <h3
          className={[
            'font-heading font-bold text-[color:var(--color-card-title)]',
            featured ? 'mt-[22px] text-[clamp(22px,2.2vw,30px)] leading-[40px]' : 'mt-[15px] text-[20px] leading-[30px]',
          ].join(' ')}
        >
          {post.title}
        </h3>

        {post.excerpt && (
          <div
            className={`font-body text-[18px] leading-[30px] text-[color:var(--color-muted)] [&_p]:mb-0 ${
              featured ? 'mt-[22px] max-w-[750px]' : 'mt-[15px]'
            }`}
            // Staff-authored rich text (Filament RichEditor).
            dangerouslySetInnerHTML={{ __html: post.excerpt }}
          />
        )}

        <span
          className={`inline-flex items-center gap-[8px] font-body text-[18px] font-semibold leading-[28px] text-[color:var(--color-link-strong)] ${
            featured ? 'mt-[26px]' : 'mt-auto pt-[20px]'
          }`}
        >
          Lees meer
          <svg width="15" height="14" viewBox="0 0 14.5 13.4998" fill="none" aria-hidden="true" className="chevron-anim">
            <path
              d="M7.75026 12.7497C7.75026 12.7497 13.75 8.33091 13.75 6.74984C13.7501 5.16877 7.75021 0.750049 7.75021 0.750049"
              stroke="currentColor"
              strokeWidth="1.5"
              strokeLinecap="round"
              strokeLinejoin="round"
            />
            <path
              opacity="0.4"
              d="M0.750096 12.7497C0.750096 12.7497 6.74987 8.33091 6.74989 6.74984C6.7499 5.16877 0.750049 0.750049 0.750049 0.750049"
              stroke="currentColor"
              strokeWidth="1.5"
              strokeLinecap="round"
              strokeLinejoin="round"
            />
          </svg>
        </span>
      </div>
    </Link>
  );
}

/** Date + reading time. The featured card shows them with icons, below the CTA. */
export function Meta({ post, withIcons = false }: { post: BlogCardData; withIcons?: boolean }) {
  const date = formatDate(post.publishedAt);
  const read = post.readMinutes ? `${post.readMinutes} min leestijd` : '';

  if (!date && !read) return null;

  return (
    <div
      className={`flex flex-wrap items-center gap-x-[24px] gap-y-1 font-body text-[15px] leading-5 text-[color:var(--color-muted)] ${
        withIcons ? 'order-last mt-[30px]' : ''
      }`}
    >
      {date && (
        <span className="inline-flex items-center gap-[5px]">
          {withIcons && (
            // eslint-disable-next-line @next/next/no-img-element
            <img src="/images/design/icon-date.svg" alt="" aria-hidden="true" className="size-[18px]" />
          )}
          {date}
        </span>
      )}
      {read && (
        <span className="inline-flex items-center gap-[5px]">
          {withIcons && (
            // eslint-disable-next-line @next/next/no-img-element
            <img src="/images/design/icon-readtime.svg" alt="" aria-hidden="true" className="size-[15px]" />
          )}
          {read}
        </span>
      )}
    </div>
  );
}

/** "2026-07-12" → "12 juli 2026". */
export function formatDate(value?: string | null): string {
  if (!value) return '';
  const d = new Date(value);
  if (Number.isNaN(d.getTime())) return '';

  return d.toLocaleDateString('nl-NL', { day: 'numeric', month: 'long', year: 'numeric' });
}
