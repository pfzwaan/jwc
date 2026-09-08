'use client';

import { useEffect, useRef, useState } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { AnimatePresence, motion, useReducedMotion, type Variants } from 'framer-motion';
import { safeUrl } from '@/lib/safeUrl';
import type { MenuItem, SiteSettings } from '@/lib/api';
import { ArrowChevron } from '@/components/ui/ArrowChevron';

/**
 * Section 2 — the floating white menu bar (Figma 31761:4146). 1600×105,
 * rounded-[10px], shadow 0 5px 30px rgba(0,0,0,.2), sitting over the hero.
 * Logo left (from Instellingen), centred nav from the CMS pages overview
 * (/v1/menu), orange CTA right.
 *
 * The nav always stays on one line: when it no longer fits beside the logo and
 * CTA it collapses to the animated burger overlay. Content-based (ResizeObserver),
 * not a fixed breakpoint, so it scales with the number of menu items.
 */
export function Header({ menu, settings }: { menu: MenuItem[]; settings: SiteSettings }) {
  const [open, setOpen] = useState(false);
  const [compact, setCompact] = useState(false);
  const pathname = usePathname();

  const logoSrc = settings.logo?.trim() || '/images/design/logo.png';
  const ctaLabel = settings.header_cta_label?.trim() || 'Remote Support';
  const ctaHref = settings.header_cta_href?.trim() || '/contact';
  const ctaNewTab = settings.header_cta_new_tab === '1';

  const barRef = useRef<HTMLDivElement>(null);
  const navRef = useRef<HTMLElement>(null);
  const logoRef = useRef<HTMLAnchorElement>(null);
  const ctaRef = useRef<HTMLAnchorElement>(null);

  // Lock body scroll while the overlay is open.
  useEffect(() => {
    document.body.style.overflow = open ? 'hidden' : '';
    return () => {
      document.body.style.overflow = '';
    };
  }, [open]);

  // Resizing back to a width where the full nav fits hides the burger, so close
  // the overlay too — otherwise it stays "open" with the body scroll locked.
  useEffect(() => {
    if (!compact) setOpen(false);
  }, [compact]);

  // Always start a new page at the top — unless the URL targets an anchor.
  useEffect(() => {
    if (!window.location.hash) window.scrollTo(0, 0);
  }, [pathname]);

  // Keep the menu on one line: measure whether it fits beside the logo and CTA;
  // if not, collapse to the burger.
  useEffect(() => {
    const bar = barRef.current;
    const navEl = navRef.current;
    if (!bar || !navEl) return;

    const measure = () => {
      const hidden = getComputedStyle(navEl).display === 'none';
      if (hidden) navEl.style.display = 'flex';
      const navW = navEl.scrollWidth;
      if (hidden) navEl.style.display = '';
      const logoW = logoRef.current?.offsetWidth ?? 0;
      const ctaW = ctaRef.current?.offsetWidth ?? 0;
      const cs = getComputedStyle(bar);
      const padX = parseFloat(cs.paddingLeft) + parseFloat(cs.paddingRight);
      const avail = bar.clientWidth - padX;
      setCompact(navW + logoW + ctaW + 48 + 16 > avail);
    };

    measure();
    const ro = new ResizeObserver(measure);
    ro.observe(bar);
    if (typeof document !== 'undefined' && 'fonts' in document) {
      document.fonts.ready.then(measure).catch(() => {});
    }
    return () => ro.disconnect();
  }, [menu]);

  return (
    <header className="absolute inset-x-0 top-[29px] z-50">
      <div className="mx-auto max-w-[1800px] px-6 md:px-12 xl:px-[100px]">
        <div
          ref={barRef}
          className="flex h-[88px] items-center justify-between gap-3 rounded-[10px] bg-white px-3 shadow-[0px_5px_30px_0px_rgba(0,0,0,0.2)] sm:gap-6 sm:px-4 md:px-[26px] xl:h-[105px]"
        >
          {/* Logo — overflows the bar vertically, exactly as in the design. */}
          <Link href="/" ref={logoRef} className="shrink-0" aria-label="Naar de homepage">
            {/* eslint-disable-next-line @next/next/no-img-element */}
            {/* Bigger than the bar on purpose (as in the design at xl): the
                wordmark and badge inside the logo have to stay readable, and on
                mobile there is no nav competing for the space. */}
            <img
              src={logoSrc}
              alt={settings.company_name || 'Johan Willemsen Computers'}
              className="h-[92px] w-auto sm:h-[104px] xl:h-[134px]"
            />
          </Link>

          {/* Desktop nav — never wraps; collapses to the burger when it can't fit. */}
          <nav
            ref={navRef}
            className={`items-center justify-center whitespace-nowrap ${compact ? 'hidden' : 'hidden lg:flex'}`}
          >
            <ul className="flex items-center gap-[46px]">
              {menu.map((item) => (
                <DesktopNavItem key={item.id} item={item} pathname={pathname} />
              ))}
            </ul>
          </nav>

          <div className="flex shrink-0 items-center gap-3">
            <Link
              href={safeUrl(ctaHref)}
              ref={ctaRef}
              target={ctaNewTab ? '_blank' : undefined}
              rel={ctaNewTab ? 'noopener noreferrer' : undefined}
              className="hidden h-[65px] w-[256px] items-center justify-center gap-2 rounded-[10px] bg-[color:var(--color-brand)] font-heading text-[18px] font-extrabold text-[color:var(--color-ink)] transition hover:brightness-105 sm:inline-flex"
            >
              {ctaLabel}
              <ArrowChevron />
            </Link>

            <BurgerButton open={open} compact={compact} onClick={() => setOpen((v) => !v)} />
          </div>
        </div>
      </div>

      <MobileOverlay
        open={open}
        compact={compact}
        onClose={() => setOpen(false)}
        pathname={pathname}
        menu={menu}
        settings={settings}
        ctaLabel={ctaLabel}
        ctaHref={ctaHref}
        ctaNewTab={ctaNewTab}
      />
    </header>
  );
}

