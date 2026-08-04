import Link from 'next/link';
import { AnimateIn } from '@/components/ui/AnimateIn';
import { SmartImage } from '@/components/ui/SmartImage';
import { safeUrl } from '@/lib/safeUrl';
import { designDefaults, mediaOr } from '@/content/designDefaults';
import { arr, str } from '@/lib/blockData';

/**
 * Section 5 — "Complete ICT-oplossingen voor elke situatie" (Figma 31783:6350).
 * #fafafa band, centred 40px heading, then four 380.52×330 white cards
 * (rounded-[18.182px], 1.299px rgba(15,29,53,.09) border) with a 70px #f6f6f6
 * icon tile, title, body and a "Meer info" link.
 */
export function SolutionCardsBlock({ data }: { data: Record<string, unknown> }) {
  const heading = str(data.heading);
  const intro = str(data.intro);
  const columns = Number(data.columns ?? 4);
  const cards = arr(data.cards);

  if (cards.length === 0 && heading === '') return null;

  // 4 on the homepage, 3 for the Webdesign diensten grid.
  const gridCols =
    columns === 2 ? 'sm:grid-cols-2' : columns === 3 ? 'sm:grid-cols-2 xl:grid-cols-3' : 'sm:grid-cols-2 xl:grid-cols-4';

  return (
    <section className="w-full bg-[color:var(--color-surface-alt)]">
      <div className="mx-auto max-w-[1800px] px-6 py-[96px] md:px-12 xl:px-[100px]">
        {heading && (
          <AnimateIn>
            {/* Figma: heading top 96, cards top 191. The rendered line box is
                48px (vs Figma's tight 37px), so the gap is 47px, not 58px. */}
            <h2 className="mx-auto max-w-[950px] text-center font-heading text-[clamp(26px,2.8vw,40px)] font-extrabold leading-[1.2] text-[color:var(--color-ink)]">
              {heading}
            </h2>
            {intro && (
              <p className="mx-auto mt-[16px] max-w-[697px] text-center font-body text-[clamp(17px,1.4vw,22px)] font-medium leading-[1.7] text-[color:var(--color-ink)] 2xl:leading-[36.563px]">
                {intro}
              </p>
            )}
            <div className="h-[47px]" aria-hidden="true" />
          </AnimateIn>
        )}

        <div className={`grid gap-[26px] ${gridCols}`}>
          {cards.map((card, i) => (
            <AnimateIn key={i} delay={i * 0.08}>
              <SolutionCard
                // Empty icon → the design icon for this position.
                icon={mediaOr(card.icon, designDefaults.solutionIcons[i] ?? '')}
                title={str(card.title)}
                text={str(card.text)}
                ctaLabel={str(card.cta_label)}
                ctaHref={str(card.cta_href)}
              />
            </AnimateIn>
          ))}
        </div>
      </div>
    </section>
  );
}

function SolutionCard({
  icon,
  title,
  text,
  ctaLabel,
  ctaHref,
}: {
  icon: string;
  title: string;
  text: string;
  ctaLabel: string;
  ctaHref: string;
}) {
  return (
    // The 330px floor and the asymmetric right padding are both desktop-only.
    // At xl, Figma gives the body a 333px box inside a 380.52px card (left 32.5,
    // right 15) — the narrower right padding is what preserves the designed line
    // breaks and holds every card at 330px. Below xl the cards are full width
    // and stacked, so that same padding just looks off-centre and the floor
    // leaves dead space under the shorter cards.
    <div className="group flex h-full flex-col rounded-[18.182px] border-[1.299px] border-[color:var(--color-card-border)] bg-white p-[31px] transition duration-300 hover:shadow-[0_18px_40px_-24px_rgba(15,29,53,0.35)] xl:min-h-[288px] xl:pr-[15px]">
      <div className="flex size-[70px] items-center justify-center rounded-[18.182px] bg-[color:var(--color-icon-bg)]">
        <SmartImage src={icon} alt="" className="size-[45px] object-contain" />
      </div>

      {title && (
        <h3 className="mt-[18px] font-heading text-[22px] font-bold leading-[36.364px] text-[color:var(--color-card-title)]">
          {title}
        </h3>
      )}

      {text && (
        <p className="mt-[10px] font-card text-[18px] leading-[29.545px] text-[color:var(--color-card-body)]">
          {text}
        </p>
      )}

      {ctaLabel && (
        <Link
          href={safeUrl(ctaHref)}
          className="mt-auto inline-flex items-center gap-1 pt-6 font-card text-[18px] font-bold leading-[20.779px] text-[color:var(--color-ink)] transition-colors group-hover:text-[color:var(--color-link)]"
        >
          {ctaLabel}
          <svg width="17" height="17" viewBox="0 0 16.8831 16.8831" fill="none" aria-hidden="true">
            <path
              d="M6.33114 12.6622L10.5519 8.44141L6.33114 4.22063"
              stroke="currentColor"
              strokeWidth="1.40693"
              strokeLinecap="round"
              strokeLinejoin="round"
            />
          </svg>
        </Link>
      )}
    </div>
  );
}
