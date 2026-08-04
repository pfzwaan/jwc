import { AnimateIn } from '@/components/ui/AnimateIn';
import { SmartImage } from '@/components/ui/SmartImage';
import { arr, str } from '@/lib/blockData';

/**
 * Stats band (Figma 31847:1537). #fafafa strip, 415px tall, figures spread
 * evenly: an 80×80 white icon tile (rounded-[14px], soft drop shadow), a 45px
 * accent number and an 18px label.
 */
export function StatsBlock({ data }: { data: Record<string, unknown> }) {
  const accent = str(data.accent) || '#ffa425';
  const stats = arr(data.stats).filter((s) => str(s.value) !== '');

  if (stats.length === 0) return null;

  return (
    <section className="w-full bg-[color:var(--color-surface-alt)]">
      <div className="mx-auto max-w-[1800px] px-6 py-[80px] md:px-12 xl:px-[100px] xl:py-[116px]">
        <div className="grid grid-cols-2 gap-x-6 gap-y-12 xl:grid-cols-4">
          {stats.map((stat, i) => (
            <AnimateIn key={i} delay={i * 0.08}>
              <div className="flex flex-col items-center text-center">
                <div className="grid size-[80px] place-items-center rounded-[14px] bg-white drop-shadow-[0px_0px_10px_rgba(0,0,0,0.1)]">
                  <SmartImage src={str(stat.icon)} alt="" className="size-[46px] object-contain" />
                </div>

                <p
                  className="mt-[33px] font-heading text-[clamp(32px,3.2vw,45px)] font-extrabold leading-[36px]"
                  style={{ color: accent }}
                >
                  {str(stat.value)}
                </p>

                {stat.label && (
                  <p className="mt-[14px] font-body text-[18px] leading-5 text-[color:var(--color-ink-deep)]">
                    {str(stat.label)}
                  </p>
                )}
              </div>
            </AnimateIn>
          ))}
        </div>
      </div>
    </section>
  );
}