/** Whether a menu item is the current page (or an ancestor of it). */
function isActive(pathname: string, href: string): boolean {
  if (href === '/') return pathname === '/';
  return pathname === href || pathname.startsWith(href + '/');
}


// One top-level menu item: a plain link, or a trigger revealing a dropdown /
// mega menu on hover & focus.
function DesktopNavItem({ item, pathname }: { item: MenuItem; pathname: string }) {
  const [open, setOpen] = useState(false);
  const timer = useRef<ReturnType<typeof setTimeout> | null>(null);
  const reduce = useReducedMotion();

  const hasPanel =
    (item.type === 'dropdown' || item.type === 'mega') && (item.children.length > 0 || item.featured !== null);
  const active = isActive(pathname, item.href);

  const openNow = () => {
    if (timer.current) clearTimeout(timer.current);
    setOpen(true);
  };
  const closeSoon = () => {
    if (timer.current) clearTimeout(timer.current);
    timer.current = setTimeout(() => setOpen(false), 140);
  };

  const labelCls = `font-body text-[18px] leading-[25px] transition-colors hover:text-[color:var(--color-brand)] ${
    active ? 'font-semibold text-black' : 'font-medium text-black'
  }`;

  const panelVariants: Variants = {
    hidden: { opacity: 0, y: reduce ? 0 : 8 },
    visible: { opacity: 1, y: 0, transition: { duration: 0.18, ease: [0.22, 1, 0.36, 1] } },
    exit: { opacity: 0, y: reduce ? 0 : 8, transition: { duration: 0.12 } },
  };

  return (
    <li className="flex items-center">
      {hasPanel ? (
        <div
          className="relative"
          onMouseEnter={openNow}
          onMouseLeave={closeSoon}
          onFocus={openNow}
          onBlur={(e) => {
            if (!e.currentTarget.contains(e.relatedTarget as Node)) closeSoon();
          }}
        >
          <span className={`flex items-center gap-2 ${labelCls}`}>
            <Link href={safeUrl(item.href)} className="outline-none">
              {item.label}
            </Link>
            <button
              type="button"
              aria-haspopup="true"
              aria-expanded={open}
              aria-label={`${item.label} submenu tonen`}
              onClick={() => setOpen((v) => !v)}
              className="flex items-center outline-none"
            >
              <Caret open={open} />
            </button>
          </span>

          <AnimatePresence>
            {open && (
              <motion.div
                variants={panelVariants}
                initial="hidden"
                animate="visible"
                exit="exit"
                className="absolute left-1/2 top-full z-40 -translate-x-1/2 pt-6"
              >
                {item.type === 'mega' ? <MegaPanel item={item} /> : <DropdownPanel item={item} />}
              </motion.div>
            )}
          </AnimatePresence>
        </div>
      ) : (
        <Link href={safeUrl(item.href)} className={labelCls}>
          {item.label}
        </Link>
      )}
    </li>
  );
}

