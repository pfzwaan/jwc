import Link from 'next/link';

/**
 * Numbered pagination (Figma 31854:2005) — 60×60 boxes with prev/next arrows.
 *
 * Server-rendered links rather than client state, so every page is a real URL
 * that Google can crawl and a visitor can bookmark.
 */
export function Pagination({
  page,
  totalPages,
  basePath,
}: {
  page: number;
  totalPages: number;
  basePath: string;
}) {
  if (totalPages <= 1) return null;

  const href = (n: number) => (n === 1 ? basePath : `${basePath}?page=${n}`);
  const pages = Array.from({ length: totalPages }, (_, i) => i + 1);

  return (
    <nav className="flex justify-center gap-[12px] xl:justify-end" aria-label="Paginering">
      <Arrow direction="prev" href={href(page - 1)} disabled={page <= 1} />

      {pages.map((n) => (
        <Link
          key={n}
          href={href(n)}
          aria-current={n === page ? 'page' : undefined}
          className={[
            'grid size-[60px] place-items-center rounded-[14px] border font-heading text-[20px] font-bold transition',
            n === page
              ? 'border-transparent bg-[color:var(--color-brand)] text-[color:var(--color-ink)]'
              : 'border-[#f3f4f6] bg-white text-[color:var(--color-ink)] hover:border-[color:var(--color-brand)]',
          ].join(' ')}
        >
          {n}
        </Link>
      ))}

      <Arrow direction="next" href={href(page + 1)} disabled={page >= totalPages} />
    </nav>
  );
}

function Arrow({ direction, href, disabled }: { direction: 'prev' | 'next'; href: string; disabled: boolean }) {
  const isPrev = direction === 'prev';
  const label = isPrev ? 'Vorige pagina' : 'Volgende pagina';

  const icon = (
    <svg width="17" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true" className={isPrev ? 'rotate-180' : ''}>
      <path d="M9 5l7 7-7 7" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );

  const base = 'grid size-[60px] place-items-center rounded-[14px] border border-[#f3f4f6] bg-white transition';

  if (disabled) {
    return (
      <span aria-hidden="true" className={`${base} cursor-not-allowed text-[color:var(--color-muted)] opacity-40`}>
        {icon}
      </span>
    );
  }

  return (
    <Link href={href} aria-label={label} className={`${base} text-[color:var(--color-ink)] hover:border-[color:var(--color-brand)]`}>
      {icon}
    </Link>
  );
}
