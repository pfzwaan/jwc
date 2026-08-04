'use client';

import { useCallback, useEffect, useRef, useState } from 'react';

/**
 * Horizontal scroll-snap slider with prev/next controls.
 *
 * Children stay server-rendered — only the track and its controls are client
 * side, so the cards are still in the initial HTML for crawlers. Native
 * touch/trackpad scrolling keeps working; the arrows are an addition, not a
 * replacement, and hide themselves when there is nothing to scroll to.
 */
export function Slider({
  children,
  label,
  className = '',
  trackClassName = '',
}: {
  children: React.ReactNode;
  label: string;
  className?: string;
  trackClassName?: string;
}) {
  const trackRef = useRef<HTMLDivElement>(null);
  const [canPrev, setCanPrev] = useState(false);
  const [canNext, setCanNext] = useState(false);

  const update = useCallback(() => {
    const el = trackRef.current;
    if (!el) return;
    // 2px slack so sub-pixel widths don't leave a permanently "enabled" arrow.
    setCanPrev(el.scrollLeft > 2);
    setCanNext(el.scrollLeft + el.clientWidth < el.scrollWidth - 2);
  }, []);

  useEffect(() => {
    const el = trackRef.current;
    if (!el) return;
    update();
    el.addEventListener('scroll', update, { passive: true });
    const ro = new ResizeObserver(update);
    ro.observe(el);
    return () => {
      el.removeEventListener('scroll', update);
      ro.disconnect();
    };
  }, [update]);

  /** Scroll by one card, measured from the first child so it stays in step. */
  const scrollByCard = (direction: 1 | -1) => {
    const el = trackRef.current;
    if (!el) return;
    const first = el.firstElementChild as HTMLElement | null;
    const step = first ? first.offsetWidth + 28 : el.clientWidth * 0.8;
    el.scrollBy({ left: step * direction, behavior: 'smooth' });
  };

  return (
    <div className={`relative ${className}`}>
      <div
        ref={trackRef}
        role="region"
        aria-label={label}
        tabIndex={0}
        className={`flex snap-x snap-mandatory overflow-x-auto scroll-smooth [scrollbar-width:none] focus-visible:outline-2 focus-visible:outline-[color:var(--color-brand)] [&::-webkit-scrollbar]:hidden ${trackClassName}`}
      >
        {children}
      </div>

      <Arrow direction="prev" disabled={!canPrev} onClick={() => scrollByCard(-1)} />
      <Arrow direction="next" disabled={!canNext} onClick={() => scrollByCard(1)} />
    </div>
  );
}

function Arrow({
  direction,
  disabled,
  onClick,
}: {
  direction: 'prev' | 'next';
  disabled: boolean;
  onClick: () => void;
}) {
  const isPrev = direction === 'prev';

  return (
    <button
      type="button"
      onClick={onClick}
      disabled={disabled}
      aria-label={isPrev ? 'Vorige projecten' : 'Volgende projecten'}
      className={`absolute top-1/2 z-10 hidden size-[56px] -translate-y-1/2 place-items-center rounded-full bg-white text-[color:var(--color-ink)] shadow-[0px_5px_30px_0px_rgba(0,0,0,0.2)] transition hover:bg-[color:var(--color-brand)] disabled:pointer-events-none disabled:opacity-0 md:grid ${
        isPrev ? 'left-6 xl:left-[40px]' : 'right-6 xl:right-[40px]'
      }`}
    >
      <svg
        width="18"
        height="18"
        viewBox="0 0 24 24"
        fill="none"
        aria-hidden="true"
        className={isPrev ? 'rotate-180' : ''}
      >
        <path d="M9 5l7 7-7 7" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" />
      </svg>
    </button>
  );
}