function Caret({ open }: { open: boolean }) {
  return (
    <svg
      width="10"
      height="6"
      viewBox="0 0 10 5.44343"
      fill="none"
      aria-hidden="true"
      className={`transition-transform duration-200 ${open ? 'rotate-180' : ''}`}
    >
      <path
        d="M0.221957 0.224692C-0.0739857 0.52062 -0.0739857 1.00046 0.221957 1.29638L3.92925 5.00004C4.52124 5.59142 5.48046 5.5912 6.07215 4.99958L9.778 1.29365C10.074 0.997729 10.074 0.517892 9.778 0.221957C9.48208 -0.0739857 9.00225 -0.0739857 8.70633 0.221957L5.53449 3.39381C5.23857 3.68981 4.75873 3.68973 4.46281 3.39381L1.29364 0.224692C0.997707 -0.0712503 0.517892 -0.0712503 0.221957 0.224692Z"
        fill="currentColor"
      />
    </svg>
  );
}

// Simple dropdown: a card listing the child pages, with an optional CTA.
function DropdownPanel({ item }: { item: MenuItem }) {
  return (
    <div className="min-w-[260px] overflow-hidden whitespace-normal rounded-2xl bg-white p-2 shadow-[0_26px_60px_-24px_rgba(0,0,0,0.45)] ring-1 ring-black/5">
      {item.children.map((c) => (
        <Link
          key={c.id}
          href={safeUrl(c.href)}
          className="block rounded-xl px-4 py-3 font-body text-[16px] font-medium text-[color:var(--color-ink)] transition-colors hover:bg-[#f5f5f5] hover:text-[color:var(--color-brand)]"
        >
          {c.label}
        </Link>
      ))}
      {item.cta && (
        <Link
          href={safeUrl(item.cta.href)}
          className="mt-1 flex items-center justify-center gap-2 rounded-xl bg-[color:var(--color-brand)] px-4 py-3 font-heading text-[15px] font-extrabold text-[color:var(--color-ink)] transition hover:brightness-105"
        >
          {item.cta.label}
          <ArrowChevron />
        </Link>
      )}
    </div>
  );
}

// Mega menu: two columns of child links + a featured card, optional CTA.
function MegaPanel({ item }: { item: MenuItem }) {
  const half = Math.ceil(item.children.length / 2);
  const columns = [item.children.slice(0, half), item.children.slice(half)];
  const hasFeatured = item.featured !== null;

  return (
    <div
      className="grid w-[min(820px,90vw)] overflow-hidden whitespace-normal rounded-2xl bg-white shadow-[0_30px_70px_-24px_rgba(0,0,0,0.5)] ring-1 ring-black/5"
      // Desktop-only panel — inline columns so equal-width link columns and a
      // slightly wider featured card render reliably. minmax(0,1fr) stops the
      // card's text from blowing out a column.
      style={{
        gridTemplateColumns: hasFeatured
          ? 'minmax(0,1fr) minmax(0,1fr) minmax(0,1.15fr)'
          : 'minmax(0,1fr) minmax(0,1fr)',
      }}
    >
      {columns.map((col, ci) => (
        <div key={ci} className={`p-6 ${ci > 0 ? 'border-l border-[#f0f0f0]' : ''}`}>
          <ul className="space-y-1">
            {col.map((c) => (
              <li key={c.id}>
                <Link
                  href={safeUrl(c.href)}
                  className="block rounded-lg px-3 py-2.5 font-body text-[16px] font-semibold text-[color:var(--color-ink)] transition-colors hover:bg-[#f5f5f5] hover:text-[color:var(--color-brand)]"
                >
                  {c.label}
                </Link>
              </li>
            ))}
            {item.cta && ci === 0 && (
              <li>
                <Link
                  href={safeUrl(item.cta.href)}
                  className="mt-2 inline-flex items-center gap-2 px-3 font-heading text-[15px] font-extrabold text-[color:var(--color-brand)] hover:underline"
                >
                  {item.cta.label}
                  <ArrowChevron />
                </Link>
              </li>
            )}
          </ul>
        </div>
      ))}

      {item.featured && <FeaturedCard featured={item.featured} />}
    </div>
  );
}

