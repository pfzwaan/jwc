import Link from 'next/link';
import { AnimateIn } from '@/components/ui/AnimateIn';
import { AccentText } from '@/components/ui/AccentText';
import { ArrowChevron } from '@/components/ui/ArrowChevron';
import { safeUrl } from '@/lib/safeUrl';
import { designDefaults, mediaOr } from '@/content/designDefaults';
import { arr, str } from '@/lib/blockData';

/**
 * Hero — two variants from the same widget:
 *
 * - `full` (home, Figma 31761:4121): 1031px, centred, background image/video
 *   behind a #101010 @60% overlay, up to three 256×65 buttons.
 * - `compact` (subpages, Figma 31837:1028): 683px, left-aligned, solid #101010,
 *   title 65/82 with an accent run, intro 22/36.563 — no buttons.
 *
 * The decorative arcs sit left on the homepage and right on Over ons.
 */
export function HeroBlock({ data }: { data: Record<string, unknown> }) {
  const title = str(data.title);
  const body = str(data.body);
  const compact = str(data.size) === 'compact';
  const left = str(data.align) === 'left';
  const pattern = str(data.pattern) || 'left';
  const image = mediaOr(data.image, designDefaults.hero.image);
  const video = mediaOr(data.video, designDefaults.hero.video);
  const showOverlay = data.show_overlay !== false;
  const buttons = arr(data.buttons);

  return (
    <section className="relative w-full overflow-hidden bg-[color:var(--color-overlay)]">
      {/* Background — a video wins over a still; both are decorative. */}
      {video ? (
        <video
          className="absolute inset-0 h-full w-full object-cover"
          src={video}
          autoPlay
          muted
          loop
          playsInline
          aria-hidden="true"
        />
      ) : image ? (
        // eslint-disable-next-line @next/next/no-img-element
        <img src={image} alt="" aria-hidden="true" className="absolute inset-0 h-full w-full object-cover" />
      ) : null}

      {showOverlay && (image || video) && (
        <div className="absolute inset-0 bg-[color:var(--color-overlay)] opacity-60" aria-hidden="true" />
      )}

      {pattern !== 'none' && (
        <div className="pointer-events-none absolute inset-0 hidden xl:block" aria-hidden="true">
          {/* eslint-disable-next-line @next/next/no-img-element */}
          <img
            src={pattern === 'right' ? '/images/design/hero-swoosh-right.svg' : '/images/design/hero-swoosh.svg'}
            alt=""
            className={
              pattern === 'right'
                ? 'absolute left-[1150px] top-[-629px] h-[1384px] w-[1709px] max-w-none'
                : 'absolute left-[152px] top-[-291px] h-[1385px] w-[1710px] max-w-none'
            }
          />
        </div>
      )}

      {/* Content. The top padding clears the floating menu bar (29–163px). */}
      <div
        className={[
          'relative mx-auto flex max-w-[1800px] flex-col px-6 md:px-12 xl:px-[100px]',
          left ? 'items-start text-left' : 'items-center text-center',
          // Compact starts at its top padding (Figma puts the title at 259);
          // full centres its content in the taller frame.
          compact
            ? 'min-h-[420px] justify-start pb-[58px] pt-[200px] xl:min-h-[683px] xl:pt-[259px]'
            : 'min-h-[560px] justify-center pb-16 pt-[180px] xl:min-h-[1031px] xl:pt-[163px]',
        ].join(' ')}
      >
        {title && (
          <AnimateIn>
            <h1
              className={[
                'font-heading font-extrabold text-white',
                compact
                  ? 'max-w-[956px] text-[clamp(34px,4.6vw,65px)] leading-[1.26]'
                  : 'mx-auto max-w-[986px] text-[clamp(34px,4.6vw,65px)] leading-[1.26]',
              ].join(' ')}
            >
              <AccentText text={title} />
            </h1>
          </AnimateIn>
        )}

        {body && (
          <AnimateIn delay={0.08}>
            <p
              className={[
                'font-body font-semibold text-white',
                'text-[clamp(17px,1.4vw,22px)] leading-[1.66]',
                compact ? 'mt-[39px] max-w-[956px]' : 'mx-auto mt-[39px] max-w-[1044px]',
              ].join(' ')}
            >
              {body}
            </p>
          </AnimateIn>
        )}

        {buttons.length > 0 && (
          <AnimateIn delay={0.16}>
            <div
              className={`mt-[39px] flex flex-col gap-[25px] sm:flex-row sm:flex-wrap ${
                left ? 'items-start sm:justify-start' : 'items-center sm:justify-center'
              }`}
            >
              {buttons.map((b, i) => (
                <HeroButton key={i} label={str(b.label)} href={str(b.href)} variant={str(b.variant)} />
              ))}
            </div>
          </AnimateIn>
        )}
      </div>
    </section>
  );
}

/** 256×65, rounded-[10px], Plus Jakarta Sans ExtraBold 18px + the "»" glyph. */
function HeroButton({ label, href, variant }: { label: string; href: string; variant: string }) {
  const base =
    'inline-flex h-[65px] w-[256px] items-center justify-center gap-2 rounded-[10px] font-heading text-[18px] font-extrabold transition';
  const styles: Record<string, string> = {
    primary: 'bg-[color:var(--color-brand)] text-[color:var(--color-ink)] hover:brightness-105',
    outline: 'border-2 border-white text-white hover:bg-white hover:text-[color:var(--color-ink)]',
  };

  return (
    <Link href={safeUrl(href)} className={`${base} ${styles[variant] ?? styles.primary}`}>
      {label}
      <ArrowChevron />
    </Link>
  );
}
