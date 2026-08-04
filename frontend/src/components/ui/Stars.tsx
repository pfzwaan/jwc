/**
 * Trustpilot-style rating: filled green squares for the score, grey for the
 * rest. Geometry taken from the design's exported stars-5 asset — each tile is
 * 21.9375×23 with a 1.828px gap, so five tiles measure exactly 117px.
 */
const TILE = 21.9375;
const GAP = 1.828;
const STEP = TILE + GAP;

/** The white star glyph, as exported from Figma. */
const STAR_PATH =
  'M10.9685 15.5009L14.3048 14.6144L15.6987 19.1186L10.9685 15.5009ZM18.6466 9.67901H12.7737L10.9685 3.8811L9.16318 9.67901H3.29033L8.04346 13.2728L6.23818 19.0707L10.9913 15.4769L13.9163 13.2728L18.6466 9.67901Z';

export function Stars({ score = 5, width = 117 }: { score?: number; width?: number }) {
  const filled = Math.max(0, Math.min(5, Math.round(score)));
  const totalWidth = 5 * TILE + 4 * GAP;
  const height = (23 / totalWidth) * width;

  return (
    <svg
      width={width}
      height={height}
      viewBox={`0 0 ${totalWidth} 23`}
      fill="none"
      role="img"
      aria-label={`${filled} van de 5 sterren`}
    >
      {Array.from({ length: 5 }, (_, i) => (
        <g key={i} transform={`translate(${i * STEP} 0)`}>
          <path d={`M${TILE} 0H0V23H${TILE}V0Z`} fill={i < filled ? 'var(--color-success)' : '#dcdce6'} />
          <path d={STAR_PATH} fill="white" />
        </g>
      ))}
    </svg>
  );
}
