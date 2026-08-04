import Link from 'next/link';
import { AnimateIn } from '@/components/ui/AnimateIn';
import { CheckIcon } from '@/components/ui/CheckIcon';
import { SmartImage } from '@/components/ui/SmartImage';
import { safeUrl } from '@/lib/safeUrl';
import { dienstImageFor, mediaOr } from '@/content/designDefaults';
import type { DienstCard } from '@/lib/api';
import { arr, str } from '@/lib/blockData';
import { ArrowChevron } from '@/components/ui/ArrowChevron';

/**
 * Section 6 — the alternating dienst boxes (Figma 31789:28).
 *
 * Card 1600×781, white, 1px #e4e4e4, rounded-[30px]. Left column 738px wide
 * (label pill → title 40px → body 20/36.563 → 4-item checklist → 256×65 button),
 * right column a 725×646 rounded-[30px] photo. Both columns vertically centred.
 * Every dienst carries its own accent colour.
 */
export function DienstBoxesBlock({ data }: { data: Record<string, unknown> }) {
  const heading = str(data.heading);
  const intro = str(data.intro);
  // Resolved server-side by PageController (explicit selection, else the latest).
  const items = arr(data.items) as unknown as DienstCard[];

  if (items.length === 0 && heading === '') return null;

  return (
    <section className="w-full bg-white">
      <div className="mx-auto max-w-[1800px] px-6 py-[100px] md:px-12 xl:px-[99px]">
        {(heading || intro) && (
          <AnimateIn>
            <div className="mx-auto mb-[60px] max-w-[915px] text-center xl:mb-[135px]">
              {heading && (
                <h2 className="mx-auto max-w-[876px] font-heading text-[clamp(26px,2.8vw,40px)] font-extrabold leading-[1.2] text-[color:var(--color-ink)]">
                  {heading}
                </h2>
              )}
              {intro && (
                <p className="mt-[27px] font-body text-[clamp(17px,1.3vw,20px)] leading-[1.7] 2xl:leading-[36.563px] text-[color:var(--color-ink)]">
                  {intro}
                </p>
              )}
            </div>
          </AnimateIn>
        )}

        <div className="flex flex-col gap-[50px]">
          {items.map((dienst, i) => (
            <AnimateIn key={dienst.id ?? i}>
              <DienstBox dienst={dienst} />
            </AnimateIn>
          ))}
        </div>
      </div>
    </section>
  );
}

function DienstBox({ dienst }: { dienst: DienstCard }) {
  const accent = dienst.accent || '#fcb040';
  const accentLabel = dienst.accentLabel || accent;
  // An empty CMS image falls back to the photo shipped with the design.
  const image = mediaOr(dienst.image, dienstImageFor(dienst.slug));

  return (
    // The design's 49/68px padding is generous on a 375px screen, so it only
    // applies from xl — where the two columns sit side by side as drawn.
    // Two columns from xl in *fr*, not the design's literal 738px/725px.
    // Those pixels only fit at 1800 (738+725+45 gap+92 padding = 1600 content);
    // pinned at Tailwind's xl (1280) they pushed the card 392px past the
    // viewport. The ratio resolves to exactly 738/725 at 1800 anyway — the free
    // space there is 1463px, and 738:725 of 1463 is 738 and 725 — so this is
    // pixel-identical at the design width and fluid below it.
    <article className="grid items-center gap-[32px] rounded-[30px] border border-[#e4e4e4] bg-white px-6 py-10 md:px-10 md:py-12 xl:grid-cols-[738fr_725fr] xl:gap-[45px] xl:px-[49px] xl:py-[68px] xl:pr-[43px]">
      {/* Text column */}
      <div>
        {dienst.label && (
          <span
            className="inline-flex h-[42px] items-center rounded-[10px] px-[13px] font-heading text-[16px] font-extrabold uppercase leading-5 tracking-[0.8px]"
            // Pill background is the accent at 20% — matches the design's
            // rgba(...,0.2) fills exactly when composited on white.
            style={{ backgroundColor: `${accent}33`, color: accentLabel }}
          >
            {dienst.label}
          </span>
        )}

        <h3 className="mt-[27px] max-w-[568px] font-heading text-[clamp(28px,2.8vw,40px)] font-extrabold leading-[1.14] text-[color:var(--color-ink)]">
          {dienst.title}
        </h3>

        {dienst.excerpt && (
          <div
            className="mt-[16px] max-w-[738px] font-body text-[clamp(17px,1.3vw,20px)] font-medium leading-[1.7] 2xl:leading-[36.563px] text-[color:var(--color-ink)] [&_p]:mb-0"
            // Staff-authored rich text (Filament RichEditor).
            dangerouslySetInnerHTML={{ __html: dienst.excerpt }}
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

        {dienst.ctaLabel && (
          <Link
            href={safeUrl(dienst.ctaHref || dienst.href)}
            className="mt-[29px] inline-flex h-[65px] w-[256px] items-center justify-center gap-2 rounded-[10px] font-heading text-[18px] font-extrabold text-white transition hover:brightness-110"
            style={{ backgroundColor: accent }}
          >
            {dienst.ctaLabel}
            <ArrowChevron />
          </Link>
        )}
      </div>

      {/* Photo */}
      <SmartImage
        src={image}
        alt={dienst.title}
        className="h-[280px] w-full rounded-[30px] object-cover md:h-[420px] xl:h-[646px]"
      />
    </article>
  );
}


