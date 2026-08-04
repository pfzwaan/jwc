import type { Metadata } from 'next';
import Link from 'next/link';
import { notFound } from 'next/navigation';
import { fetchProject, fetchProjects, fetchSettings } from '@/lib/api';
import { AnimateIn } from '@/components/ui/AnimateIn';
import { ArrowChevron } from '@/components/ui/ArrowChevron';
import { DeviceMockup } from '@/components/ui/DeviceMockup';
import { ProjectCard } from '@/components/projecten/ProjectCard';
import { ContactFormBlock } from '@/components/blocks/ContactFormBlock';
import { safeUrl } from '@/lib/safeUrl';
import { pageMetadata } from '@/lib/seo';

/**
 * Project detail template (Figma 31849:2371 — "Website & Hosting-inner").
 *
 * Dark hero with the project name and a laptop/phone mockup, then the
 * description beside a meta card (website, categorieën, vaardigheden), three
 * other projects, and the contact form.
 *
 * Next.js 16: `params` is a Promise and must be awaited.
 */
type Params = { slug: string };

export async function generateMetadata({ params }: { params: Promise<Params> }): Promise<Metadata> {
  const { slug } = await params;
  const project = await fetchProject(slug);
  if (!project) return {};

  return pageMetadata({
    seo: project.seo,
    title: project.title,
    // Falls back to the summary, then the opening of the article body.
    description: project.excerpt || project.body,
    image: project.image,
  });
}

/** Pre-render every published project at build time. */
export async function generateStaticParams() {
  const projects = await fetchProjects();

  return projects.map((p) => ({ slug: p.slug }));
}

