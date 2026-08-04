'use client';

import { useEffect, useState } from 'react';
import { AnimatePresence, motion, useReducedMotion } from 'framer-motion';
import type { SiteSettings } from '@/lib/api';

/** How long each USP stays on screen in the mobile carousel. */
const ROTATE_MS = 4000;

/**
 * Section 1 — the dark USP bar above the header (Figma 31762:4173).
 * 60px tall, #2b2b2b, three icon+label items. Labels are editable in
 * Instellingen; the design copy is the fallback.
 *
 * On wide screens all three sit side by side as designed. Below that there is
 * no room for three, so they rotate through one slot instead of two being
 * hidden — every USP still gets seen.
 */
export function TopBar({ settings }: { settings: SiteSettings }) {
  const items = [
    { icon: <StarIcon />, label: settings.topbar_usp_1 || 'Geverifieerde reviews op Trustpilot' },
    { icon: <BoltIcon />, label: settings.topbar_usp_2 || 'Snelle service in de regio' },
    { icon: <ShieldIcon />, label: settings.topbar_usp_3 || 'Betrouwbare ICT-partner' },
  ].filter((item) => item.label.trim() !== '');

  if (items.length === 0) return null;

  return (
    <div className="w-full bg-[color:var(--color-ink)]">
      <div className="mx-auto flex h-[60px] max-w-[1800px] items-center px-6 md:px-12 xl:px-[100px]">
        {/* Wide: all three, as designed. */}
        <div className="hidden w-full items-center justify-between gap-6 lg:flex">
          {items.map((item, i) => (
            <Usp key={i} icon={item.icon} label={item.label} />
          ))}
        </div>

        {/* Narrow: one at a time, rotating. */}
        <div className="w-full lg:hidden">
          <UspCarousel items={items} />
        </div>
      </div>
    </div>
  );
}

function Usp({ icon, label }: { icon: React.ReactNode; label: string }) {
  return (
    <div className="flex items-center gap-3">
      <span className="flex shrink-0 items-center justify-center">{icon}</span>
      <span className="font-heading text-[15px] font-semibold leading-5 text-white whitespace-nowrap xl:text-[18px]">
        {label}
      </span>
    </div>
  );
}

function UspCarousel({ items }: { items: { icon: React.ReactNode; label: string }[] }) {
  const [index, setIndex] = useState(0);
  const reduce = useReducedMotion();

  useEffect(() => {
    if (items.length < 2) return;
    const id = setInterval(() => setIndex((i) => (i + 1) % items.length), ROTATE_MS);
    return () => clearInterval(id);
  }, [items.length]);

  const current = items[index];

  return (
    // Fixed height + centred so the swap never shifts the 60px bar.
    <div className="relative flex h-[24px] items-center justify-center overflow-hidden">
      <AnimatePresence mode="wait" initial={false}>
        <motion.div
          key={index}
          initial={reduce ? { opacity: 0 } : { opacity: 0, y: 12 }}
          animate={reduce ? { opacity: 1 } : { opacity: 1, y: 0 }}
          exit={reduce ? { opacity: 0 } : { opacity: 0, y: -12 }}
          transition={{ duration: 0.35, ease: [0.22, 1, 0.36, 1] }}
          className="absolute flex items-center gap-3"
        >
          <span className="flex shrink-0 items-center justify-center">{current.icon}</span>
          <span
            // aria-live so screen readers announce each rotation once.
            className="font-heading text-[14px] font-semibold leading-5 text-white whitespace-nowrap sm:text-[15px]"
          >
            {current.label}
          </span>
        </motion.div>
      </AnimatePresence>
      <span className="sr-only" aria-live="polite">
        {current.label}
      </span>
    </div>
  );
}

/* Icons inlined from the Figma assets so they stay crisp and recolourable. */

function StarIcon() {
  return (
    <svg width="26" height="27" viewBox="0 0 26 27" fill="none" aria-hidden="true" className="h-[22px] w-[21px] xl:h-[27px] xl:w-[26px]">
      <path
        d="M13 0.5l3.363 9.29H26l-7.82 5.742 2.988 9.468L13 19.13l-8.168 5.87 2.988-9.468L0 9.79h9.637L13 0.5z"
        fill="var(--color-success)"
      />
    </svg>
  );
}

function BoltIcon() {
  return (
    <svg width="16" height="30" viewBox="0 0 15.1608 30" fill="none" aria-hidden="true" className="h-[24px] w-[13px] xl:h-[30px] xl:w-[16px]">
      <path
        d="M0 17.5165H4.51565L2.86727 30L15.1608 11.76H10.9768L12.7784 0L0 17.5165Z"
        fill="var(--color-brand)"
      />
    </svg>
  );
}

function ShieldIcon() {
  return (
    <svg width="23" height="26" viewBox="0 0 23 26" fill="none" aria-hidden="true" className="h-[21px] w-[19px] xl:h-[26px] xl:w-[23px]">
      <path
        d="M21.4888 11.9846C21.4888 17.6109 17.5067 22.8806 12.0665 24.4223C11.6963 24.5259 11.2925 24.5259 10.9223 24.4223C5.48203 22.8806 1.5 17.6109 1.5 11.9846V6.93355C1.5 5.99008 2.19547 4.92005 3.05918 4.56337L9.30709 1.9401C10.7092 1.3533 12.2908 1.3533 13.6929 1.9401L19.9408 4.56337C20.7933 4.92005 21.5 5.99008 21.5 6.93355L21.4888 11.9846Z"
        stroke="var(--color-brand)"
        strokeWidth="3"
        strokeLinecap="round"
        strokeLinejoin="round"
      />
    </svg>
  );
}
