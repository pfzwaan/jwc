import Link from 'next/link';
import { AnimateIn } from '@/components/ui/AnimateIn';
import { ArrowChevron } from '@/components/ui/ArrowChevron';
import { Slider } from '@/components/ui/Slider';
import { ProjectCard } from '@/components/projecten/ProjectCard';
import { safeUrl } from '@/lib/safeUrl';
import type { ProjectCardData } from '@/lib/api';
import { arr, str } from '@/lib/blockData';

/**
 * Realised projects. Each card is a browser-window mockup: traffic-light dots
 * on a 59px chrome bar, #E5E5E5 rules around the screenshot, then the site name
 * with an external-link icon.
 *
 * Two layouts from one widget:
 * - `slider` — home (Figma 31793:2030): 565×485 cards, scroll-snap + arrows.
 * - `grid`   — Website & Hosting (31848:2205): 515×471 cards, 3 columns.
 */
export function ProjectsBlock({ data }: { data: Record<string, unknown> }) {
  const heading = str(data.heading);
  const intro = str(data.intro);
  const ctaLabel = str(data.cta_label);
  const ctaHref = str(data.cta_href);
  const isGrid = str(data.layout) === 'grid';
  const columns = Number(data.columns ?? 3);
  // Resolved server-side by PageController (explicit selection, else the latest).
  const projects = arr(data.items) as unknown as ProjectCardData[];

  if (projects.length === 0 && heading === '') return null;

  const gridCols =
    columns === 2 ? 'sm:grid-cols-2' : columns === 4 ? 'sm:grid-cols-2 xl:grid-cols-4' : 'sm:grid-cols-2 xl:grid-cols-3';

  return (
    <section className="w-full overflow-hidden bg-white">
      <div className={`mx-auto max-w-[1800px] pb-[100px] pt-[100px] ${isGrid ? 'px-6 md:px-12 xl:px-[100px]' : ''}`}>
        <AnimateIn>
          <div className={`text-center ${isGrid ? '' : 'px-6 md:px-12 xl:px-[100px]'}`}>
            {heading && (
              <h2 className="mx-auto max-w-[876px] font-heading text-[clamp(26px,2.8vw,40px)] font-extrabold leading-[1.2] text-[color:var(--color-ink)]">
                {heading}
              </h2>
            )}
            {intro && (
              <p className="mx-auto mt-[16px] max-w-[778px] font-body text-[clamp(17px,1.3vw,20px)] leading-[1.7] text-[color:var(--color-ink)] 2xl:leading-[36.563px]">
                {intro}
              </p>
            )}
          </div>
        </AnimateIn>

        {isGrid ? (
          <div className={`mt-[72px] grid gap-[27px] ${gridCols}`}>
            {projects.map((project) => (
              <AnimateIn key={project.id}>
                <ProjectCard project={project} variant="grid" />
              </AnimateIn>
            ))}
          </div>
        ) : (
          /* The right edge bleeds past the content column so a partial card
             hints that the row scrolls; touch scrolling still works. */
          <Slider
            label="Projecten"
            className="mt-[63px]"
            trackClassName="gap-[28px] px-6 scroll-pl-6 md:px-12 md:scroll-pl-12 xl:px-[100px] xl:scroll-pl-[100px]"
          >
            {projects.map((project) => (
              <ProjectCard key={project.id} project={project} variant="slider" />
            ))}
          </Slider>
        )}

        {ctaLabel && (
          <AnimateIn>
            <div className={`mt-[77px] flex justify-center ${isGrid ? '' : 'px-6'}`}>
              <Link
                href={safeUrl(ctaHref)}
                className="inline-flex h-[65px] w-[256px] items-center justify-center gap-2 rounded-[10px] bg-[color:var(--color-brand)] font-heading text-[18px] font-extrabold text-[color:var(--color-ink)] transition hover:brightness-105"
              >
                {ctaLabel}
                <ArrowChevron />
              </Link>
            </div>
          </AnimateIn>
        )}
      </div>
    </section>
  );
}