export default async function ProjectDetailPage({ params }: { params: Promise<Params> }) {
  const { slug } = await params;
  const [project, all, settings] = await Promise.all([fetchProject(slug), fetchProjects(), fetchSettings()]);

  if (!project) notFound();

  const others = all.filter((p) => p.slug !== project.slug).slice(0, 3);

  return (
    <>
      {/* Hero. The dark panel is only 680px tall (Figma 31849:2621) while the
          mockup runs to 944 — it deliberately overhangs onto the white below,
          so the clipping wrapper is the panel, never the section. */}
      <section className="relative w-full bg-white">
        <div
          className="absolute inset-x-0 top-0 h-[420px] overflow-hidden bg-[color:var(--color-overlay)] xl:h-[680px]"
          aria-hidden="true"
        >
          {/* eslint-disable-next-line @next/next/no-img-element */}
          <img
            src="/images/design/hero-swoosh-right.svg"
            alt=""
            className="absolute left-[1150px] top-[-629px] hidden h-[1384px] w-[1709px] max-w-none xl:block"
          />
        </div>

        <div className="relative mx-auto max-w-[1800px] px-6 pb-[96px] pt-[190px] md:px-12 xl:px-[100px] xl:pt-[272px]">
          <AnimateIn>
            <h1 className="text-center font-heading text-[clamp(30px,3.4vw,48px)] font-extrabold leading-[1.2] text-white">
              {project.title}
            </h1>
          </AnimateIn>

          <AnimateIn delay={0.08}>
            <div className="mt-[50px] xl:mt-[75px]">
              <DeviceMockup image={project.image} imageMobile={project.imageMobile} alt={project.title} />
            </div>
          </AnimateIn>
        </div>
      </section>

      {/* Description + meta card */}
      <section className="w-full bg-white">
        <div className="mx-auto max-w-[1800px] px-6 pb-[100px] pt-[60px] md:px-12 xl:px-[100px]">
          <div className="grid gap-[45px] xl:grid-cols-[1007fr_493fr]">
            <AnimateIn>
              <div>
                <h2 className="font-heading text-[clamp(28px,2.8vw,40px)] font-extrabold leading-[1.25] text-[color:var(--color-ink)]">
                  Omschrijving
                </h2>

                {project.body && (
                  <div
                    className="mt-[28px] max-w-[1007px] font-body text-[clamp(17px,1.3vw,20px)] font-medium leading-[1.7] text-[color:var(--color-ink)] [&_p]:mb-[20px] [&_p:last-child]:mb-0 2xl:leading-[36.563px]"
                    // Staff-authored rich text (Filament RichEditor).
                    dangerouslySetInnerHTML={{ __html: project.body }}
                  />
                )}

                <Link
                  href="/contact"
                  className="mt-[52px] inline-flex h-[65px] w-[256px] items-center justify-center gap-2 rounded-[10px] bg-[color:var(--color-brand)] font-heading text-[18px] font-extrabold text-[color:var(--color-ink)] transition hover:brightness-105"
                >
                  Soortgelijk project?
                  <ArrowChevron />
                </Link>
              </div>
            </AnimateIn>

            <AnimateIn delay={0.08}>
              <MetaCard project={project} />
            </AnimateIn>
          </div>
        </div>
      </section>

      {/* Other projects */}
      {others.length > 0 && (
        <section className="w-full bg-[color:var(--color-surface-alt)]">
          <div className="mx-auto max-w-[1800px] px-6 py-[98px] md:px-12 xl:px-[100px]">
            <AnimateIn>
              <h2 className="font-heading text-[clamp(26px,2.8vw,40px)] font-extrabold leading-[1.2] text-[color:var(--color-ink)]">
                Andere projecten
              </h2>
            </AnimateIn>

            <div className="mt-[83px] grid gap-[27px] sm:grid-cols-2 xl:grid-cols-3">
              {others.map((p) => (
                <AnimateIn key={p.id}>
                  <ProjectCard project={p} />
                </AnimateIn>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* Contact form */}
      <ContactFormBlock
        data={{
          heading: 'Wij helpen u graag',
          intro: '',
          show_details: true,
          details_heading: 'Liever direct bellen?',
          submit_label: 'Bericht versturen',
          subjects: [],
        }}
        settings={settings}
        source={`/projecten/${project.slug}`}
      />
    </>
  );
}

/** Website, categorieën and vaardigheden (Figma 31852:2728). */
function MetaCard({
  project,
}: {
  project: Awaited<ReturnType<typeof fetchProject>> & object;
}) {
  const host = (project.url ?? '').replace(/^https?:\/\//, '').replace(/\/$/, '');

  return (
    <div className="rounded-[30px] border-[1.25px] border-[#f3f4f6] bg-[#f9fafb] p-[31px] xl:min-h-[415px]">
      {host && (
        <>
          <h3 className="font-heading text-[18px] font-bold leading-[25px] text-[color:var(--color-card-title)]">
            Website
          </h3>
          <a
            href={safeUrl(project.url ?? '')}
            target="_blank"
            rel="noopener noreferrer"
            className="mt-[19px] flex items-center gap-[10px] font-body text-[clamp(18px,1.4vw,22px)] text-[color:var(--color-ink)] underline transition-colors hover:text-[color:var(--color-brand)]"
          >
            <svg width="27" height="27" viewBox="0 0 24 24" fill="none" aria-hidden="true" className="shrink-0">
              <path
                d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"
                stroke="currentColor"
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
              />
            </svg>
            {host}
          </a>
        </>
      )}

      <TagGroup label="Categorieën:" tags={project.categories ?? []} className={host ? 'mt-[27px]' : ''} />
      <TagGroup label="Vaardigheden:" tags={project.skills ?? []} className="mt-[30px]" />
    </div>
  );
}

function TagGroup({ label, tags, className = '' }: { label: string; tags: string[]; className?: string }) {
  if (tags.length === 0) return null;

  return (
    <div className={className}>
      <h3 className="font-heading text-[18px] font-bold leading-[25px] text-[color:var(--color-card-title)]">
        {label}
      </h3>
      <ul className="mt-[19px] flex flex-wrap gap-[16px]">
        {tags.map((tag) => (
          <li
            key={tag}
            className="inline-flex h-[38px] items-center rounded-[30px] border border-[rgba(255,164,37,0.1)] bg-[#fff3e0] px-[15px] font-body text-[18px] font-semibold leading-4 text-[color:var(--color-link)]"
          >
            {tag}
          </li>
        ))}
      </ul>
    </div>
  );
}