function FeaturedCard({ featured }: { featured: NonNullable<MenuItem['featured']> }) {
  return (
    <div className="flex flex-col bg-[color:var(--color-ink)] text-white">
      <div className="relative flex h-32 items-center justify-center bg-[radial-gradient(120%_80%_at_80%_10%,rgba(252,176,64,0.45),transparent_60%)]">
        {featured.badge && (
          <span className="absolute left-3 top-3 rounded-full bg-[color:var(--color-brand)] px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-[color:var(--color-ink)]">
            {featured.badge}
          </span>
        )}
        {featured.image && (
          // eslint-disable-next-line @next/next/no-img-element
          <img src={featured.image} alt={featured.title} className="absolute inset-0 h-full w-full object-cover" />
        )}
      </div>
      <div className="flex flex-1 flex-col gap-2 p-5">
        <h4 className="font-heading text-[21px] font-extrabold leading-tight">{featured.title}</h4>
        {featured.text && <p className="line-clamp-2 text-[13.5px] leading-relaxed text-white/70">{featured.text}</p>}
        {featured.ctaHref && (
          <Link
            href={safeUrl(featured.ctaHref)}
            className="mt-auto inline-flex items-center justify-center gap-2 rounded-lg bg-[color:var(--color-brand)] px-4 py-3 font-heading text-[14px] font-extrabold text-[color:var(--color-ink)] transition hover:brightness-105"
          >
            {featured.ctaLabel || 'Bekijk'}
            <ArrowChevron />
          </Link>
        )}
      </div>
    </div>
  );
}

// Animated hamburger that morphs into an X. Shown below `lg`, and also whenever
// the desktop nav is collapsed because it wouldn't fit on one line (`compact`).
function BurgerButton({ open, compact, onClick }: { open: boolean; compact: boolean; onClick: () => void }) {
  return (
    <button
      type="button"
      aria-label={open ? 'Menu sluiten' : 'Menu openen'}
      aria-expanded={open}
      onClick={onClick}
      className={`relative z-[60] grid h-12 w-12 place-items-center rounded-xl transition-colors ${
        compact ? '' : 'lg:hidden'
      } ${open ? 'bg-[color:var(--color-brand)]' : 'bg-[color:var(--color-ink)]'}`}
    >
      <span className="relative block h-4 w-6">
        <motion.span
          className="absolute left-0 top-0 block h-0.5 w-6 rounded bg-white"
          animate={open ? { rotate: 45, y: 7 } : { rotate: 0, y: 0 }}
          transition={{ duration: 0.3, ease: [0.22, 1, 0.36, 1] }}
        />
        <motion.span
          className="absolute left-0 top-[7px] block h-0.5 w-6 rounded bg-white"
          animate={open ? { opacity: 0, x: -8 } : { opacity: 1, x: 0 }}
          transition={{ duration: 0.2 }}
        />
        <motion.span
          className="absolute bottom-0 left-0 block h-0.5 w-6 rounded bg-white"
          animate={open ? { rotate: -45, y: -7 } : { rotate: 0, y: 0 }}
          transition={{ duration: 0.3, ease: [0.22, 1, 0.36, 1] }}
        />
      </span>
    </button>
  );
}

const overlayVariants: Variants = {
  hidden: { x: '100%' },
  visible: {
    x: 0,
    transition: {
      duration: 0.5,
      ease: [0.22, 1, 0.36, 1],
      when: 'beforeChildren',
      staggerChildren: 0.07,
      delayChildren: 0.15,
    },
  },
  exit: { x: '100%', transition: { duration: 0.4, ease: [0.55, 0, 0.55, 0] } },
};

