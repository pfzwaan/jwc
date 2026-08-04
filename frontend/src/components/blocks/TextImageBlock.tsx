import Link from 'next/link';
import { AnimateIn } from '@/components/ui/AnimateIn';
import { ArrowChevron } from '@/components/ui/ArrowChevron';
import { CheckIcon } from '@/components/ui/CheckIcon';
import { SmartImage } from '@/components/ui/SmartImage';
import { safeUrl } from '@/lib/safeUrl';
import { arr, str } from '@/lib/blockData';

/**
 * Text beside an image (Figma 31847:565 — "ONZE MISSIE" on Over ons).
 *
 * Text column 738px, image 817×646 rounded-[30px], 45px apart. Same fr trick as
 * the dienst boxes: the ratio resolves to the design's pixels at 1800 and stays
 * fluid below, instead of overflowing on mid-size screens.
 */
export function TextImageBlock({ data }: { data: Record<string, unknown> }) {
  const label = str(data.label);
  const title = str(data.title);
  const body = str(data.body);
  const image = str(data.image);
  const imageLeft = str(data.image_side) === 'left';
  const accent = str(data.accent) || '#e8891a';
  const accentLabel = str(data.accent_label_color) || accent;
  const features = arr(data.features)
    .map((f) => str(f.label))
    .filter(Boolean);
  const ctaLabel = str(data.cta_label);
  const ctaHref = str(data.cta_href);

  if (!title && !body && !image) return null;

  return (
    <section className="w-full bg-white">
      <div className="mx-auto max-w-[1800px] px-6 py-[100px] md:px-12 xl:px-[100px]">
        <div className="grid items-center gap-[32px] xl:grid-cols-[738fr_817fr] xl:gap-[45px]">
          <AnimateIn className={imageLeft ? 'xl:order-2' : undefined}>
            <div>
              {label && (
                <span
                  className="inline-flex h-[42px] items-center rounded-[10px] px-[13px] font-heading text-[16px] font-extrabold uppercase leading-5 tracking-[0.8px]"
                  // Pill background is the brand colour at 20%, as in the design.
                  style={{ backgroundColor: 'rgba(252,176,64,0.2)', color: accentLabel }}
                >
                  {label}
                </span>
              )}

              {title && (
                <h2 className="mt-[27px] max-w-[568px] font-heading text-[clamp(28px,2.8vw,40px)] font-extrabold leading-[1.25] text-[color:var(--color-ink)]">
                  {title}
                </h2>
              )}

              {body && (
                <div
                  className="mt-[25px] max-w-[738px] font-body text-[clamp(17px,1.3vw,20px)] font-medium leading-[1.7] text-[color:var(--color-ink)] [&_p]:mb-[24px] [&_p:last-child]:mb-0 2xl:leading-[36.563px]"
                  // Staff-authored rich text (Filament RichEditor).
                  dangerouslySetInnerHTML={{ __html: body }}
                />
              )}

              {features.length > 0 && (
                <ul className="mt-[37px] flex flex-col gap-[32px]">
                  {features.map((feature, i) => (
                    <li key={i} className="flex items-center gap-[15px]">
                      <CheckIcon accent={accent} />
                      <span className="font-body text-[18px] font-medium leading-[32px] text-[color:var(--color-ink)]">
                        {feature}
                      </span>
                    </li>
                  ))}
                </ul>
              )}

              {ctaLabel && (
                <Link
                  href={safeUrl(ctaHref)}
                  className="mt-[29px] inline-flex h-[65px] w-[256px] items-center justify-center gap-2 rounded-[10px] font-heading text-[18px] font-extrabold text-white transition hover:brightness-110"
                  style={{ backgroundColor: accent }}
                >
                  {ctaLabel}
                  <ArrowChevron />
                </Link>
              )}
            </div>
          </AnimateIn>

          {image && (
            <AnimateIn className={imageLeft ? 'xl:order-1' : undefined}>
              <SmartImage
                src={image}
                alt={title}
                className="h-[280px] w-full rounded-[30px] object-cover md:h-[420px] xl:h-[646px]"
              />
            </AnimateIn>
          )}
        </div>
      </div>
    </section>
  );
}
