'use client';

import { useEffect, useRef } from 'react';

/**
 * Decorative background video for the hero.
 *
 * A client component purely because playbackRate has no HTML attribute — it can
 * only be set on the element, so a server-rendered <video> can never play at
 * anything but 1×.
 */
export function BackgroundVideo({ src, speed = 1 }: { src: string; speed?: number }) {
  const ref = useRef<HTMLVideoElement>(null);

  useEffect(() => {
    const el = ref.current;
    if (!el) return;

    // Browsers reset playbackRate when a source loads, and on a cold page load
    // the effect can run before metadata arrives — so apply it both now and on
    // loadedmetadata, otherwise the first loop plays at full speed.
    const apply = () => {
      el.playbackRate = speed;
    };

    apply();
    el.addEventListener('loadedmetadata', apply);

    return () => el.removeEventListener('loadedmetadata', apply);
  }, [speed, src]);

  return (
    <video
      ref={ref}
      className="absolute inset-0 h-full w-full object-cover"
      src={src}
      autoPlay
      muted
      loop
      playsInline
      aria-hidden="true"
    />
  );
}
