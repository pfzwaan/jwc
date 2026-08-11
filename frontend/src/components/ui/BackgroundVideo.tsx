'use client';

import { useEffect, useRef, useState } from 'react';

/**
 * Decorative background video for the hero.
 *
 * A client component for two reasons: playbackRate has no HTML attribute (it can
 * only be set on the element, so a server-rendered <video> can never play at
 * anything but 1×), and the video must not be fetched at all on the devices that
 * can least afford it.
 *
 * **Why it isn't just an autoplaying <video>.** `autoplay` makes the browser
 * download the whole file immediately, and the admin accepts uploads up to
 * 128 MB. On a phone that competes with the HTML, CSS, fonts and hero image for
 * the same connection, so the page above the fold gets visibly slower — for
 * decoration nobody is watching. So the element is only mounted once we know the
 * viewport is big enough to be a desktop, and the still image stands in
 * everywhere else.
 *
 * The checks run after mount because none of them exist during SSR. That's also
 * why the poster renders first: the server and the first client paint agree, and
 * the video (when it does load) fades in over it.
 */
export function BackgroundVideo({
  src,
  speed = 1,
  poster,
}: {
  src: string;
  speed?: number;
  poster?: string;
}) {
  const ref = useRef<HTMLVideoElement>(null);
  const [enabled, setEnabled] = useState(false);

  useEffect(() => {
    // matchMedia and navigator.connection are client-only; this whole effect is
    // the "should we spend the bytes?" decision.
    const query = window.matchMedia('(min-width: 768px)');
    const motion = window.matchMedia('(prefers-reduced-motion: reduce)');

    const decide = () => {
      // Save-Data is an explicit "don't", and 2g/3g means the video would arrive
      // long after anyone stopped looking.
      const connection = (
        navigator as Navigator & {
          connection?: { saveData?: boolean; effectiveType?: string };
        }
      ).connection;

      const cheapConnection =
        connection?.saveData === true ||
        (connection?.effectiveType !== undefined && /2g|3g/.test(connection.effectiveType));

      setEnabled(query.matches && !motion.matches && !cheapConnection);
    };

    decide();

    query.addEventListener('change', decide);
    motion.addEventListener('change', decide);

    return () => {
      query.removeEventListener('change', decide);
      motion.removeEventListener('change', decide);
    };
  }, []);

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
  }, [speed, src, enabled]);

  return (
    <>
      {/* Always painted: it is the mobile background, and the thing the video
          fades in over on desktop. */}
      {poster && (
        // eslint-disable-next-line @next/next/no-img-element
        <img src={poster} alt="" aria-hidden="true" className="absolute inset-0 h-full w-full object-cover" />
      )}

      {enabled && (
        <video
          ref={ref}
          className="absolute inset-0 h-full w-full object-cover"
          src={src}
          poster={poster}
          autoPlay
          muted
          loop
          playsInline
          preload="metadata"
          aria-hidden="true"
        />
      )}
    </>
  );
}
