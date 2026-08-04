import Link from 'next/link';
import { AnimateIn } from '@/components/ui/AnimateIn';
import { safeUrl } from '@/lib/safeUrl';
import { str } from '@/lib/blockData';
import { ArrowChevron } from '@/components/ui/ArrowChevron';

/**
 * Section 9 — the dark CTA banner (Figma 31880:35).
 * A 1601×493 #111 card, rounded-[30px], with a decorative arc pattern and
 * vertically centred title (65/75 ExtraBold), body (22/36.563) and a 256×65
 * brand button.
 */
export function CtaBannerBlock({ data }: { data: Record<string, unknown> }) {
  const title = str(data.title);
  const body = str(data.body);
  const ctaLabel = str(data.cta_label);
  const ctaHref = str(data.cta_href);
  const showPattern = data.show_pattern !== false;

  if (title === '' && body === '' && ctaLabel === '') return null;

  return (
    <section className="w-full bg-white">
      <div className="mx-auto max-w-[1800px] px-6 py-[95px] md:px-12 xl:px-[100px]">
        <AnimateIn>
          <div className="relative flex min-h-[360px] flex-col items-center justify-center overflow-hidden rounded-[30px] bg-[color:var(--color-ink-deep)] px-6 py-[60px] text-center xl:min-h-[493px] xl:py-[114px]">
            {showPattern && (
              <div className="pointer-events-none absolute inset-0" aria-hidden="true">
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img
                  src="/images/design/banner-swoosh.svg"
                  alt=""
                  className="absolute left-[-175px] top-[411px] h-[899px] w-[1321px] max-w-none"
                />
              </div>
            )}

            <div className="relative">
              {title && (
                <h2 className="mx-auto max-w-[606px] font-heading text-[clamp(32px,4.4vw,65px)] font-extrabold leading-[1.154] text-white">
                  {title}
                </h2>
              )}

              {body && (
                // 25px, not the design's 40: the 65px title renders a 75px line
                // box where Figma measures a 60px tight box, so the gap absorbs
                // the 15px difference and the body/button land on the designed rows.
                <p className="mx-auto mt-[25px] max-w-[932px] font-body text-[clamp(17px,1.5vw,22px)] leading-[1.7] 2xl:leading-[36.563px] text-white">
                  {body}
                </p>
              )}

              {ctaLabel && (
                <div className="mt-[27px] flex justify-center">
                  <Link
                    href={safeUrl(ctaHref)}
                    className="inline-flex h-[65px] w-[256px] items-center justify-center gap-2 rounded-[10px] bg-[color:var(--color-brand)] font-heading text-[18px] font-extrabold text-[color:var(--color-ink)] transition hover:brightness-105"
                  >
                    {ctaLabel}
                    <ArrowChevron />
                  </Link>
                </div>
              )}
            </div>
          </div>
        </AnimateIn>
      </div>
    </section>
  );
}

