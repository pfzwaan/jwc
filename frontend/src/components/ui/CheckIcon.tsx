/**
 * Checklist tick: an accent-tinted circle with an accent check.
 *
 * Figma draws the circle as the accent at 20% opacity — compositing that on
 * white reproduces the exported fills exactly (#fcb040 → #FEEFD9,
 * #1E62D4 → #D5E1FF), so one component covers every accent on the site.
 */
export function CheckIcon({ accent, className = '' }: { accent: string; className?: string }) {
  return (
    <svg
      width="31"
      height="31"
      viewBox="0 0 31 31"
      fill="none"
      aria-hidden="true"
      className={`shrink-0 ${className}`}
    >
      <circle cx="15.5" cy="15.5" r="15.5" fill={accent} fillOpacity="0.2" />
      <path
        d="M8.61112 15.4999L13.7778 20.6666L22.3889 12.0555"
        stroke={accent}
        strokeWidth="4"
        strokeLinecap="round"
        strokeLinejoin="round"
      />
    </svg>
  );
}
