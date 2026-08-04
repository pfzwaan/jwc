import Link from 'next/link';
import { SmartImage } from '@/components/ui/SmartImage';
import { safeUrl } from '@/lib/safeUrl';
import type { ProjectCardData } from '@/lib/api';

/**
 * A project as a browser-window mockup: traffic-light dots on a 59px chrome
 * bar, #E5E5E5 rules around the screenshot, then the site name with an
 * external-link icon.
 *
 * `slider` is the 565×485 homepage card (Figma 31793:2030); `grid` is the
 * 515×471 one used on Website & Hosting and the project detail page.
 */
export function ProjectCard({
  project,
  variant = 'grid',
}: {
  project: ProjectCardData;
  variant?: 'slider' | 'grid';
}) {
  const slider = variant === 'slider';

  const card = (
    <article
      className={[
        'group flex flex-col overflow-hidden rounded-[20px] border border-[#dbdbdb] bg-white transition duration-300',
        slider
          ? // 565px as designed, but never wider than the viewport — on a phone
            // the card shrinks and the next one peeks in.
            'h-[485px] w-[min(565px,82vw)] shrink-0 snap-start'
          : 'h-full min-h-[471px] w-full',
        project.featured
          ? 'shadow-[0px_13px_34px_0px_rgba(0,0,0,0.2)]'
          : 'hover:shadow-[0px_13px_34px_0px_rgba(0,0,0,0.12)]',
      ].join(' ')}
    >
      {/* Window chrome */}
      <div className="flex h-[59px] shrink-0 items-center px-[22px]">
        <svg width="59" height="13" viewBox="0 0 59 13" fill="none" aria-hidden="true">
          <circle cx="6.5" cy="6.5" r="6.5" fill="#FF6058" />
          <circle cx="29.5" cy="6.5" r="6.5" fill="#FFBD2E" />
          <circle cx="52.5" cy="6.5" r="6.5" fill="#2BC943" />
        </svg>
      </div>

      {/* Screenshot, ruled top and bottom */}
      <div className={`shrink-0 border-y border-[#E5E5E5] ${slider ? 'h-[353px]' : 'h-[352px]'}`}>
        <SmartImage src={project.image} alt={project.title} className="h-full w-full object-cover object-top" />
      </div>

      {/* Footer */}
      <div className="flex flex-1 items-center justify-between gap-3 pl-[22px] pr-[20px]">
        <span className="font-heading text-[18px] font-bold leading-[24px] text-[color:var(--color-ink)]">
          {project.title}
        </span>
        <ExternalLinkIcon />
      </div>
    </article>
  );

  // Link to the project's own page; fall back to the live site when there isn't one.
  const href = project.href || project.url || '';
  if (href.trim() === '') return card;

  const external = href.startsWith('http');

  return (
    <Link
      href={safeUrl(href)}
      target={external ? '_blank' : undefined}
      rel={external ? 'noopener noreferrer' : undefined}
      className={slider ? 'shrink-0 snap-start' : 'block h-full'}
      aria-label={`${project.title} bekijken`}
    >
      {card}
    </Link>
  );
}

/**
 * Filled circle with an arrow leaving the box — 36×36 as in the design.
 *
 * Always brand yellow at rest and blue while the card is hovered, the same on
 * every card. It deliberately ignores the project's `accent`: most rows carry a
 * literal #1e62d4 from before this was a hover state, so honouring the field
 * would leave the grid mostly blue at rest.
 */
function ExternalLinkIcon() {
  return (
    <svg
      width="36"
      height="36"
      viewBox="0 0 36 36"
      fill="none"
      aria-hidden="true"
      className="shrink-0 text-[#fcb040] transition-colors duration-300 group-hover:text-[#1e62d4]"
    >
      <path
        fillRule="evenodd"
        clipRule="evenodd"
        d="M18 0C27.941 0 36 8.05887 36 18C36 27.941 27.941 36 18 36C8.05887 36 0 27.941 0 18C0 8.05887 8.05887 0 18 0ZM25.2 21.6C25.2 22.5941 24.3941 23.4 23.4 23.4C22.4059 23.4 21.6 22.5941 21.6 21.6V16.9456L13.8728 24.6728C13.1698 25.3757 12.0302 25.3757 11.3272 24.6728C10.6243 23.9699 10.6243 22.8301 11.3272 22.1272L19.0544 14.4H14.4C13.4059 14.4 12.6 13.5941 12.6 12.6C12.6 11.6059 13.4059 10.8 14.4 10.8H22.8091C24.1297 10.8 25.2 11.8708 25.2 13.1911V21.6Z"
        fill="currentColor"
      />
    </svg>
  );
}
