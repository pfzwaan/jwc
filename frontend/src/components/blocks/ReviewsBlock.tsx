import Link from 'next/link';
import { AnimateIn } from '@/components/ui/AnimateIn';
import { Stars } from '@/components/ui/Stars';
import { safeUrl } from '@/lib/safeUrl';
import { arr, str } from '@/lib/blockData';

/**
 * Section 7 — customer reviews (Figma 31789:145).
 * #fafafa band 740px tall: heading (40px ExtraBold), intro (20/36.563), a
 * Trustpilot rating line, then four 388×315 cards (white, 1px #dfdfdf,
 * rounded-[30px]) with name, date, stars, quote mark and the review text.
 */
export function ReviewsBlock({ data }: { data: Record<string, unknown> }) {
  const heading = str(data.heading);
  const intro = str(data.intro);
  const showRating = data.show_rating !== false;
  const ratingLabel = str(data.rating_label);
  const ratingScore = Number(data.rating_score ?? 5);
  const ratingCount = data.rating_count ? String(data.rating_count) : '';
  const ratingUrl = str(data.rating_url);
  const reviews = arr(data.reviews);

  if (reviews.length === 0 && heading === '') return null;

  return (
    <section className="w-full bg-[color:var(--color-surface-alt)]">
      <div className="mx-auto max-w-[1800px] px-6 pb-[96px] pt-[98px] md:px-12 xl:px-[100px]">
        <AnimateIn>
          <div className="text-center">
            {heading && (
              <h2 className="mx-auto max-w-[876px] font-heading text-[clamp(26px,2.8vw,40px)] font-extrabold leading-[1.2] text-[color:var(--color-ink)]">
                {heading}
              </h2>
            )}

            {intro && (
              <p className="mx-auto mt-[16px] max-w-[915px] font-body text-[clamp(17px,1.3vw,20px)] leading-[1.7] 2xl:leading-[36.563px] text-[color:var(--color-ink)]">
                {intro}
              </p>
            )}

            {showRating && (ratingLabel || ratingCount) && (
              <RatingLine label={ratingLabel} score={ratingScore} count={ratingCount} href={ratingUrl} />
            )}
          </div>
        </AnimateIn>

        <div className="mt-[86px] grid gap-[16px] sm:grid-cols-2 xl:grid-cols-4">
          {reviews.map((review, i) => (
            <AnimateIn key={i} delay={i * 0.08}>
              <ReviewCard
                name={str(review.name)}
                date={str(review.date)}
                rating={Number(review.rating ?? 5)}
                text={str(review.text)}
              />
            </AnimateIn>
          ))}
        </div>
      </div>
    </section>
  );
}

/** "Great ★★★★★ 4 reviews on Trustpilot" — one centred line, 26px tall. */
function RatingLine({
  label,
  score,
  count,
  href,
}: {
  label: string;
  score: number;
  count: string;
  href: string;
}) {
  const line = (
    <span className="inline-flex h-[26px] flex-wrap items-center justify-center gap-x-[15px] gap-y-2">
      {label && <span className="font-body text-[18px] font-bold leading-[26px] text-black">{label}</span>}
      <Stars score={score} width={106.667} />
      {count && (
        <span className="font-body text-[18px] leading-[26px] text-black">
          <strong className="font-bold">{count}</strong> reviews on
        </span>
      )}
      <TrustpilotWordmark />
    </span>
  );

  return (
    <div className="mt-[19px] flex justify-center">
      {href ? (
        <Link
          href={safeUrl(href)}
          target={href.startsWith('http') ? '_blank' : undefined}
          rel={href.startsWith('http') ? 'noopener noreferrer' : undefined}
          className="transition hover:opacity-80"
        >
          {line}
        </Link>
      ) : (
        line
      )}
    </div>
  );
}

/** Trustpilot star + wordmark, 131×24 as in the design. */
function TrustpilotWordmark() {
  return (
    <span className="inline-flex items-center gap-[6px]">
      <svg width="24" height="23" viewBox="0 0 24 23" fill="none" aria-hidden="true">
        <path
          d="M12 0l3.106 8.577H24l-7.2 5.3 2.757 8.738L12 17.667 4.443 22.615 7.2 13.877 0 8.577h8.894L12 0z"
          fill="var(--color-success)"
        />
      </svg>
      <span className="font-heading text-[19px] font-extrabold tracking-[-0.01em] text-black">Trustpilot</span>
    </span>
  );
}

function ReviewCard({ name, date, rating, text }: { name: string; date: string; rating: number; text: string }) {
  return (
    <article className="flex h-full min-h-[315px] flex-col rounded-[30px] border border-[#dfdfdf] bg-white px-[26px] pb-[43px] pt-[40px]">
      <h3 className="font-heading text-[22px] font-extrabold leading-[30px] text-black">{name}</h3>

      {date && (
        <p className="mt-[4px] font-body text-[15px] leading-[20.779px] text-[color:var(--color-ink)]">
          {formatDate(date)}
        </p>
      )}

      <div className="mt-[10px]">
        <Stars score={rating} width={117} />
      </div>

      {/* Decorative quote mark, in the brand colour. */}
      <svg width="39" height="30" viewBox="0 0 39 30" fill="none" aria-hidden="true" className="mt-[40px]">
        <path
          d="M16.6454 2.13907L15.2303 0C5.43939 6.47025 0 14.3318 0 20.802C0 27.0584 4.67875 30 8.64907 30C13.6533 30 17.1885 25.828 17.1885 21.4437C17.1885 17.7543 14.7951 14.5987 11.5855 13.4214C10.6612 13.0997 9.7909 12.8327 9.7909 11.2823C9.7909 9.30409 11.26 6.36416 16.6454 2.13907ZM38.2394 2.13907L36.8242 0C27.1413 6.47025 21.5939 14.3318 21.5939 20.802C21.5939 27.0584 26.3806 30 30.3509 30C35.4091 30 39 25.828 39 21.4437C39 17.7543 36.5527 14.5987 33.2334 13.4214C32.3091 13.0997 31.4928 12.8327 31.4928 11.2823C31.4928 9.30409 33.0158 6.36244 38.2376 2.13736L38.2394 2.13907Z"
          fill="var(--color-brand)"
        />
      </svg>

      {text && (
        <p className="mt-[15px] font-body text-[18px] leading-[29.545px] text-[color:var(--color-ink)]">{text}</p>
      )}
    </article>
  );
}

/** "2025-11-14" → "Nov 14, 2025", matching the design. Unparseable input passes through. */
function formatDate(value: string): string {
  const d = new Date(value);
  if (Number.isNaN(d.getTime())) return value;

  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}