const itemVariants: Variants = {
  hidden: { opacity: 0, x: 40 },
  visible: { opacity: 1, x: 0, transition: { duration: 0.5, ease: [0.22, 1, 0.36, 1] } },
  exit: { opacity: 0, x: 40 },
};

/**
 * The overlay must be visible in exactly the cases the burger is: below `lg`,
 * and at any width where the desktop nav was collapsed because it wouldn't fit
 * on one line (`compact`).
 */
function MobileOverlay({
  open,
  compact,
  onClose,
  pathname,
  menu,
  settings,
  ctaLabel,
  ctaHref,
  ctaNewTab,
}: {
  open: boolean;
  compact: boolean;
  onClose: () => void;
  pathname: string;
  menu: MenuItem[];
  settings: SiteSettings;
  ctaLabel: string;
  ctaHref: string;
  ctaNewTab: boolean;
}) {
  const phone = settings.contact_phone?.trim();
  const email = settings.contact_email?.trim();

  return (
    <AnimatePresence>
      {open && (
        <motion.div
          className={`fixed inset-0 z-[55] flex flex-col bg-[color:var(--color-ink)] text-white ${compact ? '' : 'lg:hidden'}`}
          variants={overlayVariants}
          initial="hidden"
          animate="visible"
          exit="exit"
        >
          {/* decorative brand glow */}
          <div className="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-[color:var(--color-brand)]/20 blur-3xl" />

          {/* Scroll container + inner centering wrapper: `justify-center` directly
              on an overflowing scroll container clips the top items out of reach;
              with `min-h-full` the wrapper only centres when the menu is shorter
              than the screen. */}
          <nav className="relative flex-1 overflow-y-auto px-8 pb-6 pt-24">
            <div className="flex min-h-full flex-col justify-center gap-1">
              {menu.map((item) => {
                const active = isActive(pathname, item.href);
                return (
                  <motion.div key={item.id} variants={itemVariants}>
                    <Link
                      href={safeUrl(item.href)}
                      onClick={onClose}
                      className="group flex items-center gap-4 py-2.5 font-heading text-[32px] font-extrabold leading-none transition-colors"
                    >
                      <span
                        className={`h-[3px] w-8 rounded bg-[color:var(--color-brand)] transition-all duration-300 group-hover:w-14 ${active ? 'w-14' : ''}`}
                      />
                      <span
                        className={
                          active ? 'text-[color:var(--color-brand)]' : 'text-white group-hover:text-[color:var(--color-brand)]'
                        }
                      >
                        {item.label}
                      </span>
                    </Link>
                    {item.children.length > 0 && (
                      <div className="mb-2 ml-12 flex flex-col gap-1">
                        {item.children.map((c) => (
                          <Link
                            key={c.id}
                            href={safeUrl(c.href)}
                            onClick={onClose}
                            className="py-1 text-[17px] text-white/70 transition-colors hover:text-[color:var(--color-brand)]"
                          >
                            {c.label}
                          </Link>
                        ))}
                      </div>
                    )}
                  </motion.div>
                );
              })}
            </div>
          </nav>

          <motion.div variants={itemVariants} className="relative space-y-5 px-8 pb-10">
            <Link
              href={safeUrl(ctaHref)}
              onClick={onClose}
              target={ctaNewTab ? '_blank' : undefined}
              rel={ctaNewTab ? 'noopener noreferrer' : undefined}
              className="flex h-[65px] items-center justify-center gap-2 rounded-[10px] bg-[color:var(--color-brand)] font-heading text-[18px] font-extrabold text-[color:var(--color-ink)]"
            >
              {ctaLabel}
              <ArrowChevron />
            </Link>
            <div className="flex flex-col gap-2 text-[15px] text-white/70">
              {phone && (
                <a href={`tel:${phone.replace(/\s/g, '')}`} className="transition hover:text-[color:var(--color-brand)]">
                  {phone}
                </a>
              )}
              {email && (
                <a href={`mailto:${email}`} className="transition hover:text-[color:var(--color-brand)]">
                  {email}
                </a>
              )}
            </div>
          </motion.div>
        </motion.div>
      )}
    </AnimatePresence>
  );
}
