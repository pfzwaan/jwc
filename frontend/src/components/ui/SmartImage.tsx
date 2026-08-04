import type { CSSProperties } from 'react';

interface SmartImageProps {
  src?: string | null;
  alt: string;
  className?: string;
  style?: CSSProperties;
}

/**
 * Renders an uploaded image, or a neutral placeholder when the value is empty.
 * Domain rule: images are placeholders — an empty CMS image field must never
 * break the layout.
 *
 * A plain <img> on purpose: sources come from the CMS (backend storage, design
 * assets, external URLs), so next/image would need a remotePatterns entry for
 * every one of them.
 */
export function SmartImage({ src, alt, className, style }: SmartImageProps) {
  if (src && src.trim() !== '') {
    // eslint-disable-next-line @next/next/no-img-element
    return <img src={src} alt={alt} className={className} style={style} />;
  }

  return (
    <div
      className={`flex items-center justify-center bg-gradient-to-br from-slate-200 to-slate-300 text-slate-400 ${className ?? ''}`}
      style={style}
      role="img"
      aria-label={alt}
    >
      <svg width="64" height="64" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path
          d="M3 5.5A2.5 2.5 0 0 1 5.5 3h13A2.5 2.5 0 0 1 21 5.5v13a2.5 2.5 0 0 1-2.5 2.5h-13A2.5 2.5 0 0 1 3 18.5v-13Z"
          stroke="currentColor"
          strokeWidth="1.5"
        />
        <circle cx="8.5" cy="9" r="1.8" stroke="currentColor" strokeWidth="1.5" />
        <path
          d="m4 17 4.5-4.5 3 3L15.5 11 20 15.5"
          stroke="currentColor"
          strokeWidth="1.5"
          strokeLinecap="round"
          strokeLinejoin="round"
        />
      </svg>
    </div>
  );
}
