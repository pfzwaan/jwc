import type { Metadata } from 'next';
import { fetchPage, fetchProjects } from '@/lib/api';
import { pageMetadata } from '@/lib/seo';
import { AnimateIn } from '@/components/ui/AnimateIn';
import { ProjectCard } from '@/components/projecten/ProjectCard';

/**
 * Projecten overview. If staff create a CMS page with slug "projecten" its
 * widgets win, so the page can be composed like any other; otherwise this
 * template lists the whole CPT.
 */
export async function generateMetadata(): Promise<Metadata> {
  const page = await fetchPage('projecten');

  return pageMetadata({
    seo: page?.seo,
    title: page?.title || 'Projecten',
    description: 'Websites, ICT-projecten en technische oplossingen die wij hebben gerealiseerd.',
  });
}

export default async function ProjectenPage() {
  const projects = await fetchProjects();

  return (
    <>
      <section className="relative w-full overflow-hidden bg-[color:var(--color-overlay)]">
        <div className="pointer-events-none absolute inset-0 hidden xl:block" aria-hidden="true">
          {/* eslint-disable-next-line @next/next/no-img-element */}
          <img
            src="/images/design/hero-swoosh-right.svg"
            alt=""
            className="absolute left-[1150px] top-[-629px] h-[1384px] w-[1709px] max-w-none"
          />
        </div>

        <div className="relative mx-auto flex min-h-[420px] max-w-[1800px] flex-col justify-start px-6 pb-[58px] pt-[200px] md:px-12 xl:min-h-[683px] xl:px-[100px] xl:pt-[259px]">
          <AnimateIn>
            <h1 className="max-w-[956px] font-heading text-[clamp(34px,4.6vw,65px)] font-extrabold leading-[1.26] text-white">
              Onze <span className="text-[color:var(--color-brand)]">projecten</span>
            </h1>
          </AnimateIn>
          <AnimateIn delay={0.08}>
            <p className="mt-[39px] max-w-[956px] font-body text-[clamp(17px,1.4vw,22px)] font-semibold leading-[1.66] text-white">
              Bekijk een selectie van websites, ICT-projecten en technische oplossingen die wij hebben gerealiseerd.
            </p>
          </AnimateIn>
        </div>
      </section>

      <section className="w-full bg-white">
        <div className="mx-auto max-w-[1800px] px-6 py-[100px] md:px-12 xl:px-[100px]">
          {projects.length === 0 ? (
            <p className="text-center font-body text-[18px] text-[color:var(--color-card-body)]">
              Er zijn nog geen projecten gepubliceerd.
            </p>
          ) : (
            <div className="grid gap-[27px] sm:grid-cols-2 xl:grid-cols-3">
              {projects.map((project) => (
                <AnimateIn key={project.id}>
                  <ProjectCard project={project} />
                </AnimateIn>
              ))}
            </div>
          )}
        </div>
      </section>
    </>
  );
}
