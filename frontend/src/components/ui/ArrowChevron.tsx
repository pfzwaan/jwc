/**
 * The "»" glyph used on every button in the design (Figma: 14.5×13.5, 1.5px
 * stroke, trailing chevron at 40% opacity).
 *
 * The `chevron-anim` class is what makes it slide right and back on a loop
 * while its button is hovered or focused — see the rule in globals.css.
 */
export function ArrowChevron({ className = '' }: { className?: string }) {
  return (
    <svg
      width="15"
      height="14"
      viewBox="0 0 14.5 13.4998"
      fill="none"
      aria-hidden="true"
      className={`chevron-anim shrink-0 ${className}`}
    >
      <path
        d="M7.75026 12.7497C7.75026 12.7497 13.75 8.33091 13.75 6.74984C13.7501 5.16877 7.75021 0.750049 7.75021 0.750049"
        stroke="currentColor"
        strokeWidth="1.5"
        strokeLinecap="round"
        strokeLinejoin="round"
      />
      <path
        opacity="0.4"
        d="M0.750096 12.7497C0.750096 12.7497 6.74987 8.33091 6.74989 6.74984C6.7499 5.16877 0.750049 0.750049 0.750049 0.750049"
        stroke="currentColor"
        strokeWidth="1.5"
        strokeLinecap="round"
        strokeLinejoin="round"
      />
    </svg>
  );
}
